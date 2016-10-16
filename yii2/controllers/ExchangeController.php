<?php

namespace app\controllers;

use yii\filters\AccessControl;
use app\components\Tools;
use app\models\Orders;
use app\models\Tovar;
use app\models\Client;
use app\models\Shops;
use app\models\UtmLabel;
use app\models\TovarRashod;
/**
* cвязь с внешними системами.
* проверка доступа и токены не требуются
*/
class ExchangeController extends \yii\web\Controller
{
	/*public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['import'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }*/
    
    public function actionExport()
    {
        return '!!!';
    }
    
    /**
	* после кода живосайта
	* <script type='text/javascript'>function jivo_onLoadCallback() {jivo_api.setUserToken("<?='host:'.$_SERVER['SERVER_NAME'].';url:'.$_SERVER['QUERY_STRING']?>"); console.log(jivo_api)}</script
	*
	*/
    public function actionFromjivo()
    {
		/*
		$fp = fopen('000.txt', 'w');
		fwrite($fp, file_get_contents('php://input'));
		fclose($fp);
		*/

		$get = file_get_contents('php://input');
		if($get !== false){
			$json = json_decode($get,true);	

			if(isset($json['event_name']) and $json['event_name'] == 'offline_message')
			{
				$ut = $return = $tovar = [];
				$return['url'] = '_jivosite_';
				$return['message'] = null;
				$return['source'] = 3;//источник - живосайт
				
				if(!empty($json['message'])) $return['message'] = $json['message']; else $return['message'] = null;
				if(!empty($json['visitor']['name'])) $return['name'] = $json['visitor']['name']; else $return['name'] = null;
				if(!empty($json['visitor']['email'])) $return['email'] = $json['visitor']['email']; else $return['email'] = null;
				if(!empty($json['visitor']['phone'])) $return['phone'] = $json['visitor']['phone'];	else $return['phone'] = null;
				
			
				if(isset($json['user_token']) and !empty($json['user_token'])) {
					$ut = explode("::", $json['user_token']);
					if(isset($ut['0']) and !empty($ut['0'])) $return['url'] = $ut['0'];
					if(isset($ut['1']) and !empty($ut['1'])) $return['utm'] = $ut['1'];			
					if(isset($ut['2']) and !empty($ut['2'])) $return['shop'] = $ut['2'];
					if(isset($ut['3']) and !empty($ut['3'])) $return['tovar']['0']['artikul'] = $ut['3'];
					if(isset($ut['4']) and !empty($ut['4'])) {$return['tovar']['0']['price'] = $ut['4']; $return['tovar']['0']['amount'] = '1';}
					if(isset($ut['2']) and !empty($ut['5'])) $return['ip_address'] = $ut['5'];
				}
				/*
				$logs = $order = $client = $utm = $utms = $tovar = [];
				$client_id = $order_id = $shop_id = false;
				$shop_id = Shops::find()->select('id')->where(['token' => $row['shop']])->scalar();
				$logs['shop'] = $shop_id;
				if(empty($row['shop']) and false === $shop_id) {return 'no shop'; die();}
						
				if(array_key_exists('shop',$return)){		
					include_once("./bd.php");
					if(!empty($return['tovar'])) $tovar = serialize($return['tovar']); 
					$sql = 'INSERT INTO `zayavka` (artikul, art1, summa1, kolvo1, fio, phone, email, utm, url, prich2, shop, tovar) VALUES (:art, :art, :price, :kolvo, :name, :phone, :email, :utm, :url, :prich2, :shop, :tovar)';
					$sth = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
					$sth->execute(array(':art' => $return['0']['tovar']['artikul'], ':price' => $return['0']['tovar']['price'],':kolvo' => $return['0']['tovar']['amount'], ':name' => $return['name'], ':phone' => $return['phone'], ':email' => $return['email'], ':utm' => $return['utm'], ':url' => $return['url'], ':prich2' => $return['message'], ':shop' => $return['shop'], ':tovar' => $tovar));
					
					$err = $sth->errorInfo();
					if($err[0] != 0) {		
						$fp = fopen('000Jivoerror.txt', 'w');
						print_r($sth->errorInfo(),true);
						fclose($fp);
					}
					
					//else $return='no data';
					$fp = fopen('000Jivoreturn-'.date('Y-m-d-H').'.txt', 'a');
					fwrite($fp, print_r($return,true));
					fclose($fp);
				}
				*/
				
				$this->savetodb($return);
				
				$fp = fopen('000Jivojson-'.date('Y-m-d-H').'.txt', 'a');
				fwrite($fp, print_r($json,true));
				fclose($fp);
			}	
			
		}
		return (json_encode(['return'=>'ok']));
	}
	
