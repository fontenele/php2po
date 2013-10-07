<?php

class Project extends Controller {

    public function init() {
        $this->breadcrumbs[] = array($this->translate('Principal'), 'home');
    }

    public function start() {
        $this->breadcrumbs[] = array($this->translate('Novo Projeto'), 'project/start');

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
        $this->session->setAttribute('des-caminho-output', $this->request->post->offsetGet('des-caminho-output'));
        $this->session->setAttribute('frk-idioma-default', $this->request->post->offsetGet('frk-idioma-default'));

        $xml = new FXml();
        $filename = $xml->createProject(
                $this->request->post->offsetGet('nom-projeto'),
                $this->request->post->offsetGet('des-projeto'),
                $this->request->post->offsetGet('des-caminho'),
                $this->request->post->offsetGet('des-caminho-output'),
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
        $this->breadcrumbs[] = array($this->translate('Importar Projeto'), 'project/import');

        $this->view->assign('projects', $this->getProjects());
        $this->displayTemplate('import.phtml');
    }

    public function importSave() {
        $xml = $project = null;

        if($this->request->offsetGet('frk-project')) {
            $xml = simplexml_load_file(APPLICATION_PATH . "projects/{$this->request->offsetGet('frk-project')}/project.xml");
            $project = $this->request->offsetGet('frk-project');
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
                $this->session->setAttribute('des-caminho-output', (string)$_project->output);
                $this->session->setAttribute('frk-idioma-default', (string)$_project->lang);

            }
            $langs = array();
            foreach($xml->xpath('//langs/lang') as $_lang) {
                $langs[(string)$_lang] = (string)$_lang;
            }

            $this->session->setAttribute('arr-langs', $langs);
            $this->session->setAttribute('des-caminho-xml', APPLICATION_PATH . "projects/{$project}/project.xml");

            $arrTerms = $arrTranslateds = array();

            if($xml->terms && count($xml->terms->term)) {
                foreach($xml->terms->term as $xmlTerm) {
                    foreach($xmlTerm->files->file as $xmlFile) {
                        $name = (string)$xmlTerm->desc;
                        $index = md5($name);

                        $arrTerms[$index]['name'] = $name;
                        $arrTerms[$index]['files'][(string)$xmlFile->attributes()->name][(string)$xmlFile->attributes()->line] = (string)$xmlFile->attributes()->line;
                    }
                    foreach($xmlTerm->translations->item as $xmlTranslated) {
                        $name = (string)$xmlTerm->desc;
                        $index = md5($name);

                        $lang = (string)$xmlTranslated->attributes()->lang;
                        $translated = (string)$xmlTranslated;
                        $arrTranslateds[$lang][$index] = $translated;
                    }
                }
            }

            $this->session->setAttribute('arr-terms', $arrTerms);
            $this->session->setAttribute('arr-translateds', $arrTranslateds);
            header('location: ' . APPLICATION_URL . 'project/view');
        }
    }

