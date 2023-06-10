<?php

class DatabaseConnectionError extends __CustomExceptions {
    private string $sqlErrorMessage;
    private string $step;

    public function __construct(string $sqlErrorMessage = "", string $message = "", string $step = "", ?Throwable $previous = null){
        parent::__construct($message, 0, 500, $previous);

        $this->sqlErrorMessage = $sqlErrorMessage;
        $this->step = $step;
    }

    /**
     * Inform of the error with step and sql error debugging info
     *
     * Kills the process
     */
    public function respondWithError(){
        $errorInfo = [];

        $errorInfo["step"] = $this->step;
        $errorInfo["sqlerror"] = $this->sqlErrorMessage;

        parent::responseHandler($errorInfo);
    }

    /**
     * For setting step after initial exception declaration
     * @param string $step Step where the error occurred for debugging info
     */
    public function setStep(string $step){
        $this->step = $step;
    }
}