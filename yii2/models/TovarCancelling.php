<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "tovar_cancelling".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 * @property integer $tovar_id
 * @property string $price
 * @property integer $amount
 * @property integer $sklad_id
 * @property string $reason
 * @property integer $shop_id
 */
class TovarCancelling extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tovar_cancelling';
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
            [['tovar_id', 'sklad_id', 'amount', 'shop_id'], 'required'],
            [['tovar_id', 'amount', 'sklad_id', 'shop_id'], 'integer'],
            [['price'], 'number'],
            [['reason'], 'string'],
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'safe'],
            //[['tovar_id', 'sklad_id'], 'unique', 'targetAttribute' => ['tovar_id', 'sklad_id'], 'message' => 'The combination of Tovar ID and Sklad ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Cоздан',
            'created_by' => 'Кем создан',
            'updated_at' => 'Изменен',
            'updated_by' => 'Updated By',
            'tovar_id' => 'Товар',
            'price' => 'Цена',
            'amount' => 'Кол-во',
            'sklad_id' => 'Склад',
            'reason' => 'Причина',
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
}
