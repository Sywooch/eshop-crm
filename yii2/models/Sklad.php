<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "sklad".
 *
 * @property integer $id
 * @property string $name
 */
class Sklad extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sklad';
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
            [['name'], 'required'],
            [['name'], 'string', 'max' => 250],            
            ['main', 'unique', 'message'=>'Основной склад уже назначен', 'filter' => ['and',['=', 'main', 1],['=','shop_id',Yii::$app->params['user.current_shop']]]],
            [['shop_id','main'], 'default', 'value'=>null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название', 
            'shop_id' => 'Shop',
            'main' => 'Основной',
        ];
    }
    /**
	* get shops object
	* 
	* @return
	*/
   /* public function getShops() {
		return $this->hasMany(Shops::className(), ['id' => 'shop_id'])
            ->viaTable('sklad_shops', ['sklad_id' => 'id']);
	}
	
	public function getShop_list() {
		return ArrayHelper::map(Shops::find()->all(), 'id', 'name');
	}*/
	/**
	* shop_id
	* 
	* @param integer $id
	* 
	* @return array
	*/
	/*
	public function setShop_id($id)
    {
        $this->shop_id = (array) $id;
    }*/
    /**
	* список ID магазинов доступных юзеру
	* 
	* @return array
	*/
	/*
	public function getShop_id(){
		return ArrayHelper::getColumn($this->getShops()->all(), 'id');
	}
	*/
	public function getRashod()
    {
        return $this->hasMany(TovarRashod::className(), ['sklad_id' => 'id'])->select('*')
        //->addSelect('(SELECT SUM(price*amount) from tovar_rashod where tovar_rashod.order_id = orders.id) as summ')
        ->addSelect('(price * amount) as summ')
        ->inverseOf('sklad');
    }
    
    public function getPrihod()
    {
        return $this->hasMany(TovarPrihod::className(), ['sklad_id' => 'id'])->select('*')
        //->addSelect('(SELECT SUM(price*amount) from tovar_rashod where tovar_rashod.order_id = orders.id) as summ')
        ->addSelect('(price_sale * amount) as summ')
        ->inverseOf('sklad');
    }
    
    public function beforeSave($insert)
	{
	    if (parent::beforeSave($insert)) {
	    	//\yii\helpers\VarDumper::dump($this,10,true);die;
	        if((!isset($this->shop_id) or is_null($this->shop_id) or empty($this->shop_id)) and (Yii::$app->params['user.current_shop'] >0))
	        	$this->shop_id = Yii::$app->params['user.current_shop'];
	        return true;
	    } else {
	        return false;
	    }
	}
	
	public static function defaultId($shop=false){
		if($shop)
			return self::find()->select('id')->where(['shop_id'=>$shop, 'main'=>1])->scalar();
		else
			return self::find()->select('id')->where(['shop_id'=>Yii::$app->params['user.current_shop'], 'main'=>1])->scalar();
	}
	
	public function getDefaultSklad(){
		
	}
	
/*	
	public function afterSave($insert, $changedAttributes)
	{
		if(isset($_POST['Sklad']['shop_id']) and !empty($_POST['Sklad']['shop_id'])) {			
			$old_shops = $new_shops = array();
			foreach($this->shops as $shops) {
				$old_shops[] = $shops->id;
			}
			$new_shops = $_POST['Sklad']['shop_id'];
			$new_id = array_diff($new_shops, $old_shops);
			$del_id = array_diff($old_shops, $new_shops);
			//\yii\helpers\VarDumper::dump($del_id,10,true);
			if (!empty($del_id)) {
				$iddel = '';
				$numdel =0;
				foreach ($del_id as $id=>$v) {
					if (empty($iddel)) $iddel .= $v;
					else $iddel .= ','.$v;
				}
				$numdel = SkladShops::deleteAll('sklad_id = :sklad_id and shop_id IN (:shop_id)', [':shop_id'=>$iddel, ':sklad_id'=>$this->id]);
				$return .= 'Привязаны магазины: '.$iddel.'. ';
			}
			if (!empty($new_id)) {
				foreach ($new_id as $id) {
			        $values[] = [$this->id, $id];
			    }
			    $ins = self::getDb()->createCommand()
			        ->batchInsert(SkladShops::tableName(), ['sklad_id', 'shop_id'], $values)->execute();
			    $return = 'Привязаны магазины '.implode(",", $new_id);
			}				
		}
		elseif(isset($_POST['Sklad']['shop_id']) and empty($_POST['Sklad']['shop_id'])) {
			SkladShops::deleteAll(['sklad_id' => $this->id]);
			$return = 'Отвязаны все магазины';
		}

	    parent::afterSave($insert, $changedAttributes);
	}
	*/
}