	/**
	* import orders from landing
	* 
	*
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;

		utm_campaign
		utm_content
		utm_source
		utm_medium
		utm_term
		source
		source_type
		group_id
		banner_id
		position
		position_type
		device
		region_name

		если надо несколько товаров - передать сюда массив 'tovar'
		tovar = array(
			['artikul'=>'hl900', 'price'=>'2090', 'amount'=>1],
		}


	* 
	* @return
	*/
    public function actionImport()
    {
        //$this->enableCsrfValidation = false;
        
        
        
		$fp = fopen('0dataz-'.date('Y-m-d').'.txt', 'a');
		fwrite($fp, print_r($_POST,true));
		fclose($fp);
		if (!isset($_POST['dataz'])) {return false; die();}
		$row = $_POST['dataz'];
		/*$row = [
            'artikul' => 'leapers12x30',
            'price' => '1790',
            'amount' => '1',
            'shop' => 'a9wZc2s',
            'name' => '',
            'phone' => '2222222',
            'utm' => '',
            'url' => 'shopinrf.ru',
            'ip_address' => '178.129.236.243',
            'source' => '1'
        ];*/
        return $this->savetodb($row);
	}
	
	public function savetodb($row)
	{
		$logs = $order = $client = $utm = $utms = $tovar = [];
		$client_id = $order_id = $shop_id = false;
		$shop_id = Shops::find()->select('id')->where(['token' => $row['shop']])->scalar();
		$logs['shop'] = $shop_id;
		if(empty($row['shop']) and false === $shop_id) {return 'no shop'; die();}
		
		///// client
		Tools::processData($row['email'],$client,'email');
		Tools::processData($row['name'],$client,'fio');				
		Tools::processData(Tools::format_phone($row['phone']),$client,'phone');
		if(!empty($client) and array_key_exists('phone', $client) and count($client['phone'] >6) and null === (Client::find()->where(['phone' => $client['phone'], 'shop_id' => $shop_id])->one())) {
			$client['shop_id'] = $shop_id;			
			$mdl = new Client;
			foreach($client as $k=>$v){
				$mdl->$k = $v;
			}
			
			if($mdl->save()) {
				$logs['client'] = "Client ".$mdl->id."/".$client['phone']." save.";
				$client_id = $mdl->id;
			}
			else $logs['client'] = "Client ".$client['phone']." NOT save. ".print_r($mdl->firstErrors, TRUE);
		}
		elseif(!empty($client) and array_key_exists('fio', $client) and $client_id ===false and is_numeric(Tools::format_phone($row['name'])) and count(Tools::format_phone($row['name']) >6) and null === (Client::find()->where(['phone' => Tools::format_phone($row['name']), 'shop_id' => $shop_id])->one())) {
			$client['shop_id'] = $shop_id;
			$client['phone'] = Tools::format_phone($row['name']);
			$mdl = new Client;
			foreach($client as $k=>$v){
				$mdl->$k = $v;
			}
			
			if($mdl->save()) {
				$logs['client'] = "Client1 ".$mdl->id."/".$client['phone']." save.";
				$client_id = $mdl->id;
			}
			else $logs['client'] = "Client1 ".$client['phone']." NOT save.";
		}
		else {
			$logs['client'] = "Client is ready, empty or < 6.";
			$client_id = Client::find()->select('id')->where(['phone' => $client['phone'], 'shop_id' => $shop_id])->scalar();
		}
		
		$logs['client_id'] = $client_id;
					
		/////Order
		if($client_id !==false and $client_id >0) {
			Tools::processData($client_id,$order,'client_id');			
			//Tools::processData($row->date0,$order,'date_at');
			Tools::processData($row['url'],$order,'url');
			Tools::processData($row['ip_address'],$order,'ip_address');
			Tools::processData($shop_id,$order,'shop_id');
			Tools::processData($row['source'],$order,'source');
			//Tools::processData($row->prich2,$order,'note');
			//if(count($order) >0 and null === (Orders::find()->where(['old_id' => $row->id, 'shop_id' => $shop_id])->one())) {
				if(!array_key_exists('source', $order)) $order['source'] = 1;//источник - форма на сайте
				$order['status'] = '1';//cтатус - новый
				$mdl = new Orders;
				foreach($order as $k=>$v){
					$mdl->$k = $v;
				}
				if($mdl->save()) {
					$order_id = $mdl->id;
					$logs['order'] = "Order ".$mdl->id." save.";
				}
				else $logs['order'] = 'Order NOT save.';
			//}						
		}
		
		/////utm							
		if($order_id !==false and $order_id >0) {
			if(!is_null($row['utm']) or !empty($row['utm'])) {
				parse_str($row['utm'], $utms);				
				Tools::processData($utms['utm_source'],$utm,'utm_source');					
				Tools::processData($utms['utm_medium'],$utm,'utm_medium');
				Tools::processData($utms['utm_campaign'],$utm,'utm_campaign');					
				Tools::processData($utms['utm_term'],$utm,'utm_term');
				Tools::processData($utms['utm_content'],$utm,'utm_content');
				Tools::processData($utms['source_type'],$utm,'source_type');
				//Tools::processData($utms['type'],$utm,'source_type');
				Tools::processData($utms['source'],$utm,'source');
				Tools::processData($utms['position_type'],$utm,'position_type');
				Tools::processData($utms['block'],$utm,'position_type');
				Tools::processData($utms['position'],$utm,'position');
				Tools::processData($utms['region_name'],$utm,'region_name');
				Tools::processData($utms['group_id'],$utm,'group_id');
				Tools::processData($utms['banner_id'],$utm,'banner_id');
				Tools::processData($utms['ad_id'],$utm,'banner_id');
				Tools::processData($utms['device'],$utm,'device');
				Tools::processData($utms['device_type'],$utm,'device');
			}		
			if(array_key_exists('utm_source',$utm)) {
				if((stripos($utm['utm_source'], 'yandex')!==false) or (stripos($utm['utm_source'], 'direct')!==false))
				$utm['utm_source'] = 'yandex';
			}
			
			if(array_key_exists('utm_campaign',$utm)) {
				$utm['utm_campaign'] = preg_replace("/[^0-9]/", "", $utm['utm_campaign']);
			}
			
			if(array_key_exists('utm_content',$utm) and is_numeric($utm['utm_content'] and !array_key_exists('banner_id', $utm))) {
				$utm['banner_id'] = $utm['utm_content'];
			}
			
			if(count($utm) >0 and null === (UtmLabel::find()->where(['order_id' => $order_id])->one())) {				
				$utm['order_id'] = $order_id;
				$mdl = new UtmLabel;
				foreach($utm as $k=>$v){
					$mdl->$k = $v;
				}
				if($mdl->save()) $logs['utm'] = "Utm save.";
				else $logs['utm'] = 'Utm NOT save.';
			}
			else
				$logs['utm'] = 'Utm is empty';
		}
		
		/////tovar rashod
		if($order_id !==false and $order_id >0) :			
		
		if(!is_null($row['tovar']) and !empty($row['tovar'])) {
			
			$tovar_list = unserialize($row['tovar']);
			foreach($tovar_list as $tovar){
				$art = strtoupper($tovar['artikul']);				
				//if(array_key_exists($art, $arprice))
				$mtovar = \app\models\Tovar::find()->where(['shop_id' => $shop_id, 'artikul'=>$art])->one();
				
				if(!is_null($mtovar))
				{					
					if(null === (\app\models\TovarRashod::find()->where(['order_id' => $order_id,'tovar_id'=>$mtovar->id])->one())) {
						
						$mdl = new \app\models\TovarRashod;
						$mdl->order_id = $order_id;
						$mdl->tovar_id = $mtovar->id;
						$mdl->shop_id = $shop_id;
						$mdl->price = $tovar['price'];
						$mdl->amount = $tovar['amount'];
						//$mdl->sklad_id = $order['sklad'];
						if($mdl->save()) $logs['rashod'][$art] = '#'.$order_id." Rashod ".$mtovar->name.' save.';
						else $logs[$row->id]['rashod'][$art] = '#'.$order_id." Rashod ".$mtovar->name.' NOT save. '.print_r($mdl->firstErrors, TRUE);
					}
					else $logs['rashod'][$art] = 'Tovar '.$art.' in db';
				}
				else $logs['rashod'][$art] = 'Tovar '.$art.' NOT in price';
			}
			
		}//if count
		elseif(!is_null($row['artikul']) and !empty($row['artikul'])) {
			$art = strtoupper($row['artikul']);	
			$mtovar = \app\models\Tovar::find()->where(['shop_id' => $shop_id, 'artikul'=>$art])->one();
			if(!is_null($mtovar))
			{
				if(null === (\app\models\TovarRashod::find()->where(['order_id' => $order_id,'tovar_id'=>$mtovar->id])->one())) {		
					
					$mdl = new \app\models\TovarRashod;
					$mdl->order_id = $order_id;
					$mdl->tovar_id = $mtovar->id;
					$mdl->shop_id = $shop_id;
					$mdl->price = $row['price'];
					$mdl->amount = $row['amount'];
					//$mdl->sklad_id = $order['sklad'];
					if($mdl->save()) $logs['rashod'][$art] = 'Rashod '.$mtovar->id.' save.';
					else {
						$logs['rashod'][$art] = "Rashod ".$mtovar->id.' NOT save. '.print_r($mdl->firstErrors, TRUE);						
					}
				}
				else $logs['rashod'][$art] = 'Tovar '.$art.' in db';
			}
			else $logs['rashod'][$art] = 'Tovar '.$art.' NOT in price';
		}//elseif count
		else $logs['rashod'] = 'Tovar NOT found';
		
		endif;
			
		
		if(!empty($logs))
		$fp = fopen('0logs-'.date('Y-m-d').'.txt', 'w'); fwrite($fp, print_r($logs,true)); fclose($fp);
		
		
        return 'ok:'.$order_id;//$this->render('import');
    }
	public function beforeAction($action) {
		$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}

}
