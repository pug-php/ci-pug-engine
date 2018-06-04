<?php

/**
 * @author kylekatarnls
 */
trait CiJade
{
    protected $jade;
    protected $jade_view_path;

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
        if (isset($options['view_path'])) {
            $this->jade_view_path = $options['view_path'];
            unset($options['view_path']);
        } else {
            $this->jade_view_path = APPPATH.'views';
        }
        if (isset($options['cache'])) {
            if ($options['cache'] === true) {
                $options['cache'] = APPPATH.'cache/jade';
            }
            if (!file_exists($options['cache']) && !@mkdir($options['cache'], 0777, true)) {
                throw new Exception('Cache folder does not exists and cannot be created.', 1);
            }
        }
        $className = class_exists('Pug\Pug') ? 'Pug\Pug' : 'Jade\Jade';
        $this->jade = new $className($options);

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
        if (!$this->jade) {
            $this->settings();
        }
        $view = $this->jade_view_path.DIRECTORY_SEPARATOR.$view.'.pug';
        if (!file_exists($view)) {
            $isIndex = (strtr('\\', '/', substr($view, -10)) === '/index.pug');
            $view = $isIndex ? substr($view, 0, -10).'.pug' : substr($view, 0, -4).DIRECTORY_SEPARATOR.'index.pug';
            if (!file_exists($view)) {
                $view = substr($view, 0, $isIndex ? -4 : -10).'.jade';
                if (!file_exists($view)) {
                    $isIndex = (strtr('\\', '/', substr($view, -11)) === '/index.jade');
                    $view = $isIndex ? substr($view, 0, -11).'.jade' : substr($view, 0, -5).DIRECTORY_SEPARATOR.'index.jade';
                }
            }
        }
        $data = array_merge($this->load->get_vars(), $data);
        $method = method_exists($this->jade, 'renderFile')
            ? [$this->jade, 'renderFile']
            : [$this->jade, 'render'];
        if ($return) {
            return call_user_func($method, $view, $data);
        }

        echo call_user_func($method, $view, $data);

        return $this;
    }
}
