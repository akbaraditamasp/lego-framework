<?php
namespace Lego;

use Bramus\Router\Router;
use Dotenv\Dotenv;
use Sabre\HTTP\Response;
use Sabre\HTTP\Sapi;

class App
{
    private Router $router;
    public Request $request;
    public Response $response;

    public function __construct()
    {
        $this->router = new Router();
        $this->request = new Request(Sapi::getRequest());
        $this->response = new Response();

        $cors = new Cors();

        $this->router->before("GET|POST|PUT|DELETE|PATCH|OPTIONS", "/.*", $cors->middleware($this));
        $this->router->match("OPTIONS", "/.*", $cors->options($this));

        $this->router->before("GET|POST|PUT|DELETE|PATCH|OPTIONS", "/.*", function () {
            $dotenv = Dotenv::createImmutable(__DIR__ . "/../");
            $dotenv->load();
        });

        $this->router->before("GET|POST|PUT|DELETE|PATCH|OPTIONS", "/.*", function () {
            Eloquent::boot();
        });
    }

    public function validate($rules) {
        Validation::validate($this, $rules, $this->request->getPostData() + $this->request->getRawFiles());
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
            $this->response->setStatus(200);

            $body = $callback($app);

            if ($body) {
                $this->response->setHeader("Content-Type", "application/json");
                $this->response->setBody(json_encode($body));
            }

            Sapi::sendResponse($this->response);
        });
    }

    public function finish()
    {
        Sapi::sendResponse($this->response);
    }

    public function before($methods, $pattern, $callback)
    {
        $app = $this;
        return $this->router->before($methods, $pattern, function () use ($callback, $app) {
            $callback($app);
        });
    }

    public function mount($pattern, $callback)
    {
        $app = $this;
        return $this->router->mount($pattern, function () use ($callback, $app) {
            $callback($app);
        });
    }

    public function getRouter()
    {
        return $this->router;
    }
}
