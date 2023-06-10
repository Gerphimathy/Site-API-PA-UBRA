<?php


class HtmlResponseHandler{

    /**
     * Formats and posts Json response, kills process
     * @param int $statusCode Html status code
     * @param array $headers additional headers list, the content type is set by default
     * @param array $body body of the response, cf index.php comments for formats
     *
     * Kills process
     */
    public static function formatedResponse(int $statusCode, array $headers = array(), array $body = array()):void{
        header("Content-Type: application/json");

        foreach ($headers as $headerName => $headerValue) {
            header("$headerName: $headerValue");
        }

        http_response_code($statusCode);

        if($body !== array()) echo json_encode($body);
        die();
    }
}