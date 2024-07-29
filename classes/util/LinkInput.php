<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\util;

use ILIAS\UI\Component\Tree\TreeRecursion;
use ILIAS\UI\Implementation\Component\Input\Field\Numeric;
use ilTree;

class LinkInput
{
    private \ILIAS\UI\Factory $factory;
    private \ILIAS\UI\Renderer $renderer;
    private \ILIAS\Refinery\Factory $refinery;
    private ilTree $tree;

    private \ILIAS\HTTP\Services $http;
    private \ILIAS\HTTP\Wrapper\ArrayBasedRequestWrapper $query;
    private const ALLOWED_TYPES = [
        'cat',
        'crs',
        'grp',
    ];

    public function __construct()
    {
        // todo container
        global $DIC;
        $this->factory = $DIC->ui()->factory();
        $this->renderer = $DIC->ui()->renderer();
        $this->refinery = $DIC->refinery();
        $this->tree = $DIC->repositoryTree();
        $this->http = $DIC->http();
        $this->query = $DIC->http()->wrapper()->query();

        $this->asyncEndpoint();
    }

    private function asyncEndpoint(): void
    {
        if ($this->query->has('async_ref')) {
            $ref_id = $this->query->retrieve('async_ref', $this->refinery->kindlyTo()->int());
            $this->expandableTree($ref_id);
            exit;
        }
    }

    public function getLinkButton(string $label, int $default_value): Numeric
    {
        $field = $this->factory->input()->field();

        return $field->numeric($label)
            ->withByline($this->linkPickerModal())
            ->withValue($default_value)
            ->withOnLoadCode(function ($id) {
                return <<<JS
                (function() {
                    const el = document.getElementById('$id');
                    el.id = 'link_input_element';
                })();
                JS;
            })
            ->withRequired(true);
    }

    private function linkPickerModal(): string
    {
        $content = $this->expandableTree();

        $modal = $this->factory->modal()->roundtrip('ILIAS Link', $this->factory->legacy($content));
        $button1 = $this->factory->button()->standard('ILIAS Link', '#')
            ->withOnClick($modal->getShowSignal());

        return $this->renderer->render([$button1, $modal]);
    }

    private function expandableTree($ref_id = null): string
    {
        /** @var ilTree $ilTree */
        $ilTree = $this->tree;

        if (is_null($ref_id)) {
            $do_async = false;
            $ref_id = ROOT_FOLDER_ID;
            $data = [$ilTree->getNodeData($ref_id)];
        } else {
            $do_async = true;
            $data = $ilTree->getChildsByTypeFilter($ref_id, self::ALLOWED_TYPES);
            if (count($data) === 0) {
                return '';
            }
        }

        $recursion = new class () implements TreeRecursion {
            public function getChildren($record, $environment = null): array
            {
                return [];
            }

            public function build($factory, $record, $environment = null): \ILIAS\UI\Component\Tree\Node\Node
            {
                $ref_id = $record['ref_id'];
                $label = $record['title'] . ' (' . $record['type'] . ', ' . $ref_id . ')';
                $icon = $environment['icon_factory']->standard($record["type"], '');
                $url = $this->getAsyncURL($environment, $ref_id);

                $node = $factory->simple($label, $icon)
                    ->withOnLoadCode(function ($id) use ($ref_id) {
                        return <<<JS
                        (function() {
                            const node = document.getElementById('$id');
                            const node_label = node.querySelector('.node-label');
                            node_label.addEventListener('click', () => {
                                const link_input_element = document.getElementById('link_input_element');
                                link_input_element.value = $ref_id;
                            });
                        })();
                        JS;
                    })
                    ->withAsyncURL($url);

                return $node;
            }

            protected function getAsyncURL($environment, string $ref_id): string
            {
                $url = $environment['url'];
                $base = substr($url, 0, strpos($url, '?') + 1);
                $query = parse_url($url, PHP_URL_QUERY);
                if ($query) {
                    parse_str($query, $params);
                } else {
                    $params = [];
                }
                $params['async_ref'] = $ref_id;
                $url = $base . http_build_query($params);
                return $url;
            }
        };

        $environment = [
            'url' => $this->http->request()->getRequestTarget(),
            'icon_factory' => $this->factory->symbol()->icon()
        ];

        $tree = $this->factory->tree()->expandable("Label", $recursion)
            ->withEnvironment($environment)
            ->withData($data);

        if (! $do_async) {
            return $this->renderer->render($tree);
        } else {
            echo $this->renderer->renderAsync($tree->withIsSubTree(true));
            return '';
        }
    }
}
