<?php
namespace app\components;

use yii\base\Component;
use yii\base\Exception;
use yii\caching\Cache;
use yii\db\Connection;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class LiveInform
 *
 * @author mindochin <mindochin@yandex.ru>
 */
class LiveInform extends Component
{
	public $api_id;		// Уникальный идентификатор API= Yii::$app->params('liveinform.api_id');
	public $type=2;		// Тип отслеживания. 2 или 1 . За 20 и 10 рублей соответственно.
	public $phone;		// Телефон клиента в формате (89001234567 или +79001234567)
	public $tracking;	// Трек-номер посылки (14-ти значный для "Почты России", либо 13 для EMS)
	public $order_id;	// Номер заказа внутри вашего магазина
	public $test=0;		// Имитирует добавление трека для тестирования ваших программ на правильность обработки ответов сервера. При этом сам трек не добавляется и баланс не расходуется.
	/**
	* 
	* 
	* @return
	*/
	
	public function  __construct()
	{
		$this->api_id = Yii::$app->params['liveinform.api_id'];
	}
	public function add() {		
		$url = "http://www.liveinform.ru/api/add/";
		$params = array(
			'api_id' => (string) $this->api_id,
			'type' => (int) $this->type,
			'phone' => (string) $this->phone,
			'tracking' => (string) $this->tracking,
			'order_id' => (int) $this->order_id,
			'test' => (bool) $this->test,
		);
		return $this->curl($url,$params);
	}
	
	private function curl( $url, $params = array() )
	{
		$ch = curl_init( $url );
		$options = array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_POSTFIELDS => $params
		);
		curl_setopt_array( $ch, $options );
		$result = curl_exec( $ch );
		curl_close( $ch );

		return $this->getResponse($result);
	}
	private function getResponse($return) {
		$r =  explode ("\n",$return);
		if($r['0'] == 100) {
			return array('success'=>'Отслеживание успешно добавлено. Код: '.$r['1']);
		}
		else {
			return array('error'=>'Отслеживание НЕ добавлено. Код ошибки: '.$r['0']);
		}		
	}
}