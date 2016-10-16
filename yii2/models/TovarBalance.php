<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "tovar_balance".
 *
 * @property integer $id
 * @property integer $tovar_id
 * @property integer $sklad_id
 * @property integer $amount
 * @property string $price
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $shop_id
 */
class TovarBalance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tovar_balance';
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
            [['tovar_id', 'sklad_id', 'amount'], 'required'],
            [['tovar_id', 'sklad_id', 'amount'], 'integer'],
            [['price'], 'number'],
            [['tovar_id', 'sklad_id', 'shop_id'], 'unique', 'targetAttribute' => ['tovar_id', 'sklad_id', 'shop_id'], 'message' => 'The combination of Tovar ID, Sklad ID and Shop ID has already been taken.'],
            [['created_at', 'updated_at', 'created_by', 'updated_by', 'shop_id'], 'safe'],
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
            'sklad_id' => 'Склад',
            'amount' => 'Кол-во',
            'price' => 'Сумма',
            'created_at' => 'Создан',
            'updated_at' => 'Изменен',
            'created_by' => 'Кем создан',
            'updated_by' => 'Updated By',
            'shop_id' => 'Shop ID',
        ];
    }

    public function getTovar()
    {
        return $this->hasOne(Tovar::className(), ['id' => 'tovar_id']);
    }
    
    public function getSklad()
    {
        return $this->hasOne(Sklad::className(), ['id' => 'sklad_id']);
    }
	/**
	* 
	* @param int $tovar_id
	* @param int $sklad_id
	* @param int $amount
	* @param int $shop_id
	* @param string $type +/-
	* 
	* @return string $msg
	*/
	public static function calc($tovar_id, $sklad_id, $amount, $shop_id, $type='-') {
    	$msg = '';
    	$balance = TovarBalance::find()->where(['tovar_id'=>$tovar_id, 'sklad_id'=>$sklad_id, 'shop_id'=>$shop_id])->one();
		if ($balance) {
			if($type == '+') 
				$balance->amount = $balance->amount + $amount;
			else
				$balance->amount = $balance->amount - $amount;
				
			if ($balance->save()) {
				$msg .= 'Остатки по товару '. $rashod->tovar->name .' пересчитаны. Текущий остаток: '.$balance->amount;							
			}
			else $msg .= 'Остатки по товару '.$rashod->tovar->name . ' НЕ пересчитаны. Ошибка: '.print_r($balance->firstErrors, true);
		}
		else {
			$balance = new TovarBalance();
			$balance->tovar_id = $tovar_id;
			$balance->sklad_id = $sklad_id;
			$balance->shop_id = $shop_id;
			
			$prihod = TovarPrihod::find()->where(['tovar_id'=>$tovar_id])->andWhere(['sklad_id'=>$sklad_id])->andWhere(['shop_id'=>$shop_id])->sum('amount');
			$rashod = TovarRashod::find()->joinWith('order')->where(['tovar_id'=>$tovar_id])->andWhere(['sklad_id'=>$sklad_id])
				->andWhere(['orders.shop_id'=>$shop_id])->andWhere(['orders.status'=>6])->andWhere(['orders.otpravlen'=>1])->sum('amount');
			
			$msg .= 'Cуществующий приход: '.$prihod. ' Расход: '.$rashod. ' ';
			
			if($type == '+')
				$balance->amount = ($prihod - $rashod) + $amount;
			else
				$balance->amount = ($prihod - $rashod) - $amount;
			
			if ($balance->save()) {
				$msg .= 'Остатки по товару '. $rashod->tovar->name .' пересчитаны! Текущий остаток: '.$balance->amount;						
			}
			else $msg .= 'Остатки по товару '.$rashod->tovar->name . ' НЕ пересчитаны. Ошибка: '.print_r($balance->firstErrors, true);
			
		}
		return $msg;
	}
  /*  
    public static function plus($tovar_id, $sklad_id, $amount, $shop_id) {
    	$msg = '';
    	$balance = TovarBalance::find()->where(['tovar_id'=>$tovar_id, 'sklad_id'=>$sklad_id, 'shop_id'=>$shop_id])->one();
		if ($balance) {
			$balance->amount = $balance->amount + $amount;
			if ($balance->save()) {
				$msg .= 'Остатки по товару '. $rashod->tovar->name .' пересчитаны. Текущий остаток: '.$balance->amount;							
			}
			else $msg .= 'Остатки по товару '.$rashod->tovar->name . ' НЕ пересчитаны. Ошибка: '.print_r($balance->firstErrors, true);
		}
		else {
			$balance = new TovarBalance();
			$balance->tovar_id = $tovar_id;
			$balance->sklad_id = $sklad_id;
			$balance->shop_id = $shop_id;
			
			$prihod = TovarPrihod::find()->where(['tovar_id'=>$tovar_id])->andWhere(['sklad_id'=>$sklad_id])->andWhere(['shop_id'=>$shop_id])->sum('amount');
			$rashod = TovarRashod::find()->joinWith('order')->where(['tovar_id'=>$tovar_id])->andWhere(['sklad_id'=>$sklad_id])
				->andWhere(['orders.shop_id'=>$shop_id])->andWhere(['orders.status'=>6])->andWhere(['orders.otpravlen'=>1])->sum('amount');
			
			$msg .= 'Cуществующий приход: '.$prihod. ' Расход: '.$rashod. ' ';
			$balance->amount = ($prihod - $rashod) + $amount;
			if ($balance->save()) {
				$msg .= 'Остатки по товару '. $rashod->tovar->name .' пересчитаны! Текущий остаток: '.$balance->amount;						
			}
			else $msg .= 'Остатки по товару '.$rashod->tovar->name . ' НЕ пересчитаны. Ошибка: '.print_r($balance->firstErrors, true);
			
		}
		return $msg;
	}
	
	public static function minus($tovar_id, $sklad_id, $amount, $shop_id) {
    	$msg = '';
    	$balance = TovarBalance::find()->where(['tovar_id'=>$tovar_id, 'sklad_id'=>$sklad_id, 'shop_id'=>$shop_id])->one();
		if ($balance) {
			$balance->amount = $balance->amount - $amount;
			if ($balance->save()) {
				$msg .= 'Остатки по товару '. $rashod->tovar->name .' пересчитаны. Текущий остаток: '.$balance->amount;							
			}
			else $msg .= 'Остатки по товару '.$rashod->tovar->name . ' НЕ пересчитаны. Ошибка: '.print_r($balance->firstErrors, true);
		}
		else {
			$balance = new TovarBalance();
			$balance->tovar_id = $tovar_id;
			$balance->sklad_id = $sklad_id;
			$balance->shop_id = $shop_id;
			
			$prihod = TovarPrihod::find()->where(['tovar_id'=>$tovar_id])->andWhere(['sklad_id'=>$sklad_id])->andWhere(['shop_id'=>$shop_id])->sum('amount');
			$rashod = TovarRashod::find()->joinWith('order')->where(['tovar_id'=>$tovar_id])->andWhere(['sklad_id'=>$sklad_id])
				->andWhere(['orders.shop_id'=>$shop_id])->andWhere(['orders.status'=>6])->andWhere(['orders.otpravlen'=>1])->sum('amount');
			
			$msg .= 'Cуществующий приход: '.$prihod. ' Расход: '.$rashod. ' ';
			$balance->amount = ($prihod - $rashod) - $amount;
			if ($balance->save()) {
				$msg .= 'Остатки по товару '. $rashod->tovar->name .' пересчитаны! Текущий остаток: '.$balance->amount;						
			}
			else $msg .= 'Остатки по товару '.$rashod->tovar->name . ' НЕ пересчитаны. Ошибка: '.print_r($balance->firstErrors, true);
			
		}
		return $msg;
	}
	*/
