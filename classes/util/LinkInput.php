<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\util;

use ILIAS\UI\Component\Tree\TreeRecursion;
use ILIAS\UI\Implementation\Component\Input\Field\Numeric;
use ilObject;
use ilTree;

class LinkInput
{
    private \ILIAS\UI\Factory $factory;
    private \ILIAS\UI\Renderer $renderer;
    private \ILIAS\Refinery\Factory $refinery;
    private ilTree $tree;
    private \ILIAS\HTTP\Services $http;
    private \ILIAS\HTTP\Wrapper\ArrayBasedRequestWrapper $query;
    private \ilObjUser $user;
    private \ILIAS\DI\RBACServices $rbac;


    private const ALLOWED_TYPES = [
        'cat', // category
        'catr', // category copy
        'crs', // course
        'wiki', // wiki
        'frm', // forum
        'chtr', // chatroom
        'exc', // exercise
        'file', // files
        'grp', // group
        'tst', // test
        'wbdv', // web Dav
        'cmix', // cmi5
        'fold', // folder
        'glo', // glossary
        'feed', // external feed
        'book', // booking manager
        'blog', // blog object
        'prg', // study programme
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
        $this->user = $DIC->user();
        $this->rbac = $DIC->rbac();

        $this->asyncEndpoint();
    }

    /**
     * @return void
     */
    private function asyncEndpoint(): void
    {
        if ($this->query->has('async_ref')) {
            $ref_id = $this->query->retrieve('async_ref', $this->refinery->kindlyTo()->int());
            $this->expandableTree($ref_id);
            exit;
        }
    }

    /**
     * @param string $label
     * @param int $default_value
     * @return Numeric
     */
    public function getLinkButton(string $label, int $default_value): Numeric
    {
        $field = $this->factory->input()->field();

        $ref_id = $default_value;
        $obj_id = ilObject::_lookupObjId($ref_id);
        $title = ilObject::_lookupTitle($obj_id);
        $description = ilObject::_lookupDescription($obj_id);

        return $field->numeric($label)
            ->withByline($this->linkPickerModal())
            ->withValue($default_value)
            ->withAdditionalTransformation(
                $this->refinery->custom()->constraint(
                    fn ($value) => ilObject::_exists($value, true),
                    'Object not found'
                )
            )
            ->withOnLoadCode(function ($id) use ($ref_id, $title, $description) {
                return <<<JS
                (function() {
                    const el = document.getElementById('$id');
                    el.id = 'link_input_element';
                    el.style.visibility = 'hidden';
                    const info = document.createElement('div');
                    info.style.marginTop = '20px';
                    info.id = 'ilias_link_info';
                    el.parentElement.appendChild(info);
                    info.innerHTML = '<b>$title</b><br>Ref ID: $ref_id<br><br>$description';
                })();
                JS;
            })
            ->withRequired(true);
    }

    /**
     * @return string
     */
    private function linkPickerModal(): string
    {
        $content = $this->expandableTree() . "<div id='ilias_link_info' style='margin-top: 20px;'></div>";

        $modal = $this->factory->modal()->roundtrip('ILIAS Link', $this->factory->legacy($content))->withCancelButtonLabel('ok');
        $button1 = $this->factory->button()->standard('ILIAS Link', '#')
            ->withOnClick($modal->getShowSignal());

        return $this->renderer->render([$button1, $modal]);
    }

    /**
     * @param $ref_id
     * @return string
     */
    private function expandableTree($ref_id = null): string
    {
        /** @var ilTree $ilTree */
        $ilTree = $this->tree;

        if (is_null($ref_id)) {
            $do_async = false;
            $ref_id = $this->user->getTimeLimitOwner();
            $ref_id = $ref_id === 7 ? ROOT_FOLDER_ID : $ref_id;
            $data = [$ilTree->getNodeData($ref_id)];

            $data = array_filter($data, fn ($item) => $this->rbac->system()->checkAccess('visible', (int) $item['ref_id']));

        } else {
            $do_async = true;
            $data = $ilTree->getChildsByTypeFilter($ref_id, self::ALLOWED_TYPES);

            $data = array_filter($data, fn ($item) => $this->rbac->system()->checkAccess('visible', (int) $item['ref_id']));

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

                $obj_id = ilObject::_lookupObjId((int) $ref_id);
                $title = ilObject::_lookupTitle($obj_id);
                $description = ilObject::_lookupDescription($obj_id);

                $node = $factory->simple($label, $icon)
                    ->withOnLoadCode(function ($id) use ($ref_id, $title, $description) {
                        return <<<JS
                        (function() {
                            const node = document.getElementById('$id');
                            const node_label = node.querySelector('.node-label');
                            node_label.addEventListener('click', () => {
                                const ilias_link_info_elements = Array.from(document.querySelectorAll('#ilias_link_info'));
                                ilias_link_info_elements.forEach(ilias_link_info => {
                                    ilias_link_info.innerHTML = '<b>$title</b><br>Ref ID: $ref_id<br><br>$description';
                                });
                                const link_input_element = document.getElementById('link_input_element');
                                link_input_element.value = $ref_id;
                            });
                        })();
                        JS;
                    })
                    ->withAsyncURL($url);

                return $node;
            }

            /**
             * @param $environment
             * @param string $ref_id
             * @return string
             */
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
