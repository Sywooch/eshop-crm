<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "statmetrika".
 *
 * @property integer $id
 * @property string $date_at
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 * @property string $host
 * @property string $label
 * @property integer $visits
 * @property integer $page_views
 * @property integer $new_visitors
 * @property string $denial
 * @property string $depth
 * @property integer $visit_time
 */
class Statmetrika extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'statmetrika';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_at'], 'safe'],
            [['created_at', 'created_by', 'updated_at', 'updated_by', 'visits', 'page_views', 'new_visitors', 'visit_time'], 'integer'],
            [['label'], 'string'],
            [['denial', 'depth'], 'number'],
            [['host'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date_at' => 'Date At',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'host' => 'Host',
            'label' => 'Метки',
            'visits' => 'Визиты',
            'page_views' => 'Просмотры',
            'new_visitors' => 'Новые посетители',
            'denial' => 'Отказы',
            'depth' => 'Глубина просмотра.',
            'visit_time' => 'Среднее время в секундах, проведенное на сайте посетителями',
        ];
    }
    /**
    * получить инфу счетчиков с метрики
    * 
    * @url string $url
    * @return array
    */
    public static function _get_metrika($url=false) {		
        if(!$url)
                $url = 'https://api-metrika.yandex.ru/management/v1/counters?oauth_token='.Settings::getKey('ya_metrika_token').'&field=labels';

        $list=array();
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL,$url);
        curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
        curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        $metrika = curl_exec ($ch);
        curl_close($ch);

        $return = json_decode($metrika,true);			

        if($return->counters) {
                $list = $return->counters;
                if($return->links) {
                        $list = array_merge($list, _get_metrika($return->links->next));
                }
        }
        else $list = $return;
        //echo '<pre>';print_r($return);echo '</pre>';die;
        return ($list);
    }
}
