<?php

namespace app\controllers;

use Yii;
use app\models\Tovar;
use app\models\TovarSearch;
use app\models\Category;
use app\models\Sklad;
use app\models\TovarOstatokSearch;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TovarController implements the CRUD actions for Tovar model.
 */
class TovarController extends BaseController
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
     * Lists all Tovar models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TovarSearch();
        $mdlCategory = Category::find()->select(['name', 'id'])->where(['shop_id'=>Yii::$app->params['user.current_shop']])->indexBy('id')->column();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'mdlCategory' => $mdlCategory,
        ]);
    }
    
     /**
     * Lists all Tovar models.
     * @return mixed
     */
    public function actionPopup()
    {
        $searchModel = new TovarOstatokSearch();    
        $dataProvider = $searchModel->searchforpopup(Yii::$app->request->queryParams);
        /*$current_shop = Yii::$app->params['user.current_shop'];
        $active = 1;
        $sql = "SELECT `tovar`.`artikul` AS `t_art`, `tovar`.`name` AS `t_name`, `tovar`.`price` AS `t_price`, `sklad`.`id` AS `s_id`, `sklad`.`name` AS `s_name`, `category`.`id` AS `cat_id`, `category`.`name` AS `cat_name`, IFNULL((tovar_prihod.amount - tovar_rashod.amount), 0) as ostatok FROM `sklad` LEFT JOIN `tovar_prihod` ON `sklad`.`id` = `tovar_prihod`.`sklad_id` LEFT JOIN `tovar_rashod` ON `sklad`.`id` = `tovar_rashod`.`sklad_id` LEFT JOIN `tovar` ON `tovar_prihod`.`tovar_id` = `tovar`.`id` LEFT JOIN `category` ON `tovar`.`category_id` = `category`.`id` WHERE (`tovar`.shop_id='$current_shop') AND (`tovar`.active='$active') GROUP BY `sklad`.`id`, `tovar`.`name`";
        $db = Yii::$app->db;
		$count = $db->createCommand($sql)->queryColumn();
		$count = count($count);

		$dataProvider = new \yii\data\SqlDataProvider([
		    'sql' => $sql,
		    //'params' => [':status' => 1],
		    'totalCount' => $count,
		    'pagination' => [
		        'pageSize' => 50,
		    ],
		    'sort' => [
		        'attributes' => ['s_id', 'cat_id', 't_id', 't_art', 's_name', 't_name', 'cat_name', 't_price'],
		    ],
		]);
		*/
        $category = Category::find()->select(['name', 'id'])->where(['shop_id'=>Yii::$app->params['user.current_shop']])->indexBy('id')->column();
        $sklad = Sklad::find()->select(['name', 'id'])->where(['shop_id'=>Yii::$app->params['user.current_shop']])->indexBy('id')->column();
        
        $this->layout = 'popup';

        return $this->render('popup', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'category' => $category,
            'sklad' => $sklad,
        ]);
    }

     /**
     * Lists all Tovar models.
     * @return mixed
     */
    public function actionPopup1()
    {
        $searchModel = new \app\models\TovarBalanceSearch();    
        $dataProvider = $searchModel->searchPopup(Yii::$app->request->queryParams);

        $category = Category::find()->select(['name', 'id'])->where(['shop_id'=>Yii::$app->params['user.current_shop']])->indexBy('id')->column();
        $sklad = Sklad::find()->select(['name', 'id'])->where(['shop_id'=>Yii::$app->params['user.current_shop']])->indexBy('id')->column();
        
        $this->layout = 'popup';

        return $this->render('popup1', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'category' => $category,
            'sklad' => $sklad,
        ]);
    }

    /**
     * Displays a single Tovar model.
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
     * Creates a new Tovar model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tovar();

        if ($model->load(Yii::$app->request->post())) {
        	//$model->created_at = gmdate('Y-m-d H:i:s');
        	if ($model->save()) 
            	return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
           'model' => $model,
        ]);        
    }

    /**
     * Updates an existing Tovar model.
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
     * Deletes an existing Tovar model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (count($model->rashod) > 0)
        	Yii::$app->session->setFlash('danger', 'Нельзя удалить - есть ссылки из прайса');
        else {
        	$model->delete();
        	Yii::$app->session->setFlash('success', 'Товар удален!');
		}        	

        return $this->redirect(['index']);
    }
    
    public function actionOstatok()
    {
        $searchModel = new TovarOstatokSearch();        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams); 
        $category = Category::find()->select(['name', 'id'])->where(['shop_id'=>Yii::$app->params['user.current_shop']])->indexBy('id')->column();
        $sklad = Sklad::find()->select(['name', 'id'])->where(['shop_id'=>Yii::$app->params['user.current_shop']])->indexBy('id')->column();
 
        return $this->render('ostatok', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'category' => $category,
            'sklad' => $sklad,         
        ]);
    }

    /**
     * Finds the Tovar model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tovar the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tovar::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
