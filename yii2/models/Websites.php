<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "websites".
 *
 * @property integer $id
 * @property string $host
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $active
 * @property integer $category_id
 * @property integer $tovar_id
 * @property integer $shop_id
 */
class Websites extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'websites';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['host'], 'required'],
            [['created_at', 'updated_at', 'created_by', 'updated_by', 'active', 'category_id', 'tovar_id', 'shop_id'], 'integer'],
            [['host'], 'string', 'max' => 250],
            [['tovar_id','category_id'], 'default', 'value' => null],
            [['created_at', 'updated_at', 'created_by', 'updated_by', 'shop_id'],'save'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'host' => 'Host',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'active' => 'Active',
            'category_id' => 'Category ID',
            'tovar_id' => 'Tovar ID',
            'shop_id' => 'Shop ID',
        ];
    }
    
	public function itemAlias($type, $item=false) {
    	$_yesno = ['0' => 'Нет', '1' => 'Да'];
		$_items = array(			
			'active' => $_yesno,
		);
		if ($item === false)
			return isset($_items[$type]) ? $_items[$type] : false;
		else
			return isset($_items[$type][$item]) ? $_items[$type][$item] : false;
	}
	
	public function beforeSave($insert)
	{
	    if (parent::beforeSave($insert)) {
	        $this->shop_id = Yii::$app->params['user.current_shop'];
	        return true;
	    } else {
	        return false;
	    }
	}
}
