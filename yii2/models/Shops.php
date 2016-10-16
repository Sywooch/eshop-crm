<?php

namespace app\models;

use Yii;
use app\modules\user\models\User;

/**
 * This is the model class for table "shops".
 *
 * @property integer $id
 * @property string $name
 */
class Shops extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shops';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],            
            [['name'], 'string', 'max' => 200],
            ['token', 'default']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'token' => 'Токен',
        ];
    }
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('user_shops', ['shops_id' => 'id'])->inverseOf('shops');
    }
    
	public function beforeSave($insert)
	{
	    if (parent::beforeSave($insert)) {
	    	//generate shop token
	    	if(is_null($this->token) or empty($this->token)) {
				//$this->token = \app\components\Tools::dec2link(md5($this->id.$this->name));
				//$this->token = \app\components\Tools::dec2link(base_convert(md5($this->id), 16, 10));
				//$this->token = uniqid($this->id.$this->name);
				$exist = false;
				do {
					$this->token = \app\components\Tools::shortString();
					if(null !== self::find()->where(['token' => $this->token])->one())
						$exist = true;
				}
				while ($exist);
				
				//die($this->token);
			}	        	
	        return true;
	    } else {
	        return false;
	    }
	}
}
