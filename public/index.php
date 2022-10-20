<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Lego\App;

$app = new App();

$app->route("GET", "/", function () {
    echo "Hello world!";
});

$app->run();
