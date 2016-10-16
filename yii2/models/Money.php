<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use app\modules\user\models\User;
use app\models\MoneyItem;
use app\models\MoneyMethod;
/**
 * This is the model class for table "money".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $summa
 * @property integer $item_id
 * @property integer $method_id
 * @property string $type
 * @property string $note
 */
class Money extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'money';
    }
    
    public function behaviors()
	{
	    return [
	        TimestampBehavior::className(),
	        BlameableBehavior::className(),
	        /*[
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
               'updatedByAttribute' => 'updated_by',
            ],*/
	    ];
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'safe'],
            [['date_at', 'summa', 'item_id', 'method_id', 'type'], 'required'],
            [['summa'], 'number'],
            [['item_id', 'method_id'], 'integer'],
            [['type', 'note'], 'string'],
            [['note'], 'default', 'value'=>null]//'updated_at','updated_by', 'created_at', 'updated_at', 
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
            'created_by' => 'Кем создан',
            'updated_by' => 'Кем изменен',
            'date_at' => 'Дата',
            'summa' => 'Сумма',
            'item_id' => 'Статья прихода/расхода',
            'method_id' => 'Способ прихода/расхода',
            'type' => 'Направление',
            'note' => 'Примечание',
        ];
    }
    
    public function itemAlias($type, $item=false) {    	
		$_items = array(
			'type' => ['in' => 'Приход', 'out' => 'Расход']
		);
		if ($item === false)
			return isset($_items[$type]) ? $_items[$type] : false;
		else
			return isset($_items[$type][$item]) ? $_items[$type][$item] : false;
	}
	
	public function getUser () {
		return $this->hasOne(User::className(), ['id' => 'created_by','id' => 'updated_by']);//
	}
	
	public function getItem () {
		return $this->hasOne(MoneyItem::className(), ['id' => 'item_id']);
	}
	
	public function getMethod () {
		return $this->hasOne(MoneyMethod::className(), ['id' => 'method_id']);
	}
}
