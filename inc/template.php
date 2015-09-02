<?php
if (!defined('READFILE'))
    exit ( "Error, wrong way to file. <a href=\"/\">Go to main</a>." );

class Template
{
    public static function load($path)
    {
        ob_start();
        include $path;
        $template = ob_get_contents();
        ob_end_clean();
        return $template;
    }

    public static function loadScript($path)
    {
        $str = @file_get_contents($path);
        if (!$str)
            return '';
        $str = '<script type="text/javascript">'.$str.'</script>';
        return $str;
    }

    public function loadCss($path)
    {
        $str = @file_get_contents($path);
        if (!$str)
            return '';
        $str = '<style type="text/css">'.$str.'</style>';
        return $str;
    }

    public static function loadImage($path)
    {
        $str = @file_get_contents($path);
        if (!$str)
            return '';
        $str = 'data:image/png;base64,'.base64_encode($str);
        return $str;
    }

    public static function render($template, $params=array())
    {
        foreach ($params as $k=>$v) {
            $template = preg_replace('/<%\s*?'.$k.'\s*?%>/', $v, $template);
        }
        return $template;
    }
}