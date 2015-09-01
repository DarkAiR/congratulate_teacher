<?php
if (!defined('READFILE'))
    exit ( "Error, wrong way to file. <a href=\"/\">Go to main</a>." );

class Request
{
    public static function getPost($name, $def=null)
    {
        $v = isset($_POST[$name])
            ? $_POST[$name]
            : $def;

        $v = strip_tags($v);
        $v = htmlspecialchars($v);
        return $v;
    }
}