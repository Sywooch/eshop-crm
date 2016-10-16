<?php

namespace app\models;

use Yii;
use app\models\Orders;
use app\models\Tovar;
use app\models\Sklad;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "tovar_rashod".
 *
 * @property integer $id
 * @property string $created_at
 * @property integer $order_id
 * @property integer $tovar_id
 * @property string $price
 * @property integer $amount
 * @property integer $sklad_id
 */
class TovarRashod extends \yii\db\ActiveRecord
{
	public $summ;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tovar_rashod';
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
            [['order_id', 'tovar_id', 'price', 'amount'], 'required'],
            [['created_at','updated_at'], 'safe'],
            [['order_id', 'tovar_id', 'amount', 'sklad_id'], 'integer'],
            [['price'], 'number'],
            [['order_id', 'tovar_id', 'sklad_id'], 'unique', 'targetAttribute' => ['order_id', 'tovar_id', 'sklad_id'], 'message' => 'The combination of Order ID, Tovar ID and Sklad ID has already been taken.']
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
            'order_id' => 'Заказ',
            'tovar_id' => 'Товар',
            'price' => 'Цена 1шт',
            'amount' => 'Кол-во',
            'sklad_id' => 'Склад',
        ];
    }
    
    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['id' => 'order_id'])->inverseOf('rashod');
    }
    
    public function getTovar()
    {
        return $this->hasOne(Tovar::className(), ['id' => 'tovar_id']);
    }
    
    public function getSklad()
    {
        return $this->hasOne(Sklad::className(), ['id' => 'sklad_id']);
    }
    
    public function beforeSave($insert)
	{
	    if (parent::beforeSave($insert)) {
	        $this->shop_id = $this->tovar->shop_id;//Yii::$app->params['user.current_shop'];
	        if(empty($this->sklad_id)) $this->sklad_id = Sklad::find()->select('id')->where(['shop_id'=>$this->shop_id, 'main'=>1])->scalar();//Sklad::defaultId($this->shop_id);
	        return true;
	    } else {
	        return false;
	    }
	}
}
