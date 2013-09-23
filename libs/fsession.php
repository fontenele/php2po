<?php

class FSession {

    const NAME = 'php2po';

    /**
     * Session Constructor
     */
    public function __construct() {
        if(!isset($_SESSION[self::NAME])) {
            $_SESSION[self::NAME] = array();
        }
    }

    /**
     * Set a Session attribute
     * @param string $attr
     * @param string $val
     */
    public function setAttribute($attr, $val) {
        $_SESSION[self::NAME][$attr] = $val;
    }

    /**
     * Return Session attribute
     * @param string $attr
     * @return string
     */
    public function getAttribute($attr) {
        return isset($_SESSION[self::NAME][$attr]) ? $_SESSION[self::NAME][$attr] : null;
    }

    /**
     * Return all Session attributes
     * @return array
     */
    public function getAllAttributes() {
        return $_SESSION[self::NAME];
    }

    /**
     * Clear all Session attributes
     */
    public function clear($clearLang = true) {
        if(!$clearLang) { $lang = $this->getAttribute('php2poLang'); }
        $_SESSION[self::NAME] = array();
        if(!$clearLang) { $this->setAttribute('php2poLang', $lang); }
    }
}