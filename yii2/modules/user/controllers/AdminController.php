<?php

namespace app\modules\user\controllers;

use Yii;
use app\modules\user\models\UserAdmin;
use app\modules\user\models\UserAdminSearch;
use app\models\Shops;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class AdminController extends Controller
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserAdminSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserAdmin();
        $model->scenario = 'create';

        if ($model->load(Yii::$app->request->post())) {
        	//$model->updated_at = date('Y-m-d H:i:s');
            //$model->created_at = date('Y-m-d H:i:s');
            $model->setPassword();
            $model->generateAuthKey();
			if ($model->save()) {
            	return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        $shops_list = ArrayHelper::map(Shops::find()->all(), 'id', 'name');  
        return $this->render('create', [
                'model' => $model,
                'shops_list' => $shops_list,
            ]);        
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';

        if ($model->load(Yii::$app->request->post())) {
        	//$model->updated_at = date('Y-m-d H:i:s');
        	if (!empty($model->password)) {
				$model->setPassword();
            	$model->generateAuthKey();            	
			}
        	if ($model->save()) {
        		//$model->saveShops();
            	//return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        $shops_list = ArrayHelper::map(Shops::find()->all(), 'id', 'name');        
        //\yii\helpers\VarDumper::dump($_POST,10,true);        
        return $this->render('update', [
           'model' => $model,
           'shops_list' => $shops_list,
           //'shop_id' => $shop_id,
           //'mdl_shops' => $model->shops,
        ]);        
    }

    /**
     * Deletes an existing User model.
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
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserAdmin::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
