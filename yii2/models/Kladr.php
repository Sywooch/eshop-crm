<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "kladr".
 *
 * @property string $name
 * @property string $socr
 * @property string $code
 * @property string $index
 * @property string $gninmb
 * @property string $uno
 * @property string $ocatd
 * @property string $status
 */
class Kladr extends \yii\db\ActiveRecord
{
    public $kname;
    public $pname;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kladr';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['name'], 'string', 'max' => 40],
            [['socr'], 'string', 'max' => 10],
            [['code'], 'string', 'max' => 13],
            [['index'], 'string', 'max' => 6],
            [['gninmb', 'uno'], 'string', 'max' => 4],
            [['ocatd'], 'string', 'max' => 11],
            [['status'], 'string', 'max' => 1],
            ['level', 'integer', 'max'=> 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'socr' => 'Socr',
            'code' => 'Code',
            'index' => 'Index',
            'gninmb' => 'Gninmb',
            'uno' => 'Uno',
            'ocatd' => 'Ocatd',
            'status' => 'Status',
            'level' => 'Level'
        ];
    }
    /*
    public function getClientRegion()
	{
	    return $this->hasOne(Client::className(), ['region' => 'code']);//->select(['code',"concat_ws(' ',substr(`code`, 0, 2), `name`) as kname"])->where();
	}*/
}
