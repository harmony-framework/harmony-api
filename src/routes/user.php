<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->post('/getUserInformation', function (Request $request, Response $response, array $args) {
    // get params
    $params = $request->getParsedBody();
   
    // Sample log message
    $this->logger->info("User::getUserInformation -> params: ".json_encode($params));
    // get database
    $db = $this->db;
    // check if user not exists
    $result = $db->select("* FROM users WHERE `username`=:username",array("username" => $params['username']));
 
    if(count($result) == 0){
        throw (new \ExceptionResponse("User not exists"));
    }
    
    $id = $result[0]->id;
    // return response
    if($id){
        return $response->withJSON(
            [
                'id' => $id
            ],
            200,
            JSON_UNESCAPED_UNICODE
        );
    }
    
});
