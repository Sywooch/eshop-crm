<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "sms".
 *
 * @property integer $id
 * @property string $sms_id
 * @property integer $order_id
 * @property string $event
 * @property integer $status
 * @property string $cost
 * @property string $msg
 * @property string $note
 */
class Sms extends \yii\db\ActiveRecord
{
	public $eventlist;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms';
    }
    
    public function behaviors()
	{
	    return [
	        TimestampBehavior::className(),
	        BlameableBehavior::className(),
	    ];
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'required'],
            [['order_id', 'status'], 'integer'],
            [['cost'], 'number'],
            [['msg', 'note', 'phone'], 'string'],
            [['sms_id', 'event'], 'string', 'max' => 20],
            [['sms_id', 'status', 'event', 'cost', 'msg', 'note', 'order_id'], 'default', 'value' => null],
            //['eventlist', 'each', 'rule' => ['string']],
            [['eventlist', 'created_at', 'created_by'], 'save'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sms_id' => 'Sms ID',
            'order_id' => 'Заказ',
            'event' => 'Cобытие',
            'status' => 'Код ответа',
            'cost' => 'Стоимость',
            'msg' => 'Сообщение',
            'note' => 'Примечание',
            'eventlist' =>'Выбрать события для отправки смс',
            'created_at' => 'Cоздан',
            'created_by' => 'Кем создан',
            'phone' => 'Получатель',
        ];
    }
    
    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['id' => 'order_id']);//->where('{{order}}.shop_id = :shop_id', [':shop_id' => Yii::$app->params['user.current_shop']]);//->inverseOf('sms');;
    }
    
    /**
	* gри изменении проверить в модели заявок afterSave()
	* @param undefined $type
	* @param undefined $item
	* 
	* @return array
	*/
    public function itemAlias($type, $item=false) {
		$_items = array(
			'event' => array(
				'raw' => 'С пылу с жару',
				'accept' => 'Принят',				
				'shipped' => 'Отгружен',
				'delivered' => 'Доставлен',
				'delivered3day' => 'Повтор доставки',
				'posrtrack' => 'Трекинг',
				'mailing' => 'Рассылка',
			),
		);
		
		if ($item === false) 
			return isset($_items[$type]) ? $_items[$type] : null;
		else
			return isset($_items[$type][$item]) ? $_items[$type][$item] : null;
	}
	
	public function sendSms($model, $event) { 
		$return = false;
		$config = \app\models\Settings::find()->where(['shop_id'=>$model->shop_id])->andWhere(['name'=>'sms.send.event'])->one();
    	$allow = unserialize($config->value);
    	if(!in_array($event,$allow))
    		return $false;//'Cобытие не разрешено';
		
		$msg = '';
		$id = $model->id;
		$summa = $model->tovarsumma;
    	if($summa >0)
    		$summa = 'на сумму '.$summa.'руб ';
    	else
    		$summa = '';    	
    	
    	//\yii\helpers\VarDumper::dump($allow,10,true);die;
    	if(!empty($model->identif)) $code = '. Трек номер '.$model->identif;
    	else $code = '';
    	
    	$tel = '. Техподдержка 8(987)2582225'; 
    		
    	if($event == 'raw') 
    		$msg = "Ваш товар к заказу №{$id} {$summa}зарезервирован{$tel}";
    	elseif ($event == 'accept')     		
			$msg = "Ваш заказ №{$id} {$summa}передан в службу доставки{$tel}";
		elseif ($event == 'shipped')
			$msg = "Ваш заказ №{$id} {$summa}отправлен{$code}{$tel}. Пожалуйста, обратите внимание: в случае невыкупа заказа с Вас будут взысканы расходы на доставку и судебные издержки на основании п.4 ст. 497 ГК РФ";
		elseif ($event == 'delivered' or $event == 'delivered3day')			
			$msg = "Ваш заказ №{$id} {$summa}доставлен{$code}{$tel}. В случае невыкупа заказа с Вас в судебном порядке будут взысканы расходы на доставку и судебные издержки на основании п.4 ст. 497 ГК РФ";
		else return $return;    	
    	
    	$to = $model->client->phone;
	
		$response = Yii::$app->sms->sms_send($to, $msg);		
		//\yii\helpers\VarDumper::dump($response,10,true);die;
		$this->status = $response['code'];
		$cost = Yii::$app->sms->sms_cost($to, $msg);
		$this->cost = $cost['price'];
		//\yii\helpers\VarDumper::dump($response,10,true);die;				
		$this->sms_id = $response['ids']['0'];			
		$this->order_id = $id;
		$this->msg = $msg;
		$this->event = $event;
		$this->phone = $to;
		//$this->msg = $msg;
		
		if ($this->save()) {
			$return = 'СМС на событие "'.$this->itemAlias('event',$event).'" отправлена с кодом '.$this->status.'. ';
		}
		/*else {
			$return = $this->firstErrors;
		}*/
		return $return;

	}
/*	
	public function getEventlist() {
		$r = Settings::getKey('sms.send.event');
		\yii\helpers\VarDumper::dump($r,20,true);die;
		return Settings::getKey('sms.send.event');
	}
*/
/*	
	public function setEventlist($ar) {\yii\helpers\VarDumper::dump($this,5,true);die;
		$this->eventlist = (array) $ar;
		return Settings::setKey('sms.send.event', $this->eventlist);
	}*/
	
	public function beforeSave($insert)
	{
	    if (parent::beforeSave($insert)) {
	        if((!isset($this->shop_id) or is_null($this->shop_id) or empty($this->shop_id)) and (Yii::$app->params['user.current_shop'] >0)) $this->shop_id = Yii::$app->params['user.current_shop'];
	        return true;
	    } else {
	        return false; 
	    }
	}
}
