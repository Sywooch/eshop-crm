<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
namespace app\commands;
//ini_set("display_errors",1);
//error_reporting(2047);
use yii\console\Controller;
use app\components\Tools;
use app\models\Orders;
//use app\models\Tovar;
//use app\models\Client;
//use app\models\Shops;
use app\models\Senders;
//use app\models\TovarRashod;
/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SendmoskvaController extends Controller
{
	protected $client = 'IPDINIKINSAMATDINAROVICH';
	protected $key = '7AF64490';
	//public $current_shop=0;
	
/*	public function init()
    {
        parent::init();    
        

    }
*/
	public function actionSendnew()		
	{
		$shop_id = '2';
		$orders_list = Orders::find()->joinWith(['client','rashod'])->where(['status' => 8])->andWhere(['b2c_id' => null])->andWhere(['send_moskva' => 1])->andWhere(['orders.shop_id' => $shop_id])->all();
		//print_r($orders_list);
		if (count($orders_list) <1) exit;
		
		$ar_csv = [];
		$header = ['Номер накладной','Номер посылки','Номер клиента','Дата заказа','Индекс','Город','Адрес','ФИО','Телефон мобильный','Телефон дополнительный','e-mail','Вес посылки','Полная стоимость доставки','Стоимость доставки к оплате','Артикул','Товар','Кол-во ед. товара','Полная стоимость ед. товара','Стоимость ед. товара к оплате','Вес товара','Тип доставки','Доставка авиа','Хрупкий товар','Оценочная стоимость посылки','Код b2c','Условия доставки','Упаковка','Отправитель','Дата доставки','Интервал доставки','Комментарии','Частичный отказ'];
		$ar_csv[] = $header;
		
		$dir = '../upload/'; // Директория для создания		
		$file = 'export.csv';
 
		/*if (!file_exists($dir.$file)){ // Если файл не существует, то создаем		   
		   $fp = fopen($dir.$file, 'w'); // Создаем файл 
		}*/	
		$fp = fopen($dir.$file, 'w');
		$order = new Orders();
		foreach($orders_list as $l) {;
			$rashod = $l->rashod;
			$n=0;			
			foreach($rashod as $r) {
				$row = [];
				$row = array_fill(0, 32, '');
				if($n==0) {
					$row['1'] = $l->id;
					$row['3'] = $l->date_at;
					$row['4'] = $l->client->postcode;
					$row['6'] = $l->client->address;
					$row['7'] = $l->client->fio;
					$row['8'] = $l->client->phone;//9
					$row['13'] = 0;
					$row['20'] = 'пр1';
					$row['23'] = $l->tovarSumma;
					$row['26'] = 'омни';
					$row['30'] = $l->note;//Senders::find()->select('name')->where(['id' => $l->sender_id])->scalar();
				}				
				$row['14'] = $r->tovar->artikul;
				$row['15'] = $r->tovar->name;
				$row['16'] = $r->amount;
				$row['17'] = $r->price;
				$row['18'] = $r->price;
				//print_r($row);
				$ar_csv[] = $row;
				$n++;
			}			
		}
		foreach($ar_csv as $ar){
			fputcsv($fp, $ar, ';');
		}
		fclose($fp);
		
		$url = 'http://is.b2cpl.ru/portal/client_api.ashx?client='.$client.'&key='.$key.'&func=upload&file=http://erp.kupiturist.ru/upload/export.csv&report=0&stickers=1';
				
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL,$url);
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$ret = curl_exec ($ch);
		curl_close($ch);

		$return = json_decode($ret,true);
		//$return = iconv ("CP1251", "UTF-8", $return);
		
		$to      = 'isoteplo@mail.ru';
		$subject = 'Результат отправки заявок фулфилмент';
		$message = 'Результат: '.$return['file_codes'] ."<br>";//"\r\n";
		$message .= 'Cтикеры: '.$return['file_stickers'] ."<br>";//."\r\n";
		$message .= 'Принято заказов: '.$return['cnt_ok'] ."<br>";//."\r\n";
		$message .= 'НЕ принято заказов: '.$return['cnt_error'] ."<br>";//."\r\n";
		//$message .= print_r($return, true);
		//$headers = 'From: webmaster@example.com' . "\r\n" . 'Reply-To: webmaster@example.com' . "\r\n" . 'X-Mailer: PHP/';
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		
		//$from_user = "=?UTF-8?B?".base64_encode($from_user)."?=";
      //$subject = "=?UTF-8?B?".base64_encode($subject)."?=";
       // $message = "=?UTF-8?B?".base64_encode($message)."?=";
      //$headers = //"From: $from_user <$from_email>\r\n". "MIME-Version: 1.0" . "\r\n" . "Content-type: text/html; charset=UTF-8" . "\r\n";windows-1251

		@mail($to, $subject, $message, $headers);

	}
}
