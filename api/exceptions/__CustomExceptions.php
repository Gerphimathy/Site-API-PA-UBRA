<?php

class __CustomExceptions extends Exception {
    private int $htmlCode;
    private string $detailedErrMessage;

    /**
     * @param string $message Constructed Error message
     * @param int $code Internal error code, uniform to be used by switch in javascript
     * @param int $htmlCode HTML response code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, int $htmlCode = 500, ?Throwable $previous = null){
        parent::__construct($message, $code, $previous);
        $this->htmlCode = $htmlCode;
    }

    /**
     * Base Response Handler for exceptions, kills the process after response
     * @param array $debugInfo debug info used only for internal server errors, free format
     * @param array $errorInfo strictly formatted parsable error information
     */
    protected function responseHandler(array $errorInfo = []){
        $error = ["code"=>self::getCode(), "message"=>self::getMessage(), "info"=>(object)$errorInfo];

        HtmlResponseHandler::formatedResponse($this->htmlCode, [], ["error"=>$error]);
    }
}