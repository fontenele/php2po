<?php

class Home extends Controller {

    public function init() {
        $this->breadcrumbs[] = array($this->translate('Principal'), 'home');
    }

    public function index() {
        $this->session->clear(false);
        $this->displayTemplate('home.phtml');
    }

    public function changeLang() {
        if($this->request->get->offsetExists('lang')) {
            $this->session->clear();
            $this->session->setAttribute('php2poLang', $this->request->get->offsetGet('lang'));

            header('location: ' . APPLICATION_URL);
            exit;
        }

        $this->index();
    }
}
