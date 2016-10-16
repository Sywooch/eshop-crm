<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "tovar_costs".
 *
 * @property integer $id
 * @property integer $tovar_id
 * @property string $cost
 * @property integer $current
 * @property integer $active
 * @property string $note
 */
class TovarCosts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tovar_costs';
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
            [['tovar_id', 'cost'], 'required'],
            [['tovar_id', 'current', 'active'], 'integer'],
            [['cost'], 'number'],
            [['note'], 'string', 'max' => 200],
            [['note'], 'default', 'value'=>null],
            [['created_at','updated_at'], 'safe'],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tovar_id' => 'Товар',
            'cost' => 'Цена',
            'current' => 'Цена текущая',
            'active' => 'Цена активна',
            'note' => 'Описание',
            'created_at' => 'Создан',
            'updated_at' => 'Изменен',
        ];
    }
    
    public function itemAlias($type, $item=false) {
    	$_yesno = ['0' => 'Нет', '1' => 'Да'];
		$_items = array(			
			'active' => $_yesno,
			'current' => $_yesno,
		);
		if ($item === false)
			return isset($_items[$type]) ? $_items[$type] : false;
		else
			return isset($_items[$type][$item]) ? $_items[$type][$item] : false;
	}
}
