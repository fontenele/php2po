<?php

class POutils {

	private $dir;
	private $terms = array();
    private $patterns = array();
    private $ignoreDirs = array();

	public function __construct($directory, $patterns = null, $ignoreDirs = null) {
		if(is_dir($directory)) {
			$this->dir = dir(substr($directory, 0, strlen($directory) - 1));
		}
        if($patterns) {
            $this->setPatterns($patterns);
        }
        if($ignoreDirs) {
            $this->setIgnoreDirs($ignoreDirs);
        }
	}
    
    public function setPatterns($patterns) {
        $this->patterns = is_array($patterns) ? $patterns : array_filter(explode("\n",$patterns), 'strlen');
    }
    
    public function setIgnoreDirs($ignoreDirs) {
        $this->ignoreDirs = is_array($ignoreDirs) ? $ignoreDirs : array_filter(explode("\n",$ignoreDirs), 'strlen');
    }

	public function getTerms() { return $this->terms; }

	public function startSearch($dir = null) {
		if(!$dir) { $dir = $this->dir; }
		else{ $dir = dir($dir); }

		while(($_dir = $dir->read()) != false) {
			if (!in_array($_dir, $this->ignoreDirs)) {
				if(is_dir($dir->path . '/' . $_dir)) {
					$this->findInDir($dir->path . '/' . $_dir);
				}else{
					$this->searchInFile($dir->path . '/' . $_dir);
				}
			}
		}
	}

	protected function findInDir($dir) {
		$dir = dir($dir);

		while (($_dir = $dir->read()) != false) {
			if (!in_array($_dir, $this->ignoreDirs)) {
				if(is_dir($dir->path . '/' . $_dir)) {
					$this->findInDir($dir->path . '/' . $_dir);
				}else{
					$this->searchInFile($dir->path . '/' . $_dir);
				}
			}
		}
	}

	protected function searchInFile($file) {
		$content = file_get_contents($file);

		preg_match_all('/(?P<tudo>\$this\s*->\s*translate\s*\(\s*("|\')(?P<palavra>.*?)("|\')\s*\))/sm', $content, $result);
		$arLinhas = preg_split('/(\r\n|\n|\r)/', $content);
		
		$terms = $result['palavra'];
		$toFindInFile = $result['tudo'];
        
		foreach($arLinhas as $i => $conteudoLinha) {
			$linha = $i + 1;
			foreach($toFindInFile as $j => $match) {
				if(strstr($conteudoLinha, $match)) {
                    $this->terms[$terms[$j]][$file][$linha] = $linha;
				}
			}
		}
	}

    public function createPoFile($project, $lang) {
        $tmpFile = md5(date('Y-m-d-h:i:s'));
        $output = '/tmp/output.po';
        $res = fopen($output, 'w');

        fwrite($res, "msgid \"\"\r");
        fwrite($res, "msgstr \"\"\r");

        fwrite($res, '"Project-Id-Version: FS 1.0\n"' . "\n"); // Nome projeto
        fwrite($res, '"POT-Creation-Date: 2013-03-19 22:45-0300\n"' . "\n"); // Data criaçao
        fwrite($res, '"PO-Revision-Date: '. date('Y-m-d h:i:s') . '\n"' . "\n"); // Data revisao
        fwrite($res, '"MIME-Version: 1.0\n"' . "\n");
        fwrite($res, '"Content-Type: text/plain; charset=UTF-8\n"' . "\n");
        fwrite($res, '"Content-Transfer-Encoding: 8bit\n"' . "\n");
        fwrite($res, '"X-Generator: php2po\n"' . "\n");
        fwrite($res, '"X-Poedit-KeywordsList: translate\n"' . "\n");
        fwrite($res, '"X-Poedit-Basepath: /Applications/XAMPP/htdocs/fs/app/module/Album\n"' . "\n"); // BaseDir
        fwrite($res, '"X-Poedit-SourceCharset: UTF-8\n"' . "\n\n");

        foreach($this->getTerms() as $_term => $files) {
            foreach($files as $_file => $lines) {
                foreach($lines as $_line) {
                    fwrite($res, "#: {$_file}:{$_line}" . "\n");
                }
            }

            fwrite($res, "msgid \"{$_term}\"\n");
            fwrite($res, "msgstr \"\"\n\n");
        }

        fclose($res);
        return $output;
    }

    public function createMoFile($poFile) {
        $output = '/tmp/output.mo';

        require("msgfmt-functions.php");

        if(file_exists($poFile)) {

            $hash = parse_po_file($poFile);

            if ($hash === FALSE) {
                print(nl2br("Error reading '{$poFile}', aborted.\n"));
            }else {
                write_mo_file($hash, $output);
                return $output;
            }
        }
    }
}