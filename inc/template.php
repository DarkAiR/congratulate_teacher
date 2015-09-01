<?php
if (!defined('READFILE'))
    exit ( "Error, wrong way to file. <a href=\"/\">Go to main</a>." );

class Template
{
    public static function load($path)
    {
        $DR = $_SERVER['DOCUMENT_ROOT'];

        if ($path[0] != '/')
            $path = '/'.$path;

        ob_start();
        include $DR.$path;
        $template = ob_get_contents();
        ob_end_clean();
        return $template;
    }

    public static function render($template, $params=array())
    {
        foreach ($params as $k=>$v) {
            $template = preg_replace('/<%\s*?'.$k.'\s*?%>/', $v, $template);
        }
        return $template;
    }
}