<?php

//http://translate.google.com.br/translate_a/t?client=t&sl=pt&tl=en&hl=pt-BR&sc=2&ie=UTF-8&oe=UTF-8&swap=1&oc=2&prev=conf&psl=pt&ptl=en&otf=1&it=sel.3844&ssel=5&tsel=5&q=Trabalho

session_start();

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
ini_set('display_errors', false);

spl_autoload_register('autoload');

function autoload($class) {
    if(file_exists(APPLICATION_PATH . 'controllers/' . $class . '.php')) {
        require_once(APPLICATION_PATH . 'controllers/' . $class . '.php');
        return true;
    }else{
        //xd($class);
    }
}

// Libs
require_once('libs/fdebug.php');
require_once('libs/frequest.php');
require_once('libs/fsession.php');
require_once('libs/fxml.php');
require_once('libs/smarty/Smarty.class.php');
require_once('libs/controller.php');

$request = new FRequest($_SERVER['REQUEST_URI']);
$session = new FSession();

$controller = $request->controller;
$action = $request->action;

$controller = new $controller($request, $session);
$controller->$action();
