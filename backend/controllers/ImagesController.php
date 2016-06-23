<?php

namespace backend\controllers;

use common\enums\ImageType;
use common\helpers\HImage;
use frontend\components\Controller;
use Yii;
use yii\web\Response;
use yii\web\UploadedFile;


/**
 * WidgetsController implements the CRUD actions for Widget model.
 */
class ImagesController extends Controller
{

    public function actionUpload($type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!ImageType::hasValue($type)){
            return [
                'success'=>false,
                'error' => 'Не корректный тип изображения',
            ];
        }

        $image = UploadedFile::getInstanceByName('img');

        if (!$image) {
            return [
                'success'=>false,
                'error' => 'Не удалось загрузить изображение',
            ];
        }

        if ($error = $this->validateSize($type, $image))
        {
            return [
                'success'=>false,
                'error' => $error,
            ];
        }

        $path = ImageType::getPath($type);

        $baseFileName = time() . '_' . $image->baseName . '.' . strtolower($image->extension);
        $filename = $path . $baseFileName;
        $fullName = Yii::getAlias('@upload') . $filename;

        HImage::createDir($fullName);

        if ($width = ImageType::needResize($type)) {
            HImage::resize($image->tempName, $fullName, $width);
        } else {
            $image->saveAs($fullName);
        }
        chmod($fullName, 0777);

        return [
            'success'=>true,
            'filename' => $baseFileName,
            'src' => \Yii::getAlias('@static') . $filename,
        ];
    }

    private function validateSize($type, UploadedFile $image)
    {
        list($width, $height, $img_type) = getimagesize($image->tempName);
        if ($minWidth = ImageType::getMinWidth($type) and $minWidth > $width) {
            return "Слишком маленькая ширина. Должна быть больше $minWidth";
        }
        if ($minHeight = ImageType::getMinHeight($type) and $minHeight > $height) {
            return "Слишком маленькая высота. Должна быть больше $minHeight";
        }
        if ($ratio = ImageType::getRatio($type) and abs($width/$height - $ratio) > 0.01) {
            return "Неверное соотношение сторон. Должно быть ".ImageType::getRatioText($type);
        }
        return false;
    }
}
