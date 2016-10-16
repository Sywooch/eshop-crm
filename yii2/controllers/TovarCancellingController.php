<?php

namespace app\controllers;

use Yii;
use app\models\TovarCancelling;
use app\models\TovarCancellingSearch;
use app\models\Sklad;
use app\models\Tovar;
use app\models\TovarBalance;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * TovarCancellingController implements the CRUD actions for TovarCancelling model.
 */
class TovarCancellingController extends \app\components\BaseController
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
     * Lists all TovarCancelling models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TovarCancellingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $sklad_list = \app\models\Sklad::find()->select(['name', 'id'])->where(['shop_id'=>$this->shop_id])->indexBy('id')->column();
        $tovar_list = ArrayHelper::map(Tovar::find()->where(['active'=>1])->with('category')->all(), 'id', 'name', 'category.name');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sklad_list' => $sklad_list,
            'tovar_list' => $tovar_list,
        ]);
    }

    /**
     * Displays a single TovarCancelling model.
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
     * Creates a new TovarCancelling model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TovarCancelling();
        $model->shop_id = $this->shop_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) { // {        	
        	$msg = TovarBalance::calc($model->tovar_id, $model->sklad_id, $model->amount, $model->shop_id, '-');
        	Yii::$app->session->addFlash('info', $msg);
            //print_r($model);
            return $this->redirect('index');//(['view', 'id' => $model->id]);
        } else {
        	$sklad_list = \app\models\Sklad::find()->select(['name', 'id'])->where(['shop_id'=>$this->shop_id])->indexBy('id')->column();
        	$tovar_list = ArrayHelper::map(Tovar::find()->where(['active'=>1])->andWhere(['shop_id'=>$this->shop_id])->with('category')->all(), 'id', 'name', 'category.name');
               	
            return $this->render('create', [
                'model' => $model,
                'sklad_list' => $sklad_list,
                'tovar_list' => $tovar_list,
            ]);
        }
    }

    /**
     * Updates an existing TovarCancelling model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
/*    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->shop_id = $this->shop_id;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
        	$sklad_list = \app\models\Sklad::find()->select(['name', 'id'])->where(['shop_id'=>$this->shop_id])->indexBy('id')->column();
        	$tovar_list = ArrayHelper::map(Tovar::find()->where(['active'=>1])->with('category')->all(), 'id', 'name', 'category.name');       	
        	
            return $this->render('update', [
                'model' => $model,
                'sklad_list' => $sklad_list,
                'tovar_list' => $tovar_list,
            ]);
        }
    }*/

    /**
     * Deletes an existing TovarCancelling model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
/*    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Finds the TovarCancelling model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TovarCancelling the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TovarCancelling::find()->where('id = :id and shop_id = :shop_id')->addParams([':id'=>$id, 'shop_id'=>$this->shop_id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
