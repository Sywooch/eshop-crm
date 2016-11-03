<?php

namespace app\models;

use yii\base\Model;
//use app\models\Category;

class ReportManagers extends Model
{
    public $date1;
    public $date2;
    public $category_id;
    public $source;

    public function rules()
    {
        return [
            [['date1', 'date2', 'category_id'], 'required'],
            [['date1', 'date2'], 'date','format'=>'yyyy-M-d'],
            [['date1', 'date2'], 'default', 'value' => date('Y-m-d')], 
            [['category_id', 'source'], 'integer']          
        ];
    }
    public function attributeLabels()
    {
        return [
            'date1' => 'Дата от',
            'date2' => 'Дата до',
            'category_id' => 'Категория допродаж',
            'source' => 'Источник заявки',
        ];
    }
    public function getCategoryList(){
	return Category::find()->select(['name', 'id'])->where(['shop_id'=>\Yii::$app->params['user.current_shop']])->indexBy('id')->column();
    }
    
    public function getSourceList(){
	return Orders::itemAlias('source');
    }
        
    public function itemAlias($type, $item=false) {    	
        $_items = array(
            'date_column' => ['1' => 'Приход', '0' => 'Расход']
        );
        if ($item === false)
            return isset($_items[$type]) ? $_items[$type] : false;
        else
            return isset($_items[$type][$item]) ? $_items[$type][$item] : false;
    }
}