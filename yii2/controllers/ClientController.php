<?php

namespace app\controllers;

use Yii;
use app\models\Client;
use app\models\ClientSearch;
use app\models\Orders;
use app\models\OrderSearch;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * ClientController implements the CRUD actions for Client model.
 */
class ClientController extends BaseController
{
    public function behaviors()
    {
        return [
/*        	'access' => [
                'class' => AccessControl::className(),
            ],
*/           'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//\yii\helpers\VarDumper::dump($dataProvider,10,true);        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Client model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        /*$query = OrderSearch::find();//->where(['client_id' => $id])->all();

		$dataProvider = new ActiveDataProvider([
		    'query' => $query,
		    'pagination' => [
		        'pageSize' => 50,
		    ],
		    'sort' => [
		        'defaultOrder' => [
		            'id' => SORT_DESC,		            
		        ]
		    ],
		]);
        */
        $searchModel = new OrderSearch();
             
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);
		$dataProvider->setPagination([
			'pageSize' => 50,
   		]);
        $dataProvider->setSort([
        'defaultOrder' => ['id'=>SORT_DESC],]);
      
        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Client();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
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
     * Deletes an existing Client model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (count($model->orders) > 0)
        	Yii::$app->session->setFlash('danger', 'Нельзя удалить - есть заявки от клиента');
        else {
        	$model->delete();
        	Yii::$app->session->setFlash('success', 'Клиент удален. Прощай, неудачник!');
		} 

        return $this->redirect(['index']);
    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
/*    
    public function actionInkladr ()
	{
		echo phpinfo();
		$open_db = dbase_open('kladr.dbf', 0); // открываем файл из которого будем производить импорт данных
		$records = dbase_numrecords($open_db); // Количество записей в импортируемом файле
		echo "<p>total row: $records</p>";die;
		$count = 0;
		for ($i=1; $i<= $records; $i++) // Переход на следующ. запись
		{
			$row= dbase_get_record($open_db, $i); // Чтение записи
			$name=$row[0]; // Наименование 
			$socr=$row[1]; // Сокращенное наименование типа объекта
			$code=$row[2]; // Код
			$index=$row[3]; // Почтовый индекс
			$gninmb=$row[4]; // Код ИФНС
			$uno=$row[5]; // Код территориального участка ИФНС
			$ocatd=$row[6]; // Код ОКАТО
			$status=$row[7]; // Статус объекта
			
			$r = Yii::$app->db->createCommand()->insert('kladr', [
			    'name' => $name,
			    'socr' => $socr,
			    'code' => $code,
			    'index' => $index,
			    'gninmb' => $gnimb,
			    'uno' => $uno,
			    'ocatd' => $ocatd,
			    'status' => $status,
			])->execute();
			$count = $count + $r;
		}
		echo "<p>insert row: $count</p>";
	}*/
}
