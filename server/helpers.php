<?php

include_once 'config.php';

function autolink($str, $attributes=array()) {
    $attrs = '';
    foreach ($attributes as $attribute => $value) {
        $attrs .= " {$attribute}=\"{$value}\"";
    }

    $str = ' ' . $str;
    $str = preg_replace(
        '`([^"=\'>])(((http|https|ftp)://|www.)[^\s<]+[^\s<\.)])`i',
        '$1<a href="$2"'.$attrs.'>$2</a>',
        $str
    );
    $str = substr($str, 1);
    $str = preg_replace('`href=\"www`','href="http://www',$str);
    return $str;
}

function getConfig($key) {
    global $conf;
    return $conf ? $conf[$key] : getenv($key);
}

?>