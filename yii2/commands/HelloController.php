<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;

/**
 * Отправка смс по событиям заказа из крона
 * 
 *  
 */
class HelloController extends Controller
{
	public function actionIndex()
    {
    	/*
		//cписок всех событий
		$eventlist = \app\models\Sms::itemAlias('event');
		//по каким событиям отправлять с привязкой к магазину
		$configlist = \app\models\Settings::find()->where(['>','shop_id','0'])->andWhere(['name'=>'sms.send.event'])->all();
		$list = [];
		//единый массив событие->магазины
		foreach($configlist as $l) {			
			$elist = unserialize($l['value']);
			//print_r($elist);
			foreach($elist as $e){
				$list[$e][] = $l['shop_id'];
			}			
		}		
		print_r($list);
		//общий для всех событий запрос
		$orderlist = \app\models\Orders::find()
		    ->joinWith('sms')
		    ->where(['orders.status' => '6'])
		    ->andWhere('orders.shop_id IN (:shoplist)')
		    ->andWhere('sms.event = :event');
		//условия для событий
		
		//GO
		foreach($list as $k=>$s){
			if($k == 'accept') {
				$orderlist->andWhere(['orders.otpravlen' => '0']);
			}
			elseif($k == 'service') {
				$orderlist->andWhere(['orders.otpravlen' => '1']);
			}
			elseif($k == 'shipped')
			elseif($k == 'delivered')
			elseif($k == 'delivered3day')
			$r = $orderlist->addParams([':shoplist' => $s, ':event' => $k])->all();
			
		}
		//$orderlist->all();
		print_r($r);
		*/
        /*$orderlist = \app\models\Orders::find()
		    ->joinWith('sms')
		    ->where(['orders.status' => '6'])
		    ->andWhere(['IN','orders.shop_id',$shoplist])
		    ->andWhere([''])
		    ->andWhere(['sms.event' => $event])
		    ->all();
		    }*/
		    
	}
}
