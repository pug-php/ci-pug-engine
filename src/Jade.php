<?php

/**
 * @author kylekatarnls
 */

trait Jade {

    protected $jade;
    protected $jade_view_path;

    public function settings(array $options = NULL) {

        if(is_null($options)) {
            $options = defined('static::SETTINGS') ? ((array) static::SETTINGS) : array();
        }
        if(isset($options['view_path'])) {
            $this->jade_view_path = $options['view_path'];
            unset($options['view_path']);
        } else {
            $this->jade_view_path = APPPATH . 'views';
        }
        if(isset($options['cache'])) {
            if($options['cache'] === TRUE) {
                $options['cache'] = APPPATH . 'cache/jade';
            }
            if(! file_exists($options['cache']) && ! mkdir($options['cache'], 0777, TRUE)) {
                throw new Exception("Cache folder does not exists and cannot be created.", 1);
            }
        }
        $className = class_exists('Pug\Pug') ? 'Pug\Pug' : 'Jade\Jade';
        $this->jade = new $className($options);
        return $this;
    }

    public function view($view = NULL, array $data = array(), $return = FALSE) {

        if(is_array($view) || $view === TRUE) {
            $return = !! $data;
            $data = $view;
            $view = NULL;
        }
        if($data === TRUE) {
            $data = array();
            $return = TRUE;
        }
        if(is_null($view)) {
            $view = $this->router->class . DIRECTORY_SEPARATOR . $this->router->method;
        }
        if(! $this->jade) {
            $this->settings();
        }
        $view = $this->jade_view_path . DIRECTORY_SEPARATOR . $view . '.pug';
        if(! file_exists($view)) {
            $isIndex = (strtr('\\', '/', substr($view, -11)) === '/index.pug');
            $view = $isIndex ? substr($view, 0, -11) . '.pug' : substr($view, 0, -5) . DIRECTORY_SEPARATOR . 'index.pug';
            if(! file_exists($view)) {
                $view = $this->jade_view_path . DIRECTORY_SEPARATOR . $view . '.jade';
                if(! file_exists($view)) {
                    $isIndex = (strtr('\\', '/', substr($view, -11)) === '/index.jade');
                    $view = $isIndex ? substr($view, 0, -11) . '.jade' : substr($view, 0, -5) . DIRECTORY_SEPARATOR . 'index.jade';
                }
            }
        }
        $data = array_merge($this->load->get_vars(), $data);
        $method = method_exists($this->jade, 'renderFile')
            ? [$this->jade, 'renderFile']
            : [$this->jade, 'render'];
        if($return) {
            return call_user_func($method, $view, $data);
        } else {
            echo call_user_func($method, $view, $data);
            return $this;
        }
    }
}
