<?php

namespace app\controllers;

use Yii;
use app\models\Price;
use app\models\PriceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PriceController implements the CRUD actions for Price model.
 */
class PriceController extends Controller
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
     * Lists all Price models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PriceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Price model.
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
     * Creates a new Price model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Price(['scenario'=>'create']);

        if ($model->load(Yii::$app->request->post())) {
        	$model->created_at = date('Y-m-d H:i:s');
        	//$model->updated_at = date('Y-m-d H:i:s');
        	if (!empty($model->name)) $model->name = $model->tovar->name . ' (' . $model->name . ')';
        	else $model->name = $model->tovar->name;
        	if (!empty($model->artikul)) $model->artikul = $model->tovar->artikul . '_' . $model->artikul;
        	else $model->artikul = $model->tovar->artikul;
        	if ($model->save()) 
            	return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
           'model' => $model,
        ]);        
    }

    /**
     * Updates an existing Price model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';

        if ($model->load(Yii::$app->request->post())) {
        	$model->updated_at = date('Y-m-d H:i:s');
        	if ($model->save())
        		return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
           'model' => $model,
        ]);        
    }

    /**
     * Deletes an existing Price model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (count($model->rashod) > 0)
        	Yii::$app->session->setFlash('danger', 'Нельзя удалить - есть ссылки из заявок');
        else {
        	$model->delete();
        	Yii::$app->session->setFlash('success', 'Товар удален из прайса!');
		} 

        return $this->redirect(['index']);
    }

    /**
     * Finds the Price model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Price the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Price::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
