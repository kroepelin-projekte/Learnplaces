<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;
use KPG\Learnplaces\persistence\entity\VisitJournal;
use ILIAS\ResourceStorage\Identification\ResourceIdentification;
use ilObject;
use ILIAS\DI\Container;
use KPG\Learnplaces\persistence\dto\Learnplace;
use ilObjLearnplaces;
use KPG\Learnplaces\persistence\dto\Configuration;

class Learnplaces
{
    private Container $dic;
    private const TYPE_COURSE = "crs";
    private const TYPE_GROUP = "grp";

    public function __construct()
    {
        global $DIC;
        $this->dic = $DIC;
    }

    /**
     * Handles an API endpoint operation to retrieve and process learnplaces within a container.
     *
     * This method takes provided parameters and request body to identify a container, validate it,
     * and fetch the associated learnplaces. Depending on the result, it sends an appropriate
     * HTTP response indicating success or no content.
     *
     * @param array $params       Parameters passed to the endpoint, including 'container_ref_id'.
     * @param array $request_body The body of the request, typically containing additional data.
     *
     * @return void
     */
    public function endpoint(array $params, array $request_body): void
    {
        $container_ref_id = $params['container_ref_id'];

        $container_obj_id = ilObject::_lookupObjectId($container_ref_id);
        $container_type = ilObject::_lookupType($container_obj_id);

        $this->checkContainer($container_ref_id, $container_type);

        $container_learnplaces = $this->getContainerLearnPlacesByContainerRefID($container_ref_id, $container_type);

        if (empty($container_learnplaces)) {
            Response::send(204, null, []);
        }
        Response::send(200, null, $container_learnplaces);
    }

    /**
     * Validates the container's existence, type, and user access rights.
     *
     * This function performs several checks on the container referenced by its ID:
     * - Ensures the reference ID is a valid integer.
     * - Confirms the container exists and is not in the trash.
     * - Verifies the container type is either a course or a group.
     * - Checks if the current user has access to the container.
     *
     * Sends an HTTP 400 response with an error message if any validation fails.
     *
     * @param int    $container_ref_id The reference ID of the container to validate.
     * @param string $container_type   The type of the container (e.g., course or group).
     *
     * @return void
     */
    private function checkContainer(int $container_ref_id, string $container_type): void
    {
        // Check ref_id is an integer
        if (!is_numeric($container_ref_id)) {
            Response::send(400, DEVMODE ? 'Container ref_id is not an integer' : null, []);
        }

        // Check Container exists
        if (!ilObject::_exists($container_ref_id, true)) {
            Response::send(400, DEVMODE ? "Container doesn't exist" : null, []);
        }

        // Check Container in Trash
        if (ilObject::_isInTrash($container_ref_id)) {
            Response::send(400, DEVMODE ? 'Container is in Trash' : null, []);
        }

        // Check Type
        if (!in_array($container_type, [self::TYPE_COURSE, self::TYPE_GROUP])) {
            Response::send(400, DEVMODE ? 'Container isnt Course or Group' : null, []);
        }

        // Check User Access to Container
        if (!\ilParticipants::_isParticipant($container_ref_id, $this->dic->user()->getId())) {
            Response::send(400, DEVMODE ? 'User has no Access to Container' : null, []);
        }
    }

