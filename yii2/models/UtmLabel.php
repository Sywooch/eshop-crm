<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "utm_label".
 *
 * @property string $id
 * @property string $order_id
 * @property string $utm_campaign
 * @property string $utm_content
 * @property string $utm_source
 * @property string $utm_medium
 * @property string $utm_term
 * @property string $source_type
 * @property string $source
 * @property string $group_id
 * @property string $banner_id
 * @property string $position
 * @property string $position_type
 * @property string $region_name
 */
class UtmLabel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utm_label';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id'], 'integer'],
            [['utm_content'], 'string'],
            [['utm_campaign', 'utm_source', 'utm_term', 'source_type', 'region_name'], 'string', 'max' => 250],
            [['utm_medium', 'source', 'group_id', 'banner_id', 'position', 'position_type'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'ID заявки',
            'utm_campaign' => 'UTM Кампания',
            'utm_content' => 'UTM Content',
            'utm_source' => 'UTM система',
            'utm_medium' => 'UTM средство',
            'utm_term' => 'UTM фраза',
            'source_type' => 'UTM тип',
            'source' => 'UTM площадка',
            'group_id' => 'UTM группа ID',
            'banner_id' => 'UTM объяв-е ID',
            'position' => 'UTM Позиция',
            'position_type' => 'UTM № места',
            'region_name' => 'UTM регион',
        ];
    }
	
	public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['id' => 'order_id'])->inverseOf('utmLabel');
    }
}
