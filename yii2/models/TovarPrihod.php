<?php

namespace app\models;

use Yii;
use app\models\Tovar;
use app\models\Sklad;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "tovar_prihod".
 *
 * @property integer $id
 * @property integer $tovar_id
 * @property string $date_at
 * @property double $price
 * @property integer $amount
 * @property integer $supplier_id
 * @property integer $sklad_id
 * @property string $doc
 * @property string $note
 */
class TovarPrihod extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tovar_prihod';
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
            [['tovar_id', 'date_at', 'price', 'sklad_id', 'amount'], 'required'],
            [['tovar_id', 'amount', 'sklad_id'], 'integer'],
            [['date_at'], 'date', 'format' => 'php:Y-m-d'],
            //[['date_at'], 'safe'],
            [['price', 'price_sale'], 'number'],
            [['supplier_id', 'price_sale', 'doc', 'note'], 'default', 'value'=>null],
            [['note'], 'string'],
            [['supplier_id', 'doc'], 'string', 'max' => 300],           
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Дата создания',
            'updated_at' => 'Изменен',
            'created_by' => 'Кто создал',
            'updated_by' => 'Кто изменил',
            'date_at' => 'Дата прихода',
            'tovar_id' => 'Товар',            
            'price' => 'Цена закупа 1 шт',
            'price_sale' => 'Цена продажи 1 шт',
            'amount' => 'Количество',
            'supplier_id' => 'Поставщик',
            'sklad_id' => 'Склад',
            'doc' => 'Документ',
            'note' => 'Примечание',
        ];
    }
    public function getActiveTovar()
    {
        return $this->hasOne(Tovar::className(), ['id' => 'tovar_id'])->where(['active'=>1]);
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
	    	$this->shop_id = $this->tovar->shop_id;
	        //$this->shop_id = Yii::$app->params['user.current_shop'];
	        return true;
	    } else {
	        return false;
	    }
	}
}
