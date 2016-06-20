<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article_banks".
 *
 * @property integer $article_id
 * @property integer $bank_id
 *
 * @property Article $article
 */
class ArticleBanks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_banks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_id', 'bank_id'], 'required'],
            [['article_id', 'bank_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'article_id' => 'Article ID',
            'bank_id' => 'Bank ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(Article::className(), ['id' => 'article_id']);
    }
}
