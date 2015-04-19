<?php

/**
 * @author kylekatarnls
 */

class Jade {
    
    protected $CI;
    protected $jade;
    protected $view_path;

    public function __construct(array $options = array()) {

        if(isset($options['view_path'])) {
            $this->view_path = $options['view_path'];
            unset($options['view_path']);
        } else {
            $this->view_path = APPPATH . 'views';
        }
        if(isset($options['cache'])) {
            if($options['cache'] === TRUE) {
                $options['cache'] = __DIR__ . '/../cache/jade';
            }
            if(! file_exists($options['cache']) && ! mkdir($options['cache'], 0777, TRUE)) {
                throw new Exception("Cache folder does not exists and cannot be created.", 1);
            }
        }
        $this->CI =& get_instance();
        $this->jade = new Jade\Jade($options);
    }

    public function view($view, array $data = array(), $return = false) {

        $view = $this->view_path . DIRECTORY_SEPARATOR . $view . '.jade';
        $data = array_merge($this->CI->load->get_vars(), $data);
        if($return) {
            return $this->jade->render($view, $data);
        } else {
            echo $this->jade->render($view, $data);
        }
    }
}
