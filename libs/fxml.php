<?php

class FXml {

    public function createProject($name, $description, $path, $output, $lang) {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<php2po>
    <project>
        <name>{$name}</name>
        <description>{$description}</description>
        <path>{$path}</path>
        <output>{$output}</output>
        <lang>{$lang}</lang>
    </project>
    <langs></langs>
    <terms></terms>
</php2po>
XML;

        $name = FString::removeSpecialChars($name);

        if(!is_dir(APPLICATION_PATH . "projects/{$name}")) {
            mkdir(APPLICATION_PATH . "projects/{$name}", 0777, true);
        }

        $filename = APPLICATION_PATH . "projects/{$name}/project.xml";

        if(file_put_contents($filename, $xml)) {
            return $filename;
        }else{
            return false;
        }
    }

    public function createTerms($filename, $terms, $translations = array()) {
        $xml = simplexml_load_file($filename);

        $xml->terms = new SimpleXMLElement('<terms />');
        $xmlTerms = $xml->terms;
        $langs = array_keys($translations);

        foreach($terms as $index => $term) {
            $xmlTerm = $xmlTerms->addChild('term');
            $xmlTerm->addChild('desc', $term['name']);

            $xmlFiles = $xmlTerm->addChild("files");
            foreach($term['files'] as $file => $lines) {
                foreach($lines as $line) {
                    $xmlFile = $xmlFiles->addChild('file');
                    $xmlFile->addAttribute('name', $file);
                    $xmlFile->addAttribute('line', $line);
                }
            }

            if($translations) {
                $xmlTranslations = $xmlTerm->addChild("translations");

                $_index = md5($term['name']);
                foreach($langs as $lang) {
                    if(isset($translations[$lang][$index])) {
                        $xmlLangItem = $xmlTranslations->addChild('item', $translations[$lang][$index]);
                        $xmlLangItem->addAttribute('lang', $lang);
                    }
                }
            }
        }


        return $xml->asXML($filename);
    }

    public function setLangs($filename, $langs = array()) {
        $xml = simplexml_load_file($filename);

        $xml->langs = new SimpleXMLElement('<langs />');
        $xmlLangs = $xml->langs;
        foreach($langs as $lang) {
            $xmlLangItem = $xmlLangs->addChild('lang', $lang);
        }

        return $xml->asXML($filename);
    }

}
