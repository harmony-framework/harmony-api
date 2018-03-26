<?php

class ExceptionResponse extends \Exception
{
    private $status;
    // Redefine the exception so message isn't optional
    public function __construct($message, $status = 200, $code = 0, Exception $previous = null) {
        $this->status = $status;
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getResponseStatus(){
        return $this->status;
    }
}