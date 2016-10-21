<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use app\components\Tools;
use app\models\Orders;
use app\models\Tovar;
use app\models\Client;
use app\models\Shops;
use app\models\UtmLabel;
use app\models\TovarRashod;
/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ReceiveController extends Controller
{
	//public $current_shop=0;
	
/*	public function init()
    {
        parent::init();    
        

    }
*/
	public function actionIndex()
	{
		$max_old_id = Orders::find()->max('old_id');
		//echo $max_old_id;

		if ($max_old_id >0) :
			
		$url='http://lrfcrm.tmweb.ru/aXget.php?id='.$max_old_id;
	    
		//\yii\helpers\VarDumper::dump($max_old_id,10,true);
		
		$rows = $this->data_url($url);
			
		if($rows != 'no data' and substr($rows,0,5) != 'Error' and !empty($rows)) {
			
		$logs = [];
		
		$rows = json_decode($rows);
		//print_r($rows);	die;
		//$arprice = \yii\helpers\ArrayHelper::index(\app\models\Tovar::find()->where(['shop_id' => $shop_id])->asArray()->all(), 'artikul');
		
		foreach($rows as $row) {				
			$order = $client = $utm = $utms = $tovar = $sms = [];
			$client_id = $order_id = $shop_id = false;
			$shop_id = Shops::find()->select('id')->where(['token' => $row->shop])->scalar();
			$logs[$row->id]['shop'] = $shop_id;
			if(empty($row->shop) and false === $shop_id) continue;
			
			///// client
			Tools::processData($row->email,$client,'email');
			Tools::processData($row->fio,$client,'fio');				
			Tools::processData(Tools::format_phone($row->phone),$client,'phone');
			if(!empty($client) and array_key_exists('phone', $client) and count($client['phone'] >6) and null === (Client::find()->where(['phone' => $client['phone']])->one())) {
					$client['shop_id'] = $shop_id;
					$mdl = new Client;
					foreach($client as $k=>$v){
						$mdl->$k = $v;
					}
					if($mdl->save()) {
						$logs[$row->id]['client'] = "Client ".$mdl->id."/".$client['phone']." save.";
						$client_id = $mdl->id;
					}
					else $logs[$row->id]['client'] = "Client ".$client['phone']." NOT save.";
				}
			elseif(!empty($client) and array_key_exists('fio', $client) and $client_id ===false and is_numeric(Tools::format_phone($row->fio)) and count(Tools::format_phone($row->fio) >6) and null === (Client::find()->where(['phone' => Tools::format_phone($row->fio), 'shop_id' => $shop_id])->one())) {
					$client['shop_id'] = $shop_id;
					$client['phone'] = Tools::format_phone($row->fio);
					$mdl = new Client;
					foreach($client as $k=>$v){
						$mdl->$k = $v;
					}
					if($mdl->save()) {
						$logs[$row->id]['client'] = "Client1 ".$mdl->id."/".$client['phone']." save.";
						$client_id = $mdl->id;
					}
					else $logs[$row->id]['client'] = "Client1 ".$client['phone']." NOT save.";
				}
			
			$mdl = \app\models\Client::find()->where(['phone' => $client['phone'], 'shop_id' => $shop_id])->one();
			if(null !== $mdl)
				$client_id = $mdl->id;
			$logs[$row->id]['client_id'] = $client_id;
			
			/////Order
			if($client_id !==false and $client_id >0) {
					Tools::processData($client_id,$order,'client_id');
					Tools::processData($row->id,$order,'old_id');
					Tools::processData($row->date0,$order,'date_at');
					Tools::processData($row->url,$order,'url');
					Tools::processData($row->ip_address,$order,'ip_address');
					Tools::processData($shop_id,$order,'shop_id');
					Tools::processData($row->source,$order,'source');
					Tools::processData($row->prich2,$order,'note');
					if(count($order) >0 and null === (Orders::find()->where(['old_id' => $row->id, 'shop_id' => $shop_id])->one())) {
						if(!array_key_exists('source', $order)) $order['source'] = 1;//источник - форма на сайте
						$order['status'] = '1';//cтатус - новый
						$mdl = new Orders;
						foreach($order as $k=>$v){
							$mdl->$k = $v;
						}
						if($mdl->save()) $logs[$row->id]['order'] = "Order ".$mdl->old_id.'/'.$mdl->id." save.";
						else $logs[$row->id]['order'] = 'Order NOT save.';
					}						
				}
			
			/////utm
			$mdl = Orders::find()->where(['old_id' => $row->id, 'shop_id' => $shop_id])->one();
				if(null!==$mdl) $order_id = $mdl->id;
			$logs[$row->id]['order_id'] = $order_id;
							
			if($order_id !==false and $order_id >0) {
				if(!is_null($row->utm) or !empty($row->utm)) {
					parse_str($row->utm, $utms);				
					Tools::processData($utms['utm_source'],$utm,'utm_source');					
					Tools::processData($utms['utm_medium'],$utm,'utm_medium');
					Tools::processData($utms['utm_campaign'],$utm,'utm_campaign');					
					Tools::processData($utms['utm_term'],$utm,'utm_term');
					Tools::processData($utms['utm_content'],$utm,'utm_content');
					Tools::processData($utms['source_type'],$utm,'source_type');
					Tools::processData($utms['type'],$utm,'source_type');
					Tools::processData($utms['source'],$utm,'source');
					Tools::processData($utms['position_type'],$utm,'position_type');
					Tools::processData($utms['block'],$utm,'position_type');
					Tools::processData($utms['position'],$utm,'position');
					Tools::processData($utms['region_name'],$utm,'region_name');
					Tools::processData($utms['group_id'],$utm,'group_id');
					Tools::processData($utms['banner_id'],$utm,'banner_id');
					Tools::processData($utms['device'],$utm,'device');
					Tools::processData($utms['device_type'],$utm,'device');
				}
				else {
					Tools::processData($row->keyword,$utm,'utm_term');
					Tools::processData($row->posit,$utm,'position');
					Tools::processData($row->positt,$utm,'position_type');
					Tools::processData($row->who,$utm,'utm_source');
					Tools::processData($row->typep,$utm,'source_type');
					Tools::processData($row->plosh,$utm,'source');
					Tools::processData($row->kompany,$utm,'utm_campaign');
				}				
				if(array_key_exists('utm_source',$utm)) {
					if((stripos($utm['utm_source'], 'yandex')!==false) or (stripos($utm['utm_source'], 'direct')!==false))
					$utm['utm_source'] = 'yandex';
				}
				
				if(array_key_exists('utm_campaign',$utm)) {
					$utm['utm_campaign'] = preg_replace("/[^0-9]/", "", $utm['utm_campaign']);
				}
				
				if(count($utm) >0 and null === (UtmLabel::find()->where(['order_id' => $order_id])->one())) {				
					$utm['order_id'] = $order_id;
					$mdl = new UtmLabel;
					foreach($utm as $k=>$v){
						$mdl->$k = $v;
					}
					if($mdl->save()) $logs[$row->id]['utm'] = "Utm save.";
					else $logs[$row->id]['utm'] = 'Utm NOT save.';
				}
				else
					$logs[$row->id]['utm'] = 'Utm is empty';
			}
			
			/////tovar rashod
			if($order_id !==false and $order_id >0) :
			
			
			if(!is_null($row->tovar) and !empty($row->tovar)) {
			
			$tovar_list = unserialize($row->tovar);
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
						$mdl->sklad_id = $order['sklad'];
						if($mdl->save()) $logs[$row->id]['rashod'][$art] = '#'.$row->id." Rashod ".$mtovar->name.' save.';
						else $logs[$row->id]['rashod'][$art] = '#'.$row->id." Rashod ".$mtovar->name.' NOT save. '.print_r($mdl->firstErrors, TRUE);
					}
					else $logs[$row->id]['rashod'][$art] = 'Tovar '.$art.' in db';
				}
				else $logs[$row->id]['rashod'][$art] = 'Tovar '.$art.' NOT in price';
			}
			
			}//if count
			elseif(!is_null($row->artikul) and !empty($row->artikul)) {
				$art = strtoupper($row->artikul);	
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
						$mdl->sklad_id = $order['sklad'];
						if($mdl->save()) $logs[$row->id]['rashod'][$art] = 'Rashod '.$mtovar->id.' save.';
						else {
							$logs[$row->id]['rashod'][$art] = "Rashod ".$mtovar->id.' NOT save. ';
							$logs[$row->id]['rashod'][$art]['error'] = print_r($mdl->firstErrors, TRUE);
						}
					}
					else $logs[$row->id]['rashod'][$art] = 'Tovar '.$art.' in db';
				}
				else $logs[$row->id]['rashod'][$art] = 'Tovar '.$art.' NOT in price';
			}//elseif count
			else $logs[$row->id]['rashod'] = 'Tovar NOT found';
			
			endif;
			
		}//foreach
		
		if(!empty($logs))
		$fp = fopen('/home/l/linerfmail/erp/public_html/receive-'.date('Y-m-d').'.txt', 'a');
		fwrite($fp, print_r($logs,true)); fclose($fp);
		
		return print_r($logs,true);
		}//if count rows
	    //else return date('Y-m-d-H-m-s').' No new order/n';
        endif;//if max old id
	    
	    return 'false old id';
	}
	
	public function data_url($url) {    
		//\yii\helpers\VarDumper::dump($max_old_id,10,true);	
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL,$url);
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$rows = curl_exec ($ch);
		curl_close($ch);
		if (curl_errno($ch)) { 
	        return "error: " . curl_error($ch);
	        //$fp = fopen('curl_err-'.date('Y-m-d').'.txt', 'w');
			//fwrite($fp, print_r(curl_error($ch),true));
			//fclose($fp);
		}
		else {
			return $rows;
		}  
	}
	
	private function _art($art) {
		//if(strtoupper(substr($art, 0,2)) == 'NF') $art = str_ireplace("NF", "HL", $art);
		//if(stripos($art, 'hl-') !==false) $art = str_ireplace("hl-", "HL", $art);
		if(substr($art, 0,2) == 'b.') $art = str_ireplace("b.", "", $art);
		if(stripos($art, 'nf600') !==false) $art = 'HL600';
		elseif(stripos($art, 'hl600') !==false) $art = 'HL600';
		elseif(stripos($art, 'hl 600') !==false) $art = 'HL600';
		elseif(stripos($art, 'hl 900') !==false) $art = 'HL900';
		elseif(stripos($art, 'hl 2200') !==false) $art = 'HL2200';
		elseif(stripos($art, 'hl-2200') !==false) $art = 'HL2200';
		elseif(stripos($art, 'hl4000') !==false) $art = 'HL4000';
		elseif(stripos($art, 'hl-720') !==false) $art = 'HL720';
		elseif(stripos($art, 'hl-t700') !==false) $art = 'HLT700';
		elseif(stripos($art, 'hl-855') !==false) $art = 'HL855';
		elseif(stripos($art, 'hl29') !==false) $art = 'HL29';
		elseif(stripos($art, 'hl-29') !==false) $art = 'HL29';
		elseif(stripos($art, 'hl-39') !==false) $art = 'HL39';
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
		elseif(stripos($art, 'video3in1') !==false) $art = 'gps-ve-450r';
		elseif(stripos($art, 'gamo') !==false and stripos($art, '3') !==false and stripos($art, '9x40') !==false) $art = 'gamo3-9x40';
		elseif(stripos($art, 'gamo') !==false and stripos($art, '3') !==false and stripos($art, '9x32') !==false) $art = 'gamo3-9x32';
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
		elseif(stripos($art, 'N83250-1') !==false) $art = 'nikon8-32x50';
		elseif(stripos($art, 'p5050') !==false) $art = 'poisk50x50';
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
		elseif(stripos($art, 'MONIKUL-BUSH95X52') !==false) $art = 'bushnell95x52';
		else $art = $art;
		return $art;
	}
}
