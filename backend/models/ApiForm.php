<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 5/5/15
 * Time: 10:11 AM
 */

namespace backend\models;


use common\enums\ApiStatus;
use common\enums\ApiType;
use common\models\ApiLog;
use yii\base\Model;

class ApiForm extends Model
{
    public $types;
    public $allTypes;

    protected $progressLogs = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['types'], 'required'],
            [['types'], 'validateTypes'],
        ];
    }

    public function validateTypes()
    {
        $availableTypes = $this->getAvailableTypes();
        foreach($availableTypes as $type) {
            if (in_array($type, $this->types)) {
                $this->addError('types', "$type уже синхронизируются.");
                break;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'types' => 'Выберите сущности для синхронизации',
        ];
    }

    public function getAvailableTypes()
    {
        $logs = $this->getProgressLogs();
        $allTypes = ApiType::getList();
        foreach($logs as $log) {
            unset($allTypes[$log->type]);
        }
        return $allTypes;
    }

    /**
     * @return ApiLog[]
     */
    public function getProgressLogs()
    {
        if (is_null($this->progressLogs)) {
            $this->updateProgressLogs();
        }
        return $this->progressLogs;
    }

    public function updateProgressLogs()
    {
        $this->progressLogs = ApiLog::find()->where(['status' => ApiStatus::IN_PROGRESS])->orderBy('id')->all();
    }

}