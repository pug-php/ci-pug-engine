<?php

/**
 * @author kylekatarnls
 */

trait Jade {

    protected $jade;
    protected $jade_view_path;

    public function settings(array $options = array()) {

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
        $this->jade = new Jade\Jade($options);
        return $this;
    }

    public function view($view, array $data = array(), $return = false) {

        if(! $this->jade) {
            $this->settings();
        }
        $view = $this->jade_view_path . DIRECTORY_SEPARATOR . $view . '.jade';
        $data = array_merge($this->load->get_vars(), $data);
        if($return) {
            return $this->jade->render($view, $data);
        } else {
            echo $this->jade->render($view, $data);
            return $this;
        }
    }
}
