<?php

namespace app\models;

use yii\base\Model;

class SmsMailing extends Model
{
    public $date1;
    public $date2;
    public $status;
    public $category;
    public $msg;
    public $count;
    public $yes;

    public function rules()
    {
        return [
            [['date1', 'date2', 'category', 'status', 'msg'], 'required'],
            [['date1', 'date2'], 'date','format'=>'yyyy-M-d'],
            [['date1', 'date2'], 'default', 'value' => date('Y-m-d')], 
            [['category', 'status', 'yes'], 'integer'],
            ['msg', 'trim'],
            ['msg', 'string'],
            ['count', 'safe']
        ];
    }
    public function attributeLabels()
    {
        return [
            'date1' => 'Дата от',
            'date2' => 'Дата до',
            'status' => 'Статус заявки',
            'category' => 'Из категории',
            'msg' => 'Сообщение смс',
            'count' => 'Кол-во клиентов',
            'yes' => 'Да, точно отправить',
        ];
    }
    public function getCategoryList(){
		return Category::find()->select(['name', 'id'])->where(['shop_id'=>\Yii::$app->params['user.current_shop']])->indexBy('id')->column();
	}
	public function getStatusList(){
		return Orders::itemAlias('status');
	}
}