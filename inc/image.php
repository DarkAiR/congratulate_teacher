<?php
if (!defined('READFILE'))
    exit ( "Error, wrong way to file. <a href=\"/\">Go to main</a>." );

class Image
{
    public static $docRoot = '';

    public static function loadImageFromForm($name)
    {
        if ($_FILES[$name]['error'] != UPLOAD_ERR_OK)
            return array('error' => 'Ошибка загрузки файла');

        $fileName = $_FILES[$name]['tmp_name'];

        if (filesize($fileName) > 16 * 1024 * 1024)
            return array('error' => 'Файл слишком большой');

        $size = getimagesize($fileName);
        $mimeArr = array('image/jpeg');
        if (!in_array($size['mime'], $mimeArr))
            return array('error' => 'Неправильный тип файла');

        return array('image' => $fileName, 'w' => $size[0], 'h' => $size[1]);
    }

    public static function getBackgroundImage($idx)
    {
        switch ($idx) {
            case 1:
                return self::$docRoot.'/img/fon1.jpg';
            case 2:
                return self::$docRoot.'/img/fon2.jpg';
            default:
                return false;
        }
    }

    public static function createImage($baseImageName, $photoImageName, $x, $y, $w, $h)
    {
        $imageBg = @imagecreatefromjpeg($baseImageName);
        $imagePhoto = @imagecreatefromjpeg($photoImageName);
        if (!$imageBg || !$imagePhoto)
            throw new Exception('Ошибка создания изображения (1)');

        if (!@imagecopymerge($imageBg, $imagePhoto, $x, $y, 0, 0, $w, $h, 100))
            throw new Exception('Ошибка создания изображения (2)');

        return $imageBg;
    }

    public static function appendText($image, $text, $x, $y)
    {
        if (!@imagettftext($image, 15, 0, $x, $y, 0x00ff00, self::$docRoot.'/fonts/arial.ttf', "hello\nWORLD!!!"))
            throw new Exception('Ошибка создания изображения (3)');
        return $image;
    }

    public static function saveResultImage($name, $image)
    {
        $uploadDir = self::$docRoot.'/upload/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir);
        }

        $ext = pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION);
        $filename = md5($name.time());
        if (!imagejpeg($image, $uploadDir.$filename.'.'.$ext))
            throw new Exception('Ошибка создания изображения (4)');

        return $filename.'.'.$ext;
    }

    public static function getImageContent($image)
    {
        ob_start();
        imagejpeg($image);
        $str = ob_get_contents();
        ob_end_clean();

        $str = 'data:image/png;base64,'.base64_encode($str);
        return $str;
    }
}