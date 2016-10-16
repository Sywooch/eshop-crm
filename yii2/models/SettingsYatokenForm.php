<?php

namespace app\models;

use yii\base\Model;

class SettingsYatokenForm extends Model
{
    public $client_id;
    public $client_secret;

    public function rules()
    {
        return [
            [['client_id', 'client_secret'], 'required'],            
            [['client_id', 'client_secret'], 'trim'],
            [['client_id', 'client_secret'], 'string']
        ];
    }
    public function attributeLabels()
    {
        return [
            'client_id' => 'client_id',
            'client_secret' => 'client_secret',
        ];
    }
}