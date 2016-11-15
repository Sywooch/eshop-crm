<?php

namespace app\controllers;

use Yii;
use app\models\Statcompany;
use app\models\StatcompanySearch;
use app\models\AdvertSearch;
use app\models\AdvertForm;
use app\models\AdvertUploadStat;
use app\models\Category;
use app\models\Tovar;
use app\models\UtmLabel;
//use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\SqlDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;

/**
 * AdvertController implements the CRUD actions for Statcompany model.
 */
class AdvertController extends \app\components\BaseController
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
        ];
    }

    /**
     * Lists all Statcompany models.
     * @return mixed
     */
     
	public function actionIndex1()
    {
        $searchModel = new AdvertSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->setPagination([
			'pageSize' => 50,
	    ]);
        $dataProvider->setSort(['defaultOrder' => ['statcompany.id'=>SORT_DESC],]);
        
        return $this->render('index1', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        
        // returns an array of data rows
		//$models = $provider->getModels();
				
		//return $this->render('index1',['model'=>$model, 'dataProvider'=>$dataProvider]);
    }

     
    public function actionIndex()
    {
        $result = $errors = [];
        $date1 = $date2 = $wherecamp = '';
        $group = 's.id_company';
        $where = "u.utm_campaign LIKE CONCAT('%',s.id_company,'%')";
        $model = new AdvertForm();
        
        
        if ($model->load(Yii::$app->request->queryParams) && $model->validate()) {	
			$date1 = $model->date1;
			$date2 = $model->date2;
			if(!empty($model->campaign)) $wherecamp = 'and id_company = "'.$model->campaign.'"';
			
		}
		else {
			$date1 = $model->date1 = date('Y-m-d');			
			$date2 = $model->date2 = date('Y-m-d', strtotime('+1 day'));
		}
		//$date_t = "and DATE(FROM_UNIXTIME(`tovar_rashod`.`created_at`)) BETWEEN '$date1' AND '$date2'";//расход
		$date_o = "and DATE(o.date_at) BETWEEN '$date1' AND '$date2'";//приход	
		$date_s = "and DATE(s.date_at) BETWEEN '$date1' AND '$date2'";//приход	
		
		//$current_shop = Yii::$app->params['user.current_shop'];
		if(!empty($model->host)) {
			$sql = "SELECT '$date1' as date1, '$date2' as date2, date_at, id_company, name, sum(shows) as shows, sum(clicks) as clicks, sum(costs) as costs, host, s.source,
				(SELECT count(o.id) FROM orders o WHERE o.status NOT IN (2,8) AND o.url = s.host $date_o) as cnt_za,
				(SELECT count(o.id) FROM orders o WHERE o.status = 6 AND o.url = s.host $date_o) as cnt_zz
			 FROM statcompany s
			 WHERE s.shop_id = '$this->shop_id' $date_s
			 GROUP BY s.host
			 ";		
		}
		else {		
			$sql = "SELECT '$date1' as date1, '$date2' as date2, date_at, id_company, name, sum(shows) as shows, sum(clicks) as clicks, sum(costs) as costs, host, s.source,
			(SELECT count(o.id) FROM utm_label u LEFT JOIN orders o ON o.id = u.order_id WHERE o.status NOT IN (2,8) AND u.utm_campaign LIKE CONCAT('%',s.id_company,'%') $date_o) as cnt_za,
			(SELECT count(o.id) FROM utm_label u LEFT JOIN orders o ON o.id = u.order_id WHERE o.status = 6 AND u.utm_campaign LIKE CONCAT('%',s.id_company,'%') $date_o) as cnt_zz
		 FROM statcompany s
		 WHERE s.shop_id = '$this->shop_id' $date_s $wherecamp
		 GROUP BY s.id_company
		 ";//s.id_company = u.utm_campaign
		}		 
		$count = "SELECT count(*) FROM statcompany s WHERE s.shop_id = '$current_shop' $date_s $wherecamp GROUP BY id_company ";
		
	
		$db = Yii::$app->db;
		//echo $db->createCommand($count)->queryScalar();
		$count = $db->createCommand($count)->queryColumn();
		$count = count($count);

		$provider = new SqlDataProvider([
		    'sql' => $sql,
		    //'params' => [':status' => 1],
		    'totalCount' => $count,
		    'pagination' => [
		        'pageSize' => 200,
		    ],
		    'sort' => [
		        'attributes' => [
		            'id_company',
		            'name',		           
		            'shows',
		            'clicks',
		            'costs',		            
		            'host',
		            'source',
		            'cnt_za',
		            'cnt_zz',
		        ],
		    ],
		]);

		// returns an array of data rows
		//$models = $provider->getModels();
				
		return $this->render('index',['model'=>$model, 'provider'=>$provider]);
    }

	public function actionCampaign($idc=null,$date1=null,$date2=null)
    {      
		//$idc = (int)$idc;
		$form = new AdvertForm();
		$form->date1 = $date1;
		$form->date2 = $date2;
		if(!is_null($idc)) $form->campaign = $idc;
		$wherecamp = '';
		$provider = $dataProvider = null;
        
        $form->load(Yii::$app->request->queryParams);
        
        if ($form->validate()) {	
			$date1 = $form->date1;
			$date2 = $form->date2;
			if(!empty($form->campaign)) $idc = $form->campaign;		
		}
		else {
			$date1 = $form->date1 = date('Y-m-01');//, strtotime('-1 day'));			
			$date2 = $form->date2 = date('Y-m-d');
		}
		
		if(!is_null($idc)) {
			
		//$date_t = "and DATE(FROM_UNIXTIME(`tovar_rashod`.`created_at`)) BETWEEN '$date1' AND '$date2'";//расход
		$date_o = "and DATE(o.date_at) BETWEEN '$date1' AND '$date2'";//приход	
		$date_s = "and DATE(s.date_at) BETWEEN '$date1' AND '$date2'";//приход	
				
		$curshop = Yii::$app->params['user.current_shop'];
		$shop = "and o.shop_id = '$curshop'";
		
		$sql = "SELECT '$date1' as date1, '$date2' as date2, date_at, id_company, name, sum(shows) as shows, sum(clicks) as clicks, sum(costs) as costs, host, s.source,
			(SELECT count(u.id) FROM utm_label u LEFT JOIN orders o ON o.id = u.order_id WHERE o.status NOT IN (2,8) AND u.utm_campaign LIKE CONCAT('%',s.id_company,'%') $date_o $shop) as cnt_za,
			(SELECT count(u.id) FROM utm_label u LEFT JOIN orders o ON o.id = u.order_id WHERE o.status = 6 AND u.utm_campaign LIKE CONCAT('%',s.id_company,'%') $date_o $shop) as cnt_zz
		 FROM statcompany s
		 WHERE s.shop_id = '$curshop' $date_s AND id_company = '$idc'
		 GROUP BY s.id_company
		 ";//s.id_company = u.utm_campaign
		 
		//$count = "SELECT count(*) FROM statcompany s WHERE s.shop_id = '$current_shop' $date_s AND id_company = '$idc' GROUP BY id_company";
	
		$db = Yii::$app->db;		

		$provider = new SqlDataProvider([
		    'sql' => $sql,
		    'totalCount' => 2,	    		    
		]);
		
		$model = UtmLabel::find()->joinWith('order')->select('utm_label.*, orders.status')->where(['orders.shop_id'=>$curshop])->andWhere(['like','utm_campaign',$idc])->andWhere(['between','orders.date_at',$date1,$date2])->andWhere(['not in','status',[2,8]])->orderBy(['date_at' => SORT_DESC]);
		$dataProvider = new ActiveDataProvider([
            'query' => $model,
            'pagination' => [
		        'pageSize' => 200,
		    ],
            'sort' => [
				'defaultOrder' => ['date_at' => SORT_DESC],
            ]
        ]);
        $dataProvider->sort->attributes['order.status'] = [
		    'asc' => ['orders.status' => SORT_ASC],
		    'desc' => ['orders.status' => SORT_DESC],
		];
        
        return $this->render('campaign', ['model' => $form, 'dataProvider' => $dataProvider, 'provider'=>$provider,'idc'=>$idc]);
        
        }//if idc
        else 
        return $this->render('campaign', ['model' => $form, 'dataProvider' => null, 'provider'=>null,'idc'=>null]);
    }
    /**
     * Displays a single Statcompany model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

	public function actionImportstat()
    {    	
    	$errors = $result = [];
    	$temp = null;
   		$shop_id = Yii::$app->params['user.current_shop'];
   	
    	$mdlUpload = new AdvertUploadStat();

    	if($mdlUpload->load(Yii::$app->request->post()))//(Yii::$app->request->isPost)
    	{
            $mdlUpload->statFile = UploadedFile::getInstance($mdlUpload, 'statFile');
            
            if ($mdlUpload->upload()) {        
                //\yii\helpers\VarDumper::dump($mdlUpload,7,true);
				$xls = $mdlUpload->statFile->tempName;	       		        
				$objPHPExcel = \PHPExcel_IOFactory::load($xls);		
				$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);				
				
				$n = $ns = 0;
				foreach($sheetData as $row) {		//\yii\helpers\VarDumper::dump($row,7,true);							
					//$host = $name = $date = $id_company = false;
					$data = [];
					
					if (is_numeric($row['B']))					
						$id_company = (int) $row['B'];
					
					if (is_string($row['A']))
						$name = $row['A'];
					
					if (date_create_from_format('d.m.Y', $row['C'])) {
						$date = date_format(date_create_from_format('d.m.Y', $row['C']), 'Y-m-d');						
					}
					else {
						$id_company = false;
						$name = false;				
					}

				/*
					$host = Websites::findOne(['host' => $ar_name['2'], 'shop_id' => $shop_id]);
					if(!is_null($host)) 
						$data['site_id'] = $host->id;
					else $host = false;
				*/
				//\yii\helpers\VarDumper::dump($data,7,true);
					if ($id_company && $name && $date)// && $host)
					{
						//яндекс, точняк
						$data['source'] = 'yandex';
						$ar_name = explode(' ',$name);
						//$host =  filter_var('http://'.$ar_name['2'], FILTER_VALIDATE_URL);
						//if($ar_name['0'] == 'rus' or $ar_name['0'] == 'msk')
						{
							$category = $ar_name['1'];
							$host = $ar_name['2'];
							$art = $ar_name['3'];
						}
						/*else {
							$category = $ar_name['0'];
							$host = $ar_name['1'];
							$art = $ar_name['2'];
						}*/					
						$data['id_company'] = $id_company;
						$data['name'] = $name;
						$data['date_at'] = $date;
						$data['shows'] = $row['D'];
						$data['clicks'] = $row['E'];
						$data['costs'] = $row['G'];
						
						
						
						//%region = $ar_name['0'];
						
						//$category = mb_substr(strval($ar_name['1']),0,-1);
						$category = mb_substr(strval($category),0,-1);
						$temp = Category::find()->where("name like '$category%'")->andWhere(['shop_id' => $shop_id])->one();
						if(!is_null($temp)) 
							$data['category_id'] = $temp->id;
						elseif(!empty($row['F']))
							$data['category_id'] = $row['F'];
							
						//$data['site_id'] = filter_var('http://'.$host, FILTER_VALIDATE_URL);
						if(\app\components\Tools::is_url($host) == 1) $data['host'] = $host;
															
						//$temp = Tovar::findOne(['artikul' => \app\components\Tools::art($art), 'shop_id' => $shop_id]);
						$temp = Tovar::findOne(['artikul' => $art, 'shop_id' => $shop_id]);
						if(!is_null($temp)) 
							$data['tovar_id'] = $temp->id;
						//мало ли что
						else
							//$temp = Tovar::findOne(['artikul' => \app\components\Tools::art($host), 'shop_id' => $shop_id]);
							$temp = Tovar::findOne(['artikul' => $host, 'shop_id' => $shop_id]);
						if(!is_null($temp)) 
							$data['tovar_id'] = $temp->id;
				//\yii\helpers\VarDumper::dump($temp,7,true);		 
						$data['shop_id'] = $shop_id;
						if(!array_key_exists('source', $data)) $data['source'] = $mdlUpload->source;
						
						$model = new Statcompany();
						
						$model->attributes = $data;
						if ($model->validate()) {
							$verify = Statcompany::findOne([
							    'id_company' => $data['id_company'],
							    'date_at' => $data['date_at'],
							]);
							if(!is_null($verify)) {
								$verify->attributes = $data;
								$verify->save();
							}
							else {
								$model->save();
							}							
							$result[] = $data;
						} else {
						    // проверка не удалась:  $errors - это массив содержащий сообщения об ошибках
						    $errors[$data['id_company']] = $model->errors;
						}
						/*
						$transaction = Statcompany::getDb()->beginTransaction();
						try {
						    $model->load($data);
						    $customer->save();
						    // ...другие операции с базой данных...
						    $transaction->commit();
						} catch(\Exception $e) {
						    $transaction->rollBack();
						    throw $e;
						}
						*/
                                                $ns++;
					}
					$n++;
					//if($n==500) die;//break;
				}				
                                if($ns == 0) {$errors[] = 'Нет данных либо данные не загружены';}
                            if(count($errors) > 0) {\Yii::$app->session->addFlash('error', $errors);}
                                
			    $provider = new ArrayDataProvider([
				    'allModels' => $result,
				    'pagination' => [
				        'pageSize' => 1000,
				    ],
				    /*'sort' => [
				        'attributes' => ['id', 'name'],
				    ],*/
				]);
				$_POST = null;
				return $this->render('importstat', [
		    		//'errors' => $errors,
		    		'data' => $result,
		    		'provider' => $provider,
		    		'model'=>$mdlUpload
				]);
        		
			}	        
	    }
	           
        else {
			return $this->render('formUploadStat', ['model'=>$mdlUpload]);
		}

	}
	


    /**
     * Finds the Statcompany model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Statcompany the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Statcompany::find()->where(['shop_id'=>Yii::$app->params['user.current_shop']])->andWhere(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
