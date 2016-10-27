<?php

namespace app\models;

use yii\base\Model;

class StatmetrikaForm extends Model
{
    public $date1;    

    public function rules()
    {
        return [
            [['date1'], 'required'],
            [['date1'], 'date','format'=>'yyyy-M-d'],
            [['date1'], 'default', 'value' => date('Y-m-01')],            
        ];
    }
    public function attributeLabels()
    {
        return [
            'date1' => 'Дата от',            
        ];
    }
}