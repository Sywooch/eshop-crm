<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property integer $id
 * @property string $name
 * @property string $value
 * @property string $desc
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['value'], 'string'],
            [['shop_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['desc'], 'string', 'max' => 254],
            [['desc'], 'default', 'value'=>null]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'value' => 'Value',
            'desc' => 'Desc',
            'shop_id' => 'Магазин'
        ];
    }
    
    static function getKey($name) {
		$row = self::findOne(['name' => $name, 'shop_id' => Yii::$app->params['user.current_shop']]);
		//\yii\helpers\VarDumper::dump(unserialize($row->value),20,true);die;
		if($row->value == serialize(false) || @unserialize($row->value) !== false)
			return unserialize($row->value);
		else 
			return $row->value;
	}
	
	static function setKey($name, $value) {
		$row = self::findOne(['name' => $name, 'shop_id' => Yii::$app->params['user.current_shop']]);
		if(null == $row) {
			$row = new Settings();//\yii\helpers\VarDumper::dump($row,20,true);die;
			$row->name = $name;
		}		
		$row->value = serialize($value);
		$row->shop_id = Yii::$app->params['user.current_shop'];
		if(!$row->save()) return $row->errors;
		else return true;
	}
}
