<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UtilsUploadRpo extends Model
{
    public $sender;
    public $statFile;
    
    public function rules()
    {
        return [
            [['sender', 'statFile'], 'required'],
            ['sender', 'integer'],
            [['statFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xls, xlsx','checkExtensionByMimeType'=>false],           
        ];
    }
    public function attributeLabels()
    {
        return [
            'sender' => 'Служба доставки',
            'statFile' => 'Файл со статистикой',
        ];
    }
    
    public function upload()
    {
        if ($this->validate()) {
            $this->statFile->saveAs('uploads/' . $this->statFile->baseName . '.' . $this->statFile->extension);
            return true;
        } else {
            return false;
        }
    }
}