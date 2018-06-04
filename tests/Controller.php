<?php

namespace Tests;

define('APPPATH', __DIR__.'/');

class Vars
{
    public function get_vars()
    {
        return [
            'foo' => 'bar',
        ];
    }
}

class Router
{
    public $foo;
    public $bar;

    public function __construct()
    {
        $this->class = 'foo';
        $this->method = 'bar';
    }
}

class Controller
{
    use \CiPug;

    /**
     * @var Vars
     */
    protected $load;

    /**
     * @var Router
     */
    protected $router;

    public function __construct()
    {
        $this->load = new Vars();
        $this->router = new Router();
    }

    /**
     * @throws \Exception
     */
    public function index()
    {
        $this->view('myview');
    }
}
