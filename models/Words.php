<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "words".
 *
 * @property integer $id
 * @property string $word
 * @property integer $userID
 * @property integer $teach_priority
 * @property string $in_russian
 * @property string $in_armenian
 * @property integer $created_at
 * @property integer $updated_at
 */
class Words extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'words';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['word', 'userID', 'teach_priority', 'in_russian', 'in_armenian'], 'required'],
            [['userID', 'teach_priority', 'created_at', 'updated_at'], 'integer'],
            [['word'], 'string', 'max' => 60],
            [['in_russian', 'in_armenian'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'word' => 'Word',
            'userID' => 'User ID',
            'teach_priority' => 'Teach Priority',
            'in_russian' => 'In Russian',
            'in_armenian' => 'In Armenian',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUserWords(){
        return $this->find()->where(['userID' => Yii::$app->user->id])->orderBy('id DESC')->all();
    }
}
