<?php


class ErrorHandler {

    public function __construct($message = "Generic error message", $status = 200)
    {
        /** Rewrite options */
        $this->message = $message;
        $this->status  = $status;
    }

    public function setMessage($message){
        $this->message = $message;
    }

    public function setStatus($status){
        $this->status = $status;
    }
    
    public function __invoke($request, $response, $exception) {
        
        return $response->withJSON(
            [
                'message' => $exception->getMessage(),
            ],
            method_exists($exception, "getResponseStatus") ? $exception->getResponseStatus() : 200,
            JSON_UNESCAPED_UNICODE
        );
    }
 }