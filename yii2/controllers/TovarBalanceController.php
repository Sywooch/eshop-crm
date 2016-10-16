<?php

namespace app\controllers;

use Yii;
use app\models\TovarBalance;
use app\models\TovarBalanceSearch;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TovarBalanceController implements the CRUD actions for TovarBalance model.
 */
class TovarBalanceController extends BaseController
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
     * Lists all TovarBalance models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TovarBalanceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $dataProvider->setPagination([
			'pageSize' => 100,
    	]);
        
        $sklad_list = \app\models\Sklad::find()->select(['name', 'id'])->where(['shop_id'=>$this->shop_id])->indexBy('id')->column();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sklad_list' => $sklad_list,
        ]);
    }

    /**
     * Displays a single TovarBalance model.
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
     * Creates a new TovarBalance model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
 /*   public function actionCreate()
    {
        $model = new TovarBalance();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }*/

    /**
     * Updates an existing TovarBalance model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
 /*   public function actionUpdate($id)
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
     * Deletes an existing TovarBalance model.
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
     * Finds the TovarBalance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TovarBalance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
/*    protected function findModel($id)
    {
        if (($model = TovarBalance::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }*/
}
