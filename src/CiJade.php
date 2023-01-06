<?php

/**
 * @author kylekatarnls
 */
trait CiJade
{
    /**
     * @var array
     */
    private $jade_vars = [];

    /**
     * @var \Pug\Pug|null
     */
    protected $jade;

    /**
     * @var string|null
     */
    protected $jade_view_path;

    /**
     * @var bool
     */
    protected $allow_jade_file = true;

    /**
     * Allow legacy .jade file extension. (Reduce performances).
     *
     * @return $this
     */
    public function allowJadeFile()
    {
        $this->allow_jade_file = true;

        return $this;
    }

    /**
     * Disallow legacy .jade file extension. (Improve performances).
     *
     * @return $this
     */
    public function disallowJadeFile()
    {
        $this->allow_jade_file = false;

        return $this;
    }

    /**
     * Returns true if legacy .jade file extension is allowed.
     *
     * @return bool
     */
    public function isJadeFileAllowed()
    {
        return $this->allow_jade_file;
    }

    /**
     * Extract view path from the options or return the default app view path.
     *
     * @param array $options
     *
     * @return string
     */
    private function extractViewPathFromOptions(array &$options)
    {
        if (isset($options['view_path'])) {
            $path = $options['view_path'];
            unset($options['view_path']);

            return $path;
        }

        return APPPATH.'views';
    }

    /**
     * Create the given cache directory.
     *
     * @param string $directory cache folder path
     *
     * @throws Exception
     */
    private function createCacheDirectory($directory)
    {
        if (!file_exists($directory) && !@mkdir($directory, 0777, true)) {
            throw new Exception('Cache folder does not exists and cannot be created.', 1);
        }
    }

    /**
     * Setup options for Pug cache.
     *
     * @param array $options
     *
     * @throws Exception
     */
    private function setUpCacheOnOptions(array &$options)
    {
        if (isset($options['cache'])) {
            if ($options['cache'] === true) {
                $options['cache'] = APPPATH.'cache/jade';
            }
            $this->createCacheDirectory($options['cache']);
        }
    }

    /**
     * Change the view settings.
     *
     * @param array|null $options Pug options
     *
     * @throws Exception if the cache folder does not exists and cannot be created
     *
     * @return $this
     */
    public function settings(array $options = null)
    {
        if (is_null($options)) {
            $options = defined('static::SETTINGS') ? ((array) static::SETTINGS) : [];
        }

        $this->jade_view_path = $this->extractViewPathFromOptions($options);
        $this->setUpCacheOnOptions($options);
        $className = class_exists('Pug\Pug') ? 'Pug\Pug' : 'Jade\Jade';
        $this->jade = new $className($options);

        return $this;
    }

    /**
     * Returns a jade file path if it match the given view path without extension.
     *
     * @param string $view
     *
     * @return string
     */
    private function lookForJadeFile($view)
    {
        $view .= '.jade';
        if (!file_exists($view)) {
            $isIndex = (strtr('\\', '/', substr($view, -11)) === '/index.jade');
            $view = $isIndex ? substr($view, 0, -11).'.jade' : substr($view, 0, -5).DIRECTORY_SEPARATOR.'index.jade';
        }

        return $view;
    }

    /**
     * Returns the matching path of a given view name.
     *
     * @param string $view view name
     *
     * @return string
     */
    public function getViewPath($view)
    {
        $view = $this->jade_view_path.DIRECTORY_SEPARATOR.$view.'.pug';
        if (!file_exists($view)) {
            $isIndex = (strtr('\\', '/', substr($view, -10)) === '/index.pug');
            $view = $isIndex ? substr($view, 0, -10).'.pug' : substr($view, 0, -4).DIRECTORY_SEPARATOR.'index.pug';
            if ($this->isJadeFileAllowed() && !file_exists($view)) {
                $view = $this->lookForJadeFile(substr($view, 0, $isIndex ? -4 : -10));
            }
        }

        return $view;
    }

    /**
     * Render the view with Pug.
     *
     * @param string $view   view name/path
     * @param array  $data   list of local variables
     * @param bool   $return true to returns the rendered view, false to display it
     *
     * @throws Exception
     *
     * @return $this|string
     */
    public function renderPugView($view, $data = [], $return = false)
    {
        if (!$this->jade) {
            $this->settings();
        }

        $view = $this->getViewPath($view);
        $data = array_merge($this->getAllVars(), $data);
        $method = method_exists($this->jade, 'renderFile')
            ? [$this->jade, 'renderFile']
            : [$this->jade, 'render'];
        if ($return) {
            return call_user_func($method, $view, $data);
        }

        echo call_user_func($method, $view, $data);

        return $this;
    }

    /**
     * Render the view with Pug.
     *
     * @param string|null $view   view name/path
     * @param array       $data   list of local variables
     * @param bool        $return true to returns the rendered view, false to display it
     *
     * @throws Exception
     *
     * @return $this|string
     */
    public function view($view = null, $data = [], $return = false)
    {
        if (is_array($view) || $view === true) {
            $return = (bool) $data;
            $data = $view;
            $view = null;
        }
        if ($data === true) {
            $data = [];
            $return = true;
        }
        if (is_null($view)) {
            $view = $this->router->class.DIRECTORY_SEPARATOR.$this->router->method;
        }

        return $this->renderPugView($view, $data, $return);
    }

    /**
     * Render the view with Pug and returns the HTML output.
     *
     * @param string|null $view view name/path
     * @param array       $data list of local variables
     *
     * @throws Exception
     *
     * @return string
     */
    public function renderView($view = null, $data = [])
    {
        return $this->view($view, $data, true);
    }

    /**
     * Render the view with Pug and display the HTML output.
     *
     * @param string|null $view view name/path
     * @param array       $data list of local variables
     *
     * @throws Exception
     *
     * @return $this
     */
    public function displayView($view = null, $data = [])
    {
        return $this->view($view, $data, false);
    }

    public function addVars(array $vars)
    {
        if (isset($this->load) && is_object($this->load) && method_exists($this->load, 'vars')) {
            $this->load->vars($vars);

            return;
        }

        $this->jade_vars = array_merge($this->jade_vars, $vars);
    }

    /**
     * @return array
     */
    public function getAllVars()
    {
        if (isset($this->load) && is_object($this->load) && method_exists($this->load, 'get_vars')) {
            return $this->load->get_vars();
        }

        return $this->jade_vars;
    }
}
