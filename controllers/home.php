<?php

class Home extends Controller {

    public function init() {
        $this->breadcrumbs[] = array($this->translate('Principal'), 'home');
    }

    public function index() {
        if($this->request->get->offsetExists('lang')) {
            $this->session->setAttribute('php2poLang', $this->request->get->offsetGet('lang'));
            header('location: ' . APPLICATION_URL);
            exit;
        }

        $this->session->clear();
        $this->displayTemplate('home.phtml');
    }
}
