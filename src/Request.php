<?php

namespace JscPhp\Routes;

class Request {
    private function __construct() {
    }

    public static function getMethod(): string {
        return strtoupper(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));
    }

    public static function getQueryString(): string {
        return filter_input(INPUT_SERVER, 'QUERY_STRING');
    }

    public static function getHttpHost() {
        return filter_input(INPUT_SERVER, 'HTTP_HOST');
    }

    public static function getServerName() {
        return filter_input(INPUT_SERVER, 'SERVER_NAME');
    }

    public static function getServerPort() {
        return filter_input(INPUT_SERVER, 'SERVER_PORT');
    }

    public static function getScriptName() {
        return filter_input(INPUT_SERVER, 'SCRIPT_NAME');
    }

    public static function getRemoteAddr() {
        return filter_input(INPUT_SERVER, 'REMOTE_ADDR');
    }

    public static function getHttpUserAgent() {
        return filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
    }

    public static function getHttpReferer() {
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }

    public static function getHttps() {
        return filter_input(INPUT_SERVER, 'HTTPS');
    }

    public static function getServerProtocol() {
        return filter_input(INPUT_SERVER, 'SERVER_PROTOCOL');
    }

    public static function getContentType() {
        return filter_input(INPUT_SERVER, 'CONTENT_TYPE');
    }

    public static function getContentLength() {
        return filter_input(INPUT_SERVER, 'CONTENT_LENGTH');
    }

    public static function getPathInfo() {
        return filter_input(INPUT_SERVER, 'PATH_INFO');
    }

    public static function getDocumentRoot() {
        return filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
    }

    public static function getScriptFilename() {
        return filter_input(INPUT_SERVER, 'SCRIPT_FILENAME');
    }

    public static function getHttpAccept() {
        return filter_input(INPUT_SERVER, 'HTTP_ACCEPT');
    }

    public static function getHttpAcceptLanguage() {
        return filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE');
    }

    public static function getHttpAcceptEncoding() {
        return filter_input(INPUT_SERVER, 'HTTP_ACCEPT_ENCODING');
    }

    public static function getRequestScheme() {
        return filter_input(INPUT_SERVER, 'REQUEST_SCHEME');
    }

    public static function getUri(): string {
        return filter_input(INPUT_SERVER, 'REQUEST_URI');
    }
}