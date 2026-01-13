<?php

class Request
{
    private array $server;

    public function __construct()
    {
        $this->server = filter_input_array(INPUT_SERVER);
    }
}