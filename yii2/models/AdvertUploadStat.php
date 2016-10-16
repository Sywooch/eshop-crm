<?
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class AdvertUploadStat extends Model
{
    /**
     * @var UploadedFile
     */
    public $statFile;
    public $source;

    public function rules()
    {
        return [
            [['statFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xls, xlsx','checkExtensionByMimeType'=>false],
            ['source','required'],
            ['source','string'],
        ];
    }
    
    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'statFile' => 'Файл со статистикой',
            'source' => 'Источник',
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