<?php

namespace app\models;

use yii\base\Model;

class StatmetrikaForm extends Model
{
    public $date1;   
    public $date2;

    public function rules()
    {
        return [
            [['date1', 'date2'], 'required'],
            [['date1', 'date2'], 'date','format'=>'yyyy-M-d'],
            [['date1'], 'default', 'value' => date('Y-m-01')],            
            [['date1'], 'default', 'value' => date('Y-m-d')],            
        ];
    }
    public function attributeLabels()
    {
        return [
            'date1' => 'Дата от',            
            'date2' => 'Дата по',            
        ];
    }
}