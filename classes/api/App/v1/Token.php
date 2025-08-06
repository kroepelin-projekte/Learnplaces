<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;
use Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Handler\HTTPHandler;
use Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Handler\PKCEHandler;

class Token
{
    /**
     * @throws ResponseSendingException
     */
    public function endpoint(array $params, array $request_body): void
    {
        $logger = \ilLoggerFactory::getLogger('Learnplaces');
        $logger->info('API Token endpoint');
        if (!isset($request_body['state'], $request_body['code'], $request_body['code_verifier'])) {
            $logger->info('[API Token endpoint] State, Code or Code Verifier not set.');
            Response::send(401, DEVMODE ? 'token route: wrong body' : '', ['success' => false]);
        }

        $http_handler = new HTTPHandler();
        $http_handler->setState($request_body['state']);
        $http_handler->setCode($request_body['code']);
        $http_handler->setCodeVerifier($request_body['code_verifier']);

        (new PKCEHandler($http_handler))->initTokenAuth();

        Response::send(
            201, null, ['success' => true]
        );
    }
}