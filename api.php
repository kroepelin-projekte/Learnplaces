<?php

use Flulabs\Learnplaces\Adapters\Storage\LearnplacesRepository;

require_once './src/Adapters/Storage/LearnplacesRepository.php';

header("Content-Type:application/json");
$resp = LearnplacesRepository::new();
print_r($resp->getAllReadableLearnplaces());exit;
response(200,"Product Found",["learnplace1", "learnplace2"]);




function response($status,$status_message,$data)
{
    header("HTTP/1.1 ".$status);

    $response['status']=$status;
    $response['status_message']=$status_message;
    $response['data']=$data;

    $json_response = json_encode($response);
    echo $json_response;
}
