<?php

namespace app\controllers;

use Yii;
use app\models\Kladr;
use app\models\KladrSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * KladrController implements the CRUD actions for Kladr model.
 */
class KladrController extends Controller
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
     * Lists all Kladr models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new KladrSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Kladr model.
     * @param string $id
     * @return mixed
     */
/*    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
*/
    /**
     * Creates a new Kladr model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
/*    public function actionCreate()
    {
        $model = new Kladr();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->code]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
*/
    /**
     * Updates an existing Kladr model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
/*    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->code]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
*/
    /**
     * Deletes an existing Kladr model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
/*    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
*/
    /**
     * Finds the Kladr model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Kladr the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Kladr::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionAreaList() {
		$return_msg = '';
		if (Yii::$app->request->isAjax) {//printvar($_POST);
			if (is_numeric($_POST['code'])) {
				$q = KladrSearch::areaList($_POST['code']);
				foreach ($q as $key => $value) {
					$haOptions[] = array('optionKey' => $key, 'optionValue' => $value);
				}
				$return_msg = json_encode($haOptions);
			} else {
				$return_msg = json_encode('code is no numeric');
			}
		} else {
			$return_msg = 'Некорректный формат запроса';
		}
		echo ($return_msg);
	}

	public function actionCityList() {
		$return_msg = '';
		if (Yii::$app->request->isAjax) {//printvar($_POST);
			if (is_numeric($_POST['code'])) {
				$q = KladrSearch::cityList($_POST['code']);
				foreach ($q as $key => $value) {
					$haOptions[] = array('optionKey' => $key, 'optionValue' => $value);
				}
				$return_msg = json_encode($haOptions);
			} else {
				$return_msg = json_encode('code is no numeric');
			}
		} else {
			$return_msg = 'Некорректный формат запроса';
		}
		echo ($return_msg);
	}

	public function actionSettlementList() {
		$return_msg = '';
		if(!Yii::$app->user->can('kladr')) {
			return json_encode('no access');
		}
		if (Yii::$app->request->isAjax) {//printvar($_POST);
			if (is_numeric($_POST['code'])) {
				$q = KladrSearch::settlementList($_POST['code']);
				foreach ($q as $key => $value) {
					$haOptions[] = array('optionKey' => $key, 'optionValue' => $value);
				}
				$return_msg = json_encode($haOptions);
			} else {
				$return_msg = json_encode('code is no numeric');
			}
		} else {
			$return_msg = 'Некорректный формат запроса';
		}
		echo ($return_msg);
	}
	/**
	* добавим столбец уровня для комфортной выборки и индексации
	* 
	* @return none
	*/
/*	function actionRecalc() {
		//ini_set("max_execution_time", "0");
		$n=0; 
		$q = Yii::$app->db->createCommand("SELECT * FROM `kladr` WHERE `level` = '0' LIMIT 0,50000")->queryAll();
        foreach ($q as $qq) {
    		$n++;   		
			
			if (substr($qq['code'],8,3) != '000') $level=4;
			elseif (substr($qq['code'],5,3)  != '000') $level= 3;
			elseif (substr($qq['code'],2,3)  != '000') $level= 2;
			else $level=1;
			
			$r = Yii::$app->db->createCommand()->update('kladr', ['level' => $level], "code = '".$qq['code']."'")->execute();
			if($r<1) echo 'error: '.$qq['code'].' ;';
		}		
		//foreach (Kladr::find()->each(100) as $qq) {
    		$n++;
    		//foreach($q as $qq) {				
			
			if (substr($qq->code,8,3) != '000') $qq->level=4;
			elseif (substr($qq->code,5,3)  != '000') $qq->level= 3;
			elseif (substr($qq->code,2,3)  != '000') $qq->level= 2;
			else $qq->level=1;
			
			if($qq->save()) true;
			else {				
				\yii\helpers\VarDumper::dump($qq->firstErrors,2,true);
				die('error');
			//}			
			}		
		}
		echo " $n \n";
	}*/
}
