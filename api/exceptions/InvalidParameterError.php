<?php

/**
 * Misuse cases of the parameter with strict formatted label
 */
enum ParameterErrorCase
{
    case Empty;
    case Long;
    case Format;
    case Short;
    case Unknown;

    public function code(): int
    {
        return match($this) {
            ParameterErrorCase::Empty => 0,
            ParameterErrorCase::Long => 1,
            ParameterErrorCase::Format => 2,
            ParameterErrorCase::Short => 3,
            ParameterErrorCase::Unknown => 4,
        };
    }
}

/**
 * info format:
 *
 * info:{
 * parameter:name of the incorrect parameter
 * case:case of misuse of the parameter, integer to be siwtched on
 * }
 */

class InvalidParameterError extends __CustomExceptions {
    private string $parameter;
    private ParameterErrorCase $case;

    public function __construct(ParameterErrorCase $case, string $parameter, string $message = "",  ?Throwable $previous = null){
        parent::__construct($message, 1, 400, $previous);
        $this->case = $case;
        $this->parameter = $parameter;
    }

    /**
     * Inform of the error with parameter and case error info
     *
     * Kills the process
     */
    public function respondWithError(){
        $errorInfo = [];

        $errorInfo["parameter"] = $this->parameter;
        $errorInfo["case"] = $this->case->code();

        parent::responseHandler($errorInfo);
    }
}