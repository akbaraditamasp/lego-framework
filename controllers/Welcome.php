<?php
namespace Controller;

use Lego\App;

class Welcome
{
    public static function index(App $app)
    {
        return ["message" => "Hello World!"];
    }
}
