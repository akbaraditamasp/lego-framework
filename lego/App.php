<?php
namespace Lego;

use Bramus\Router\Router;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\Response;
use Sabre\HTTP\Sapi;

class App
{
    private Router $router;
    public RequestInterface $request;
    public Response $response;

    public function __construct()
    {
        $this->router = new Router();
        $this->request = Sapi::getRequest();
        $this->response = new Response();
    }

    public function finish() {
        Sapi::sendResponse($this->response);
    }

    public function set(string $key, $value)
    {
        $this->$key = $value;
    }

    public function run()
    {
        $this->router->run();
    }

    public function route($methods, $pattern, $callback)
    {
        $app = $this;
        return $this->router->match($methods, $pattern, function (...$params) use ($callback, $app) {
            $app->set("params", $params);
            $callback($app);
        });
    }

    public function before($methods, $pattern, $callback)
    {
        $app = $this;
        return $this->router->before($methods, $pattern, function () use ($callback, $app) {
            $callback($app);
        });
    }

    public function mount($pattern, $callback) {
        $app = $this;
        return $this->router->mount($pattern, function () use ($callback, $app) {
            $callback($app);
        });
    }

    public function getRouter() {
        return $this->router;
    }
}
