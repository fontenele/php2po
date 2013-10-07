<?php

abstract class Controller {
    /**
     * HTTP Request
     * @var FRequest
     */
    public $request;

    /**
     * Session
     * @var FSession
     */
    public $session;
    /**
     * View
     * @var Smarty
     */
    public $view;
    /**
     * JS Files for View
     * @var array
     */
    public $jsFiles;
    /**
     * CSS Files for View
     * @var array
     */
    public $cssFiles;

    public $breadcrumbs = array();

    public function __construct(FRequest $request, FSession $session) {
        $this->request = $request;
        $this->session = $session;

        $this->view = new Smarty();
        $this->view->setCacheDir(APPLICATION_PATH . 'cache');
        $this->view->setCompileDir(APPLICATION_PATH . 'tmp');
        $this->view->setTemplateDir(APPLICATION_PATH . 'views');

        $this->registerDefaultViewVars();

        $this->init();
    }

    public function init() { }

    protected function registerDefaultViewVars() {
        $this->view->assign('fullPath', APPLICATION_PATH);
        $this->view->assign('hostPath', APPLICATION_URL);

        $view = new View();
        $this->view->assign('this', $view);

        if(file_exists(APPLICATION_PATH . "js/{$this->request->controller}/{$this->request->action}.js")) {
            $this->jsFiles[] = "{$this->request->controller}/{$this->request->action}.js";
        }

        if(file_exists(APPLICATION_PATH . "css/{$this->request->controller}/{$this->request->action}.css")) {
            $this->cssFiles[] = "{$this->request->controller}/{$this->request->action}.css";
        }

        $this->view->assign('php2poProjects', $this->getProjects());

    }

    public function getProjects() {
        $projects = array();

        $baseDir = APPLICATION_PATH . 'projects/';
        $dir = dir($baseDir);
        while($project = $dir->read()) {
            if(is_dir($baseDir . $project) && !in_array($project, array('.svn','.', '..'))) {
                $projects[$project] = $project;
            }
        }

        return $projects;
    }

    public function displayTemplate($template) {
        if($this->view->templateExists($template)) {
            $content = $this->view->fetch($template);

            $this->view->assign('php2poBreadCrumbs', $this->breadcrumbs);
            $this->view->assign('php2poContent', $content);
            $this->view->assign('php2poJsFiles', $this->jsFiles);
            $this->view->assign('php2poCssFiles', $this->cssFiles);

            $this->view->display('template.phtml');
        }
    }

    public function translate($string) {
        return _($string) ? _($string) : $string;
    }
}
