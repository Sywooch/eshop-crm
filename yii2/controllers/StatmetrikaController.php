<?php

namespace app\controllers;

use Yii;
use app\models\Settings;
use app\models\Statmetrika;
use app\models\StatmetrikaForm;
use app\models\StatmetrikaSearch;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StatmetrikaController implements the CRUD actions for Statmetrika model.
 */
class StatmetrikaController extends BaseController
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
     * Lists all Statmetrika models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StatmetrikaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Statmetrika model.
     * @param integer $id
     * @return mixed
     */
    /*public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }*/

    /**
     * Creates a new Statmetrika model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StatmetrikaForm();        

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $ya_list = Statmetrika::_get_metrika();
            if(array_key_exists('errors', $ya_list)) {
                Yii::$app->session->addFlash('error', $ya_list['errors']);                
            }
            else {
                $date = date('Y-m-d', strtotime($model->date1.' - 1 days'));
                $ydate = date('Ymd', strtotime($date));
                while($date < $model->date2){
                    $date = date('Y-m-d', strtotime($date.' + 1 days'));			
		
                    foreach($ya_list['counters'] as $ya) {
                        //$ya_stat = Statmetrika::_get_metrika('http://api-metrika.yandex.ru/stat/traffic/summary.json?id='.$ya['id'].'&pretty=1&date1='.$ydate.'&date2='.$ydate.'&oauth_token='.Settings::getKey('ya_metrika_token'));
                        $ya_stat = Statmetrika::_get_metrika('https://api-metrika.yandex.ru/stat/v1/data?ids='.$ya['id'].'&pretty=0&oauth_token='.Settings::getKey('ya_metrika_token').'&metrics=ym:s:visits&preset=traffic');
                        \yii\helpers\VarDumper::dump($ya_stat,10,true);
                    }
                    
                }
                die;
            }
            
            return $this->redirect(['index']);
        } else {
            if(empty($model->date1)) $model->date1 = date('Y-m-01');
            if(empty($model->date2)) $model->date2 = date('Y-m-d');
            
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Statmetrika model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    /*public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }*/

    /**
     * Deletes an existing Statmetrika model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/
    public function actionStatmetrikaForm()
    {
        $model = new app\models\Statmetrika();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                // form inputs are valid, do something here
                return;
            }
        }

        return $this->render('StatmetrikaForm', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Statmetrika model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Statmetrika the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Statmetrika::findOne(['id'=>$id,'shop_id'=> $this->shop_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