    /**
     * Retrieves a list of learnplaces associated with a specified container by its reference ID.
     *
     * The function fetches all learnplaces within the specified container, checking their eligibility
     * based on the container type and using additional validations such as whether the learnplaces
     * are in the trash, offline, or have restricted visibility. It collates the relevant information
     * for each valid learnplace, including metadata, tags, visit status, and images.
     *
     * @param int    $container_ref_id Reference ID of the container to search for learnplaces.
     * @param string $container_type   Type of the container (e.g., course or other types).
     *
     * @return array Returns an associative array containing the container title and a list of valid learnplaces.
     *               Each learnplace entry contains details such as ID, title, description, tags, tile image,
     *               and visit status.
     */
    private function getContainerLearnPlacesByContainerRefID(int $container_ref_id, string $container_type): array
    {
        // Get All Learnplaces from Container
        $learnplaces = $this->dic->repositoryTree()->getSubTree(
            $this->dic->repositoryTree()->getNodeData($container_ref_id),
            true,
            ['xsrl']
        );

        $all_learnplaces['container_title'] = ilObject::_lookupTitle(ilObject::_lookupObjectId($container_ref_id));
        $all_learnplaces['learn_places'] = [];

        foreach ($learnplaces as $learnplace) {

            //Check if the container is of type "Course".
            // If it is, each learning location must be checked to see if a group qualifies as a container.
            if ($container_type == self::TYPE_COURSE and $this->checkPossiblyGroupContainerByLearnPlaceRefId(
                    $learnplace['ref_id'], $container_ref_id
                )) {
                continue;
            }

            $obj_learnplace = new \ilObjLearnplaces($learnplace['ref_id']);
            $obj_learnplace_repository = PluginContainer::resolve(LearnplaceRepository::class)->findByObjectId($obj_learnplace->getId()); // eventuell nur mit ilObject::getObjID. zeile Darüber
            $obj_learnplace_configuration = $obj_learnplace_repository->getConfiguration();

            // Check if Learnplace in trash or offline or visibility is never
            if(!$this->checkLearnplace($obj_learnplace_repository, $obj_learnplace_configuration)) {
                continue;
            }

            // visit status
            $visit_result = $this->dic->database()->query(
                "SELECT * FROM xsrl_visit_journal WHERE fk_learnplace_id = " . $obj_learnplace->getID(
                ) . " AND user_id = " . $this->dic->user()->getId()
            );

            // Learnplace Tags
            $string_tags = trim($obj_learnplace_configuration->getTags(), ',');

            // build Learnplace Infos
            $all_learnplaces['learn_places'][] = [
                "id" => $obj_learnplace_repository->getId(),
                "obj_id" => $obj_learnplace_repository->getObjectId(),
                "title" =>   $obj_learnplace->getTitle(),
                "description" => nl2br($obj_learnplace->getDescription()),
                "tile_image" => $obj_learnplace->getObjectProperties()->getPropertyTileImage()->getTileImage(
                )->getRid(),
                "visited" => $visit_result->rowCount() > 0,
                "tags" =>  explode(",", $string_tags)
            ];
        }
        return $all_learnplaces;
    }

    /**
     * Checks the status of the provided learnplace repository and its configuration.
     *
     * This function determines whether the learnplace repository is in the trash
     * and evaluates its configuration for online status and default visibility.
     *
     * @param Learnplace    $obj_learnplace_repository    Instance of the learnplace repository to check.
     * @param Configuration $obj_learnplace_configuration Configuration of the learnplace to validate.
     *
     * @return bool Returns true if the learnplace is not in the trash, is online, and visible; otherwise, false.
     */
    private function checkLearnplace(Learnplace $obj_learnplace_repository, Configuration $obj_learnplace_configuration): bool
    {
        // Check Learnplace is in trash
        if(ilObject::_isInTrash($obj_learnplace_repository->getObjectId())) {
            return false;
        }

        // Check Learnplace offline status
        if(ilObject::lookupOfflineStatus($obj_learnplace_repository->getObjectId())) {
            return false;
        }

        //Check Learnplace is offline or visibility is never
        if (!$obj_learnplace_configuration->isOnline() or $obj_learnplace_configuration->getDefaultVisibility() === "NEVER") {
            return false;
        }
        return true;
    }

    /**
     * Checks whether the given learning place reference ID corresponds to a group container.
     *
     * Iterates through the reversed node path of the provided learning place reference ID and determines
     * if any parent node has a type of group or course. If a course type node is encountered, the method
     * returns false. If a group type node is encountered, the method returns true.
     *
     * @param int $learnplace_ref_id The reference ID of the learning place to check.
     *
     * @return bool True if a group container is found in the node path, false otherwise.
     */
    private function checkPossiblyGroupContainerByLearnPlaceRefId(int $learnplace_ref_id, int $container_ref_id): bool
    {
        foreach (array_reverse($this->dic->repositoryTree()->getNodePath($learnplace_ref_id, $container_ref_id)) as $parent) {
            if($parent['type'] == self::TYPE_COURSE) {
                return false;
            }
            if($parent['type'] == self::TYPE_GROUP) {
                return true;
            }
        }
        return false;
    }
}