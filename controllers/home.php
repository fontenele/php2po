<?php

class Home extends Controller {

    public function init() {
        $this->breadcrumbs[] = array('Principal', 'home');
    }

    public function index() {
        session_destroy();
        $this->displayTemplate('home.phtml');
    }
}
