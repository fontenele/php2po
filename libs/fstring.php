<?php

class FString {
    public static function removeSpecialChars($string) {
        $string = strtolower($string);
        $string = preg_replace('/[^a-z0-9 -]+/', '', $string);
        $string = str_replace(' ', '-', $string);
        return trim($string, '-');
    }
}
