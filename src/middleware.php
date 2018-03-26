<?php
// Application middleware

$authenticator = function($request){
    // get app container
    global $container;
    // get database 
    $db = $container['db'];
    
    $authorization = count($request->getHeader('Authorization')) > 0 ? $request->getHeader('Authorization')[0] : "";
    
    $result = $db->select("* FROM authentication WHERE `authToken`=:authToken LIMIT 1",array("authToken" => $authorization));
    if(count($result) == 0){
        return false;
    }
    return true;
};


$app->add(new Authentication([
    'path' => ['/getUserInformation','/authentication/logout'],
    'passthrough' => [],
    'authenticator' => $authenticator,
    'secure' => true,
    'relaxed' => ['localhost']
]));