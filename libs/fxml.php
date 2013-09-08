<?php

class FXml {

    public function createProject($name, $description, $path, $lang) {
        $xml = <<<XML
<?xml version="1.0"?>
<php2po>
    <project>
        <name>{$name}</name>
        <description>{$description}</description>
        <path>{$path}</path>
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

    public function createTerms($filename, $terms) {
        $xml = simplexml_load_file($filename);

        $xmlTerms = $xml->terms;

        foreach($terms as $term => $files) {
            $xmlTerm = $xmlTerms->addChild('term');
            $xmlTerm->addChild('desc', $term);

            $xmlFiles = $xmlTerm->addChild("files");

            foreach($files as $file => $lines) {
                foreach($lines as $line) {
                    $xmlFile = $xmlFiles->addChild('file');
                    $xmlFile->addAttribute('name', $file);
                    $xmlFile->addAttribute('line', $line);
                }
            }
        }


        return $xml->asXML($filename);
    }

}
