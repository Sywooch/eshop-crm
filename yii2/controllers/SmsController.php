<?php

namespace app\controllers;

use Yii;
use app\models\Sms;
use app\models\SmsSearch;
use app\models\SmsMailing;
use app\models\Orders;
use app\models\Client;
use app\models\Category;
//use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
/**
 * SmsController implements the CRUD actions for Sms model.
 */
class SmsController extends \app\components\BaseController
{
/*	public function behaviors()
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
*/
    /**
     * Lists all Sms models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SmsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams); 
        $dataProvider->setSort(['defaultOrder' => ['id'=>SORT_DESC],]);
        
		$e = \app\models\Settings::getKey('sms.send.event');
		$searchModel->eventlist = $e;
//\yii\helpers\VarDumper::dump(Yii::$app->sms->sms_send('9174128873', 'test'),20,true);//die;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sms model.
     * @param integer $id
     * @return mixed
     */
/*	public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
*/
    /**
     * Creates a new Sms model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
/*    public function actionCreate()
    {
        $model = new Sms();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
*/
    /**
     * Updates an existing Sms model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionConfig()
    {       
       	//$model = new SmsSearch();   

        //if ($model->load(Yii::$app->request->post()))
        if (isset($_POST['SmsSearch']) and array_key_exists('eventlist', $_POST['SmsSearch']))    
        {
        	$r =  \app\models\Settings::setKey('sms.send.event', $_POST['SmsSearch']['eventlist']);
        	if ($r ===true) {
        		Yii::$app->session->setFlash('success', 'Настройки сохранены');
        	}
        	else {
				Yii::$app->session->setFlash('error', print_r($r,true));
			}
		}
		else {
			Yii::$app->session->setFlash('info', 'Нет данных для сохранения');
		}          
        return $this->redirect(['index']);
    }
    
    public function actionMailing() {
    	$model = new SmsMailing();        
        //$current_shop = Yii::$app->params['user.current_shop'];
        
        if ($model->load(Yii::$app->request->queryParams) && $model->validate()) {	//Yii::$app->request->post()
			$date1 = $model->date1;
			$date2 = $model->date2;
			$c_id = $model->category;
			if($model->status >0) $status = array('1','4','5','6','7','9');
			else $status = array('6');
			
			$results = Client::find()
			->select('phone, fio')			
			->joinWith(['orders','orders.rashod.tovar'])
			->where(['in', 'orders.status', $status])
			->andWhere(['tovar.category_id' => $model->category])
			->andWhere(['orders.shop_id'=>$this->shop_id])
			->andWhere(['BETWEEN','DATE(orders.date_at)',$date1,$date2])
			->groupBy('phone')
			->asArray()
			->all();
			
			//$results = array_unique($results);
			$model->count = count($results);
			//отправим смс
			if($model->yes == 1) {
				$n=0;
				foreach($results as $res) {
					$sms = new Sms();
					//$to = '89174128873';//89373133871';//$res['phone']
					$to = $res['phone'];
					$response = Yii::$app->sms->sms_send($to, $model->msg);		
					//\yii\helpers\VarDumper::dump($response,10,true);die;
					$sms->status = $response['code'];
					$cost = Yii::$app->sms->sms_cost($to, $msg);
					$sms->cost = $cost['price'];
					//\yii\helpers\VarDumper::dump($response,10,true);die;				
					$sms->sms_id = $response['ids']['0'];			
					//$sms->order_id = null;
					$sms->msg = $model->msg;
					$sms->event = 'mailing';
					$sms->phone = $to;
					//$this->msg = $msg;
					
					if ($sms->save()) {						
						$n++;
					}
					else $er =  print_r($sms->firstErrors,true);
					Yii::$app->session->setFlash('info', 'Обработано смс: '.$n.' '.$er);
			   		//$msg = $sms->sendSms($model, 'mailing');
				}
			}			
			//\yii\helpers\VarDumper::dump($sms,10,true);
			
			$provider = new ArrayDataProvider([
			    'allModels' => $results,
			    'pagination' => [
			        'pageSize' => 500,
			    ],
			    'sort' => [
			        'attributes' => ['phone', 'fio'],
			    ],
			]);
		}
		
		return $this->render('mailing', [
			'model' => $model, 
			'provider' => $provider,
        ]);
	}

    /**
     * Deletes an existing Sms model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
/*    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
*/
    /**
     * Finds the Sms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sms::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