/*    
    public function prihod($tovar_id, $sklad_id, $amount) {
		$msg = '';
		$balance = TovarBalance::find()->where(['tovar_id'=>$tovar_id, 'sklad_id'=>$sklad_id])->one();
		if ($balance) {
			$balance->amount = $balance->amount + $amount;
			if ($balance->save()) {
				$msg .= 'Приход товара '. $rashod->tovar->name .' проведен! ';						
			}
			else $msg .= 'Приход товара '.$rashod->tovar->name . ' провести НЕ удалось. ';
		}
		else {
			$balance = new TovarBalance();
			$balance->tovar_id = $tovar_id;
			$balance->sklad_id = $sklad_id;
			$balance->amount = $amount;
			//$balance->updated_at = gmdate('Y-m-d H:i:s');
			if ($balance->save()) {
				$msg .= 'Приход товара '. $rashod->tovar->name .' создан! ';						
			}
			else $msg .= 'Приход товара '.$rashod->tovar->name . ' создать НЕ удалось. ';
		}
		return $msg;
	}
	
	public function rashod($order_id) {
		$msg = '';
		$rashod = TovarRashod::find()->where(['order_id'=>$order_id])->indexBy('id')->asArray()->all();
		if ($rashod) {
			foreach ($rashod as $val) {
				$tovar_id = $val['tovar_id'];				
				$sklad_id = $val['sklad_id'];
				$amount = $val['amount'];
				
				$balance = TovarBalance::find()->where(['tovar_id'=>$tovar_id, 'sklad_id'=>$sklad_id])->one();
				if ($balance) {
					$balance->amount = $balance->amount - $amount;
					if ($balance->save()) {
						$msg .= 'Списание товара '. $rashod->tovar->name .' проведено! ';						
					}
					else $msg .= 'Списание товара '.$rashod->tovar->name . ' провести НЕ удалось. ';
				}
				else {
					$balance = new TovarBalance();
					$balance->tovar_id = $tovar_id;
					$balance->sklad_id = $sklad_id;
					$balance->amount = 0 - $amount;
					//$balance->updated_at = gmdate('Y-m-d H:i:s');
					if ($balance->save()) {
						$msg .= 'Списание товара '. $rashod->tovar->name .' создано! ';						
					}
					else $msg .= 'Списание товара '.$rashod->tovar->name . ' создать НЕ удалось. ';
				}
				
			}
			
		}
		else {
			$msg .= 'Товар по заявке не найден. ';
		}
		return $msg;
	}
*/
}
