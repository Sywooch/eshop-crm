<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property integer $shop_id
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }
    
    public function behaviors()
	{
	    return [
	        TimestampBehavior::className(),
	        BlameableBehavior::className(),
			[
				'class' => SluggableBehavior::className(),
				'attribute' => 'name',
				'slugAttribute' => 'slug',
			],
	    ];
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['shop_id', 'parent_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['name', 'slug'], 'string', 'max' => 255],            
            ['slug', 'default']
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
            'slug' => 'Slug',
            'parent_id' => 'Parent',
            'shop_id' => 'Shop ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id'])->from(Category::tableName() . ' AS parent');;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTovar()
    {
        return $this->hasMany(Tovar::className(), ['category_id' => 'id']);
    }
    
    public function beforeSave($insert)
	{
	    if (parent::beforeSave($insert)) {
	    	if(is_null($this->shop_id) or empty($this->shop_id))
	        	$this->shop_id = Yii::$app->params['user.current_shop'];
	        return true;
	    } else {
	        return false;
	    }
	}
}
