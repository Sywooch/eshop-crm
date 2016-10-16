<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "price".
 *
 * @property integer $id
 * @property integer $tovar_id
 * @property string $artikul
 * @property string $name
 * @property double $price
 * @property string $created_at
 * @property string $updated_at
 */
class Price extends \yii\db\ActiveRecord
{
    public $nameart;//название + артикул
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'price';
    }
	
	public function scenarios()
    {
        return [
            'create' => ['artikul', 'name', 'price', 'tovar_id'],
            'update' => ['artikul', 'name', 'price', 'tovar_id'],
        ];
    }

    public function fields()
	{
	    $fields = $this->fields();
	    $fields['nameart'] = function () {
            return $this->name . ' [' . $this->artikul. ']';
        };
        return $fields;
//	    print_r($fields);
	    /*return [
	        // здесь имя поля совпадает с именем атрибута
	        'id',

	        // здесь имя поля - "email", соответствующее ему имя атрибута - "email_address"
	        'email' => 'email_address',

	        // здесь имя поля - "name", а значение определяется обратным вызовом PHP
	        'name' => function () {
	            return $this->first_name . ' ' . $this->last_name;
	        },
	    ];*/
	}
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tovar_id', 'price'], 'required'],
            [['artikul', 'name'], 'required', 'on'=>'update'],
            [['tovar_id'], 'integer'],
            [['price'], 'number'],
            [['name', 'artikul'], 'unique'],
            [['artikul'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 250]
        ];
    }
    

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        if ($this->scenario == 'create')
        	return [
	            'id' => 'ID',
	            'tovar_id' => 'Базовый товар',
	            'artikul' => 'Дополнение к артикулу базового товара',
	            'name' => 'Дополнение к названию базового товара',
	            'price' => 'Цена за 1 единицу',
	            'created_at' => 'Создан',
	            'updated_at' => 'Изменен',
	        ];
        else
	        return [
	            'id' => 'ID',
	            'tovar_id' => 'Базовый товар',
	            'artikul' => 'Артикул в прайсе',
	            'name' => 'Название в прайсе',
	            'price' => 'Цена за 1 единицу',
	            'created_at' => 'Создан',
	            'updated_at' => 'Изменен',
	        ];
    }
    public function getTovar()
    {
        return $this->hasOne(Tovar::className(), ['id' => 'tovar_id']);
    }
    public function getRashod()
    {
        return $this->hasMany(TovarRashod::className(), ['price_id' => 'id']);
    }

}
