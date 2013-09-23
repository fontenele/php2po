<?php

class Index {

    public static function configure() {
        session_start();

        error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
        ini_set('display_errors', false);

        spl_autoload_register(array(__CLASS__, 'autoload'));

        self::setConstants();
        self::setIncludes();
        self::setGettext();
    }

    public static function autoload($class) {
        if (file_exists(APPLICATION_PATH . 'controllers/' . $class . '.php')) {
            require_once(APPLICATION_PATH . 'controllers/' . $class . '.php');
            return true;
        }
    }

    public static function setConstants() {
        $host_path = substr($_SERVER['SCRIPT_FILENAME'], 0, strlen($_SERVER['SCRIPT_FILENAME']) - strpos(strrev($_SERVER['SCRIPT_FILENAME']), "/"));
        $http_path = "http://" . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strlen($_SERVER['PHP_SELF']) - strpos(strrev($_SERVER['PHP_SELF']), "/"));
        define('APPLICATION_PATH', $host_path);
        define('APPLICATION_URL', $http_path);
    }

    public static function setIncludes() {
        require_once('libs/fdebug.php');
        require_once('libs/frequest.php');
        require_once('libs/fstring.php');
        require_once('libs/fxml.php');
        require_once('libs/fsession.php');
        require_once('libs/smarty/Smarty.class.php');
        require_once('libs/controller.php');
        require_once('libs/view.php');
    }

    public static function setGettext() {
        $session = new FSession();
        $locale = $session->getAttribute('php2poLang') ? $session->getAttribute('php2poLang') : 'pt_BR';
        $textDomain = 'messages';

        putenv('LANGUAGE=' . $locale);
        putenv('LANG=' . $locale);
        putenv('LC_ALL=' . $locale);
        putenv('LC_MESSAGES=' . $locale);

        setlocale(LC_ALL, $locale);

        bindtextdomain($textDomain, './locales/');
        bind_textdomain_codeset($textDomain, 'UTF-8');
        textdomain($textDomain);
    }

    public static function init() {
        $request = new FRequest($_SERVER['REQUEST_URI']);
        $session = new FSession();

        $controller = $request->controller;
        $action = $request->action;

        $controller = new $controller($request, $session);
        $controller->$action();
    }

}

Index::configure();
Index::init();
