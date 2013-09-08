<?php

class Project extends Controller {

    public function init() {
        $this->breadcrumbs[] = array('Principal', 'home');
    }

    public function start() {
        $this->breadcrumbs[] = array('Novo Projeto', 'project/start');

        $arrAllLangs = array();
        $xml = simplexml_load_file(APPLICATION_PATH . 'locales.xml');
        foreach($xml->locale as $locale) {
            $arrAllLangs[(string)$locale->codes->code->standard->representation] = (string)$locale->englishName;
        }
        $this->view->assign('arrAllLangs', $arrAllLangs);

        $this->displayTemplate('start.phtml');
    }

    public function startSave() {
        $this->session->setAttribute('nom-projeto', $this->request->post->offsetGet('nom-projeto'));
        $this->session->setAttribute('des-projeto', $this->request->post->offsetGet('des-projeto'));
        $this->session->setAttribute('des-caminho', $this->request->post->offsetGet('des-caminho'));
        $this->session->setAttribute('frk-idioma-default', $this->request->post->offsetGet('frk-idioma-default'));

        $xml = new FXml();
        $filename = $xml->createProject(
                $this->request->post->offsetGet('nom-projeto'),
                $this->request->post->offsetGet('des-projeto'),
                $this->request->post->offsetGet('des-caminho'),
                $this->request->post->offsetGet('frk-idioma-default')
            );

        if($filename) {
            $this->session->setAttribute('arr-langs', array($this->request->post->offsetGet('frk-idioma-default') => $this->request->post->offsetGet('frk-idioma-default')));
            $this->session->setAttribute('des-caminho-xml', $filename);
            header('location: ' . APPLICATION_URL . 'project/view');
        }else{
            // erros e reload
        }
    }

    public function import() {
        $this->breadcrumbs[] = array('Importar Projeto', 'project/import');

        $dir = dir(APPLICATION_PATH . 'projects');
        $projects = array();

        while($project = $dir->read()) {
            if(!in_array($project, array('.svn', '..', '.', 'index.php'))) {
                $projects[] = $project;
            }
        }

        $this->view->assign('projects', $projects);
        $this->displayTemplate('import.phtml');
    }

    public function importSave() {
        $xml = $project = null;

        if($this->request->post->offsetGet('frk-project')) {
            $xml = simplexml_load_file(APPLICATION_PATH . "projects/{$this->request->post->offsetGet('frk-project')}/project.xml");
            $project = $this->request->post->offsetGet('frk-project');
        }elseif($this->request->files->offsetGet('des-caminho-xml')) {
            /* @todo desabilitado pela mudança no diretorio project - remover comentário */
            /*$file = $this->request->files->offsetGet('des-caminho-xml');

            if(move_uploaded_file($file['tmp_name'], APPLICATION_PATH . "projects/{$file['name']}")) {
                $xml = simplexml_load_file(APPLICATION_PATH . "projects/{$file['name']}");
                $filename = $file['name'];
            }*/
        }

        if($xml) {
            foreach($xml->xpath('//project') as $_project) {
                $this->session->setAttribute('nom-projeto', (string)$_project->name);
                $this->session->setAttribute('des-projeto', (string)$_project->description);
                $this->session->setAttribute('des-caminho', (string)$_project->path);
                $this->session->setAttribute('frk-idioma-default', (string)$_project->lang);
                $langs = array((string)$_project->lang => (string)$_project->lang);
                $this->session->setAttribute('arr-langs', $langs);
            }

            $this->session->setAttribute('des-caminho-xml', APPLICATION_PATH . "projects/{$project}/project.xml");

            $arrTerms = array();
            if($xml->terms && count($xml->terms->term)) {
                foreach($xml->terms->term as $xmlTerm) {
                    $arrTerms[(string)$xmlTerm->desc] = array();
                    foreach($xmlTerm->files->file as $xmlFile) {
                        $arrTerms[(string)$xmlTerm->desc][(string)$xmlFile->attributes()->name][(string)$xmlFile->attributes()->line] = (string)$xmlFile->attributes()->line;
                    }
                }
            }

            $this->session->setAttribute('arr-terms', $arrTerms);
            header('location: ' . APPLICATION_URL . 'project/view');
        }
    }

    public function view() {
        $this->breadcrumbs[] = array($this->session->getAttribute('nom-projeto'), 'project/view');

        $this->view->assign('nomProjeto', $this->session->getAttribute('nom-projeto'));
        $this->view->assign('projectDir', $this->session->getAttribute('des-caminho'));
        $this->view->assign('patterns', array('$this->translate()'));
        $this->view->assign('ignoreDirs', array(".", '..', '.svn'));
        $this->view->assign('arrIdiomas',
                $this->session->getAttribute('arr-langs') && is_array($this->session->getAttribute('arr-langs')) ?
                    $this->session->getAttribute('arr-langs') : array()
            );
        $this->view->assign('arrTerms',
                $this->session->getAttribute('arr-terms') && is_array($this->session->getAttribute('arr-terms')) ?
                    $this->session->getAttribute('arr-terms') : array()
            );

        $arrAllLangs = array();
        $xml = simplexml_load_file(APPLICATION_PATH . 'locales.xml');
        foreach($xml->locale as $locale) {
            $arrAllLangs[(string)$locale->codes->code->standard->representation] = (string)$locale->englishName;
        }
        $this->view->assign('arrAllLangs', $arrAllLangs);

        $this->displayTemplate('view-project.phtml');
    }

