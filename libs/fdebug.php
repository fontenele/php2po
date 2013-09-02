<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

/**
 * Prints debug params
 * @param id $args Any arguments
 */
function x() {
    $args = func_get_args();
    $dbt = debug_backtrace();

    $line = $dbt[0]['line'];
    $file = $dbt[0]['file'];
    $funct = $dbt[0]['function'];

    $string = "<div style='display : table; width : 100%; margin: auto; background : khaki;' align='left' style='text-align: left;'><span style='display : table; padding : 3px; width : 100%; background : gold; color : black;' align='center'><b>File:</b> {$file}<br><b>Function:</b> {$funct}( )<br><b>Line:</b> {$line}</span>";
    $string.= "<pre style='padding-left: 2px;'>";

    foreach ($args as $idx => $arg) {
        $string.= "<span style='font-size : 10pt; font-style : italic; display : table-cell; border : 1px solid steelblue;'>&nbsp;#<b>{$idx}</b>&nbsp; " . gettype($arg) . " </span><br>";
        $string.= print_r($arg, true);
        $string.= "<br><br>";
    }

    $string.= "</pre></div>";

    echo $string;
}

/**
 * Prints debug params and die
 * @param id $args Any arguments
 */
function xd() {
    $args = func_get_args();
    $dbt = debug_backtrace();

    $line = $dbt[0]['line'];
    $file = $dbt[0]['file'];
    $funct = $dbt[0]['function'];

    $string = "<div style='display : table; width : 100%; margin: auto; background : firebrick; color: white;' align='left' style='text-align: left;'><span style='display : table; padding : 3px; width : 100%; background : darkred; color : white;' align='center'><b>File:</b> {$file}<br><b>Function:</b> {$funct}( )<br><b>Line:</b> {$line}</span>";
    $string.= "<pre style='padding-left: 2px;'>";

    foreach ($args as $idx => $arg) {
        $string.= "<span style='font-size : 10pt; font-style : italic; display : table-cell; border : 1px solid yellow; color: yellow;'>&nbsp;#<b>{$idx}</b>&nbsp; " . gettype($arg) . " </span><br>";
        $string.= print_r($arg, true);
        $string.= "<br><br>";
    }

    $string.= "</pre></div>";

    echo $string;
    die;
}