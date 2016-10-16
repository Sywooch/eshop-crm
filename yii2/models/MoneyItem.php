<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "money_item".
 *
 * @property integer $id
 * @property string $name
 * @property string $note
 */
class MoneyItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'money_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['note'], 'string'],
            [['name'], 'string', 'max' => 250]
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
            'note' => 'Примечание',
        ];
    }
}
