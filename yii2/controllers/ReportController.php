<?php

namespace app\controllers;

use Yii;
//use yii\web\Controller;
use app\models\Orders;
use app\models\ReportHostsDate;
use app\models\ReportTovarInOut;
use app\models\ReportDosales;
use app\models\ReportTovar;
use app\models\ReportDeliverycost;
use app\models\ReportSvod;
use app\models\TovarRashod;
use app\models\TovarPrihod;
use app\models\TovarBalance;
use app\models\Settings;
use app\models\Statcompany;
use app\models\ReportRefused;
use yii\helpers\ArrayHelper;
use app\modules\user\models\User;
use yii\data\SqlDataProvider;
use app\components\BaseController;

class ReportController extends BaseController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionYadirect()
    {
        return $this->render('yadirect');
    }
    
    public function actionHosts()
    {        
        /*echo Yii::$app->getTimeZone();
        $script_tz = date_default_timezone_get();
		echo ini_get('date.timezone');
		if (strcmp($script_tz, ini_get('date.timezone'))){
    		echo 'Временная зона скрипта отличается от заданной в INI-файле.';
		} else {
    		echo 'Временные зоны скрипта и настройки INI-файла совпадают.';
		}
        */
        $results = $errors = [];
                
        $model = new ReportHostsDate();
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {	
			$date1 = $model->date1;
			$date2 = $model->date2;
			$date = date('Y-m-d', strtotime($date1.' - 1 days'));			

			$ya_list = $this->_get_metrika();			
			
			$db = Yii::$app->db;
			
			while($date < $date2){
				$date = date('Y-m-d', strtotime($date.' + 1 days'));			
					
				foreach($ya_list['counters'] as $ya) {
					//если нет меток - пропускаем счетчик
					//if(!isset($ya['labels'])) continue;
					$res = array();				
					
					if(!empty($model->host) and $ya['site'] != $model->host) continue;
					
					//стата рекламы
					//$stat = $db->createCommand("SELECT sum(costs) as costs, sum(clicks) as clicks from statcompany WHERE `name` like lower('%".$ya['site']."%') and `date_at`='$date'")->queryOne();
					$stat = $db->createCommand("SELECT sum(costs) as costs, sum(clicks) as clicks from statcompany WHERE `host` like '".$ya['site']."%' and `date_at`='$date'")->queryOne();
					$res['clicks'] = $stat['clicks'];
					$res['costs'] = $stat['costs'];
												
					//заявки по урлу все кроме теста 
					$orders = Orders::find()->select(['count(orders.id) as cnt_all'])->where(['DATE(`date_at`)'=>$date])->andWhere('`url` like "'.$ya['site'].'%"')->andWhere(['<>', 'status','8'])->one();//(['like','url',$ya['site']])
					$res['cnt_all'] = $orders->cnt_all;
					
					//данные о посещениях
					$ydate = date('Ymd', strtotime($date));
					$ya_stat = $this->_get_metrika('http://api-metrika.yandex.ru/stat/traffic/summary.json?id='.$ya['id'].'&pretty=1&date1='.$ydate.'&date2='.$ydate.'&oauth_token='.Settings::getKey('ya_metrika_token'));
					
					
					if($res['cnt_all'] < 1 and $ya_stat['totals']['visits'] < 10 and $res['costs'] < 1 and $res['clicks'] < 1)	continue;
					
					
					//сумма заявок по урлу все кроме теста 
					$orders = Orders::find()->joinWith('rashod')->select(['sum(price*amount) as summ'])->where(['DATE(`date_at`)'=>$date])->andWhere('`url` like "'.$ya['site'].'%"')->andWhere(['<>', 'status','8'])->one();//(['like','url',$ya['site']])					
					$res['sum'] = $orders->summ;
					
					//заявки всего с дублями
					$all_dub = $db->createCommand("SELECT count(*) as all_dub from `orders` WHERE DATE(`date_at`) = '$date' and `url` like '".$ya['site']."%' and `status` <> '8'")->queryOne();
					$res['cnt_all_dub'] = $all_dub['all_dub'];				
					
					//заявки из директа без дублей
					$orders = $db->createCommand("SELECT count(*) as za_ya from `orders`, `utm_label` WHERE DATE(`date_at`) = '$date' AND `url` like '".$ya['site']."%' and `order_id` = `orders`.`id` and (`utm_source` like '%yandex%' or `utm_source` like '%direct%') and `status` not in (2,8)")->queryOne();
					$res['cnt_za_ya'] = $orders['za_ya'];
					
					//заявки из директа всего
					$orders = $db->createCommand("SELECT count(*) as za_ya_dub from `orders`, `utm_label` WHERE DATE(`date_at`) = '$date' AND `url` like '".$ya['site']."%' and `order_id` = `orders`.`id` and (`utm_source` like '%yandex%' or `utm_source` like '%direct%') and `status` <> '8'")->queryOne();
					$res['cnt_za_ya_dub'] = $orders['za_ya_dub'];				
					
					//результат
					//if($res['cnt_all']>0 or $ya_stat['totals']['visits'] >10 or $res['costs']>0 or $res['clicks'] >0)
					{
						$res['url'] = $ya['site'];						
						//$res['sum'] = $res1['sum'];
						$res['visits'] = $ya_stat['totals']['visits'];
												
						$results[$date]['rus'][$ya['site']] = $res;						
					}				
		//\yii\helpers\VarDumper::dump($res,10,true);
				}//ya_list	
				
			}//while

		} else {
			$errors = $model->errors;
		}
        
        return $this->render('hosts',['model'=>$model, 'results'=>$results, 'errors'=>$errors]);
    }
    
    public function actionOrders() {
		$result = $errors = [];
                
        $mdlDate = new ReportHostsDate();
        
        if ($mdlDate->load(Yii::$app->request->post()) && $mdlDate->validate()) {	
			$date1 = ($mdlDate->date1);
			$date2 = ($mdlDate->date2);
			$date = date('Y-m-d', strtotime($date1.' - 1 days'));
		//\yii\helpers\VarDumper::dump($date2,10,true);die;
			$db = Yii::$app->db;
			
			$where_shop = 'and orders.shop_id = '.Yii::$app->params['user.current_shop'];
			
			//ini_set("max_execution_time","0");
		 
			while($date < $date2) {
				
				$date = date('Y-m-d', strtotime($date.' + 1 days'));//Yii::$app->formatter->asDate();
				$orders = $res = [];				
			
				//все заявки кроме теста
				//$orders = Orders::find()->select(['count(*) as cnt_all'])->where(['DATE(`date`)'=>$date])->andWhere(['<>', 'status',8])->one();
				/*$orders = $db->createCommand("SELECT count(*) as cnt_all from orders WHERE DATE(date_at) = '$date' and status <>8 $where_shop")->queryOne();//
				$res['cnt_all'] = $orders['cnt_all'];
				$orders = [];
				*/
				//чистые заявки
				//$orders = Orders::find()->select(['count(*) as cnt_dub'])->where(['DATE(`date`)'=>$date])->andWhere(['status'=>2])->one();
				$orders = $db->createCommand("SELECT count(*) as cnt_za from orders WHERE DATE(date_at) = '$date' and status NOT IN (2,8) $where_shop")->queryOne();//IN (1,4,6,7)
				$res['cnt_za'] = $orders['cnt_za'];
				$orders = [];
			
				//дубли
				//$orders = Orders::find()->select(['count(*) as cnt_dub'])->where(['DATE(`date`)'=>$date])->andWhere(['status'=>2])->one();
				/*$orders = $db->createCommand("SELECT count(*) as cnt_dub from orders WHERE DATE(date) = '$date' and status = '2'")->queryOne();
				$res['cnt_dub'] = $orders['cnt_dub'];
				
				//в работе
				//$orders = Orders::find()->select(['count(*) as cnt_work'])->where(['DATE(`date`)'=>$date])->andWhere(['status'=>4])->one();
				$orders = $db->createCommand("SELECT count(*) as cnt_work from orders WHERE DATE(date) = '$date' and status = '4'")->queryOne();
				$res['cnt_work'] = $orders['cnt_work'];
				
				//недозвон
				//$orders = Orders::find()->select(['count(*) as cnt_nedozvon'])->where(['DATE(`date`)'=>$date])->andWhere(['status'=>3])->one();
				$orders = $db->createCommand("SELECT count(*) as cnt_nedozvon from orders WHERE DATE(date) = '$date' and status = '3'")->queryOne();
				$res['cnt_nedozvon'] = $orders['cnt_nedozvon'];
				
				//отказ
				//$orders = Orders::find()->select(['count(*) as cnt_otkaz'])->where(['DATE(`date`)'=>$date])->andWhere(['status'=>7])->one();
				$orders = $db->createCommand("SELECT count(*) as cnt_otkaz from orders WHERE DATE(date) = '$date' and status = '7'")->queryOne();
				$res['cnt_otkaz'] = $orders['cnt_otkaz'];
					
				//тп
				//$orders = Orders::find()->select(['count(*) as cnt_tp'])->where(['DATE(`date`)'=>$date])->andWhere(['status'=>9])->one();
				$orders = $db->createCommand("SELECT count(*) as cnt_tp from orders WHERE DATE(date) = '$date' and status = '5'")->queryOne();
				$res['cnt_tp'] = $orders['cnt_tp'];
			*/	
				//заказ
				//$orders = Orders::find()->select(['count(*) as cnt_zz'])->where(['DATE(`date`)'=>$date])->andWhere(['status'=>6])->one();
				$orders = $db->createCommand("SELECT count(*) as cnt_zz from orders WHERE DATE(date_at) = '$date' and status = '6' $where_shop")->queryOne();
				$res['cnt_zz'] = $orders['cnt_zz'];
				$orders = [];
				
				//сумма заказов
				$orders = $db->createCommand("SELECT sum(price*amount) as zz_sum from tovar_rashod WHERE order_id in (SELECT id FROM orders WHERE DATE(date_at) = '$date' and status = '6' $where_shop)")->queryOne();
				$res['zz_sum'] = $orders['zz_sum'];
				$orders = [];
				
				//отправки
				//$orders = Orders::find()->select(['count(*) as cnt_otp'])->where(['DATE(`data_otprav`)'=>$date])->andWhere(['otpravlen'=>1])->one();
				$orders = $db->createCommand("SELECT count(*) as cnt_otp from orders WHERE DATE(data_otprav) = '$date' and otpravlen = '1' and status = '6' $where_shop")->queryOne();
				$res['cnt_otp'] = $orders['cnt_otp'];
				$orders = [];
				
				//сумма отправок
				$orders = $db->createCommand("SELECT sum(price*amount) as otp_sum from tovar_rashod WHERE order_id in (SELECT id FROM orders WHERE DATE(data_otprav) = '$date' and otpravlen = '1' and status = '6' $where_shop)")->queryOne();
				$res['otp_sum'] = $orders['otp_sum'];
				$orders = [];
				
				//оплачен
				//$orders = Orders::find()->select(['count(*) as cnt_oplachen'])->where(['DATE(`data_oplata`)'=>$date])->andWhere(['oplachen'=>1])->one();
				$orders = $db->createCommand("SELECT count(*) as cnt_oplachen from orders WHERE  DATE(data_oplata) = '$date' and oplachen = '1' and status <> '8' $where_shop")->queryOne();
				$res['cnt_oplachen'] = $orders['cnt_oplachen'];
				$orders = [];
				
				//в кассе
				//$orders = Orders::find()->select(['count(*) as cnt_vkasse'])->where(['DATE(`data_vkasse`)'=>$date])->andWhere(['vkasse'=>1])->one();
				$orders = $db->createCommand("SELECT count(*) as cnt_vkasse from orders WHERE  DATE(data_vkasse) = '$date' and vkasse = '1' and status <> '8' $where_shop")->queryOne();
				$res['cnt_vkasse'] = $orders['cnt_vkasse'];
				$orders = [];
					
				//стата рекламы
				$stat = $db->createCommand("SELECT sum(shows) as shows, sum(costs) as costs, sum(clicks) as clicks from statcompany WHERE `date_at`='$date' and shop_id ='".Yii::$app->params['user.current_shop']."'")->queryOne();
				$res['show'] = $stat['shows'];
				$res['clicks'] = $stat['clicks'];
				$res['reklama'] = $stat['costs'];					
						
				$result[$date] = $res;
			}
		}
		else {
			$errors = $mdlDate->errors;
		}
        //\yii\helpers\VarDumper::dump($mdlDate,10,true);
        return $this->render('orders',['model'=>$mdlDate, 'results'=>$result, 'errors'=>$errors]);
	}
	
	public function actionManagers() {
		$result = $errors = [];
                
        $mdlDate = new ReportHostsDate();
        
        if ($mdlDate->load(Yii::$app->request->post()) && $mdlDate->validate()) {	
			$date1 = $mdlDate->date1;
			$date2 = $mdlDate->date2;
			$date = "DATE(`date_at`) BETWEEN '$date1' AND '$date2'";
			
			$db = Yii::$app->db;
			
			//ini_set("max_execution_time","0");
			
			$manager_list = User::getUsersByRole('manager');
			//$manager_list = implode(',',ArrayHelper::map($manager_list, 'id', 'id'));			
			
		 	foreach($manager_list as $manager) {
				$res = [];				
		//\yii\helpers\VarDumper::dump($manager,3,true);
				//все заявки кроме теста
				//$orders = Orders::find()->select(['count(*) as cnt_all'])->where(['DATE(`date`)'=>$date])->andWhere(['<>', 'status',8])->one();
				$orders = $db->createCommand("SELECT count(*) as cnt_all from orders WHERE  $date and status <> '8' and manager_id = '$manager[id]'")->queryOne();
				$res['cnt_all'] = $orders['cnt_all'];
			
				//чистые заявки
				//$orders = Orders::find()->select(['count(*) as cnt_dub'])->where(['DATE(`date`)'=>$date])->andWhere(['status'=>2])->one();
				$orders = $db->createCommand("SELECT count(*) as cnt_za from orders WHERE $date and status IN (1,4,6,7) and manager_id = '$manager[id]'")->queryOne();
				$res['cnt_za'] = $orders['cnt_za'];
				
				//заказ
				//$orders = Orders::find()->select(['count(*) as cnt_zz'])->where(['DATE(`date`)'=>$date])->andWhere(['status'=>6])->one();
				$orders = $db->createCommand("SELECT count(*) as cnt_zz from orders WHERE $date and status = '6' and manager_id = '$manager[id]'")->queryOne();
				$res['cnt_zz'] = $orders['cnt_zz'];
				
				//продано
				$orders = $db->createCommand("SELECT SUM(price*amount) as summ from tovar_rashod WHERE order_id IN (SELECT id from orders WHERE $date and status = '6' and manager_id = '$manager[id]')")->queryOne();
				$res['summ'] = $orders['summ'];
				
				//ср.чек
				$res['avg'] = $res['summ'] / $res['cnt_zz'];
					
				$result[$manager['fullname']] = $res;
			}			
		}
		else {
			$errors = $mdlDate->errors;
		}
        //\yii\helpers\VarDumper::dump($mdlDate,10,true);
        return $this->render('managers',['model'=>$mdlDate, 'results'=>$result, 'errors'=>$errors]);
	}
    
    public function actionTovar() {
		$result = $errors = [];
		$category_id = null;
                
        $mdl = new ReportTovar();
        
        $categories = \app\models\Category::find()->select(['name', 'id'])->where(['shop_id'=>$this->shop_id])->indexBy('id')->column();
        
        if ($mdl->load(Yii::$app->request->post()) && $mdl->validate()) {	
			$date1 = $mdl->date1;
			$date2 = $mdl->date2;
			$date = "DATE(`orders`.`date_at`) BETWEEN '$date1' AND '$date2'";
			
			$result = TovarRashod::find()
				//->select('tovar_id')			
				->joinWith(['order','tovar'])
    			->where(['not in', 'orders.status', [2,8]])
    			->andWhere(['BETWEEN','DATE(orders.date_at)',$date1,$date2])
    			->andWhere(['orders.shop_id'=>$this->shop_id])
    			->andFilterWhere(['tovar.category_id'=>$mdl->category_id])
    			->groupBy('tovar_id')
    			->asArray()
    			->all();
   //\yii\helpers\VarDumper::dump($result,3,true);die; 		
    		foreach($result as $row) {
				$results[$row['tovar_id']]['name'] = $row['tovar']['name'];
				//количество всех
				/*$results[$row['tovar_id']]['cnt'] = TovarRashod::find()
					//->select('tovar_id')			
					->joinWith(['order'])
	    			->where(['not in', 'orders.status', [2,8]])
	    			->andWhere(['BETWEEN','DATE(FROM_UNIXTIME(orders.created_at))',$date1,$date2])
	    			->andWhere(['tovar_id'=>$row['tovar_id']])
	    			//->groupBy('tovar_id')
	    			->asArray()
	    			->sum('amount');
	    		*/
	    		//сумма всех
	    		$results[$row['tovar_id']]['summ'] = TovarRashod::find()
					//->select('tovar_id')			
					->joinWith(['order'])
	    			->where(['not in', 'orders.status', [2,8]])
	    			->andWhere(['BETWEEN','DATE(orders.date_at)',$date1,$date2])
	    			->andWhere(['tovar_id'=>$row['tovar_id']])
	    			->andWhere(['orders.shop_id'=>$this->shop_id])
	    			//->groupBy('tovar_id')
	    			->asArray()
	    			->sum('(price*amount)');
	    		//в заявках
	    		$results[$row['tovar_id']]['za'] = TovarRashod::find()
					//->select('tovar_id')			
					->joinWith(['order'])
	    			->where(['not in', 'orders.status', [2,8]])
	    			->andWhere(['BETWEEN','DATE(orders.date_at)',$date1,$date2])
	    			->andWhere(['tovar_id'=>$row['tovar_id']])
	    			->andWhere(['orders.shop_id'=>$this->shop_id])
	    			//->groupBy('orders.id')
	    			->asArray()
	    			->sum('amount');
	    		//в заказах
	    		$results[$row['tovar_id']]['zz'] = TovarRashod::find()
					//->select('tovar_id')			
					->joinWith(['order'])
	    			->where(['orders.status'=>6])
	    			->andWhere(['BETWEEN','DATE(orders.date_at)',$date1,$date2])
	    			->andWhere(['tovar_id'=>$row['tovar_id']])
	    			->andWhere(['orders.shop_id'=>$this->shop_id])
	    			//->groupBy('orders.id')
	    			->asArray()
	    			->sum('amount');
	    		//в отправках
	    		$results[$row['tovar_id']]['zot'] = TovarRashod::find()
					//->select('tovar_id')			
					->joinWith(['order'])
	    			->where(['orders.status'=>6])
	    			->andWhere(['otpravlen'=>1])
	    			->andWhere(['BETWEEN','DATE(orders.data_otprav)',$date1,$date2])
	    			->andWhere(['tovar_id'=>$row['tovar_id']])
	    			->andWhere(['orders.shop_id'=>$this->shop_id])
	    			//->groupBy('orders.id')
	    			->asArray()
	    			->sum('amount');
	    			
	    		//остатки
	    		//$results[$row['tovar_id']]['sklad'] = TovarBalance::find()->where(['tovar_id'=>$row['tovar_id']])->sum('amount');
	    		$prihod = $zot =0;
	    		$prihod = TovarPrihod::find()
	    			->where(['tovar_id'=>$row['tovar_id']])
	    			->andWhere(['shop_id'=>$this->shop_id])
	    			->sum('amount');
	    			
	    		$zot = TovarRashod::find()
					//->select('tovar_id')			
					->joinWith(['order'])
	    			->where(['orders.status'=>6])
	    			->andWhere(['otpravlen'=>1])
	    			//->andWhere(['BETWEEN','DATE(orders.data_otprav)',$date1,$date2])
	    			->andWhere(['tovar_id'=>$row['tovar_id']])
	    			->andWhere(['orders.shop_id'=>$this->shop_id])
	    			//->groupBy('orders.id')
	    			//->asArray()
	    			->sum('amount');
	    		$results[$row['tovar_id']]['sklad']	= $prihod - $zot;
	    		
	    		//в оплаченных
	    		$results[$row['tovar_id']]['zop'] = TovarRashod::find()
					//->select('tovar_id')			
					->joinWith(['order'])
	    			->where(['orders.status'=>6])
	    			->andWhere(['oplachen'=>1])
	    			->andWhere(['BETWEEN','DATE(orders.data_oplata)',$date1,$date2])
	    			->andWhere(['tovar_id'=>$row['tovar_id']])
	    			->andWhere(['orders.shop_id'=>$this->shop_id])
	    			//->groupBy('orders.id')
	    			->asArray()
	    			->sum('amount');
			}
    		
			/*->with([
				'order'=>function ($query) {$query->andWhere($date)//'BETWEEN','DATE(FROM_UNIXTIME(`created_at`))',$date1,$date2])
					->andWhere(['not in', 'status', [2,8]]);},
				'tovar'])
			//->where(['BETWEEN','DATE(FROM_UNIXTIME(`orders`.`created_at`))',$date1,$date2])
			->groupBy('tovar_id')
			->all();*/
			
			/*
			$db = Yii::$app->db;
			$result = $db->createCommand("SELECT * from `tovar_rashod`
				left join `orders` on `orders`.`id` = `tovar_rashod`.`order_id`
				left join `tovar` on `tovar`.`id` = `tovar_rashod`.`tovar_id`
				WHERE $date and `orders`.`status` not in (2,8) 
				group by tovar_id")->queryAll();
				*/
			 //\yii\helpers\VarDumper::dump($results,5,true);die;
		}
		return $this->render('tovar',['model'=>$mdl, 'results'=>$results, 'errors'=>$errors, 'categories'=>$categories]);
	}
	
	public function actionTovaradv() {
		$result = $errors = [];
		$category_id = null;
                
        $mdl = new ReportTovar();
        
        $categories = \app\models\Category::find()->select(['name', 'id'])->where(['shop_id'=>$this->shop_id])->indexBy('id')->column();
        
        if ($mdl->load(Yii::$app->request->post()) && $mdl->validate()) {	
			$date1 = $mdl->date1;
			$date2 = $mdl->date2;
			$date = "DATE(`orders`.`date_at`) BETWEEN '$date1' AND '$date2'";
			
			$result = TovarRashod::find()
				//->select('tovar_id')			
				->joinWith(['order','tovar'])
    			->where(['not in', 'orders.status', [2,8]])
    			->andWhere(['BETWEEN','DATE(orders.date_at)',$date1,$date2])
    			->andWhere(['orders.shop_id'=>$this->shop_id])
    			->andWhere('tovar_rashod.price >0')
    			->andFilterWhere(['tovar.category_id'=>$mdl->category_id])
    			->groupBy('tovar_id')
    			->asArray()
    			->all();
   //\yii\helpers\VarDumper::dump($result,3,true);die; 		
    		foreach($result as $row) {
				$results[$row['tovar_id']]['name'] = $row['tovar']['name'];
				
				$reklama = Statcompany::find()
					->select('sum(costs) as costs, sum(shows) as shows, sum(clicks) as clicks')
					->where(['tovar_id'=>$row['tovar_id']])//152])
					->andWhere(['shop_id'=>$this->shop_id])
					->andWhere(['BETWEEN','date_at',$date1,$date2])
					->one();
				$results[$row['tovar_id']]['shows'] = $reklama->shows;
				$results[$row['tovar_id']]['clicks'] = $reklama->clicks;
				$results[$row['tovar_id']]['costs'] = $reklama->costs;
				//\yii\helpers\VarDumper::dump($reklama,5,true);die;
				//количество всех
				/*$results[$row['tovar_id']]['cnt'] = TovarRashod::find()
					//->select('tovar_id')			
					->joinWith(['order'])
	    			->where(['not in', 'orders.status', [2,8]])
	    			->andWhere(['BETWEEN','DATE(FROM_UNIXTIME(orders.created_at))',$date1,$date2])
	    			->andWhere(['tovar_id'=>$row['tovar_id']])
	    			//->groupBy('tovar_id')
	    			->asArray()
	    			->sum('amount');
	    		*/	    		
	    		//в заявках
	    		$results[$row['tovar_id']]['za'] = TovarRashod::find()
					//->select('tovar_id')			
					->joinWith(['order'])
	    			->where(['not in', 'orders.status', [2,8]])
	    			->andWhere(['BETWEEN','DATE(orders.date_at)',$date1,$date2])
	    			->andWhere(['tovar_id'=>$row['tovar_id']])
	    			->andWhere(['orders.shop_id'=>$this->shop_id])
	    			//->groupBy('orders.id')
	    			->asArray()
	    			->sum('amount');
	    		//cумма заказов	
	    		$results[$row['tovar_id']]['zz_summ'] = TovarRashod::find()
					//->select('tovar_id')			
					->joinWith(['order'])
	    			->where(['orders.status'=>6])
	    			->andWhere(['BETWEEN','DATE(orders.date_at)',$date1,$date2])
	    			->andWhere(['tovar_id'=>$row['tovar_id']])
	    			->andWhere(['orders.shop_id'=>$this->shop_id])
	    			//->groupBy('tovar_id')
	    			->asArray()
	    			->sum('(price*amount)');
	    		//в заказах
	    		$results[$row['tovar_id']]['zz'] = TovarRashod::find()
					//->select('tovar_id')			
					->joinWith(['order'])
	    			->where(['orders.status'=>6])
	    			->andWhere(['BETWEEN','DATE(orders.date_at)',$date1,$date2])
	    			->andWhere(['tovar_id'=>$row['tovar_id']])
	    			->andWhere(['orders.shop_id'=>$this->shop_id])
	    			//->groupBy('orders.id')
	    			->asArray()
	    			->sum('amount');
	    		//в отправках
	    		$results[$row['tovar_id']]['zot'] = TovarRashod::find()
					//->select('tovar_id')			
					->joinWith(['order'])
	    			->where(['orders.status'=>6])
	    			->andWhere(['otpravlen'=>1])
	    			->andWhere(['BETWEEN','DATE(orders.data_otprav)',$date1,$date2])
	    			->andWhere(['tovar_id'=>$row['tovar_id']])
	    			->andWhere(['orders.shop_id'=>$this->shop_id])
	    			//->groupBy('orders.id')
	    			->asArray()
	    			->sum('amount');
	    		//cумма отправок	
	    		$results[$row['tovar_id']]['zot_summ'] = TovarRashod::find()
					//->select('tovar_id')			
					->joinWith(['order'])
	    			->where(['orders.status'=>6])
	    			->andWhere(['otpravlen'=>1])
	    			->andWhere(['BETWEEN','DATE(orders.date_at)',$date1,$date2])
	    			->andWhere(['tovar_id'=>$row['tovar_id']])
	    			->andWhere(['orders.shop_id'=>$this->shop_id])
	    			//->groupBy('tovar_id')
	    			->asArray()
	    			->sum('(price*amount)');
	    		//в оплаченных
	    		$results[$row['tovar_id']]['zop'] = TovarRashod::find()
					//->select('tovar_id')			
					->joinWith(['order'])
	    			->where(['orders.status'=>6])
	    			->andWhere(['oplachen'=>1])
	    			->andWhere(['BETWEEN','DATE(orders.data_oplata)',$date1,$date2])
	    			->andWhere(['tovar_id'=>$row['tovar_id']])
	    			->andWhere(['orders.shop_id'=>$this->shop_id])
	    			//->groupBy('orders.id')
	    			->asArray()
	    			->sum('amount');
	    		//в кассе
	    		$results[$row['tovar_id']]['zvk'] = TovarRashod::find()
					//->select('tovar_id')			
					->joinWith(['order'])
	    			->where(['orders.status'=>6])
	    			->andWhere(['vkasse'=>1])
	    			->andWhere(['BETWEEN','DATE(orders.data_vkasse)',$date1,$date2])
	    			->andWhere(['tovar_id'=>$row['tovar_id']])
	    			->andWhere(['orders.shop_id'=>$this->shop_id])
	    			//->groupBy('orders.id')
	    			->asArray()
	    			->sum('amount');
			}
    		
			/*->with([
				'order'=>function ($query) {$query->andWhere($date)//'BETWEEN','DATE(FROM_UNIXTIME(`created_at`))',$date1,$date2])
					->andWhere(['not in', 'status', [2,8]]);},
				'tovar'])
			//->where(['BETWEEN','DATE(FROM_UNIXTIME(`orders`.`created_at`))',$date1,$date2])
			->groupBy('tovar_id')
			->all();*/
			
			/*
			$db = Yii::$app->db;
			$result = $db->createCommand("SELECT * from `tovar_rashod`
				left join `orders` on `orders`.`id` = `tovar_rashod`.`order_id`
				left join `tovar` on `tovar`.`id` = `tovar_rashod`.`tovar_id`
				WHERE $date and `orders`.`status` not in (2,8) 
				group by tovar_id")->queryAll();
				*/
			 //\yii\helpers\VarDumper::dump($results,5,true);//die;
		}
		return $this->render('tovaradv',['model'=>$mdl, 'results'=>$results, 'errors'=>$errors, 'categories'=>$categories]);
	}
	
	public function actionInout() {
		$result = $errors = [];
        $date1 = $date2 = '';
        $model = new ReportTovarInOut();
        
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {	
			$date1 = $model->date1;
			$date2 = $model->date2;			
		}
		else {
			$date1 = $model->date1 = date('Y-m-01');			
			$date2 = $model->date2 = date('Y-m-d', strtotime('+1 day'));
		}
		$date_t = "and DATE(FROM_UNIXTIME(`tovar_rashod`.`created_at`)) BETWEEN '$date1' AND '$date2'";//расход
		$date_d = "and DATE(`date_at`) BETWEEN '$date1' AND '$date2'";//приход	
		
		$current_shop = Yii::$app->params['user.current_shop'];
		$sql = "select *, CONCAT((before_cnt_prihod - before_cnt_rashod), ' / ', (before_sum_prihod - before_sum_rashod)) as before_ostatok,  CONCAT((after_cnt_prihod - after_cnt_rashod), ' / ', (after_sum_prihod - after_sum_rashod)) as after_ostatok
		from 
		(select tovar.id as tovar_id, tovar.name as tovar_name, sklad.id as sklad_id, sklad.name as sklad_name,		
			(select IFNULL(sum(amount), 0) from tovar_prihod where tovar_prihod.tovar_id = tovar.id and tovar_prihod.sklad_id = sklad.id and DATE(`date_at`) < '$date1') as before_cnt_prihod,
			(select IFNULL(sum(amount*price_sale), 0) from tovar_prihod where tovar_prihod.tovar_id = tovar.id and tovar_prihod.sklad_id = sklad.id and DATE(`date_at`) < '$date1') as before_sum_prihod,			
			(select IFNULL(sum(amount), 0) from tovar_prihod where tovar_prihod.tovar_id = tovar.id and tovar_prihod.sklad_id = sklad.id and DATE(`date_at`) < '$date2') as after_cnt_prihod,
			(select IFNULL(sum(amount*price_sale), 0) from tovar_prihod where tovar_prihod.tovar_id = tovar.id and tovar_prihod.sklad_id = sklad.id and DATE(`date_at`) < '$date2') as after_sum_prihod,
			(select IFNULL(sum(amount), 0) from tovar_rashod,orders where tovar_rashod.tovar_id = tovar.id and tovar_rashod.order_id = orders.id and orders.status=6 and tovar_rashod.sklad_id = sklad.id and DATE(FROM_UNIXTIME(`tovar_rashod`.`created_at`)) < '$date1') as before_cnt_rashod,
			(select IFNULL(sum(amount*price), 0) from tovar_rashod, orders where tovar_rashod.tovar_id = tovar.id and tovar_rashod.order_id = orders.id and orders.status=6 and tovar_rashod.sklad_id = sklad.id and DATE(FROM_UNIXTIME(`tovar_rashod`.`created_at`)) < '$date1') as before_sum_rashod,
			(select IFNULL(sum(amount), 0) from tovar_rashod,orders where tovar_rashod.tovar_id = tovar.id and tovar_rashod.order_id = orders.id and orders.status=6 and tovar_rashod.sklad_id = sklad.id and DATE(FROM_UNIXTIME(`tovar_rashod`.`created_at`)) < '$date2') as after_cnt_rashod,
			(select IFNULL(sum(amount*price), 0) from tovar_rashod, orders where tovar_rashod.tovar_id = tovar.id and tovar_rashod.order_id = orders.id and orders.status=6 and tovar_rashod.sklad_id = sklad.id and DATE(FROM_UNIXTIME(`tovar_rashod`.`created_at`)) < '$date2') as after_sum_rashod,
			(select IFNULL(sum(amount), 0) from tovar_prihod where tovar_prihod.tovar_id = tovar.id and tovar_prihod.sklad_id = sklad.id $date_d) as cnt_prihod,
			(select IFNULL(sum(amount*price_sale), 0) from tovar_prihod where tovar_prihod.tovar_id = tovar.id and tovar_prihod.sklad_id = sklad.id $date_d) as sum_prihod,		
			(select IFNULL(sum(amount), 0) from tovar_rashod, orders where tovar_rashod.tovar_id = tovar.id and tovar_rashod.order_id = orders.id and orders.status=6 and tovar_rashod.sklad_id = sklad.id $date_t) as cnt_rashod,
			(select IFNULL(sum(amount*price), 0) from tovar_rashod, orders where tovar_rashod.tovar_id = tovar.id and tovar_rashod.order_id = orders.id and orders.status=6 and tovar_rashod.sklad_id = sklad.id $date_t) as sum_rashod	
			from tovar, sklad			
			where tovar.active=1
			and tovar.shop_id = $current_shop
			and sklad.shop_id = $current_shop						
			group by tovar.id, sklad.id) a";/*and (
			 (select sum(amount) from tovar_prihod where tovar_prihod.tovar_id = tovar.id and tovar_prihod.sklad_id = sklad.id $date_d) is not null
			  or (select sum(amount) from tovar_rashod,orders where tovar_rashod.tovar_id = tovar.id and tovar_rashod.order_id = orders.id and orders.status=6 and tovar_rashod.sklad_id = sklad.id $date_t) is not null)*/
			
		//echo $sql;
		$db = Yii::$app->db;
		$count = $db->createCommand($sql)->queryColumn();
		$count = count($count);

		$provider = new SqlDataProvider([
		    'sql' => $sql,
		    //'params' => [':status' => 1],
		    'totalCount' => $count,
		    'pagination' => [
		        'pageSize' => 50,
		    ],
		    'sort' => [
		        'attributes' => [
		            'tovar_name',		           
		            'sklad_name',
		            'before_ostatok',
		            'after_ostatok',
		            'cnt_prihod',
		            'sum_prihod',
		            'cnt_rashod',
		            'sum_rashod',
		        ],
		    ],
		]);

		// returns an array of data rows
		//$models = $provider->getModels();
				
		return $this->render('inout',['model'=>$model, 'results'=>$results, 'provider'=>$provider]);

	}
	
	public function actionSvod() {
		$model = new ReportSvod();        
        
        $senders = \app\models\Senders::find()->select(['name', 'id'])->indexBy('id')->column();
		$categories = \app\models\Category::find()->select(['name', 'id'])->where(['shop_id'=>$this->shop_id])->indexBy('id')->column();
		$sklads = \app\models\Sklad::find()->select(['name', 'id'])->where(['shop_id'=>$this->shop_id])->indexBy('id')->column();
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {	
			$date1 = $model->date1;
			$date2 = $model->date2;			
		}
		else {
			$date1 = $model->date1 = date('Y-m-01');			
			$date2 = $model->date2 = date('Y-m-d');//, strtotime('+1 day'));
		}
		
		$query = TovarRashod::find()
			//->select('tovar_id')			
			->joinWith(['order','tovar'])
			->select(['sum(tovar_rashod.price*tovar_rashod.amount) as summ'])
			->addSelect(['sum(amount) as cnt'])
			->addSelect(['tovar.name'])
			->addSelect(['tovar.artikul'])
			->where(['orders.status'=>6])
			->andWhere(['otpravlen'=>1])    			
			//->andWhere(['dostavlen'=>0])
			->andWhere(['orders.shop_id' => $this->shop_id])
			->andFilterWhere(['tovar_rashod.sklad_id' => $model->sklad_id])
			->andWhere(['BETWEEN','DATE(orders.data_otprav)',$date1,$date2]);
			
			if(!empty($model->sender_id)) {
				$query->andWhere(['orders.sender_id'=>$model->sender_id]);
			}
			
			$results = $query->groupBy('tovar_id')->asArray()->all();
		
		return $this->render('svod',['model'=>$model, 'results'=>$results, 'senders'=>$senders, 'provider'=>$provider, 'sklads'=>$sklads]);
	}
	/*
	select tovar_prihod.id as in_id, tovar_prihod.date_at as in_date, tovar_prihod.price as in_price, tovar_prihod.amount as in_amount, tovar_prihod.tovar_id as in_tovar, tovar_prihod.sklad_id as in_sklad, tovar_rashod.id as out_id, date(from_unixtime(tovar_rashod.created_at)) as out_date, tovar_rashod.price as out_price, tovar_rashod.amount as out_amount, tovar_rashod.tovar_id as out_tovar, tovar_rashod.sklad_id as out_sklad, sklad.id as sklad_id, sklad.name as sklad_name, tovar.id as tovar_id, tovar.name as tovar_name from `tovar_prihod` left join `tovar_rashod` on tovar_prihod.tovar_id = tovar_rashod.tovar_id left join tovar on `tovar`.`id` = `tovar_prihod`.`tovar_id` left join sklad on sklad.id = tovar_prihod.sklad_id where tovar_prihod.sklad_id = tovar_rashod.sklad_id
	*/
	
	public function actionDosales() {
		$model = new ReportDosales();        
        $current_shop = Yii::$app->params['user.current_shop'];
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {	
			$date1 = $model->date1;
			$date2 = $model->date2;
			$c_id = $model->category_id;
			
			$results = Orders::find()
			->select('orders.id')			
			->joinWith(['rashod','rashod.tovar'])
			->where(['orders.status'=>6])
			->andWhere(['orders.shop_id'=>$current_shop])
			->andWhere(['BETWEEN','orders.date_at',$date1,$date2])
			->asArray()
			->all();
			
			$result = [];
			$base = $dosale = [];
			foreach($results as $res) {
				$t_base = $t_dosale = [];
				foreach($res['rashod'] as $r) {			
					if($r['tovar']['category_id'] == $c_id) {					
						/*if(!array_key_exists($r['tovar']['name'],$t_dosale)){
							//$t_dosale = $r['tovar']['name'];
							$t_dosale[$r['tovar']['name']]['price'] = $r['price'];
							$t_dosale[$r['tovar']['name']]['pprice'] = $r['tovar']['pprice'];
							$t_dosale[$r['tovar']['name']]['amount'] = $r['amount'];						
						}
						else*/
						{
							$t_dosale[$r['tovar']['name']]['price'] = $t_dosale[$r['tovar']['name']]['price'] + $r['price'];
							$t_dosale[$r['tovar']['name']]['pprice'] = $t_dosale[$r['tovar']['name']]['pprice'] + $r['tovar']['pprice'];
							$t_dosale[$r['tovar']['name']]['amount'] = $t_dosale[$r['tovar']['name']]['amount'] + $r['amount'];
						}											
					}
				}
				foreach($res['rashod'] as $r) {			
					if($r['tovar']['category_id'] != $c_id and !empty($t_dosale)) {
						//if(!array_key_exists($r['tovar']['name'],$base))
						$base[$r['tovar']['name']][] = $t_dosale;
					}
				}
				
			}
			
			foreach($base as $k => $v) {
				$t = [];
				foreach($v as $vv){ 
					foreach($vv as $k1=>$v1){//echo '<pre>';print_r($k1);echo '</pre>';
						$t[$k1]['price'] = $t[$k1]['price'] + $v1['price'];
						$t[$k1]['pprice'] = $t[$k1]['pprice'] + $v1['pprice'];
						$t[$k1]['amount'] = $t[$k1]['amount'] + $v1['amount'];	
					}
										
				}
				$base[$k] = $t;
				
			}
		}
		else {
			$date1 = $model->date1 = date('Y-m-01');			
			$date2 = $model->date2 = date('Y-m-d', strtotime('+1 day'));
		}		
		
		return $this->render('dosales',['model'=>$model, 'results'=>$base]);
	}
	
	public function actionDeliverycost() {
		$model = new ReportDeliverycost();
		
		$senders = \app\models\Senders::find()->select(['name', 'id'])->indexBy('id')->column();
		$categories = \app\models\Category::find()->select(['name', 'id'])->where(['shop_id'=>$this->shop_id])->indexBy('id')->column();
      
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {	
			$date1 = $model->date1;
			$date2 = $model->date2;
		}
		else {
			$date1 = $model->date1 = date('Y-m-01');			
			$date2 = $model->date2 = date('Y-m-d', strtotime('+1 day'));
		}
		
		if(!empty($model->sender_id)) $sender[$model->sender_id] = $senders[$model->sender_id];
		else $sender = $senders;
		$in_category = null;
		if(!empty($model->category_id)) $in_category = $model->category_id;			
		
		//echo '<pre>';print_r($query);echo '</pre>';	
		foreach($sender as $k=>$v) {
			$query = Orders::find()
			->joinWith(['rashod.tovar'])
			->where(['orders.status'=>6])
			->andWhere(['otpravlen'=>1])    						
			->andWhere(['orders.shop_id'=>$this->shop_id])
			->andWhere(['BETWEEN','orders.date_at',$date1,$date2])
			->andWhere(['sender_id'=>$k]);
						
			if(!is_null($in_category)) {
				$query->andWhere(['tovar.category_id'=>$in_category]);
				$query->groupBy(['orders.id']);
			}			
			
			$results[$v] = $query->average('summaotp');			
		}			
		
		return $this->render('deliverycost',['model'=>$model, 'results'=>$results, 'senders'=>$senders, 'categories'=>$categories]);
	}
	public function actionShipped() {
		$model = new ReportDeliverycost();
		
		$senders = \app\models\Senders::find()->select(['name', 'id'])->indexBy('id')->column();
		$categories = \app\models\Category::find()->select(['name', 'id'])->where(['shop_id'=>$this->shop_id])->indexBy('id')->column();
      
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {	
			$date1 = $model->date1;
			$date2 = $model->date2;
		}
		else {
			$date1 = $model->date1 = date('Y-m-01');			
			$date2 = $model->date2 = date('Y-m-d', strtotime('+1 day'));
		}
		
		if(!empty($model->sender_id)) $sender[$model->sender_id] = $senders[$model->sender_id];
		else $sender = $senders;
		$in_category = null;
		if(!empty($model->category_id)) $in_category = $model->category_id;			
		
		//echo '<pre>';print_r($query);echo '</pre>';
		$qTotalSumm = (new \yii\db\Query())->select(['SUM(`tovar_rashod`.`price` * `tovar_rashod`.`amount`) - IFNULL(`orders`.`discount`, 0)'])->from('tovar_rashod')->where('tovar_rashod.order_id = orders.id');
		foreach($sender as $k=>$v) {
			$query = Orders::find()->select(['sum(`tovar_rashod`.`price` * `tovar_rashod`.`amount`) - IFNULL(`orders`.`discount`, 0) as totalSumm'])
			//->select(['totalSumm'=>$qTotalSumm])
			->joinWith(['rashod', 'rashod.tovar'])
			->where(['orders.status'=>6])
			->andWhere(['otpravlen'=>1])    						
			->andWhere(['orders.shop_id'=>$this->shop_id])
			->andWhere(['BETWEEN','orders.data_vozvrat',$date1,$date2])
			->andWhere(['sender_id'=>$k]);
						
			if(!is_null($in_category)) {
				$query->andWhere(['tovar.category_id'=>$in_category]);
				$query->groupBy(['orders.id']);
			}			
			
			$results[$v] = $query->scalar();//$query->sum('totalSumm');
			
			//echo '<pre>';print_r($results);echo '</pre>';
		}			
		
		return $this->render('shipped',['model'=>$model, 'results'=>$results, 'senders'=>$senders, 'categories'=>$categories]);
	}
	/**
	* отказы
	* 
	* @return
	*/
	public function actionRefused() {
		$model = new ReportRefused();
		
		$refused = new Orders();
		$refused = $refused->itemAlias('prich_double');
		$categories = \app\models\Category::find()->select(['name', 'id'])->where(['shop_id'=>$this->shop_id])->indexBy('id')->column();
      
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {	
			$date1 = $model->date1;
			$date2 = $model->date2;
		}
		else {
			$date1 = $model->date1 = date('Y-m-01');			
			$date2 = $model->date2 = date('Y-m-d', strtotime('+1 day'));
		}
		
		if(!empty($model->refused_id)) $refuse[$model->refused_id] = $refused[$model->refused_id];
		else $refuse = $refused;
		$in_category = null;
		if(!empty($model->category_id)) $in_category = $model->category_id;			
		
		//echo '<pre>';print_r($query);echo '</pre>';
		//$qTotalSumm = (new \yii\db\Query())->select(['SUM(`tovar_rashod`.`price` * `tovar_rashod`.`amount`) - IFNULL(`orders`.`discount`, 0)'])->from('tovar_rashod')->where('tovar_rashod.order_id = orders.id');
		foreach($refuse as $k=>$v) {
			$query = Orders::find()->select(['sum(`tovar_rashod`.`price` * `tovar_rashod`.`amount`) - IFNULL(`orders`.`discount`, 0) as totalSumm'])
			//->select(['totalSumm'=>$qTotalSumm])
			->joinWith(['rashod', 'rashod.tovar'])
			//->where(['orders.status'=>6])
			//->andWhere(['otpravlen'=>1])    						
			->where(['orders.shop_id'=>$this->shop_id])
			->andWhere(['BETWEEN','orders.date_at',$date1,$date2])
			->andWhere(['prich_double'=>$k]);
						
			if(!is_null($in_category)) {
				$query->andWhere(['tovar.category_id'=>$in_category]);				
			}
					
			$query->groupBy(['orders.id']);
			$results[$v] = $query->all();//$query->scalar();//$query->sum('totalSumm');
			
			//echo '<pre>';print_r($results);echo '</pre>';
		}			
		
		return $this->render('refused',['model'=>$model, 'results'=>$results, 'refused'=>$refused, 'categories'=>$categories]);
	}
	
	/**
	* 
	* 
	* @return array
	*/
	public function actionTovardo() {
		$model = new ReportDosales();        
        $current_shop = Yii::$app->params['user.current_shop'];
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {	
			$date1 = $model->date1;
			$date2 = $model->date2;
			$c_id = $model->category_id;
			
			$results = Orders::find()
			->select('orders.id, summaotp')			
			->joinWith(['rashod','rashod.tovar'])
			->where(['orders.status'=>6])
			->andWhere(['orders.shop_id'=>$current_shop])
			->andWhere(['BETWEEN','orders.date_at',$date1,$date2])
			->asArray()
			->all();
			
			$result = [];
			$base = $dosale = [];
			//цикл по заявкам
			foreach($results as $res) {
				$t_base = $t_dosale = [];
				//цикл по апселлам
				foreach($res['rashod'] as $r) {			
					$t = [];
					if($r['tovar']['category_id'] == $c_id) {					
						/*if(!array_key_exists($r['tovar']['name'],$t_dosale)){
							//$t_dosale = $r['tovar']['name'];
							$t_dosale[$r['tovar']['name']]['price'] = $r['price'];
							$t_dosale[$r['tovar']['name']]['pprice'] = $r['tovar']['pprice'];
							$t_dosale[$r['tovar']['name']]['amount'] = $r['amount'];						
						}
						else*/
						{
							$t_dosale['price'] = $t_dosale['price'] + ($r['price'] * $r['amount']);
							$t_dosale['pprice'] = $t_dosale['pprice'] + ($r['tovar']['pprice'] * $r['amount']);
							/*$t['pprice'] = $r['tovar']['pprice'];
							$t['amount'] = $r['amount'];
							$t['name'] = $r['tovar']['name'];
							$t['artikul'] = $r['tovar']['artikul'];
							$t_dosale = $t;*/
						}											
					}
				}
				//цикл по товарам
				foreach($res['rashod'] as $r) {			
					if($r['tovar']['category_id'] != $c_id) {// and !empty($t_dosale)
						//if(!array_key_exists($r['tovar']['name'],$base))
						$base[$r['tovar']['artikul']]['price'] = $base[$r['tovar']['artikul']]['price'] + ($r['price'] * $r['amount']);
						$base[$r['tovar']['artikul']]['pprice'] = $base[$r['tovar']['artikul']]['pprice'] + ($r['tovar']['pprice'] * $r['amount']);
						$base[$r['tovar']['artikul']]['amount'] = $base[$r['tovar']['artikul']]['amount'] + $r['amount'];
						$base[$r['tovar']['artikul']]['name'] = $r['tovar']['name'];
						$base[$r['tovar']['artikul']]['id'] = $r['tovar']['id'];
						$base[$r['tovar']['artikul']]['dosales'][] = $t_dosale;
						$base[$r['tovar']['artikul']]['summaotp'][$r['id']] = $res['summaotp'];
					}
				}
				
			}

			//пересоберем апселлы и сумму отправки + реклама
			foreach($base as $k => $v) {
				$t = []; $summaotp = $prihod = $rashod = 0;
				foreach($v['dosales'] as $vv){
					$t['price'] = $t['price'] + $vv['price'];
					$t['pprice'] = $t['pprice'] + $vv['pprice'];
					//$t[$k1]['amount'] = $t[$k1]['amount'] + $v1['amount'];										
				}
				$base[$k]['dosales'] = $t;
				
				foreach($v['summaotp'] as $k1=>$v1){
					$summaotp = $summaotp + $v1;//echo '<pre>';print_r($v1);echo '</pre>';
				}
				$base[$k]['summaotp'] = $summaotp;
				
				$base[$k]['reklama'] = Statcompany::find()
					->where(['tovar_id'=>$v['id']])
					->andWhere(['shop_id'=>$current_shop])
					->andWhere(['BETWEEN','date_at',$date1,$date2])
					->sum('costs');
					
				$prihod = TovarPrihod::find()->where(['tovar_id'=>$v['id']])->andWhere(['<','date_at',$date2])->sum('amount');
				$rashod = TovarRashod::find()->joinWith(['order'])->where(['orders.status'=>6])->andWhere(['otpravlen'=>1])->andWhere(['orders.shop_id'=>$current_shop])->andWhere(['BETWEEN','orders.data_otprav',$date1,$date2])->andWhere(['tovar_id'=>$v['id']])->sum('amount');
				$base[$k]['ostatok'] = $prihod - $rashod;
								
				$vozvrat = TovarRashod::find()->select(['sum(amount * price) as price, sum(amount) as amount'])->joinWith(['order'])->where(['orders.status'=>6])->andWhere(['otpravlen'=>1])->andWhere(['vozvrat'=>1])->andWhere(['orders.shop_id'=>$current_shop])->andWhere(['BETWEEN','orders.data_vozvrat',$date1,$date2])->andWhere(['tovar_id'=>$v['id']])->one();//sum('amount * price');
				
				$base[$k]['vozvrat']['amount'] = $vozvrat->amount;
				$base[$k]['vozvrat']['price'] = $vozvrat->price;
				//$base[$k]['vozvrat']['summ'] = $vozvrat->summ;
				
			}
		}
		else {
			$date1 = $model->date1 = date('Y-m-01');			
			$date2 = $model->date2 = date('Y-m-d', strtotime('+1 day'));
		}
		ksort($base);
		//echo '<pre>';print_r($base);echo '</pre>';
		return $this->render('tovardo',['model'=>$model, 'results'=>$base]);
	}
	
    /**
	* получить инфу счетчиков с метрики
	* 
	* @url string $url
	* @return array
	*/
	private function _get_metrika($url=false) {		
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
