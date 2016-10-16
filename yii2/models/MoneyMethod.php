<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "money_method".
 *
 * @property integer $id
 * @property string $name
 * @property string $note
 */
class MoneyMethod extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'money_method';
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
