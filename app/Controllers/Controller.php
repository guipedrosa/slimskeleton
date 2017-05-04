<?php

namespace App\Controllers;

class Controller
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __get($propertiy)
    {
        if ($this->container->{$propertiy})
        {
            return $this->container->{$propertiy};
        }
    }
}
