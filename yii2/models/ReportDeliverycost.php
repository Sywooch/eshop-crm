<?php

namespace app\models;

use yii\base\Model;

class ReportDeliverycost extends Model
{
    public $date1;
    public $date2;
    public $sender_id;
    public $category_id;

    public function rules()
    {
        return [            
            [['date1', 'date2'], 'required'],
            [['date1', 'date2'], 'date','format'=>'yyyy-M-d'],
            [['date1', 'date2'], 'default', 'value' => date('Y-m-d')], 
            [['sender_id', 'category_id'], 'safe']
        ];
    }
    public function attributeLabels()
    {
        return [
            'date1' => 'Дата от',
            'date2' => 'Дата до',
            'sender_id' => 'Cлужба доставки',
            'category_id' => 'Категория'
        ];
    }

}