<?php

namespace common\models;

use common\components\helpers\HDev;
use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "form_data".
 *
 * @property integer $id
 * @property string $hash
 * @property string $model
 * @property string $data
 * @property string $created
 * @property string $last_view
 *
 * @method void touch($field)
 */
class FormData extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'form_data';
    }

    public static function getFilteredData($form_hash, $className)
    {
        return array_filter(self::getData($form_hash, $className));
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => 'last_view',
                'value' => new Expression('NOW()'),
            ],
        ];
    }



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hash', 'model', 'data'], 'required'],
            [['data'], 'string'],
            [['hash'], 'string', 'max' => 32],
            [['model'], 'string', 'max' => 255]
        ];
    }

    public static function saveForm(Model $form)
    {
        $jsonData = json_encode($form->attributes);
        $hash = md5($jsonData);

        /** @var FormData $model */
        $model = self::findOne(['hash' => $hash, 'model'=>$form->className()]);
        if (!$model) {
            $model = new self();
            $model->model = $form->className();
            $model->hash = $hash;
            $model->data = $jsonData;
            if (!$model->save()) {
                HDev::logSaveError($model, true);
            }
        }
        return $model->hash;
    }

    public static function getData($dataHash, $formName, $updateLastView = true)
    {
        /** @var FormData $model */
        $model = self::findOne(['hash' => $dataHash, 'model'=> $formName]);
        if (!$model) {
            throw new NotFoundHttpException('Некорректный поисковый запрос');
        }
        if ($updateLastView) {
            $model->touch('last_view');
            $model->save(false, ['last_view']);
        }
        return json_decode($model->data, true);
    }

}
