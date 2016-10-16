<?php

namespace app\controllers;

use Yii;
use app\models\Settings;
use app\models\SettingsYatokenForm;
use yii\data\ActiveDataProvider;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SettingsController implements the CRUD actions for Settings model.
 */
class SettingsController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Settings models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Settings::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Settings model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Settings model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Settings();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Settings model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Settings model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    /**
	* gолучить токен от яндекса для метрики
	* 
	* @return
	*/
    public function actionGetyatoken(){
		ini_set('display_errors', 1);
		/**
		* 
		* get token for metrika 
		* 
		*/
		/*
		$yandex_get_token_url = "https://oauth.yandex.ru/token";
		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $yandex_get_token_url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=password&username=isoteplo&password=russoturisto26#&client_id=fb7f9d89c18841c08e0473d1f4d90e7b&client_secret=15bf06e6874f434eacfa16f8361d39fb');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$token = curl_exec($ch);
		curl_close($ch);
		 
		echo '<pre>';print_r($token);echo '</pre>';

		die;
		*/
		/**
		* 
		* get token for works to YA.direct
		* 
		*/
		/*$type='metrika';
		
		if($type=='metrika') {
			$client_id = 'c1fd20898f4840f9914dbb4ef55e74fa';//Settings::getKey('ya_metrika_id');
			$client_secret = '411257b013fb4c49b8e7987e7da6c363';//Settings::getKey('ya_metrika_pass');
			$client_token = 'ya_metrika_token';
		}
		else {
			$client_id = $sett['yaid'];
			$client_secret = $sett['yapass'];
			$client_token = 'yatok';
		}*/

		//$client_id = '60fa9bb9681f43a69efbcbe9fb4e6459';
		//$client_secret = '9b4c906c97894b0089aa04455f6f87ad';
		$model = new SettingsYatokenForm();
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {			
				Settings::setKey('ya_metrika_id', $model->client_id);
			    Settings::setKey('ya_metrika_pass', $model->client_secret);
			    header('Location: https://oauth.yandex.ru/authorize?response_type=code&client_id='.$model->client_id);
			    die;
		}
		// Если скрипт был вызван с указанием параметра "code" в URL,
		// то выполняется запрос на получение токена
		elseif(isset($_GET['code']))
		{
		    // Формирование параметров (тела) POST-запроса с указанием кода подтверждения
		    $query = array(
		      'grant_type' => 'authorization_code',
		      'code' => $_GET['code'],
		      'client_id' => Settings::getKey('ya_metrika_id'),
		      'client_secret' => Settings::getKey('ya_metrika_pass'),
		    );
		    $query = http_build_query($query);

		    // Формирование заголовков POST-запроса
		    $header = "Content-type: application/x-www-form-urlencoded";

		    // Выполнение POST-запроса и вывод результата
		    $opts = array('http' =>
		      array(
		      'method'  => 'POST',
		      'header'  => $header,
		      'content' => $query
		      ) 
		    );
		    $context = stream_context_create($opts);
		    $result = file_get_contents('https://oauth.yandex.ru/token', false, $context);
		    $result = json_decode($result);

		    // Токен необходимо сохранить для использования в запросах к API Директа
		    echo 'Token: '.$result->access_token;
		    
		    //Settings::setKey('ya_metrika_id', $client_id);
		    //Settings::setKey('ya_metrika_pass', $client_secret);
		    $res = Settings::setKey('ya_metrika_token', $result->access_token);		    
		    if(true !== $res) print_r($res);
		}
		else {
		   	$model->client_id = Settings::getKey('ya_metrika_id');
		   	$model->client_secret = Settings::getKey('ya_metrika_pass');
		   	
			return $this->render('yatokenform', ['model' => $model]);
		}
				
	}

    /**
     * Finds the Settings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Settings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Settings::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
