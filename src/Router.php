<?php

class Router
{
    private RouterConfig $router_config;

    private RouteCollection $route_collection;

    public function __construct(RouterConfig $router_config)
    {
        $this->router_config = $router_config;
    }

}