<?php

namespace app\controllers;

use Yii;
use app\models\Tovar;
use app\models\TovarPrihod;
use app\models\TovarPrihodSearch;
use app\models\TovarBalance;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TovarPrihodController implements the CRUD actions for TovarPrihod model.
 */
class TovarPrihodController extends BaseController
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
     * Lists all TovarPrihod models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TovarPrihodSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort(['defaultOrder' => ['created_at'=>SORT_DESC],]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TovarPrihod model.
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
     * Creates a new TovarPrihod model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TovarPrihod();
        //$model->created_at = gmdate('Y-m-d H:i:s');
        //$model->updated_at = gmdate('Y-m-d H:i:s');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        	$tovar = Tovar::findOne($model->tovar_id);
        	if ($tovar and ($model->price_sale > 0 and $tovar->price != $model->price_sale) or ($model->price > 0 and $tovar->pprice != $model->price)) {
				$tovar->price = $model->price_sale;
				$tovar->pprice = $model->price;
        		$tovar->save();
				$msg = TovarBalance::calc($model->tovar_id, $model->sklad_id, $model->amount, $model->shop_id, '+');
        		Yii::$app->session->addFlash('info', $msg);
			}
			/*
			$balance = new TovarBalance();
			$msg = $balance->prihod($model->tovar_id, $model->sklad_id, $model->amount);			
			
			$costs = TovarCosts::find()->where(['tovar_id'=>$model->tovar_id, 'cost'=>$model->price_sale])->one();
			if(is_null($costs)) {
				$costs = new TovarCosts();
				$costs->tovar_id = $model->tovar_id;
				$costs->cost = $model->price_sale;
				$costs->active = 1;
				$costs->current = 1;
				if ($costs->save()) $msg .= ' Новая цена создана.';
			}
			
        	Yii::$app->session->setFlash('info', $msg);
        	*/
            
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
        	$mdlTovar = new Tovar(); 
            return $this->render('create', [
                'model' => $model,
                'mdlTovar' => $mdlTovar,
            ]);
        }
    }

    /**
     * Updates an existing TovarPrihod model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
        	//$model->updated_at = gmdate('Y-m-d H:i:s');
        	if ($model->save()) {
        		$tovar = Tovar::findOne($model->tovar_id);
	        	if ($tovar and $model->price_sale > 0 and $tovar->price != $model->price_sale) {
					$tovar->price = $model->price_sale;
	        		$tovar->save();
				}
        		/*$balance = new TovarBalance();
				$msg = $balance->prihod($model->tovar_id, $model->sklad_id, $model->amount);			
        		Yii::$app->session->setFlash('info', $msg);
            	*/
            	return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TovarPrihod model.
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
     * Finds the TovarPrihod model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TovarPrihod the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TovarPrihod::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
