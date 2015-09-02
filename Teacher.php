<?php

Yii::import('ext.teacher.inc.*');

/**
 * Учитель
 */
class Teacher
{
    private $docRoot;

    public function init($docRoot)
    {
        $this->docRoot = $docRoot;
        Image::$docRoot = $docRoot;
    }


    public function run()
    {
        $formReady = Yii::app()->request->getPost('form', 0);

        $errors = array(
            'common' => '',
            'photoTeacher' => '',
            'background' => ''
        );

        if ($formReady) {
            do {
                $name           = Yii::app()->request->getPost('name', '');
                $nameTeacher    = Yii::app()->request->getPost('nameTeacher', '');
                $school         = Yii::app()->request->getPost('school', '');
                $year           = Yii::app()->request->getPost('year', 0);
                $background     = Yii::app()->request->getPost('background', 0);

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

                    $imageName = Image::saveResultImage('photoTeacher', $resImage);
                    if (!$imageName) {
                        $errors['common'] = 'Ошибка при создании изображения';
                        break;
                    }
                } catch (Exception $ex) {
                    $errors['common'] = $ex->getMessage();
                }

                $finalTemplate = Template::load($this->docRoot.'/template/result.html');
                $finalTemplate = Template::render($finalTemplate, array(
                    'image' => Image::getImageContent($resImage),
                    'imageName' => $imageName
                ));

                $this->renderPage($finalTemplate);
                return;
            } while (0);
        }

        // Рендерим форму
        $img1       = Template::loadImage($this->getBackgroundImage(1));
        $img2       = Template::loadImage($this->getBackgroundImage(2));
        $imgInfo1   = $this->getBackgroundImageInfo(1);
        $imgInfo2   = $this->getBackgroundImageInfo(2);
        $formTemplate = Template::load($this->docRoot."/template/form.html");
        $formTemplate = Template::render($formTemplate, array(
            'action' => $_SERVER['REQUEST_URI'],
            
            'img1' => $img1,
            'img1x' => $imgInfo1['x'],
            'img1y' => $imgInfo1['y'],
            'img1w' => $imgInfo1['w'],
            'img1h' => $imgInfo1['h'],

            'img2' => $img2,
            'img2x' => $imgInfo2['x'],
            'img2y' => $imgInfo2['y'],
            'img2w' => $imgInfo2['w'],
            'img2h' => $imgInfo2['h'],

            'formReady' => 1,
            'errorCommon' => $errors['common'],
            'errorPhotoTeacher' => $errors['photoTeacher'],
            'errorBackground' => $errors['background']
        ));

        $this->renderPage($formTemplate);
    }

    private function getBackgroundImage($idx)
    {
       $info = $this->getBackgroundImageInfo($idx);
        if (!$info)
            return false;
        return $info['img'];
    }

    private function getBackgroundImageInfo($idx)
    {
        switch ($idx) {
            case 1:
                return array(
                    'img' => $this->docRoot.'/img/fon1.png',
                    'x' => 70,
                    'y' => 40,
                    'w' => 190,
                    'h' => 230
                );
            case 2:
                return array(
                    'img' => $this->docRoot.'/img/fon2.png',
                    'x' => 40,
                    'y' => 30,
                    'w' => 190,
                    'h' => 230
                );
            default:
                return false;
        }
    }

    private function renderPage($content)
    {
        $baseTemplate   = Template::load($this->docRoot.'/template/base.html');

        $css            = Template::loadCss($this->docRoot.'/css/styles.css');
        //$css           .= Template::loadCss($this->docRoot.'/vendors/jrac/jrac/style.jrac.css');
        
        $script         = Template::loadScript($this->docRoot.'/js/script.js');
        //$script        .= Template::loadScript($this->docRoot.'/vendors/jrac/jrac/jquery.jrac.js');
        
        $out = Template::render($baseTemplate, array(
            'css' => $css,
            'script' => $script,
            'content' => $content
        ));
        echo $out;
    }
}