<?php

class FXml {

    public function create($name, $description, $path, $lang) {
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

        $filename = APPLICATION_PATH . "projects/{$this->removeSpecialChars($name)}.xml";

        if(file_put_contents($filename, $xml)) {
            return $filename;
        }else{
            return false;
        }
    }

    private function removeSpecialChars($string) {
        $string = strtolower($string);
        $string = preg_replace('/[^a-z0-9 -]+/', '', $string);
        $string = str_replace(' ', '-', $string);
        return trim($string, '-');
    }

}
