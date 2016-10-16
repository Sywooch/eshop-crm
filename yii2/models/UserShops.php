<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_shops".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $shop_id
 */
class UserShops extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_shops';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'shop_id'], 'required'],
            [['user_id', 'shop_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'shop_id' => 'Shop ID',
        ];
    }
}
