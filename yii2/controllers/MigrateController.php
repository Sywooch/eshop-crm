<?php
namespace app\controllers;

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);// | E_WARNING);
//ini_set('memory_limit', '2048M');
//ini_set('max_execution_time', 600);
//error_reporting(E_ALL);

use Yii;
use app\models\Orders;
use app\models\Client;
use app\models\OrderSearch;
use app\models\TovarBalance;
use app\models\TovarSearch;
use app\models\Sms;
//use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\web\Response;

/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class MigrateController extends \app\components\BaseController
{
    public function behaviors()
    {
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
			/*'bootstrap' => [
				'class' => ContentNegotiator::className(),
		    	'only' => ['clientAjax'],
		    	'formats' => [ 'application/json' => Response::FORMAT_JSON ],
		        'languages' => ['ru'],
			],*/
		];
        
    }

    /**
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Orders::find()->where('id = :id and shop_id = :shop_id')->addParams([':id'=>$id, 'shop_id'=>Yii::$app->params['user.current_shop']])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
 
	public function actionMigrate($shop_id=false) {
		if($shop_id ===false) $shop_id = Yii::$app->params['user.current_shop'];
		if(intval($shop_id) <1) die('shop_id=0');		
		
		$arUser = [
			'1'=>'9',
			'12'=>'9',
			'1122'=>'9',
			'2233'=>'9',
			'11' => '15',
			'8' => '15',
			'444' => '7',
			'4441' => '7',
			'7' => '19',
			'3344' => '10',
			'5' => '10',
			'222' => '10',
			'233' => '10',
			'4433' => '12',
			'101' => '14',
			'1011' => '14',
			'9' => '14',
			'1133' => '16',
			'1144' => '16',
			'551' => '17',
			'552' => '17',
			'10' => '18',
			'991' => '21',
			'992' => '21',
			'993' => '21'
		];
		$max_old_id = 47167;//intval(Orders::find()->andWhere(['shop_id'=>$shop_id])->max('old_id2'));
		//\yii\helpers\VarDumper::dump($max_old_id,10,true);
		$url='http://94.41.61.180/migrateAxGet.php?id='.$max_old_id;
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL,$url);
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt ($ch, CURLOPT_TIMEOUT, 600);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$rows = curl_exec ($ch);
		curl_close($ch);

		$rows = json_decode($rows);
		if($rows == 'no data' or count($rows) <1) die('no data');
	
		
		/**
		* 
		* @var 
		* 
		*/
				
	//\yii\helpers\VarDumper::dump($arprice,10,true);die;	
		foreach($rows as $row){
			$order = $client = $utm = $tovar = $sms = [];
			$client_id = $order_id = false;
			//\yii\helpers\VarDumper::dump($row,10,true);								
			
			//client
			\app\components\Tools::processData(\app\components\Tools::joinString([$row->region,$row->area,$row->city,$row->adress]),$client,'address');
			\app\components\Tools::processData($row->indexx,$client,'postcode');
			\app\components\Tools::processData($row->email,$client,'email');
			\app\components\Tools::processData($row->fio,$client,'fio');
			\app\components\Tools::processData(\app\components\Tools::format_phone($row->phone),$client,'phone');
			if(count($client) >0 and array_key_exists('phone', $client) and null === (\app\models\Client::find()->where(['phone' => $client['phone']])->one())) {				
				$client['shop_id'] = $shop_id;
				$mdl = new \app\models\Client;
				foreach($client as $k=>$v){
					$mdl->$k = $v;
				}/*
				$mdlc->address = $client['address'];
				$mdlc->postcode = $client['postcode'];
				$mdlc->email = $client['email'];
				$mdlc->fio = $client['fio'];
				$mdlc->phone = $client['phone'];*/
				if($mdl->save()) {
					echo "Client ".$client['phone']." save.";
					//$client_id = $mdl->id;
				}
				else echo "Client ".$client['phone']." NOT save.";
			}
			$client_id = \app\models\Client::find()->where(['phone' => $client['phone']])->one();
			if(null!==$client_id) $client_id = $client_id->id;
			else $client_id = false;
					
			
			//order
			if($client_id !==false and $client_id >0) {
			$order['client_id'] = $client_id;
			$order['old_id2'] = $row->id;
			$order['date_at'] = ($row->date0);
			$order['shop_id'] = $shop_id;
			\app\components\Tools::processData($row->old_id,$order,'old_id');
			if($row->obrabotka >0) {
				\app\components\Tools::processData('4',$order,'status');
			}
			elseif($row->ddouble >0) {
				\app\components\Tools::processData('2',$order,'status');
				//\app\components\Tools::processData($row->datadouble,$order,'data_duble');
				//if(array_key_exists('data_duble',$order)) $order['data_duble'] = strtotime($order['data_duble']);
			}
			elseif($row->zakaz =='1') {
				\app\components\Tools::processData('6',$order,'status');				
			}
			elseif($row->otkaz >0) {
				\app\components\Tools::processData('7',$order,'status');				
			}
			elseif($row->zakaz == '3') {
				\app\components\Tools::processData('4',$order,'status');				
			}
			elseif($row->zakaz == '2') {
				\app\components\Tools::processData('7',$order,'status');				
			}
			elseif($row->zakaz == '4') {
				\app\components\Tools::processData('5',$order,'status');				
			}
			elseif($row->zakaz == '5') {
				\app\components\Tools::processData('3',$order,'status');				
			}
			//\app\components\Tools::processData($row->prich,$order,'prich_double');
			\app\components\Tools::processData($row->dater,$order,'updated_at');
			if(array_key_exists('updated_at',$order)) $order['updated_at'] = ($order['updated_at']);//strtotime
			\app\components\Tools::processData($row->otprav,$order,'otpravlen');
			\app\components\Tools::processData($row->dataotp,$order,'data_otprav');
			if(array_key_exists('data_otprav',$order)) $order['data_otprav'] = ($order['data_otprav']);
			\app\components\Tools::processData($row->summaotp,$order,'summaotp');
			\app\components\Tools::processData($row->dostavlen,$order,'dostavlen');
			\app\components\Tools::processData($row->datados,$order,'data_dostav');
			if(array_key_exists('data_dostav',$order)) $order['data_dostav'] = ($order['data_dostav']);
			\app\components\Tools::processData($row->identif,$order,'identif');
			\app\components\Tools::processData($row->oplachen,$order,'oplachen');
			\app\components\Tools::processData($row->dataopl,$order,'data_oplata');
			if(array_key_exists('data_oplata',$order)) $order['data_oplata'] = ($order['data_oplata']);
			\app\components\Tools::processData($row->dostavza,$order,'dostavza');
			\app\components\Tools::processData($row->vkasse,$order,'vkasse');
			\app\components\Tools::processData($row->datavkas,$order,'data_vkasse');
			if(array_key_exists('data_vkasse',$order)) $order['data_vkasse'] = ($order['data_vkasse']);
			\app\components\Tools::processData($row->ret,$order,'vozvrat');
			\app\components\Tools::processData($row->date_return,$order,'data_vozvrat');
			if(array_key_exists('data_vozvrat',$order)) $order['data_vozvrat'] = ($order['data_vozvrat']);
			\app\components\Tools::processData($row->return_cost,$order,'vozvrat_cost');
			\app\components\Tools::processData($row->prich,$order,'note');
			\app\components\Tools::processData($row->discount,$order,'discount');
			\app\components\Tools::processData($row->fast,$order,'fast');
			\app\components\Tools::processData($row->ip_address,$order,'ip_address');
			if($row->keyword == 'Звонок') $order['source'] = '2';
			else $order['source'] = '1';
			if($row->sender == 'cdek') $order['sender_id'] = '3';
			else $order['sender_id'] = '2';//почта
			\app\components\Tools::processData($row->user,$order,'manager_id');
			if(array_key_exists('manager_id',$order)) $order['manager_id'] = $arUser[$order['manager_id']];
			\app\components\Tools::processData($row->packer_id,$order,'packer_id');
			if(array_key_exists('packer_id',$order)) $order['packer_id'] = $arUser[$order['packer_id']];
			\app\components\Tools::processData($row->url,$order,'url');
			if($row->sklad =='msk') $order['sklad'] = '4';
			else $order['sklad'] = '3';
			\app\components\Tools::processData($row->type_oplata,$order,'type_oplata');
			if(count($order) >0 and null === (\app\models\Orders::find()->where(['old_id2' => $order['old_id2']])->one())) {				
				$mdl = new \app\models\Orders;
				foreach($order as $k=>$v){
					$mdl->$k = $v;
				}
				if($mdl->save()) {
					echo "Order ".$mdl->old_id2.'/'.$mdl->id." save.";
					//$order_id = $mdl->id;
				}
				else 'Order not save.';
			}
			}//if client
			
			$order_id = \app\models\Orders::find()->where(['old_id2' => $row->id])->one();
			if(null!==$order_id) $order_id = $order_id->id;
			else $order_id = false;
			echo 'Order ID:'.$order_id.'.';
			
			//utm
			if($order_id !==false and $order_id >0) {
			\app\components\Tools::processData($row->keyword,$utm,'utm_term');
			\app\components\Tools::processData($row->posit,$utm,'position');
			\app\components\Tools::processData($row->positt,$utm,'position_type');
			\app\components\Tools::processData($row->who,$utm,'utm_source');
			if(array_key_exists('utm_source',$utm)) {
				if((stripos($utm['utm_source'], 'yandex')!==false) or (stripos($utm['utm_source'], 'direct')!==false))
				$utm['utm_source'] = 'yandex';
			}
			\app\components\Tools::processData($row->typep,$utm,'source_type');
			\app\components\Tools::processData($row->plosh,$utm,'source');
			if($row->idc1 >0)
				\app\components\Tools::processData($row->idc1,$utm,'utm_campaign');
			elseif($row->idc2 >0)
				\app\components\Tools::processData($row->idc2,$utm,'utm_campaign');					
				//\app\components\Tools::processData($row->kompany,$utm,'utm_campaign');
			\app\components\Tools::processData($row->region_name,$utm,'region_name');
			if(count($utm) >0 and $utm['utm_term'] != 'Звонок' and $utm['utm_term'] != 'Заказ с сайта' and null === (\app\models\UtmLabel::find()->where(['order_id' => $order_id])->one())) {				
				$utm['order_id'] = $order_id;
				$mdl = new \app\models\UtmLabel;
				foreach($utm as $k=>$v){
					$mdl->$k = $v;
				}
				if($mdl->save()) echo "Utm save.";
				else echo 'Utm NOT save.';
			}			
			}			
			
			//tovar_rashod
			if($order_id !==false and $order_id >0) :
			//добавим в прайс товар 
			$tovarprice = $this->toPrice($row, $shop_id);
			//\yii\helpers\VarDumper::dump($tovarprice,10,true);
			echo $tovarprice;
			
			$arprice = \yii\helpers\ArrayHelper::index(\app\models\Tovar::find()->where(['shop_id' => $shop_id])->asArray()->all(), 'artikul');
			
			for($i = 1; $i < 6; $i++){
				$p = $art = null;
				$a = "art{$i}";				
				$k = "kolvo{$i}";
				$s = "summa{$i}";
				$b = "base{$i}";			
				$tovar = [];//\yii\helpers\VarDumper::dump($row->$a,10,true);
				if(!empty($row->$a)) {//or $row->$a !='' or !is_null($row->$a)
					if(empty($row->$k)) $row->$k = 1;
					$art = strtoupper($this->_art($row->$a));					
					if(!array_key_exists($art, $arprice)) {
						$art = strtoupper($this->_art($row->$b));
					}
					if(array_key_exists($art, $arprice)) {						
						if(null === (\app\models\TovarRashod::find()->where(['order_id' => $order_id,'tovar_id'=>$arprice[$art]['id']])->one())) {		
							
							$mdl = new \app\models\TovarRashod;
							$mdl->order_id = $order_id;
							$mdl->tovar_id = $arprice[$art]['id'];
							$mdl->shop_id = $shop_id;
							$mdl->price = $row->$s / $row->$k;
							$mdl->amount = $row->$k;
							$mdl->sklad_id = $order['sklad'];
							if($mdl->save()) {echo "Rashod ".$arprice[$art]['name'].' save.';}
							else {echo "Rashod ".$arprice[$art]['name'].' NOT save. '; print_r($mdl->firstErrors);}
						}
						else echo 'Tovar '.$row->$a.' in db.';
					}
					else echo 'Tovar '.$row->$a.' NOT in price.';				
				}
				else echo 'Tovar #'.$i.' '.$row->$a.' NOT if.';				
			}
			endif;
			
			//Sms
			echo $this->retSms($shop_id, $row->id);
			
			echo '<hr>';
			//\yii\helpers\VarDumper::dump($order,10,true);
			
		}//foreach	
		
	}
	
	/**
	* migrate statistic yandex
	* @param undefined $shop_id
	* 
	* @return
	*/
	public function actionMstat($shop_id=false){
		if($shop_id ===false) $shop_id = Yii::$app->params['user.current_shop'];
		if(intval($shop_id) <1) die('shop_id=0');
		
		$max_old_id = 0;//intval(Orders::find()->andWhere(['shop_id'=>$shop_id])->max('old_id2'));
		//\yii\helpers\VarDumper::dump($max_old_id,10,true);
		$url='http://94.41.61.180/migrateAxGet.php?statid='.$max_old_id;
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL,$url);
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt ($ch, CURLOPT_TIMEOUT, 600);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$rows = curl_exec ($ch);
		curl_close($ch);

		$rows = json_decode($rows);
		if($rows == 'no data' or count($rows) <1) die('no data');
					
	\yii\helpers\VarDumper::dump($rows,10,true);die;	
		foreach($rows as $row){
			$stat = [];
			\app\components\Tools::processData($row->id,$stat,'old_id');
			
			if(!empty($stat) and array_key_exists('id_company', $stat) and null === (\app\models\Statcompany::find()->where(['old_id' => $row->id, 'id_company' => $stat['id_company']])->one()))
			{				
				$stat['shop_id'] = $shop_id;
				$mdl = new \app\models\Statcompany;
				foreach($stat as $k=>$v){
					$mdl->$k = $v;
				}
				//if($mdl->save()) echo "Stat save.";
				//else echo 'Stat NOT save.';
			}	
		}
	}
	/**
	* add tovar to price
	* @param object $row
	* 
	* @return
	*/
	public function toPrice($row, $shop_id){
		$prices = [];
		$ret = '';
		//сначала заполним прайс		
		//foreach($rows as $row){
			for($i = 1; $i < 6; $i++){
				$p = $art = null;
				$a = "art{$i}";
				//$p = "price{$i}";
				$pp = "pprice{$i}";
				$n = "name{$i}";
				$k = "kolvo{$i}";
				$s = "summa{$i}";
				$b = "base{$i}";
				$c = "cat{$i}";
				$price = [];//\yii\helpers\VarDumper::dump($row->$a,10,true);
				if(!empty($row->$a) and $row->$a !='' and !is_null($row->$a) and !empty($row->$k)) {
					//$art = str_replace(" ", "_", $row->$a);
					//if(stripos($row->$a, 'hl-') !==false) $art = str_ireplace("hl-", "HL", $art);
					$art = strtoupper($this->_art($row->$a));
					\app\components\Tools::processData($row->$a,$price,'art');
					\app\components\Tools::processData($row->$pp,$price,'pprice');
					\app\components\Tools::processData($row->$n,$price,'name');
					if(!array_key_exists('name',$price)) $price['name'] = $art;
					$p = $row->$s / $row->$k;
					\app\components\Tools::processData($p,$price,'price');				
					\app\components\Tools::processData($this->_art($row->$b),$price,'base');
					if(!array_key_exists('base',$price)) $price['base'] = $art;
					//else $price['base'] = $this->_art($price['base']);
					if(empty($row->$c)) {
						if(substr($art, 0,4) == 'bino')
							$price['cat'] = '2';
						else 
							$price['cat'] = '10';
					}
					else
						$price['cat'] = $row->$c;
					$price['id'] = $row->id;					
					$prices[$art] = $price;
					
				}			
			}			
		//}
		$ret .= "<table class='table table-bordered'>";
		$ret .= '<tr><td>NN</td><td>ID</td><td>BASE</td><td>ARTIKUL</td><td>CATEGORY</td><td>NAME</td><td>SALE</td><td>ZAKUP</td><td></td></tr>';
		//ksort($prices);
		
		//sort
		$sortArray = array(); 
		foreach($prices as $person){ 
		    foreach($person as $key=>$value){ 
		        if(!isset($sortArray[$key])){ 
		            $sortArray[$key] = array(); 
		        } 
		        $sortArray[$key][] = $value; 
		    } 
		} 
		$orderby = "base"; //change this to whatever key you want from the array 
		array_multisort($sortArray[$orderby],SORT_DESC,$prices);
		
		foreach($prices as $k=>$v) {
			$r[$k] = $v;
		}
		
		//echo
		$nn=0;
		foreach($r as $price){	
			$ret .= '<tr><td>'.++$nn.'</td><td>'.$price['id'].'</td><td>'.$price['base'].'</td><td>'.$price['art'].'</td><td>'.$price['cat'].'</td><td>'.$price['name'].'</td><td>'.$price['pprice'].'</td><td>'.$price['price'].'</td>';
			
			//save to tovar
			if(null === (\app\models\Tovar::find()->where(['artikul' => $price['base']])->one())){
				$mdl = new \app\models\Tovar;
				$mdl->shop_id = $shop_id;
				$mdl->artikul = strtoupper($price['base']);
				$mdl->shop_id = $shop_id;
				$mdl->name = $price['name'];
				$mdl->price = (empty($price['price'])) ? '0' : $price['price'];
				$mdl->pprice = (empty($price['pprice'])) ? '0' : $price['pprice'];
				$mdl->category_id = $price['cat'];
				$mdl->active = '1';
				if($mdl->save()) echo "<td>Tovar ".$price['base'].' save</td>';
				else $ret .= "<td>Tovar ".$price['base'].' NOT save:'.print_r($mdl->firstErrors,true).'</td>';
			}
			else $ret .= "<td>Tovar is</td>";
			$ret .= '</tr>';
		}
		$ret .= "</table>";
		
		return $ret;
		//return $prices;
	}
	
	/**
	* 'event' => array(
				'accept' => 'Принят',				
				'shipped' => 'Отгружен',
				'delivered' => 'Доставлен',
				'delivered3day' => 'Повтор доставки',
			)
	* @param undefined $shop_id
	* 
	* @return
	*/
	public function retSms($shop_id=false, $order_id=false) {
		if($shop_id ===false) $shop_id = Yii::$app->params['user.current_shop'];
		if(intval($shop_id) <1) die('shop_id=0');
		ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', 300);
		
		$max_old_id = $order_id;//1;//intval(Orders::find()->andWhere(['shop_id'=>$shop_id])->max('old_id2'));
		//\yii\helpers\VarDumper::dump($max_old_id,10,true);
		$url='http://94.41.61.180/migrateAxGet.php?smsid='.$max_old_id;
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL,$url);
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt ($ch, CURLOPT_TIMEOUT, 360);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$rows = curl_exec ($ch);
		curl_close($ch);

		$rows = json_decode($rows);
		if($rows == 'no data' or count($rows) <1) return('Sms: no data.');
		//return \yii\helpers\VarDumper::dump($utm,10,true);
			
		//sms
		
		foreach($rows as $row){
			$sms = [];
			$mdl = Orders::find()->where(['old_id2' => $row->zayavka_id, 'shop_id' => $shop_id])->one();
			if(null!==$mdl) $order_id = $mdl->id;			
			else $order_id = false;
			
			if($order_id !==false and $order_id >0) {
				if($row->zayavka_id >0){
					\app\components\Tools::processData($order_id,$sms,'order_id');
					\app\components\Tools::processData($row->sms_id,$sms,'sms_id');
					//\app\components\Tools::processData($row->sms_status,$sms,'status');					
					if(count($sms) >0 and null === (\app\models\Sms::find()->where(['order_id' => $order_id])->one())) {				
						$mdl = new \app\models\Sms;
						$mdl->status = '100';
						$mdl->event = 'accept';
						foreach($sms as $k=>$v){
							$mdl->$k = $v;
						}
						if($mdl->save()) return "Sms ".$sms->sms_id." to order ".$order_id." save.";
						else return "Sms not save.";
					}
					else return "SMS to Order ID $order_id found in DB.";
				}
				else return "SMS: Zayavka ID not accept.";
			}
			else return "SMS: Order ID not accept.";
		}
	}
	
	public function _art($art) {
		//if(strtoupper(substr($art, 0,2)) == 'NF') $art = str_ireplace("NF", "HL", $art);
		//if(stripos($art, 'hl-') !==false) $art = str_ireplace("hl-", "HL", $art);
		if(substr($art, 0,2) == 'b.') $art = str_ireplace("b.", "", $art);
		if(stripos($art, 'nf600') !==false) $art = 'HL600';
		elseif(stripos($art, 'hl600') !==false) $art = 'HL600';
		elseif(stripos($art, 'hl 600') !==false) $art = 'HL600';
		elseif(stripos($art, 'hl 900') !==false) $art = 'HL900';
		elseif(stripos($art, 'hl900') !==false) $art = 'HL900';
		elseif(stripos($art, 'hl 2200') !==false) $art = 'HL2200';
		elseif(stripos($art, 'hl2200') !==false) $art = 'HL2200';
		elseif(stripos($art, 'hl-2200') !==false) $art = 'HL2200';
		elseif(stripos($art, 'hl4000') !==false) $art = 'HL4000';
		elseif(stripos($art, 'hl-720') !==false) $art = 'HL720';
		elseif(stripos($art, 'hl720') !==false) $art = 'HL720';
		elseif(stripos($art, 'hl-t700') !==false) $art = 'HLT700';
		elseif(stripos($art, 'hlt700') !==false) $art = 'HLT700';
		elseif(stripos($art, 'hl-855') !==false) $art = 'HL855';
		elseif(stripos($art, 'hl855') !==false) $art = 'HL855';
		elseif(stripos($art, 'hl29') !==false) $art = 'HL29';
		elseif(stripos($art, 'hl-29') !==false) $art = 'HL29';
		elseif(stripos($art, 'hl-39') !==false) $art = 'HL39';
		elseif(stripos($art, 'hl39') !==false) $art = 'HL39';
		elseif(stripos($art, 'pf900') !==false) $art = 'PF900';
		elseif(stripos($art, 'pf901') !==false) $art = 'PF901';
		elseif(stripos($art, 'pf902') !==false) $art = 'PF902';
		elseif(stripos($art, 'pf903') !==false) $art = 'PF903';
		elseif(stripos($art, 'pf904') !==false) $art = 'PF904';
		elseif(stripos($art, 'pf-02') !==false) $art = 'PF02';
		elseif(stripos($art, 'pf02') !==false) $art = 'PF02';
		elseif(stripos($art, 'pf-03') !==false) $art = 'PF03';
		elseif(stripos($art, 'pf-04') !==false) $art = 'PF04';
		elseif(stripos($art, 'pf-05') !==false) $art = 'PF05';
		elseif(stripos($art, 'pf-07') !==false) $art = 'PF07';
		elseif(stripos($art, 'pf-09') !==false) $art = 'PF09';
		elseif(stripos($art, 'hl-t6') !==false) $art = 'HLT6';
		elseif(stripos($art, 'hlt') !==false) $art = 'HLT1';
		elseif(stripos($art, 'hl-t') !==false) $art = 'HLT1';		
		elseif(stripos($art, 'hl-100') !==false) $art = 'HLT100';
		elseif(stripos($art, 'hl-101d') !==false) $art = 'HLT101D';
		elseif(stripos($art, 'hl-102d') !==false) $art = 'HLT102D';
		elseif(stripos($art, 'hl170') !==false) $art = 'HL170';
		elseif(stripos($art, 'nf170') !==false) $art = 'HL170';
		elseif(stripos($art, 'hl300') !==false) $art = 'HL300';
		elseif(stripos($art, 'hl500') !==false) $art = 'HL500';
		elseif(stripos($art, 'nf500') !==false) $art = 'HL500';
		elseif(stripos($art, 'hl-500') !==false) $art = 'HL500';
		elseif(stripos($art, 'nf-500') !==false) $art = 'HL500';		
		elseif(stripos($art, 'hl-87') !==false) $art = 'HLT87';
		elseif(stripos($art, 'hl-12s') !==false) $art = 'HL12S';
		elseif(stripos($art, 'g85') !==false) $art = 'G85';
		elseif(stripos($art, '85g') !==false) $art = 'G85';
		elseif(stripos($art, 'hl-p') !==false) $art = 'HLP1';
		elseif(mb_stripos($art, 'фонарь-дубинка') !==false) $art = 'HLP1';
		elseif(mb_stripos($art, 'охотник') !==false or mb_stripos($art, 'комплект') !==false) $art = 'komplekt';
		elseif(stripos($art, 'liion') !==false) $art = 'A5800';
		elseif(stripos($art, '18650') !==false and stripos($art, '5200') !==false) $art = 'A5200';
		elseif(stripos($art, '18650') !==false and stripos($art, '5800') !==false) $art = 'A5800';
		elseif(stripos($art, 'A5200') !==false) $art = 'A5200';
		elseif(stripos($art, 'A5800') !==false) $art = 'A5800';
		elseif(stripos($art, 'A2400') !==false) $art = 'A2400';
		elseif(stripos($art, 'video3in1') !==false) $art = 'gps-ve-450r';
		elseif(stripos($art, 'gamo') !==false and stripos($art, '3') !==false and stripos($art, '9x40') !==false) $art = 'gamo3-9x40';
		elseif(stripos($art, 'gamo') !==false and stripos($art, '3') !==false and stripos($art, '9-40') !==false) $art = 'gamo3-9x40';
		elseif(stripos($art, 'gamo') !==false and stripos($art, '3') !==false and stripos($art, '9x32') !==false) $art = 'gamo3-9x32';
		elseif(stripos($art, 'gamo') !==false and stripos($art, '3') !==false and stripos($art, '9-32') !==false) $art = 'gamo3-9x32';
		elseif(stripos($art, 'Bushnell') !==false and stripos($art, '3') !==false and stripos($art, '9x40') !==false) $art = 'bushnell3-9x40';
		elseif(stripos($art, 'Bushnell') !==false and stripos($art, '3') !==false and stripos($art, '9x32') !==false) $art = 'bushnell3-9x32';
		elseif(stripos($art, 'OPTIK-B3') !==false and stripos($art, '3') !==false and stripos($art, '9x40') !==false) $art = 'bushnell3-9x40';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') !==false and stripos($art, '60x60') !==false) $art = 'alpen10-60x60';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') !==false and stripos($art, '50x50') !==false) $art = 'alpen10-50x50';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') !==false and stripos($art, '70x70') !==false) $art = 'alpen10-70x70';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') ===false and stripos($art, '60x60') !==false) $art = 'alpen60x60';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') ===false and stripos($art, '50x50') !==false) $art = 'alpen50x50';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') ===false and stripos($art, '70x70') !==false) $art = 'alpen70x70';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') !==false and stripos($art, '60x60') !==false) $art = 'bresser10-60x60';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') !==false and stripos($art, '50x50') !==false) $art = 'bresser10-50x50';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') !==false and stripos($art, '70x70') !==false) $art = 'bresser10-70x70';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') !==false and stripos($art, '90x80') !==false) $art = 'bresser10-90x80';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') ===false and stripos($art, '60x60') !==false) $art = 'bresser60x60';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') ===false and stripos($art, '50x50') !==false) $art = 'bresser50x50';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') ===false and stripos($art, '70x70') !==false) $art = 'bresser70x70';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') !==false and stripos($art, '60x60') !==false) $art = 'bushnell10-60x60';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') !==false and stripos($art, '50x50') !==false) $art = 'bushnell10-50x50';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') !==false and stripos($art, '70x70') !==false) $art = 'bushnell10-70x70';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') ===false and stripos($art, '60x60') !==false) $art = 'bushnell60x60';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') ===false and stripos($art, '50x50') !==false) $art = 'bushnell50x50';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') ===false and stripos($art, '70x70') !==false) $art = 'bushnell70x70';
		elseif((stripos($art, 'bino-n') !==false or stripos($art, 'nikon') !==false) and stripos($art, '10') !==false and stripos($art, '90x80') !==false) $art = 'nikon10-90x80';
		elseif((stripos($art, 'bino-n') !==false or stripos($art, 'nikon') !==false) and stripos($art, '10') !==false and stripos($art, '90*80') !==false) $art = 'nikon10-90x80';
		elseif((stripos($art, 'bino-n') !==false or stripos($art, 'nikon') !==false) and stripos($art, '10') !==false and stripos($art, '60x60') !==false) $art = 'nikon10-60x60';
		elseif((stripos($art, 'bino-n') !==false or stripos($art, 'nikon') !==false) and stripos($art, '10') ===false and stripos($art, '28x40') !==false) $art = 'nikon28x40';
		elseif((stripos($art, 'bino-n') !==false or stripos($art, 'nikon') !==false) and stripos($art, '10') ===false and stripos($art, '70x70') !==false) $art = 'nikon70x70';
		//elseif(stripos($art, 'N83250-1') !==false) $art = 'nikon8-32x50';
		elseif(stripos($art, 'N83250') !==false) $art = 'nikon8-32x50';
		elseif(stripos($art, 'p5050') !==false) $art = 'poisk50x50';
		elseif(stripos($art, 'n1850') !==false) $art = 'nikon18x50';
		elseif(stripos($art, 'b2050') !==false) $art = 'baigish20x50';
		elseif(stripos($art, 'b1050') !==false) $art = 'baigish10x50';
		elseif(stripos($art, 'b1650') !==false) $art = 'baigish16x50';
		elseif(stripos($art, 'b2840') !==false) $art = 'baigish28x40';
		elseif(stripos($art, 'n2050') !==false) $art = 'nikon20x50';
		elseif(stripos($art, 'n750') !==false) $art = 'nikon7x50';
		elseif(stripos($art, 'n1042') !==false) $art = 'nikon10x42';
		elseif(stripos($art, 'BAIGISH') !==false and stripos($art, '10') ===false and stripos($art, 'af') !==false and stripos($art, '70x70') !==false) $art = 'baigish70x70';
		elseif(stripos($art, 'bino-b70x70') !==false) $art = 'baigish70x70';
		elseif(stripos($art, 'b50x50') !==false) $art = 'baigish50x50';
		elseif(stripos($art, 'bino-b32x40') !==false) $art = 'baigish32x40';
		elseif(stripos($art, 'b30x50') !==false) $art = 'baigish30x50';
		elseif(stripos($art, 'bino-b20x50') !==false) $art = 'baigish20x50';
		elseif(stripos($art, 'bino-b20x40') !==false) $art = 'baigish20x40';
		elseif(stripos($art, 'bino-b10x40') !==false) $art = 'baigish10x40';
		elseif(stripos($art, 'bino-b10-90x80') !==false) $art = 'baigish10-90x80';
		elseif(stripos($art, 'BINO-BRECCER70X70') !==false) $art = 'breaker70x70';
		elseif(stripos($art, 'BINO-BRECCER50X50') !==false) $art = 'breaker50x50';
		elseif(stripos($art, 'BINO-BREAKER70X70') !==false) $art = 'breaker70x70';
		elseif(stripos($art, 'LEAPERS') !==false and stripos($art, '6') !==false and stripos($art, '24x50') !==false) $art = 'leapers6-24x50';
		elseif(stripos($art, 'fnp') !==false) $art = 'FNP';
		elseif(stripos($art, 'kompas') !==false) $art = 'KOMPASS';
		elseif(stripos($art, 'upsell-mltt') !==false) $art = 'MLTT';
		elseif(stripos($art, 'Gerber_bearg_113') !==false) $art = 'GERBER_BG';
		elseif(stripos($art, 'optik-c1x35') !==false) $art = 'GAMO1X35';
		elseif(stripos($art, 'monikul-bres35x95') !==false) $art = 'BRESSER35X95';
		elseif(stripos($art, 'upsell-zaryadnik') !==false) $art = 'ZARYADNIK';
				
		elseif(stripos($art, 'MONIKUL-BUSH95X52') !==false) $art = 'bushnell95x52';
		else $art = $art;
		return $art;
	}

}
