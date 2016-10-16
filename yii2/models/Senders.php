<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "senders".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $code
 * @property string $name
 */
class Senders extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'senders';
    }
    
    public function behaviors()
	{
	    return [
	        TimestampBehavior::className(),	      
	    ];
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'name'], 'required'],
            [['created_at', 'updated_at'], 'save'],
            [['code'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Создан',
            'updated_at' => 'Изменен',
            'code' => 'Код',
            'name' => 'Название',
        ];
    }
}
