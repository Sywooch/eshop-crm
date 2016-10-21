<?php

namespace app\models;

use yii\base\Model;

class ReportHostsDate extends Model
{
    public $date1;
    public $date2;
    public $rowTotal;
    public $host;
    public $cat_id;

    public function rules()
    {
        return [
            [['date1', 'date2'], 'required'],
            [['date1', 'date2'], 'date','format'=>'yyyy-M-d'],
            [['date1', 'date2'], 'default', 'value' => date('Y-m-d')], 
            ['rowTotal', 'boolean'],        
            ['host', 'trim'],
            ['host', 'string'],
            ['cat_id', 'integer']
        ];
    }
    public function attributeLabels()
    {
        return [
            'date1' => 'Дата от',
            'date2' => 'Дата до',
            'rowTotal' => 'Общий итог',
            'host' => 'Сайт',
            'cat_id' => 'Категория'
        ];
    }
}