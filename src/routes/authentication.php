<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->group('/authentication', function(){
    $this->post('/createUser', function (Request $request, Response $response, array $args) {
        // get params
        $params = $request->getParsedBody();
        // Sample log message
        $this->logger->info("Authentication::createUser -> params: ".json_encode($params));
        // get database
        $db = $this->db;
        // check if user not exists
        $result = $db->select("* FROM users WHERE `username`=:username",array("username" => $params['username']));
        
        if(count($result) > 0){
            throw (new \ExceptionResponse("User exists"));
        }
        $authentication = $this->authentication;
        $options = $authentication["options"];
        $params['password'] = password_hash($params['password'], PASSWORD_BCRYPT,$options);
        // create the user
        $data = array(
            "username" => $params['username'],
            "password" => $params['password']
        );
        // get last inserted id
        $id = $db->insert('users', $data);
        
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


    $this->post('/login', function (Request $request, Response $response, array $args) {
        // get params
        $params = $request->getParsedBody();
        // Sample log message
        $this->logger->info("Authentication::login -> params: ".json_encode($params));
        // get database
        $db = $this->db;

        // check if user not exists
        $result = $db->select("* FROM users WHERE `username`=:username LIMIT 1",array("username" => $params['username']));
        
        if(count($result) == 0){
            throw (new \ExceptionResponse("Login failed"));
        }
        $hash = $result[0]->password;
        $token = strtoupper(bin2hex(openssl_random_pseudo_bytes(64)));

        
        $data = array(
            "authToken" => $token,
            "user_id" => $result[0]->id
        );
        // get last inserted id
        $id = $db->insert('authentication', $data);

        // return response
        if(password_verify($params['password'], $hash)){
            $response = $response->withHeader("Authorization", $token);
            return $response->withJSON(
                [
                    'id' => $result[0]->id
                ],
                200,
                JSON_UNESCAPED_UNICODE
            );
        }else{
            throw (new \ExceptionResponse("Login failed"));
        } 
    });

    $this->post('/reset', function (Request $request, Response $response, array $args) {
        // get params
        $params = $request->getParsedBody();
        $authentication = $this->authentication;
        $options = $authentication["options"];
        // Sample log message
        $this->logger->info("Authentication::reset -> params: ".json_encode($params));
        // get database
        $db = $this->db;
    
        // check if user not exists
        $result = $db->select("* FROM users WHERE `username`=:username AND `token`=:token LIMIT 1",array("username" => $params['username'], "token" => $params['token']));
        
        
        if(count($result) == 0 || $result[0]->token == ""){
            throw (new \ExceptionResponse("Reset failed"));
        }
    
        $hash = password_hash($params['password'], PASSWORD_BCRYPT, $options);
    
        $data = array(
            'password' => $hash
        );
        $where = array('id' => $result[0]->id);
        $rowAffected = $db->update("users",$data,$where);
    
        // return response
        return $response->withJSON(
                [
                    'id' => $result[0]->id
                ],
                200,
                JSON_UNESCAPED_UNICODE
        );

    });


    $this->post('/generateToken', function (Request $request, Response $response, array $args) {
        // get params
        $params = $request->getParsedBody();
        // Sample log message
        $this->logger->info("Authentication::generateToken -> params: ".json_encode($params));
        // get database
        $db = $this->db;
    
        // check if user not exists
        $result = $db->select("* FROM users WHERE `username`=:username LIMIT 1",array("username" => $params['username']));
        
        
        if(count($result) == 0){
            throw (new \ExceptionResponse("generate failed"));
        }
    
        $token = bin2hex(openssl_random_pseudo_bytes(16));
    

        $data = array(
            'token' => $token
        );
        $where = array('id' => $result[0]->id);
        $rowAffected = $db->update("users",$data,$where);
        $mailer = $this->mailer;

        $sent = $mailer->sendTemplate("generate-token.html", $token, "Password Change", $params['username']);
        
        // return response
        if($sent){
            return $response->withJSON(
                [
                
                ],
                200,
                JSON_UNESCAPED_UNICODE
            );
        }else{
            throw (new \ExceptionResponse("generate failed"));
        }
        

    });

    $this->post('/logout', function (Request $request, Response $response, array $args) {
        // get params
        $params = $request->getParsedBody();
        // Sample log message
        $this->logger->info("Authentication::logout -> params: ".json_encode($params));
        // get database
        $db = $this->db;
        $authorization = count($request->getHeader('Authorization')) > 0 ? $request->getHeader('Authorization')[0] : "";
        
        $result = $db->select("* FROM authentication WHERE `authToken`=:authToken LIMIT 1",array("authToken" => $authorization));
        
        if(count($result) == 0){
            throw (new \ExceptionResponse("logout failed"));
        }
    
        $where = array('id' => $result[0]->id);
        $rowAffected = $db->delete("authentication",$where);
    
        // return response
        if($rowAffected){
            return $response->withJSON(
                [
                
                ],
                200,
                JSON_UNESCAPED_UNICODE
            );
        }else{
            throw (new \ExceptionResponse("logout failed"));
        }
        

    });
});