    public function view() {
        $this->breadcrumbs[] = array($this->session->getAttribute('nom-projeto'), 'project/view');

        $this->view->assign('nomProjeto', $this->session->getAttribute('nom-projeto'));
        $this->view->assign('projectDir', $this->session->getAttribute('des-caminho'));
        $this->view->assign('patterns', array('$this->translate()'));
        $this->view->assign('ignoreDirs', array(".", '..', '.svn'));
        $this->view->assign('langDefault', $this->session->getAttribute('frk-idioma-default'));

        $arrIdiomas = $this->session->getAttribute('arr-langs') && is_array($this->session->getAttribute('arr-langs')) ? $this->session->getAttribute('arr-langs') : array();
        $arrTerms = $this->session->getAttribute('arr-terms') && is_array($this->session->getAttribute('arr-terms')) ? $this->session->getAttribute('arr-terms') : array();
        $this->view->assign('arrIdiomas', $arrIdiomas);
        $this->view->assign('arrTerms', $arrTerms);

        $arrTranslateds = $this->session->getAttribute('arr-translateds');

        foreach($arrIdiomas as $idioma) {
            if(!isset($arrTranslateds[$idioma])) { $arrTranslateds[$idioma] = array(); }
        }

        // clean if empty translation
        foreach($arrTranslateds as $idioma => $traducoes) {
            foreach($traducoes as $id => $value) {
                if(!trim($value)) {
                    unset($arrTranslateds[$idioma][$id]);
                }
            }
        }

        $this->view->assign('arrTranslateds', $arrTranslateds);

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
            $pathXml = $this->session->getAttribute('des-caminho-xml');
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
                $xml = new FXml();
                $xml->setLangs($pathXml, $langs);

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
            $this->session->setAttribute('arr-terms', $terms);
            echo json_encode(array('result' => '1', 'total' => count($terms)));
        }else{
            echo json_encode(array('result' => '2'));
        }
    }

    public function viewLang() {
        $this->breadcrumbs[] = array($this->session->getAttribute('nom-projeto'), 'project/view');
        $this->breadcrumbs[] = array($this->translate('Tradução - ') . ' ' .$this->request->get->offsetGet('lang'), 'project/viewLang');

        $lang = $this->request->get->offsetGet('lang');

        $this->view->assign('langDefault', $this->session->getAttribute('frk-idioma-default'));
        $this->view->assign('lang', $lang);
        $this->view->assign('terms', $this->session->getAttribute('arr-terms'));
        $arrTranslateds = $this->session->getAttribute('arr-translateds');
        $this->view->assign('arrTranslateds', isset($arrTranslateds[$lang]) ? $arrTranslateds[$lang] : array());

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
        $terms = $translateds = array();
        parse_str($this->request->post->offsetGet('terms'), $terms);

        $origem = $this->request->post->offsetGet('origem');
        $destino = $this->request->post->offsetGet('destino');

        foreach($terms as $_term => $_val) {
            if(substr($_term, 0, 2) == 't_') {
                $id = substr($_term, 2);

                $_origem = $terms[$id];
                $_destino = $_val;

                if(!$_destino) {
                    $html = $this->searchGoogle($origem, $destino, $_origem);

                    $translate = explode('"',substr($html, 4));
                    $_destino = array_shift($translate);

                    $translateds[$id] = $_destino;
                    $terms['t_' . $id] = $_destino;
                }
            }
        }

        $arrTranslateds = $this->session->getAttribute('arr-translateds');
        $arrTranslateds[$destino] = $translateds;
        $this->session->setAttribute('arr-translateds', $arrTranslateds);

        echo json_encode(array('terms' => $terms));
    }

    public function googleOne() {
        $origem = $this->request->post->offsetGet('origem');
        $destino = $this->request->post->offsetGet('destino');
        $term = $this->request->post->offsetGet('term');

        $translateds = array();

        $html = $this->searchGoogle($origem, $destino, $term);

        $translate = explode('"',substr($html, 4));
        $_destino = array_shift($translate);

        $arrTranslateds = $this->session->getAttribute('arr-translateds');

        if(!isset($arrTranslateds[$destino])) {
            $arrTranslateds[$destino] = array();
        }

        $arrTranslateds[$destino][md5($term)] = $_destino;
        $this->session->setAttribute('arr-translateds', $arrTranslateds);

        echo json_encode(array('translated' => $_destino, 'id' => md5($term)));
    }

    protected function searchGoogle($langOri, $langDest, $term) {
        $strUrlParams = rawurlencode($term);
        $url = "https://translate.google.com/translate_a/t?client=t&sl={$langOri}&tl={$langDest}&hl=pt-BR&ie=UTF-8&oe=UTF-8&prev=btn&ssel=4&tsel=4&q={$strUrlParams}";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $html = curl_exec($curl);
        curl_close($curl);

        return $html;
    }

    public function exportPo() {
        $project = $this->request->post->offsetGet('project');
        $lang = $this->request->post->offsetGet('lang');

        $input = APPLICATION_PATH . "projects/{$project}/{$lang}.po";
        $output = $this->session->getAttribute('des-caminho-output') . "/{$lang}.po";

        if (file_exists($input)) {
            if(copy($input, $output)) {
                echo json_encode(array('status' => 'ok'));
            }

            /*header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;*/
        }
    }

    public function exportMo() {
        $project = $this->request->post->offsetGet('project');
        $lang = $this->request->post->offsetGet('lang');

        require_once(APPLICATION_PATH . 'libs/poutils.php');

        $input = POutils::createMoFile($project, $lang);
        $output = $this->session->getAttribute('des-caminho-output') . "/{$lang}.mo";

        if (file_exists($input)) {
            if(copy($input, $output)) {
                echo json_encode(array('status' => 'ok'));
            }

            /*header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;*/
        }
    }

    public function saveTermsLang() {
        $dir = $this->session->getAttribute('des-caminho');
        $pathXml = $this->session->getAttribute('des-caminho-xml');
        $nomProjeto = $this->session->getAttribute('nom-projeto');
        $basePath = $this->session->getAttribute('des-caminho');
        $terms = $this->session->getAttribute('arr-terms');

        $lang = $this->request->post->offsetGet('lang');
        $this->request->post->offsetUnset('lang');

        require_once(APPLICATION_PATH . 'libs/poutils.php');
        $poUtils = new POutils($dir, null, null, $terms);
        $poUtils->createPoFile($nomProjeto, $basePath, $lang, (array)$this->request->post);

        $xml = new FXml();
        $translateds = array();

        foreach((array)$this->request->post as $_term => $_val) {
            if(substr($_term, 0, 2) == 't_') {
                $index = substr($_term, 2);
                $translateds[$index] = $_val;
            }
        }

        $arrTranslateds = $this->session->getAttribute('arr-translateds');
        if(!is_array($arrTranslateds)) { $arrTranslateds = array(); }
        $arrTranslateds[$lang] = $translateds;
        $this->session->setAttribute('arr-translateds', $arrTranslateds);

        $xml->createTerms($pathXml, $terms, $arrTranslateds);
        header('location: ' . APPLICATION_URL . 'project/view');
    }
}
