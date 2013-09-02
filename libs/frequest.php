<?php

/**
 * HTTPRequest Class
 */
class FRequest extends ArrayObject {
    /**
     * Timestamp
     * @var integer
     */
    public $timestamp;
    /**
     * DateTime
     * @var string
     */
    public $time;

    public $controller;
    public $controllerDefault = 'home';
    public $action;
    public $actionDefault = 'index';

    public $post;
    public $files;
    public $get;

    /**
     * Construct
     * @param string $uri
     */
    public function __construct($uri) {
        $this->time = date('Y-m-d h:i:s', $_SERVER['REQUEST_TIME']);
        $this->timestamp = $_SERVER['REQUEST_TIME'];

        $host_path = substr($_SERVER['SCRIPT_FILENAME'], 0, strlen($_SERVER['SCRIPT_FILENAME']) - strpos(strrev($_SERVER['SCRIPT_FILENAME']), "/"));
        $http_path = "http://" . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strlen($_SERVER['PHP_SELF']) - strpos(strrev($_SERVER['PHP_SELF']), "/"));
        define('APPLICATION_PATH', $host_path);
        define('APPLICATION_URL', $http_path);

        $this->get = new ArrayObject();
        $this->post = new ArrayObject();
        $this->files = new ArrayObject();

        $aux = substr($uri, strlen('/'));

        if (substr($aux, -1) == '/') {
            $aux = substr($aux, 0, -1);
        }

        $url = explode('/', $aux);
        array_shift($url);

        if(count($url)) {
            $this->controller = array_shift($url);
        }else{
            $this->controller = $this->controllerDefault;
        }

        if(count($url)) {
            $this->action = array_shift($url);
        }else{
            $this->action = $this->actionDefault;
        }

        for ($i = 0, $l = count($url); $i < $l; $i++) {
            if ($i % 2) {
                $this->get->offsetSet($url[$i - 1], $url[$i]);
                $this->offsetSet($url[$i - 1], $url[$i]);
            }
        }

        if($_POST) {
            foreach($_POST as $param => $value) {
                $this->post->offsetSet($param, $value);
                $this->offsetSet($param, $value);
            }
        }

        if($_FILES) {
            foreach($_FILES as $param => $value) {
                $this->files->offsetSet($param, $value);
                $this->offsetSet($param, $value);
            }
        }
    }

}