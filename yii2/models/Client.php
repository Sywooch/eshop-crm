<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "client".
 *
 * @property string $id
 * @property string $fio
 * @property string $phone
 * @property string $email
 * @property string $address
 * @property string $ident
 * @property string $note
 */
class Client extends \yii\db\ActiveRecord
{
    public $regular = null; //заказывал больше одного раза
    public $fulladdress;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client';
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
            [['region_id', 'area_id', 'city_id', 'settlement_id', 'address', 'ident', 'flat', 'note'], 'string'],
            [['fio', 'flat'], 'string', 'max' => 250],
            [['phone', 'email'], 'string', 'max' => 50],
            [['postcode','shop_id'], 'integer'],            
            [['fio', 'postcode', 'region_id', 'area_id', 'city_id', 'settlement_id', 'address', 'ident', 'note', 'email', 'flat'], 'default', 'value' => null],
            [['postcode','fio','phone', 'email', 'note', 'flat', 'address'], 'trim'],  
            [['phone', 'shop_id'], 'unique', 'targetAttribute' => ['phone', 'shop_id'], 'message' => 'The combination of Phone and Shop ID has already been taken.'],
            [['regular','created_at', 'fulladdress'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Дата',
            'updated_at' => 'Изменен',
            'created_by' => 'Кто создал',
            'updated_by' => 'Кто изменил',
            'fio' => 'ФИО',
            'phone' => 'Телефон',
            'email' => 'Email',
            'postcode' => 'Индекс',
            'region_id' => 'Регион',
            'area_id' => 'Район',
            'city_id' => 'Город',
            'settlement_id' => 'Нас. пункт',
            'flat' => 'Улица, дом, кв-ра',
            'address' => 'Полный адрес',
            'fulladdress' => 'Полный адрес',
            'ident' => 'Уник ID',
            'note' => 'Примечание',
            'regular' => 'Постоянный',
            'shop_id' => '',
        ];
    }
    /*
    public function countRegular()
    {
		return self::find()->where(['id' => $this->id])->count();
		
	}
	*/
	public function getOrders()
	{
	    return $this->hasMany(Orders::className(), ['client_id' => 'id']);
	}
	public function getRegion()
	{
	    return $this->hasOne(Kladr::className(), ['code' => 'region_id'])->select(['code',"concat_ws(' ',substr(`code`, 1, 2), `name`, `socr`) as kname","concat_ws(' ',`name`, `socr`) as pname"]);
	}
	public function getArea()
	{
	    return $this->hasOne(Kladr::className(), ['code' => 'area_id'])->select(['code',"concat_ws(' ', `name`, `socr`) as kname"]);
	}
	public function getCity()
	{
	    return $this->hasOne(Kladr::className(), ['code' => 'city_id'])->select(['code',"concat_ws(' ', `socr`, `name`) as kname"]);
	}
	public function getSettlement()
	{
	    return $this->hasOne(Kladr::className(), ['code' => 'settlement_id'])->select(['code',"concat_ws(' ', `socr`, `name`) as kname"]);
	}
	public function getFulladdress() {
		//\yii\helpers\VarDumper::dump($this->region->pname,5,true);die;
		$return = null;//return 'q';
		if(is_null($this->address) or empty($this->address)){
			
			$addr = [];
			if(!is_null($this->region->pname)) $addr[] = $this->region->pname;
			if(!is_null($this->area->kname)) $addr[] = $this->area->kname;
			if(!is_null($this->city->kname)) $addr[] = $this->city->kname;
			if(!is_null($this->settlement->kname)) $addr[] = $this->settlement->kname;
			if(!is_null($this->flat)) $addr[] = $this->flat;
			
			$array_empty = array('');
			$addr = array_diff($addr, $array_empty);
			if(count($addr) >0) {				
				$return = implode(', ', $addr);				
			}		
		}
		else $return = $this->address;
		
		return $return;
	}
	
	public function beforeSave($insert)
	{
	    if (parent::beforeSave($insert)) {
	        if((is_null($this->shop_id) or empty($this->shop_id)) and Yii::$app->params['user.current_shop'] > 0) $this->shop_id = Yii::$app->params['user.current_shop']; 
	        return true;
	    } else {
	        return false;
	    }
	}
}
