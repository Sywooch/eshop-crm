<?php

namespace app\models;

use yii\base\Model;

class AdvertForm extends Model
{
    public $date1;
    public $date2;
    public $campaign;
    public $host;

    public function rules()
    {
        return [
            [['date1', 'date2'], 'required'],
            [['date1', 'date2'], 'date','format'=>'yyyy-M-d'],
            [['date1', 'date2'], 'default', 'value' => date('Y-m-d')], 
            [['campaign', 'host'], 'trim'],
            [['campaign'], 'string']
        ];
    }
    public function attributeLabels()
    {
        return [
            'date1' => 'Дата от',
            'date2' => 'Дата до',
            'campaign' => 'Кампания ID',
            'host' => 'Группировать по хостам',
        ];
    }
}