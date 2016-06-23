<?php

namespace common\helpers;

use common\enums\ImageType;
use common\enums\WidgetType;
use yii\base\Exception;

class HImage
{
    const FB_MAX_WIDTH = 1200;
    const FB_PROPORTION = '1.91:1';

    /**
     * для iOS: 640, 750, 1242, 1536
     * для Android: 320, 768, 1080, 1200

     * @var array
     */
    public static $SIZES = [
        1536,
        1242,
        1200,
        1080,
        768,
        750,
        640,
        320
    ];

    const JPG_QUOLITY = 85;

    public static $sliceFolder = 'orig';

    /**
     * @param $source_path
     * @param $destination_path
     * @param $newwidth
     * @param bool $newheight
     * @param bool $quality // качество для формата jpeg
     * @return array
     */
    public static function resize($source_path, $destination_path, $newwidth, $newheight = FALSE, $quality = FALSE)
    {
        HImage::createDir($destination_path);
        ini_set("gd.jpeg_ignore_warning", 1); // иначе на некотоых jpeg-файлах не работает

        list($oldwidth, $oldheight, $type) = getimagesize($source_path);

        if ($oldwidth<$newwidth){
            copy($source_path, $destination_path);
            @chmod($destination_path, 0666);
            return [$oldwidth, $oldheight];
        }

        switch ($type) {
            case IMAGETYPE_JPEG:
                $typestr = 'jpeg';
                if(!$quality) {
                    $quality = self::JPG_QUOLITY;
                }
                break;
            case IMAGETYPE_GIF:
                $typestr = 'gif';
                break;
            case IMAGETYPE_PNG:
                $typestr = 'png';
                break;
        }
        $function = "imagecreatefrom$typestr";
        $src_resource = $function($source_path);

        if (!$newheight) {
            $newheight = round($newwidth * $oldheight / $oldwidth);
        } elseif (!$newwidth) {
            $newwidth = round($newheight * $oldwidth / $oldheight);
        }
        $destination_resource = imagecreatetruecolor($newwidth, $newheight);

        switch ($type) {

            case IMAGETYPE_GIF : // gif
                imagecopyresampled($destination_resource, $src_resource, 0, 0, 0, 0, $newwidth, $newheight, imagesx($src_resource), imagesy($src_resource));
                imagegif($destination_resource, $destination_path);
                break;

            case IMAGETYPE_JPEG : // jpeg
                imagecreatefromjpeg($source_path);
                imagecopyresampled($destination_resource, $src_resource, 0, 0, 0, 0, $newwidth, $newheight, imagesx($src_resource), imagesy($src_resource));
                imageinterlace($destination_resource, 1); // чересстрочное формирование изображение
                imagejpeg($destination_resource, $destination_path, $quality);
                break;

            case IMAGETYPE_PNG : // png
                imageAlphaBlending($destination_resource, false);
                imageSaveAlpha($destination_resource, true);
                imagecopyresampled($destination_resource, $src_resource, 0, 0, 0, 0, $newwidth, $newheight, imagesx($src_resource), imagesy($src_resource));
                imagepng($destination_resource, $destination_path);
                break;
        }

        imagedestroy($destination_resource);
        imagedestroy($src_resource);
        @chmod($destination_path, 0666);
        return [$newwidth, $newheight];
    }

    /**
     * Resize to horizontal proportion and fill expty space by white color
     * @param $source_path
     * @param $destination_path
     * @param string $proportion
     * @param bool|FALSE $quality
     * @return array
     * @throws Exception
     */
    public static function saveAsProportional($source_path, $destination_path, $proportion = '1:1', $quality = FALSE)
    {
        HImage::createDir($destination_path);
        ini_set("gd.jpeg_ignore_warning", 1); // иначе на некотоых jpeg-файлах не работает

        list($oldwidth, $oldheight, $type) = getimagesize($source_path);

        switch ($type) {
            case IMAGETYPE_JPEG:
                $typestr = 'jpeg';
                if(!$quality) {
                    $quality = self::JPG_QUOLITY;
                }
                break;
            case IMAGETYPE_GIF:
                $typestr = 'gif';
                break;
            case IMAGETYPE_PNG:
                $typestr = 'png';
                break;
        }
        $function = "imagecreatefrom$typestr";
        $src_resource = $function($source_path);

        $arProp = explode(':', $proportion);
        if (count($arProp) !== 2) {
            throw new Exception('Incorrect format of proportion');
        }


        if (($oldwidth/$oldheight) > $arProp[0]) {
            $newwidth = $oldwidth;
            $newheight = round($oldwidth/$arProp[0]);
        } else {
            $newwidth = round($oldheight * $arProp[0]);
            $newheight = $oldheight;
        }

        if ($newwidth > self::FB_MAX_WIDTH) {
            $newwidth   = self::FB_MAX_WIDTH;
            $newheight  = round(self::FB_MAX_WIDTH / $arProp[0]);

            if ($oldwidth/$oldheight > $arProp[0]) {
                $toWidth = $newwidth;
                $toHeight = round($newwidth * $oldheight / $oldwidth);
            } else {
                $toHeight = $newheight;
                $toWidth = round($newheight * $oldwidth / $oldheight);
            }
        } else {
            $toWidth = $oldwidth;
            $toHeight = $oldheight;
        }

        $left   = $newwidth > $toWidth ?  (int)(($newwidth - $toWidth) / 2) : 0;
        $top    = $newheight > $toHeight ? (int)(($newheight - $toHeight) / 2) : 0;

        $destination_resource = imagecreatetruecolor($newwidth, $newheight);
        $bgColor = array(255, 255, 255);
        $bg = imagecolorallocate($destination_resource, $bgColor[0], $bgColor[1], $bgColor[2]);
        imagefill($destination_resource, 0, 0, $bg);

        imagecopyresampled($destination_resource, $src_resource, $left, $top, 0, 0, $toWidth, $toHeight, $oldwidth, $oldheight);

        if ($type == 2) { # jpeg
            imageinterlace($destination_resource, 1); // чересстрочное формирование изображение
            imagejpeg($destination_resource, $destination_path, $quality);
        } else { # gif, png
            $function = "image$typestr";
            $function($destination_resource, $destination_path);
        }

        imagedestroy($destination_resource);
        imagedestroy($src_resource);
        return [$newwidth, $newheight];
    }


    public static function createDir($path)
    {
        $dir = dirname($path);
        if (!file_exists($dir)) {
            if (@!mkdir($dir)) {
                throw new Exception("Can't create dir: $dir");
            }

            chmod($dir, 0777);
        }
    }

    public static function checkSizes($fileName)
    {
        $error = null;
        $info = getimagesize($fileName);
        if (!$info) {
            return 'Не верный формат изображения';
        }
        if ($info[0] < 1080) {
            return 'Минимальная ширина должна быть 1080 пикселей';
        }
        if ($info[1]/$info[0] > 3.792)  {
            return 'Соотношение сторон не верно, при ширине 1080 высота не должна превышать 4096';
        }
        return null;
    }

    public static function getSrc($type, $filename)
    {
        if (!$filename) return null;

        if (strpos($filename, 'http') === 0) {
            return $filename;
        }
        $path = ImageType::getPath($type);

        return \Yii::getAlias("@static").$path.$filename;
    }

    public static function getPath($type, $filename)
    {
        if (!$filename) return null;
        if (strpos($filename, 'http') === 0) return $filename;
        return \Yii::getAlias('@upload').ImageType::getPath($type).$filename;
    }

}