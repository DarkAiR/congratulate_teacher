<?php

define ('READFILE', true);

$DR = $_SERVER['DOCUMENT_ROOT'];
require_once $DR.'/inc/template.php';
require_once $DR.'/inc/request.php';
require_once $DR.'/inc/image.php';

$formReady = Request::getPost('form', 0);

$errors = array(
    'common' => '',
    'photoTeacher' => '',
    'background' => ''
);

if ($formReady) {
    //echo '<pre>';
    //var_dump($_POST);
    //var_Dump($_FILES);
    //exit;
    do {
        $name           = Request::getPost('name', '');
        $nameTeacher    = Request::getPost('nameTeacher', '');
        $school         = Request::getPost('school', '');
        $year           = Request::getPost('year', 0);
        $background     = Request::getPost('background', 0);

        $photoTeacherInfo = Image::loadImageFromForm('photoTeacher');
        if (isset($photoTeacherInfo['error'])) {
            $errors['photoTeacher'] = $photoTeacherInfo['error'];
            break;
        }

        if ($background == 0) {
            $errors['background'] = 'Необходимо выбрать фоновое изображение';
            break;
        }

        $bgImage = Image::getBackgroundImage($background);
        if (!$bgImage) {
            $errors['background'] = 'Некорректное фоновое изображение';
            break;
        }

        try {
            $resImage = Image::createImage($bgImage, $photoTeacherInfo['image'], 300, 50, 200, 200);
            $resImage = Image::appendText($resImage, $name,         400, 50);
            $resImage = Image::appendText($resImage, $nameTeacher,  400, 100);
            $resImage = Image::appendText($resImage, $school,       400, 150);
            $resImage = Image::appendText($resImage, $year,         400, 200);

            if (!Image::saveResultImage('photoTeacher', $resImage)) {
                $errors['common'] = 'Ошибка при создании изображения';
                break;
            }
        } catch (Exception $ex) {
            $errors['common'] = $ex->getMessage();
        }

        $finalTemplate = Template::load('template/result.html');
        $finalTemplate = Template::render($finalTemplate, array(
            'image' => $resImage
        ));

        exit(0);
    } while (0);
}

// Рендерим форму
$formTemplate = Template::load("template/form.html");
$formTemplate = Template::render($formTemplate, array(
    'formReady' => 1,
    'errorCommon' => $errors['common'],
    'errorPhotoTeacher' => $errors['photoTeacher'],
    'errorBackground' => $errors['background']
));

$baseTemplate = Template::load('template/base.html');
$out = Template::render($baseTemplate, array(
    'content' => $formTemplate
));
echo $out;
