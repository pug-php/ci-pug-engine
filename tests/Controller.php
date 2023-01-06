<?php

namespace Tests;

define('APPPATH', __DIR__.'/');

class Vars
{
    private $vars = [
        'foo' => 'bar',
    ];

    public function get_vars()
    {
        return $this->vars;
    }

    public function vars($vars)
    {
        $this->vars = array_merge($this->vars, $vars);
    }

    public function reset()
    {
        $this->vars = [];
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

    public function resetVars()
    {
        $this->load->reset();
    }
}
