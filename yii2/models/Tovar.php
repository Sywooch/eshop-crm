<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
/**
 * This is the model class for table "tovar".
 *
 * @property integer $id
 * @property string $artikul
 * @property string $name
 * @property string $created_at
 */
class Tovar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tovar';
    }
    
    public function behaviors()
	{
	    return [
	        TimestampBehavior::className(),
	        BlameableBehavior::className(),
	        [
				'class' => SluggableBehavior::className(),
				'attribute' => 'name',
				'slugAttribute' => 'slug',
			],
	    ];
	}
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'price', 'active','category_id'], 'required'],
            //[['created_at'], 'safe'],
            [['price','pprice'], 'number'],
            [['active', 'category_id', 'type'], 'integer'],
            [['artikul'], 'string', 'max' => 100],
            [['name', 'slug'], 'string', 'max' => 255],            
            [['created_at','created_by','updated_at', 'updated_by', 'slug'], 'default', 'value'=>null],
            [['type'], 'default', 'value'=>0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'artikul' => 'Артикул',
            'name' => 'Название',
            'slug' => 'Псевдоним',
            'created_at' => 'Дата',
            'updated_at' => 'Изменен',
            'created_by' => 'Кто создал',
            'updated_by' => 'Кто изменил',
            'active' => 'Активен',
            'price' => 'Цена продажи',
            'pprice' => 'Цена закупа',
            'category_id' => 'Категория',
            'type' => 'Тип товара'
        ];
    }
    
    public function getPrihod()
    {
        return $this->hasOne(TovarPrihod::className(), ['tovar_id' => 'id']);
    }
    
    public function getRashod()
    {
        return $this->hasMany(TovarRashod::className(), ['tovar_id' => 'id']);
    }
    
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
    /*public function getKit()
    {
        return $this->hasMany(TovarKit::className(), ['tovar_id' => 'id']);
    }*/
    /*
    public function getPrice()
    {
        return $this->hasMany(Price::className(), ['tovar_id' => 'id']);
    }
    
    public function getCost()
    {
        return $this->hasMany(Costs::className(), ['tovar_id' => 'id']);
    }
    */
    public function itemAlias($type, $item=false) {
    	$_yesno = ['0' => 'Нет', '1' => 'Да'];
		$_items = array(			
			'active' => $_yesno,
			'type' => ['0'=>'Обычный', '1'=>'Комплект']
		);
		if ($item === false)
			return isset($_items[$type]) ? $_items[$type] : false;
		else
			return isset($_items[$type][$item]) ? $_items[$type][$item] : false;
	}
	
	public function beforeSave($insert)
	{
	    if (parent::beforeSave($insert)) {
	    	if(is_null($this->shop_id) or empty($this->shop_id))
	        	$this->shop_id = Yii::$app->params['user.current_shop'];
	    	//generete artikul
	        if(is_null($this->artikul) or empty($this->artikul)) {
				$last = 0;//$this->find()->where(['category_id'=>$this->category_id])->count();
				$last++; 
				if(strlen($last) == 2) $last = '0'.$last;
				elseif(strlen($last) == 1) $last = '00'.$last;
				$this->artikul = $this->category_id.'.'.$last;
			}
			elseif(substr($this->artikul,-1) == '.') {
				$art = explode('.',$this->artikul);				
				$last = $this->find()->where(['category_id'=>$this->category_id, 'artikul'=>$art['0']])->count();
				$last++; 
				if(strlen($last) == 2) $last = '0'.$last;
				elseif(strlen($last) == 1) $last = '00'.$last;
				$this->artikul = $this->artikul.$last;
			}
			$this->artikul = mb_strtoupper($this->artikul);
	        return true;
	    } else {
	        return false;
	    }
	}
	
	public function afterSave($insert, $changedAttributes)
	{   	   
	    parent::afterSave($insert, $changedAttributes);	    
	}
}
