<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use app\modules\user\models\User;
/**
 * This is the model class for table "money_balance".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $summa
 * @property integer $method_id
 */
class MoneyBalance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'money_balance';
    }
    
    public function behaviors()
	{
	    return [
	        TimestampBehavior::className(),
	        BlameableBehavior::className(),
	    ];
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['summa', 'method_id', 'money_id'], 'required'],
            [['method_id'], 'integer'],
            [['created_at', 'updated_at', 'created_by', 'updated_by',], 'save'],
            [['summa'], 'number'],
            ['summa', 'default', 'value'=>'0'],
            [['method_id', 'money_id'], 'unique']
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
            'money_id' => 'Источник',
            'summa' => 'Сумма',
            'method_id' => 'Способ оплаты',
        ];
    }
    
    public function getUser () {
		return $this->hasOne(User::className(), ['id' => 'created_by','id' => 'updated_by']);//
	}
	
	public function getMethod () {
		return $this->hasOne(MoneyMethod::className(), ['id' => 'method_id']);
	}
	
	public function getMoney () {
		return $this->hasOne(Money::className(), ['id' => 'money_id']);
	}
	
	static function calcBalance($model) {
		$b = self::findOne(['method_id'=>$model->method_id]);
		if (is_null($b))
			$b = new MoneyBalance();
		else {
			if($b->money_id == $model->id) return 'По этой операции баланс уже просчитан. ';
		}
		if($model->type == 'in') $b->summa = $b->summa + $model->summa;
		else $b->summa = $b->summa - $model->summa;
		$b->money_id = $model->id;
		$b->method_id = $model->method_id;
		
		if($b->save()) 
			return 'Баланс пересчитан! ';
		else
			return 'Баланс НЕ пересчитан. '.print_r($b->errors, true);
	}
}