    public function addNewLang() {
        if($this->request->get->offsetExists('lang')) {
            $langs = $this->session->getAttribute('arr-langs');
            $newLang = trim($this->request->get->offsetGet('lang'));
            $status = false;

            switch(true) {
                case !$langs || !is_array($langs):
                    $langs = array($newLang => $newLang);
                    $status = true;
                break;
                case $langs && is_array($langs) && !isset($langs[$newLang]):
                    $langs[$newLang] = $newLang;
                    $status = true;
                break;
            }

            if($status) {
                $this->session->setAttribute('arr-langs', $langs);
                echo json_encode(array('result' => '1'));
            }else{
                echo json_encode(array('result' => '2'));
            }
        }
    }

    public function searchTerms() {
        $patterns = htmlspecialchars(urldecode(stripcslashes($this->request->post->offsetGet('patterns'))));
        $ignore = htmlspecialchars(urldecode(stripcslashes($this->request->post->offsetGet('ignore'))));

        require_once(APPLICATION_PATH . 'libs/poutils.php');

        $dir = $this->session->getAttribute('des-caminho');
        $langs = $this->session->getAttribute('arr-langs');
        $pathXml = $this->session->getAttribute('des-caminho-xml');
        $nomProjeto = $this->session->getAttribute('nom-projeto');
        $basePath = $this->session->getAttribute('des-caminho');

        $poUtils = new POutils($dir, $patterns, $ignore);

        $poUtils->startSearch();
        $terms = $poUtils->getTerms();

        foreach($langs as $lang) {
            $poUtils->createPoFile($nomProjeto, $basePath, $lang);
        }

        $xml = new FXml();
        if($xml->createTerms($pathXml, $terms)) {
            $this->session->setAttribute('arr-terms', $poUtils->getTerms());
            echo json_encode(array('result' => '1', 'total' => count($poUtils->getTerms())));
        }else{
            echo json_encode(array('result' => '2'));
        }
    }

    public function viewLang() {
        $this->breadcrumbs[] = array($this->session->getAttribute('nom-projeto'), 'project/view');
        $this->breadcrumbs[] = array('Tradução - ' . $this->request->get->offsetGet('lang'), 'project/viewLang');

        $this->view->assign('lang', $this->request->get->offsetGet('lang'));
        $this->view->assign('terms', $this->session->getAttribute('arr-terms'));

        $arrAllLangs = array();
        $xml = simplexml_load_file(APPLICATION_PATH . 'locales.xml');
        foreach($xml->locale as $locale) {
            $arrAllLangs[(string)$locale->codes->code->standard->representation] = (string)$locale->englishName;
        }

        $this->view->assign('arrAllLangs', $arrAllLangs);
        $this->view->assign('nomProjeto', FString::removeSpecialChars($this->session->getAttribute('nom-projeto')));

        $this->displayTemplate('view-lang.phtml');
    }

    public function googleAll() {
        $terms = array();
        parse_str($this->request->post->offsetGet('terms'), $terms);

        $origem = $this->request->post->offsetGet('origem');
        $destino = $this->request->post->offsetGet('destino');

        $termsOrigem = '';
        $j = 0;

        foreach($terms as $_term => $_val) {
            if(strstr($_term, 'term_origin_')) {
                $id = (int)str_replace('term_origin_', '', $_term);

                if($j%2 == 0) {
                    $_origem = $_val;
                    $_destino = $terms['term_translate_' . $id];

                    if(!$_destino) {
                        $termsOrigem .= "{$_origem}\n";

                        $strUrlParams = rawurlencode($_origem);
                        $url = "https://translate.google.com/translate_a/t?client=t&sl={$origem}&tl={$destino}&hl=pt-BR&ie=UTF-8&oe=UTF-8&prev=btn&ssel=4&tsel=4&q={$strUrlParams}";

                        $curl = curl_init($url);
                        curl_setopt($curl, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
                        $html = curl_exec($curl);
                        curl_close($curl);

                        error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
                        ini_set('display_errors', false);

                        $translate = explode('"',substr($html, 4));
                        $_destino = array_shift($translate);

                        $terms['term_translate_' . $id] = $_destino;
                    }
                }
            }

            $j++;
        }

        echo json_encode(array('terms' => $terms));
    }

    public function exportPo() {
        $project = $this->request->get->offsetGet('project');
        $lang = $this->request->get->offsetGet('lang');

        $file = APPLICATION_PATH . "projects/{$project}/{$lang}.po";

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }

    public function exportMo() {
        $project = $this->request->get->offsetGet('project');
        $lang = $this->request->get->offsetGet('lang');

        require_once(APPLICATION_PATH . 'libs/poutils.php');
        $file = POutils::createMoFile($project, $lang);

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }
}
