<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "statcompany".
 *
 * @property integer $id
 * @property string $name
 * @property string $date_at
 * @property integer $shows
 * @property integer $clicks
 * @property string $costs
 * @property integer $id_company
 * @property integer $category_id
 * @property string $goods_art
 * @property integer $tovar_id
 * @property integer $site_id
 * @property string $host
 * @property integer $shop_id
 * @property string $source
 */
class Statcompany extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'statcompany';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'date_at', 'shows', 'clicks', 'costs', 'id_company'], 'required'],
            [['date_at','shop_id'], 'safe'],
            [['shows', 'clicks', 'id_company', 'category_id', 'tovar_id', 'site_id', 'shop_id'], 'integer'],
            [['costs'], 'number'],
            [['name'], 'string', 'max' => 250],
            [['goods_art', 'host', 'source'], 'string', 'max' => 100]
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
            'date_at' => 'Date At',
            'shows' => 'Shows',
            'clicks' => 'Clicks',
            'costs' => 'Costs',
            'id_company' => 'Id Company',
            'category_id' => 'Category ID',
            'goods_art' => 'Goods Art',
            'tovar_id' => 'Tovar ID',
            'site_id' => 'Site ID',
            'host' => 'Host',
            'shop_id' => 'Shop ID',
            'source' => 'Source',
        ];
    }
    
	public function getTovar()
    {
        return $this->hasOne(Tovar::className(), ['id' => 'tovar_id']);
    }
    
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function getUtm()
    {
        return $this->hasMany(UtmLabel::className(), ['utm_campaign' => 'id_company']);
    }
    
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['id' => 'order_id'])
            ->via('utm');
    }
    
    public function itemAlias($type, $item=false) {    	
		$_items = array(			
			'source' => [
				'yandex'=>'Яндекс',
				'google'=>'Гугл',
				'vk'=>'Вконтакте',
			],
		);
		if ($item === false)
			return isset($_items[$type]) ? $_items[$type] : false;
		else
			return isset($_items[$type][$item]) ? $_items[$type][$item] : false;
	}
}
