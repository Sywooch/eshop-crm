<?php

namespace app\controllers;

use Yii;
use app\models\Orders;
use app\models\Client;
use app\models\OrderSearch;
use app\models\TovarBalance;
use app\models\TovarSearch;
use app\models\Sms;
//use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\web\Response;
//use app\components\BaseController;

/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class OrdersController extends \app\components\BaseController
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
			/*'bootstrap' => [
				'class' => ContentNegotiator::className(),
		    	'only' => ['clientAjax'],
		    	'formats' => [ 'application/json' => Response::FORMAT_JSON ],
		        'languages' => ['ru'],
			],*/
		];
        
    }

    /**
     * Lists all Orders models.
     * @return mixed
     */
    public function actionIndex()
    {//echo '<pre>';print_r(Yii::$app->params);echo '</pre>';   	    	
        $searchModel = new OrderSearch();
        
        $searchModel->generateColumn();
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->setPagination([
			'pageSize' => 50,
    ]);
        $dataProvider->setSort(['defaultOrder' => ['id'=>SORT_DESC],]);
//\yii\helpers\VarDumper::dump($dataProvider,true,5);die;
		Yii::$app->user->returnUrl = Yii::$app->request->url;
		
		Yii::info('Список заявок: ' . Yii::$app->user->identity->username . ' IP: '.Yii::$app->request->userIP, 'order_list');
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }    
     /**
     * Lists all Orders models by client.
     * @return mixed
     */
    public function actionClient($id)
    {
        $model = $this->findModel($id);
        $searchModel = new OrderSearch();
        //print_r(Yii::$app->request->queryParams);die;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);
		$dataProvider->setPagination([
			'pageSize' => 50,
    ]);
        $dataProvider->setSort([
        'defaultOrder' => ['id'=>SORT_DESC],]);

        return $this->render('client', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Orders model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Orders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
  //echo '<pre>';print_r($_POST);echo '</pre>';die; 	
        $model = new Orders();

        if ($model->load(Yii::$app->request->post())) {
        	if (empty($model->manager_id) and (Yii::$app->user->can('manager')))
        		$model->manager_id = Yii::$app->user->id;
        	//автоматом пропишем упаковщика
        	if (empty($model->packer_id) and (Yii::$app->user->can('packer')))
        		$model->packer_id = Yii::$app->user->id; 
        	if ($model->save()) {
	        	$msg = $model->saveTovar();
	        	Yii::$app->session->addFlash('info', $msg);
	        	$client = Client::findOne($model->client_id);
	        	$client->load(Yii::$app->request->post());
	        	if ($client->save())
        			$msg .= ' Клиент сохранен';
	        	else {
					$msg .= ' Клиент НЕ сохранен';					
				}        	
        		Yii::$app->session->addFlash('info', $msg);
        		Yii::$app->session->addFlash('success', 'Заявка сохранена!');
    		
        		return $this->redirect(Yii::$app->user->returnUrl);
            	//return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
        	$mdlTovar = new TovarSearch();
            $prvTovar = $mdlTovar->search(Yii::$app->request->queryParams);
        
            return $this->render('create', [
                'model' => $model,
                'mdlTovar' => $mdlTovar,
                'prvTovar' => $prvTovar
            ]);
        }
    }

    /**
     * Updates an existing Orders model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $msg = '';
        $model = $this->findModel($id);
        //$old_otpravlen = $model->otpravlen;
        //$old_dostavlen = $model->dostavlen;
		//echo '<pre>';print_r($_POST);echo '</pre>';die;
        if ($model->load(Yii::$app->request->post())) {
        	//автоматом пропишем менеджера
        	if (empty($model->manager_id) and (Yii::$app->user->can('manager')))
        		$model->manager_id = Yii::$app->user->id;
        	//автоматом пропишем упаковщика
        	if (empty($model->packer_id) and (Yii::$app->user->can('packer')))
        		$model->packer_id = Yii::$app->user->id; 
        	     		
        	if ($model->save()) {
	        	$msg = $model->saveTovar();
	        	Yii::$app->session->addFlash('info', $msg);
	        	/*
	        	if($old_otpravlen == 0 and $model->otpravlen == 1) {
	        		//вычтем товар из остатков
					$balance = new TovarBalance();
					$msg .= $balance->rashod($model->id);
					//отправим смс
					if ($model->status == '6') {
						$sms = new Sms();
						$msg .= $sms->sendSms($model->id, 'otprav');
					}
				}
				if($old_dostavlen == 0 and $model->dostavlen == 1) {	        		
					//отправим смс
					if ($model->status == '6') {
						$sms = new Sms();
						$msg .= $sms->sendSms($model->id, 'dostav');
					}
				}
	        	*/
	        	$client = Client::findOne($model->client_id);
        		$client->load(Yii::$app->request->post());
        		if ($client->save())
        			$msg = ' Клиент сохранен';
	        	else {
					$msg = ' Клиент НЕ сохранен: '.print_r($client->getErrors(),true);					
				}
	        	if (!empty($msg)) Yii::$app->session->addFlash('info', $msg);
	        	Yii::$app->session->addFlash('success', 'Заявка сохранена!');
	        	
	        	Yii::info('Изменение заявки #'.$model->id.': ' . Yii::$app->user->identity->username . ' IP: '.Yii::$app->request->userIP. ' Status: '.$model->itemAlias('status',$model->status), 'order_status');	        	        	
	        	
	    //\yii\helpers\VarDumper::dump(\Yii::$app->session->getAllFlashes(),10,true);die;
	        	return $this->redirect(Yii::$app->user->returnUrl);
	            //Yii::app()->user->returnUrl = Yii::app()->request->urlReferrer;
	            //return $this->redirect(['view', 'id' => $model->id]);
	        }
        }
        if(count($model->getErrors())>0)
        	Yii::$app->session->addFlash('error', 'Заявка НЕ сохранена! '.print_r($model->getErrors(),true));
        	
        //$mdlTovar = new TovarSearch();
        //$prvTovar = $mdlTovar->search(Yii::$app->request->queryParams);
        //$prvTovar->pagination->pageSize = 10;
        //\yii\helpers\VarDumper::dump($prvTovar,true,10);die;
        
        $call = $model->getCall();
        //\yii\helpers\VarDumper::dump($call,true,10);
        	
        return $this->render('update', [
            'model' => $model,
            'call' => $call,
            //'mdlTovar' => $mdlTovar,
            //'prvTovar' => $prvTovar,
        ]);        
    }
    
       public function actionUpdate1($id)
    {
        $msg = '';
        $model = $this->findModel($id);
        //$old_otpravlen = $model->otpravlen;
        //$old_dostavlen = $model->dostavlen;
		//echo '<pre>';print_r($_POST);echo '</pre>';die;
        if ($model->load(Yii::$app->request->post())) {
        	//автоматом пропишем менеджера
        	if (empty($model->manager_id) and (Yii::$app->user->can('manager')))
        		$model->manager_id = Yii::$app->user->id;
        	//автоматом пропишем упаковщика
        	if (empty($model->packer_id) and (Yii::$app->user->can('packer')))
        		$model->packer_id = Yii::$app->user->id; 
        	     		
        	if ($model->save()) {
	        	$msg = $model->saveTovar();
	        	Yii::$app->session->addFlash('info', $msg);
	        	/*
	        	if($old_otpravlen == 0 and $model->otpravlen == 1) {
	        		//вычтем товар из остатков
					$balance = new TovarBalance();
					$msg .= $balance->rashod($model->id);
					//отправим смс
					if ($model->status == '6') {
						$sms = new Sms();
						$msg .= $sms->sendSms($model->id, 'otprav');
					}
				}
				if($old_dostavlen == 0 and $model->dostavlen == 1) {	        		
					//отправим смс
					if ($model->status == '6') {
						$sms = new Sms();
						$msg .= $sms->sendSms($model->id, 'dostav');
					}
				}
	        	*/
	        	$client = Client::findOne($model->client_id);
        		$client->load(Yii::$app->request->post());
        		if ($client->save())
        			$msg = ' Клиент сохранен';
	        	else {
					$msg = ' Клиент НЕ сохранен: '.print_r($client->getErrors(),true);					
				}
	        	if (!empty($msg)) Yii::$app->session->addFlash('info', $msg);
	        	Yii::$app->session->addFlash('success', 'Заявка сохранена!');
	        	
	        	Yii::info('Изменение заявки #'.$model->id.': ' . Yii::$app->user->identity->username . ' IP: '.Yii::$app->request->userIP. ' Status: '.$model->itemAlias('status',$model->status), 'order_status');	        	        	
	        	
	    //\yii\helpers\VarDumper::dump(\Yii::$app->session->getAllFlashes(),10,true);die;
	        	return $this->redirect(Yii::$app->user->returnUrl);
	            //Yii::app()->user->returnUrl = Yii::app()->request->urlReferrer;
	            //return $this->redirect(['view', 'id' => $model->id]);
	        }
        }
        if(count($model->getErrors())>0)
        	Yii::$app->session->addFlash('error', 'Заявка НЕ сохранена! '.print_r($model->getErrors(),true));
        	
        //$mdlTovar = new TovarSearch();
        //$prvTovar = $mdlTovar->search(Yii::$app->request->queryParams);
        //$prvTovar->pagination->pageSize = 10;
        //\yii\helpers\VarDumper::dump($prvTovar,true,10);die;
        	
        return $this->render('update1', [
            'model' => $model,
            //'mdlTovar' => $mdlTovar,
            //'prvTovar' => $prvTovar,
        ]);        
    }
    /**
     * Deletes an existing Orders model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    /**
	* 
	* @param integer $id
	* 
	* @return file xls
	*/
    public function actionF116($id) {
		$mdl = $this->findModel($id);
		//\yii\helpers\VarDumper::dump($mdl->client->region,5,true);die;
		$fileName = 'f116-'.$id.'-'.date('Y-m-d').'.xls';
		
		$sum1 = '';
		$sum2 = '';
		/*
		if ($mdl->type_oplata == 2) {
			$sum1 = UtilsController::num2str("100");
			$sum2 = "-";
		} else {
			$sum1 = UtilsController::num2str($mdl->tovarSumma);
			$sum2 = $sum1;
		}		
		*/
		$addr_name = $mdl->client->fio;
		//$addr_address = $mdl->client->region->pname. ', '.$mdl->client->area->kname. ', ' .$mdl->client->city->kname. ', ' .$mdl->client->settlement->kname.', ' .$mdl->client->address;
		$addr = [];
		$addr[] = $mdl->client->region->pname;
		$addr[] = $mdl->client->area->kname;
		$addr[] = $mdl->client->city->kname;
		$addr[] = $mdl->client->settlement->kname;
		$addr[] = $mdl->client->address;
		
		$array_empty = array('');
		$addr = array_diff($addr, $array_empty);
		$addr_address = '';
		$addr_address = implode(', ', $addr);
		
		$addr_zip_code = $mdl->client->postcode;
		//$region = $myrow['sklad'];
 
		$objReader = \PHPExcel_IOFactory::createReader('Excel5');
				
		$objPHPExcel = $objReader->load(Yii::$app->basePath."/../lib/template/116-dinikiev.xls");
				
		//if($region == 'rus') $objPHPExcel = $objReader->load("templates/116-dinikiev.xls");
	  	//elseif($region == 'msk') $objPHPExcel = $objReader->load("templates/116-dinikiev-fiz.xls");
		$objPHPExcel->getDefaultStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		
		// делаем первую страницу активной
		$page = $objPHPExcel->setActiveSheetIndex(0);
		// заполняем лист
		$page->setCellValue("M42", $sum1); 
		$page->setCellValue("M47", $sum2); 
		
		//данные получателя
		$page->setCellValue("S52", $addr_name); 
		$page->setCellValue("S132", $addr_name); 

		$addr_address_part1 = '';
		$addr_address_part2 = '';
		$addr_address_part3 = '';
		
		//$addr_address = iconv(mb_detect_encoding($addr_address, mb_detect_order(), true), "utf-8", $addr_address);
		//UtilsController::wordwrapaddress($addr_address, $addr_address_part1, $addr_address_part2, $addr_address_part3);
		
		$str = wordwrap($addr_address, 70, '*', true);
		$ar = explode('*', $str);		
		$addr_address_part1 = $ar['0'];
		unset($ar['0']);
	
		$str = implode('*', $ar);
		$str = wordwrap($str, 80, '*', true);
		$ar = explode('*', $str);
	//\yii\helpers\VarDumper::dump($ar,5,true);die;
		$addr_address_part2 = $ar['0'];
		$addr_address_part3 = $ar['1'];
		


		$page->setCellValue("T57", $addr_address_part1,  \PHPExcel_Cell_DataType::TYPE_STRING); 

		$page->setCellValueExplicit("T57", $addr_address_part1, \PHPExcel_Cell_DataType::TYPE_STRING);
		$page->setCellValueExplicit("M61", $addr_address_part2, \PHPExcel_Cell_DataType::TYPE_STRING);
		$page->setCellValueExplicit("M66", $addr_address_part3, \PHPExcel_Cell_DataType::TYPE_STRING);
		
		$page->setCellValueExplicit("U137", $addr_address_part1, \PHPExcel_Cell_DataType::TYPE_STRING); 
		$page->setCellValueExplicit("M143", $addr_address_part2.$addr_address_part3, \PHPExcel_Cell_DataType::TYPE_STRING); 

		
		$page->setCellValue("BB66", ''.$addr_zip_code[0]);  
		$page->setCellValue("BE66", ''.$addr_zip_code[1]);  
		$page->setCellValue("BH66", ''.$addr_zip_code[2]);  
		$page->setCellValue("BK66", ''.$addr_zip_code[3]);  
		$page->setCellValue("BN66", ''.$addr_zip_code[4]);  
		$page->setCellValue("BQ66", ''.$addr_zip_code[5]);  

		$page->setCellValue("CB143", ''.$addr_zip_code[0]);  
		$page->setCellValue("CE143", ''.$addr_zip_code[1]);  
		$page->setCellValue("CH143", ''.$addr_zip_code[2]);  
		$page->setCellValue("CK143", ''.$addr_zip_code[3]);  
		$page->setCellValue("CN143", ''.$addr_zip_code[4]);  
		$page->setCellValue("CQ143", ''.$addr_zip_code[5]);  
		
		$page->setTitle("Ф116Лицо"); // Ставим заголовок на странице

		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$fileName.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

	    $objWriter->save('php://output');
	}
	
	/**
	* 
	* @param integer $id
	* 
	* @return
	*/
    public function actionF112($id) {
		$mdl = $this->findModel($id);
		//\yii\helpers\VarDumper::dump($mdl->client->fulladdress,5,true);die;
		$fileName = 'f112-'.$id.'-'.date('Y-m-d').'.xls';
		
		$sum1 = '';
		$sum2 = '';

		$addr_name = $mdl->client->fio;
				
		$addr = [];
		$addr[] = $mdl->client->region->pname;
		$addr[] = $mdl->client->area->kname;
		$addr[] = $mdl->client->city->kname;
		$addr[] = $mdl->client->settlement->kname;
		$addr[] = $mdl->client->address;

		$array_empty = array('');
		$addr = array_diff($addr, $array_empty);
		$addr_address = '';
		$addr_address = implode(', ', $addr);
		
		$addr_zip_code = $mdl->client->postcode;
		//$region = $myrow['sklad'];
 
		$objReader = \PHPExcel_IOFactory::createReader('Excel5');
				
		$objPHPExcel = $objReader->load(Yii::$app->basePath."/../lib/template/f112-dinikiev.xls");
				
		// делаем первую страницу активной
		$page = $objPHPExcel->setActiveSheetIndex(0);
		// заполняем лист
		$page->setCellValue("BP45", $sum1); 
		$page->setCellValue("AB112", ''.$addr_name); 
		$page->setCellValue("AW118", ''.$addr_address); 
	  
		$page->setCellValue("HA122", ''.$addr_zip_code[0]); 
		$page->setCellValue("HH122", ''.$addr_zip_code[1]); 
		$page->setCellValue("HO122", ''.$addr_zip_code[2]); 
		$page->setCellValue("HV122", ''.$addr_zip_code[3]); 
		$page->setCellValue("IC122", ''.$addr_zip_code[4]); 
		$page->setCellValue("IJ122", ''.$addr_zip_code[5]); 


		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$fileName.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	/**
	* 
	* @param integer $id
	* 
	* @return
	*/
	public function actionF7p($id){
		$mdl = $this->findModel($id);		
		$fileName = 'f112-'.$id.'-'.date('Y-m-d').'.xls';

		$postcode = $mdl->client->postcode;
		$full_address = $mdl->client->getFulladdress();
		//\yii\helpers\VarDumper::dump($mdl->client->getFulladdress(),5,true);die;
		$str = wordwrap($full_address, 70, '*', true);
		$ar = explode('*', $str);		
		$addres1 = $ar['0'];
		unset($ar['0']);
	
		$str = implode('*', $ar);
		$str = wordwrap($str, 80, '*', true);
		$ar = explode('*', $str);

		$addres2 = $ar['0'];
		$addres3 = $ar['1'];
		
		$objReader = \PHPExcel_IOFactory::createReader('Excel5');
		
		$objPHPExcel = $objReader->load(Yii::$app->basePath."/../lib/template/F7P-dinikiev.xls");		
				
		$itog = \app\components\Tools::num2str($mdl->tovarsumma);
		// делаем первую страницу активной
		$page = $objPHPExcel->setActiveSheetIndex(0);
		
		$page->setCellValue("AA20", $mdl->client->fio);//
		$page->setCellValue("Z30", $mdl->client->phone);
		$page->setCellValue("Y24", $addres1);
		
		if ($mdl->type_oplata =='1' ) {
			$page->setCellValue("Y2", X);	
		}
		
		$page->setCellValue("Y25", $addres2);	
		$page->setCellValue("Y26", $addres3);	
		//$page->setCellValue("Y10", '(' .$mdl->tovarsumma .'руб.)' .' '.$itog  );
		$page->setCellValue("AM30", $postcode);	
		
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$fileName.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	/**
	* 
	* @param undefined $id
	* 
	* @return
	*/
	public function actionSms($id){
		$mdl = $this->findModel($id);		
		$fileName = 'sms-'.$id.'-'.date('Y-m-d').'.xls';
		//tel client
		$phone1 = $mdl->client->phone;
		//tel tehpodderjka
		$phone2 = '';
		
		$objReader = \PHPExcel_IOFactory::createReader('Excel5');
		
		$objPHPExcel = $objReader->load(Yii::$app->basePath."/../lib/template/postsms-dinikiev.xls");		
				
		$itog = \app\components\Tools::num2str($mdl->tovarsumma);
		// делаем первую страницу активной
		$page = $objPHPExcel->setActiveSheetIndex(0);
		
		$page->setCellValue("N10", $myrow['fio']);//галка
	
		$page->setCellValue("C15", 'X');//галка
		$page->setCellValue("P15", $phone1['1']);//9
		$page->setCellValue("Q15", $phone1['2']);//1
		$page->setCellValue("R15", $phone1['3']);//7
		$page->setCellValue("S15", $phone1['4']);//4
		$page->setCellValue("T15", $phone1['5']);//1	
		$page->setCellValue("U15", $phone1['6']);//2
		$page->setCellValue("V15", $phone1['7']);//8	
		$page->setCellValue("W15", $phone1['8']);//8
		$page->setCellValue("X15", $phone1['9']);//7
		$page->setCellValue("Y15", $phone1['10']);//3

		if(!empty($phone2)) {
			$page->setCellValue("C12", 'X');//галка
			$page->setCellValue("P12", $phone2['1']);//9
			$page->setCellValue("Q12", $phone2['2']);//1
			$page->setCellValue("R12", $phone2['3']);//7
			$page->setCellValue("S12", $phone2['4']);//4
			$page->setCellValue("T12", $phone2['5']);//1	
			$page->setCellValue("U12", $phone2['6']);//2
			$page->setCellValue("V12", $phone2['7']);//8	
			$page->setCellValue("W12", $phone2['8']);//8
			$page->setCellValue("X12", $phone2['9']);//7
			$page->setCellValue("Y12", $phone2['10']);//3		
		}
		
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$fileName.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
    
    /**
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Orders::find()->where('id = :id and shop_id = :shop_id')->addParams([':id'=>$id, 'shop_id'=>Yii::$app->params['user.current_shop']])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionClientajax() {
		$r = Client::find()
            ->select(['id as value', '(concat_ws(" / ",phone,fio,address)) as label'])
            ->andFilterWhere(['like', 'phone', Yii::$app->request->get('term')])            
            ->asArray()
            ->all();
		return json_encode($r);
	}
/*	
	public function actionMigrate1($shop_id=false) {
		if($shop_id ===false) $shop_id = Yii::$app->params['user.current_shop'];
		if(intval($shop_id) <1) die('shop_id=0');
		ini_set('memory_limit', '1024M');
		
		$arUser = [
			'1'=>'9',
			'12'=>'9',
			'1122'=>'9',
			'2233'=>'9',
			'11' => '15',
			'8' => '15',
			'444' => '7',
			'4441' => '7',
			'7' => '19',
			'3344' => '10',
			'5' => '10',
			'222' => '10',
			'233' => '10',
			'4433' => '12',
			'101' => '14',
			'1011' => '14',
			'9' => '14',
			'1133' => '16',
			'1144' => '16',
			'551' => '17',
			'552' => '17',
			'10' => '18',
			'991' => '21',
			'992' => '21',
			'993' => '21'
		];
		$max_old_id = 1;//intval(Orders::find()->andWhere(['shop_id'=>$shop_id])->max('old_id2'));
		//\yii\helpers\VarDumper::dump($max_old_id,10,true);
		$url='http://94.41.61.180/migrateAxGet.php?id='.$max_old_id;
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL,$url);
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt ($ch, CURLOPT_TIMEOUT, 360);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$rows = curl_exec ($ch);
		curl_close($ch);

		$rows = json_decode($rows);
		if($rows == 'no data' or count($rows) <1) die('no data');
	
		$arprice = \yii\helpers\ArrayHelper::index(\app\models\Tovar::find()->where(['shop_id' => $shop_id])->asArray()->all(), 'artikul');
	//\yii\helpers\VarDumper::dump($arprice,10,true);die;	
		foreach($rows as $row){
			$order = $client = $utm = $tovar = [];
			$client_id = $order_id = false;
			//\yii\helpers\VarDumper::dump($row,10,true);								
			
			//client
			\app\components\Tools::processData(\app\components\Tools::joinString([$row->region,$row->area,$row->city,$row->adress]),$client,'address');
			\app\components\Tools::processData($row->indexx,$client,'postcode');
			\app\components\Tools::processData($row->email,$client,'email');
			\app\components\Tools::processData($row->fio,$client,'fio');
			\app\components\Tools::processData(\app\components\Tools::format_phone($row->phone),$client,'phone');
			if(count($client) >0 and null === (\app\models\Client::find()->where(['phone' => $client['phone']])->one())) {				
				$client['shop_id'] = $shop_id;
				$mdl = new \app\models\Client;
				foreach($client as $k=>$v){
					$mdl->$k = $v;
				}
				if($mdl->save()) echo "\nClient ".$client['phone']." save.";
				else echo "\nClient ".$client['phone']." NOT save.";
			}
			$client_id = \app\models\Client::find()->where(['phone' => $client['phone']])->one();
			if(null!==$client_id) $client_id = $client_id->id;
			else $client_id = false;
			//$client_id = $client_id->id;
			
			
			//order
			if($client_id !==false and $client_id >0) {
			$order['client_id'] = $client_id;
			$order['old_id2'] = $row->id;
			$order['date_at'] = ($row->date0);
			$order['shop_id'] = $shop_id;
			\app\components\Tools::processData($row->old_id,$order,'old_id');
			if($row->obrabotka >0) {
				\app\components\Tools::processData('4',$order,'status');
			}
			elseif($row->ddouble >0) {
				\app\components\Tools::processData('2',$order,'status');
				//\app\components\Tools::processData($row->datadouble,$order,'data_duble');
				//if(array_key_exists('data_duble',$order)) $order['data_duble'] = strtotime($order['data_duble']);
			}
			elseif($row->zakaz =='1') {
				\app\components\Tools::processData('6',$order,'status');				
			}
			elseif($row->otkaz >0) {
				\app\components\Tools::processData('7',$order,'status');				
			}
			elseif($row->zakaz == '3') {
				\app\components\Tools::processData('4',$order,'status');				
			}
			elseif($row->zakaz == '2') {
				\app\components\Tools::processData('7',$order,'status');				
			}
			elseif($row->zakaz == '4') {
				\app\components\Tools::processData('5',$order,'status');				
			}
			elseif($row->zakaz == '5') {
				\app\components\Tools::processData('3',$order,'status');				
			}
			//\app\components\Tools::processData($row->prich,$order,'prich_double');
			\app\components\Tools::processData($row->dater,$order,'updated_at');
			if(array_key_exists('updated_at',$order)) $order['updated_at'] = ($order['updated_at']);//strtotime
			\app\components\Tools::processData($row->otprav,$order,'otpravlen');
			\app\components\Tools::processData($row->dataotp,$order,'data_otprav');
			if(array_key_exists('data_otprav',$order)) $order['data_otprav'] = ($order['data_otprav']);
			\app\components\Tools::processData($row->summaotp,$order,'summaotp');
			\app\components\Tools::processData($row->dostavlen,$order,'dostavlen');
			\app\components\Tools::processData($row->datados,$order,'data_dostav');
			if(array_key_exists('data_dostav',$order)) $order['data_dostav'] = ($order['data_dostav']);
			\app\components\Tools::processData($row->identif,$order,'identif');
			\app\components\Tools::processData($row->oplachen,$order,'oplachen');
			\app\components\Tools::processData($row->dataopl,$order,'data_oplata');
			if(array_key_exists('data_oplata',$order)) $order['data_oplata'] = ($order['data_oplata']);
			\app\components\Tools::processData($row->dostavza,$order,'dostavza');
			\app\components\Tools::processData($row->vkasse,$order,'vkasse');
			\app\components\Tools::processData($row->datavkas,$order,'data_vkasse');
			if(array_key_exists('data_vkasse',$order)) $order['data_vkasse'] = ($order['data_vkasse']);
			\app\components\Tools::processData($row->ret,$order,'vozvrat');
			\app\components\Tools::processData($row->date_return,$order,'data_vozvrat');
			if(array_key_exists('data_vozvrat',$order)) $order['data_vozvrat'] = ($order['data_vozvrat']);
			\app\components\Tools::processData($row->return_cost,$order,'vozvrat_cost');
			\app\components\Tools::processData($row->prich,$order,'note');
			\app\components\Tools::processData($row->discount,$order,'discount');
			\app\components\Tools::processData($row->fast,$order,'fast');
			\app\components\Tools::processData($row->ip_address,$order,'ip_address');
			if($row->keyword == 'Звонок') $order['source'] = '2';
			else $order['source'] = '1';
			if($row->sender == 'cdek') $order['sender_id'] = '3';
			else $order['sender_id'] = '2';//почта
			\app\components\Tools::processData($row->user,$order,'manager_id');
			if(array_key_exists('manager_id',$order)) $order['manager_id'] = $arUser[$order['manager_id']];
			\app\components\Tools::processData($row->packer_id,$order,'packer_id');
			if(array_key_exists('packer_id',$order)) $order['packer_id'] = $arUser[$order['packer_id']];
			\app\components\Tools::processData($row->url,$order,'url');
			if($row->sklad =='msk') $order['sklad'] = '4';
			else $order['sklad'] = '3';
			\app\components\Tools::processData($row->type_oplata,$order,'type_oplata');
			if(count($order) >0 and null === (\app\models\Orders::find()->where(['old_id2' => $order['old_id2']])->one())) {				
				$mdl = new \app\models\Orders;
				foreach($order as $k=>$v){
					$mdl->$k = $v;
				}
				if($mdl->save()) echo "\nOrder ".$mdl->old_id2.'/'.$mdl->id." save.";
				else '\nOrder not save.';
			}
			}
			
			$order_id = \app\models\Orders::find()->where(['old_id2' => $row->id])->one();
			if(null!==$order_id) $order_id = $order_id->id;
			else $order_id = false;
			
			//utm
			if($order_id !==false and $order_id >0) {
			\app\components\Tools::processData($row->keyword,$utm,'utm_term');
			\app\components\Tools::processData($row->posit,$utm,'position');
			\app\components\Tools::processData($row->positt,$utm,'position_type');
			\app\components\Tools::processData($row->who,$utm,'utm_source');
			if(array_key_exists('utm_source',$utm)) {
				if((stripos($utm['utm_source'], 'yandex')!==false) or (stripos($utm['utm_source'], 'direct')!==false))
				$utm['utm_source'] = 'yandex';
			}
			\app\components\Tools::processData($row->typep,$utm,'source_type');
			\app\components\Tools::processData($row->plosh,$utm,'source');
			if($row->idc1 >0)
				\app\components\Tools::processData($row->idc1,$utm,'utm_campaign');
			elseif($row->idc2 >0)
				\app\components\Tools::processData($row->idc2,$utm,'utm_campaign');					
				//\app\components\Tools::processData($row->kompany,$utm,'utm_campaign');
			\app\components\Tools::processData($row->region_name,$utm,'region_name');
			if(count($utm) >0 and $utm['utm_term'] != 'Звонок' and $utm['utm_term'] != 'Заказ с сайта' and null === (\app\models\UtmLabel::find()->where(['order_id' => $order_id])->one())) {				
				$utm['order_id'] = $order_id;
				$mdl = new \app\models\UtmLabel;
				foreach($utm as $k=>$v){
					$mdl->$k = $v;
				}
				if($mdl->save()) echo "\nUtm save.";
				else echo '\nUtm NOT save.';
			}			
			}
			
			//tovar_rashod
			if($order_id !==false and $order_id >0) :
			for($i = 1; $i < 6; $i++){
				$p = $art = null;
				$a = "art{$i}";				
				$k = "kolvo{$i}";
				$s = "summa{$i}";				
				$tovar = [];//\yii\helpers\VarDumper::dump($row->$a,10,true);
				if(!empty($row->$a)) {//or $row->$a !='' or !is_null($row->$a)
					if(empty($row->$k)) $row->$k = 1;
					$art = strtoupper($this->_art($row->$a));
					
					if(array_key_exists($art, $arprice)) {
						
						if(null === (\app\models\TovarRashod::find()->where(['order_id' => $order_id,'tovar_id'=>$arprice[$art]['id']])->one())) {		
							
							$mdl = new \app\models\TovarRashod;
							$mdl->order_id = $order_id;
							$mdl->tovar_id = $arprice[$art]['id'];
							$mdl->shop_id = $shop_id;
							$mdl->price = $row->$s / $row->$k;
							$mdl->amount = $row->$k;
							$mdl->sklad_id = $order['sklad'];
							if($mdl->save()) {echo '#'.$row->id." \nRashod ".$arprice[$art]['name'].' save.';}
							else {echo '#'.$row->id." \nRashod ".$arprice[$art]['name'].' NOT save. '; print_r($mdl->firstErrors);}
						}
						else echo 'Tovar '.$row->$a.' in db';
					}
					else echo 'Tovar '.$row->$a.' NOT in price';				
				}
				else echo 'Tovar #'.$i.' '.$row->$a.' NOT if';				
			}
			endif;
			
			echo '<hr>';
			//\yii\helpers\VarDumper::dump($order,10,true);
			//\yii\helpers\VarDumper::dump($client,10,true);
			//\yii\helpers\VarDumper::dump($utm,10,true);
		}		
		
	}
	private function _art($art) {
		//if(strtoupper(substr($art, 0,2)) == 'NF') $art = str_ireplace("NF", "HL", $art);
		//if(stripos($art, 'hl-') !==false) $art = str_ireplace("hl-", "HL", $art);
		if(substr($art, 0,2) == 'b.') $art = str_ireplace("b.", "", $art);
		if(stripos($art, 'nf600') !==false) $art = 'HL600';
		elseif(stripos($art, 'hl600') !==false) $art = 'HL600';
		elseif(stripos($art, 'hl 600') !==false) $art = 'HL600';
		elseif(stripos($art, 'hl 900') !==false) $art = 'HL900';
		elseif(stripos($art, 'hl 2200') !==false) $art = 'HL2200';
		elseif(stripos($art, 'hl-2200') !==false) $art = 'HL2200';
		elseif(stripos($art, 'hl4000') !==false) $art = 'HL4000';
		elseif(stripos($art, 'hl-720') !==false) $art = 'HL720';
		elseif(stripos($art, 'hl-t700') !==false) $art = 'HLT700';
		elseif(stripos($art, 'hl-855') !==false) $art = 'HL855';
		elseif(stripos($art, 'hl29') !==false) $art = 'HL29';
		elseif(stripos($art, 'hl-29') !==false) $art = 'HL29';
		elseif(stripos($art, 'hl-39') !==false) $art = 'HL39';
		elseif(stripos($art, 'pf900') !==false) $art = 'PF900';
		elseif(stripos($art, 'pf901') !==false) $art = 'PF901';
		elseif(stripos($art, 'pf902') !==false) $art = 'PF902';
		elseif(stripos($art, 'pf903') !==false) $art = 'PF903';
		elseif(stripos($art, 'pf904') !==false) $art = 'PF904';
		elseif(stripos($art, 'pf-02') !==false) $art = 'PF02';
		elseif(stripos($art, 'pf02') !==false) $art = 'PF02';
		elseif(stripos($art, 'pf-03') !==false) $art = 'PF03';
		elseif(stripos($art, 'pf-04') !==false) $art = 'PF04';
		elseif(stripos($art, 'pf-05') !==false) $art = 'PF05';
		elseif(stripos($art, 'pf-07') !==false) $art = 'PF07';
		elseif(stripos($art, 'pf-09') !==false) $art = 'PF09';
		elseif(stripos($art, 'hl-t6') !==false) $art = 'HLT6';
		elseif(stripos($art, 'hlt') !==false) $art = 'HLT1';
		elseif(stripos($art, 'hl-t') !==false) $art = 'HLT1';		
		elseif(stripos($art, 'hl-100') !==false) $art = 'HLT100';
		elseif(stripos($art, 'hl-101d') !==false) $art = 'HLT101D';
		elseif(stripos($art, 'hl-102d') !==false) $art = 'HLT102D';
		elseif(stripos($art, 'hl170') !==false) $art = 'HL170';
		elseif(stripos($art, 'nf170') !==false) $art = 'HL170';
		elseif(stripos($art, 'hl300') !==false) $art = 'HL300';
		elseif(stripos($art, 'hl500') !==false) $art = 'HL500';
		elseif(stripos($art, 'nf500') !==false) $art = 'HL500';
		elseif(stripos($art, 'hl-500') !==false) $art = 'HL500';
		elseif(stripos($art, 'nf-500') !==false) $art = 'HL500';		
		elseif(stripos($art, 'hl-87') !==false) $art = 'HLT87';
		elseif(stripos($art, 'hl-12s') !==false) $art = 'HL12S';
		elseif(stripos($art, 'g85') !==false) $art = 'G85';
		elseif(stripos($art, '85g') !==false) $art = 'G85';
		elseif(stripos($art, 'hl-p') !==false) $art = 'HLP1';
		elseif(mb_stripos($art, 'фонарь-дубинка') !==false) $art = 'HLP1';
		elseif(mb_stripos($art, 'охотник') !==false or mb_stripos($art, 'комплект') !==false) $art = 'komplekt';
		elseif(stripos($art, 'liion') !==false) $art = 'A5800';
		elseif(stripos($art, '18650') !==false and stripos($art, '5200') !==false) $art = 'A5200';
		elseif(stripos($art, '18650') !==false and stripos($art, '5800') !==false) $art = 'A5800';
		elseif(stripos($art, 'video3in1') !==false) $art = 'gps-ve-450r';
		elseif(stripos($art, 'gamo') !==false and stripos($art, '3') !==false and stripos($art, '9x40') !==false) $art = 'gamo3-9x40';
		elseif(stripos($art, 'gamo') !==false and stripos($art, '3') !==false and stripos($art, '9x32') !==false) $art = 'gamo3-9x32';
		elseif(stripos($art, 'Bushnell') !==false and stripos($art, '3') !==false and stripos($art, '9x40') !==false) $art = 'bushnell3-9x40';
		elseif(stripos($art, 'Bushnell') !==false and stripos($art, '3') !==false and stripos($art, '9x32') !==false) $art = 'bushnell3-9x32';
		elseif(stripos($art, 'OPTIK-B3') !==false and stripos($art, '3') !==false and stripos($art, '9x40') !==false) $art = 'bushnell3-9x40';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') !==false and stripos($art, '60x60') !==false) $art = 'alpen10-60x60';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') !==false and stripos($art, '50x50') !==false) $art = 'alpen10-50x50';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') !==false and stripos($art, '70x70') !==false) $art = 'alpen10-70x70';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') ===false and stripos($art, '60x60') !==false) $art = 'alpen60x60';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') ===false and stripos($art, '50x50') !==false) $art = 'alpen50x50';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') ===false and stripos($art, '70x70') !==false) $art = 'alpen70x70';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') !==false and stripos($art, '60x60') !==false) $art = 'bresser10-60x60';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') !==false and stripos($art, '50x50') !==false) $art = 'bresser10-50x50';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') !==false and stripos($art, '70x70') !==false) $art = 'bresser10-70x70';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') !==false and stripos($art, '90x80') !==false) $art = 'bresser10-90x80';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') ===false and stripos($art, '60x60') !==false) $art = 'bresser60x60';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') ===false and stripos($art, '50x50') !==false) $art = 'bresser50x50';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') ===false and stripos($art, '70x70') !==false) $art = 'bresser70x70';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') !==false and stripos($art, '60x60') !==false) $art = 'bushnell10-60x60';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') !==false and stripos($art, '50x50') !==false) $art = 'bushnell10-50x50';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') !==false and stripos($art, '70x70') !==false) $art = 'bushnell10-70x70';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') ===false and stripos($art, '60x60') !==false) $art = 'bushnell60x60';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') ===false and stripos($art, '50x50') !==false) $art = 'bushnell50x50';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') ===false and stripos($art, '70x70') !==false) $art = 'bushnell70x70';
		elseif((stripos($art, 'bino-n') !==false or stripos($art, 'nikon') !==false) and stripos($art, '10') !==false and stripos($art, '90x80') !==false) $art = 'nikon10-90x80';
		elseif((stripos($art, 'bino-n') !==false or stripos($art, 'nikon') !==false) and stripos($art, '10') !==false and stripos($art, '90*80') !==false) $art = 'nikon10-90x80';
		elseif((stripos($art, 'bino-n') !==false or stripos($art, 'nikon') !==false) and stripos($art, '10') !==false and stripos($art, '60x60') !==false) $art = 'nikon10-60x60';
		elseif((stripos($art, 'bino-n') !==false or stripos($art, 'nikon') !==false) and stripos($art, '10') ===false and stripos($art, '28x40') !==false) $art = 'nikon28x40';
		elseif(stripos($art, 'N83250-1') !==false) $art = 'nikon8-32x50';
		elseif(stripos($art, 'p5050') !==false) $art = 'poisk50x50';
		elseif(stripos($art, 'BAIGISH') !==false and stripos($art, '10') ===false and stripos($art, 'af') !==false and stripos($art, '70x70') !==false) $art = 'baigish70x70';
		elseif(stripos($art, 'bino-b70x70') !==false) $art = 'baigish70x70';
		elseif(stripos($art, 'b50x50') !==false) $art = 'baigish50x50';
		elseif(stripos($art, 'bino-b32x40') !==false) $art = 'baigish32x40';
		elseif(stripos($art, 'b30x50') !==false) $art = 'baigish30x50';
		elseif(stripos($art, 'bino-b20x50') !==false) $art = 'baigish20x50';
		elseif(stripos($art, 'bino-b20x40') !==false) $art = 'baigish20x40';
		elseif(stripos($art, 'bino-b10x40') !==false) $art = 'baigish10x40';
		elseif(stripos($art, 'bino-b10-90x80') !==false) $art = 'baigish10-90x80';
		elseif(stripos($art, 'BINO-BRECCER70X70') !==false) $art = 'breaker70x70';
		elseif(stripos($art, 'BINO-BRECCER50X50') !==false) $art = 'breaker50x50';
		elseif(stripos($art, 'BINO-BREAKER70X70') !==false) $art = 'breaker70x70';
		elseif(stripos($art, 'LEAPERS') !==false and stripos($art, '6') !==false and stripos($art, '24x50') !==false) $art = 'leapers6-24x50';
		elseif(stripos($art, 'MONIKUL-BUSH95X52') !==false) $art = 'bushnell95x52';
		else $art = $art;
		return $art;
	}
*/
}
