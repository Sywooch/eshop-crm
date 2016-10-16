<?php

namespace app\models;

use yii\base\Model;

class UtilsDownloadSend extends Model
{
    public $sender;
    
    public function rules()
    {
        return [
            [['sender'], 'required'],            
        ];
    }
    public function attributeLabels()
    {
        return [
            'sender' => 'Служба доставки',
        ];
    }
}