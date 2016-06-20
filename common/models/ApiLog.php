<?php

namespace common\models;

use common\components\helpers\HDates;
use common\enums\ApiStatus;
use Yii;
use yii\caching\DbDependency;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "api_log".
 *
 * @property integer $id
 * @property string $created
 * @property string $duration
 * @property string $type
 * @property string $status
 * @property integer $count
 * @property string $sub_count
 * @property string $error
 * @property integer $pid
 */
class ApiLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_log';
    }

    public static function getDependency()
    {
        return new DbDependency(['sql' => 'select max(id) from api_log', 'reusable' => true]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created', 'type', 'status'], 'required'],
            [['created'], 'safe'],
            [['duration'], 'number'],
            [['count', 'pid'], 'integer'],
            [['count', 'duration'], 'default', 'value' => 0],
            [['sub_count'], 'string'],
            [['type', 'status'], 'string', 'max' => 255],
            [['id', 'type', 'status', 'created'], 'safe', 'on' => 'search']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created' => 'Дата',
            'type' => 'Тип',
            'status' => 'Статус',
            'count' => 'Количество',
            'duration' => 'Длительность',
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = self::find();

        $query->andFilterWhere([
            'id' => $this->id,
            'count' => $this->count,
            'type' => $this->type,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['=', '(created::date)', $this->created]);
        $progressType = ApiStatus::IN_PROGRESS;
        $query->select[] = "(case when status = '$progressType' then 1 else 0 end) as progress";
        $query->select[] = "*";
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created' => SORT_DESC],
                'attributes' => [
                    'id', 'created', 'status',
                    'date' => ['asc' => ['(created::date)' => 'asc'], 'desc' => ['(created::date)' => 'desc']],
                    'progress'/* => ['asc' => ["(status = 'in_progress')" => 'asc'], 'desc' => ["(status = 'in_progress')" => 'desc']]*/
                ]
            ]
        ]);
    }

    public function getLiveStatus()
    {
        $fileName = $this->getLiveFileFullName();
        $data = null;
        if (file_exists($fileName)) {
            $data = file_get_contents($fileName);
        }
        return json_decode($data, true);
    }

    public function saveLiveStatus($data)
    {
        $dir = Yii::getAlias('@runtime')."/api";
        if (!file_exists($dir)) {
            mkdir($dir);
            chmod($dir, 0777);
        }

        $fileName = $this->getLiveFileFullName();
        if (!file_exists($fileName)) {
            touch($fileName);
            chmod($fileName, 0666);
        }
        file_put_contents($fileName, json_encode($data));
    }

    public function getLiveStatusDir()
    {
        return Yii::getAlias('@console')."/runtime/api";
    }

    public function getLiveFileName()
    {
        return $this->id.".json";
    }

    public function getLiveFileFullName()
    {
        return $this->getLiveStatusDir().'/'.$this->getLiveFileName();
    }

    public function isToday()
    {
        return HDates::short($this->created) == HDates::short();
    }
}
