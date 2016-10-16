<?php
namespace app\controllers;

//error_reporting(E_ERROR);// | E_WARNING);
ini_set('memory_limit', '2048M');
set_time_limit(600);

use Yii;
use app\models\Statcompany;
use app\models\UtilsUploadStat;
use app\models\UtilsDownloadSend;
use app\models\UtilsUploadRpo;
use yii\web\UploadedFile;
use yii\data\ArrayDataProvider;
use app\models\Orders;
use app\models\Category;
use app\models\Websites;
use app\models\Tovar;


//ini_set('display_errors',1);

class UtilsController extends \app\components\BaseController
{
    public function actionImportstat()
    {    	
    	$errors = $result = [];
    	$temp = null;
   		$shop_id = Yii::$app->params['user.current_shop'];
   	
    	$mdlUpload = new UtilsUploadStat();

    	if($mdlUpload->load(Yii::$app->request->post()))//(Yii::$app->request->isPost)
    	{
            $mdlUpload->statFile = UploadedFile::getInstance($mdlUpload, 'statFile');
            
            if ($mdlUpload->upload()) {        
                
				$xls = $mdlUpload->statFile->tempName;	       		        
				$objPHPExcel = \PHPExcel_IOFactory::load($xls);		
				$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);				
				
				$n=0;
				foreach($sheetData as $row) {		//\yii\helpers\VarDumper::dump($row,7,true);							
					//$host = $name = $date = $id_company = false;
					$data = [];
					
					if (is_numeric($row['A']))					
						$id_company = (int) $row['A'];
					
					if (is_string($row['B']))
						$name = $row['B'];
					
					if (date_create_from_format('d.m.Y', $row['C'])) {
						$date = date_format(date_create_from_format('d.m.Y', $row['C']), 'Y-m-d');						
					}
					else {
						$id_company = '';
						$name = '';				
					}

				/*
					$host = Websites::findOne(['host' => $ar_name['2'], 'shop_id' => $shop_id]);
					if(!is_null($host)) 
						$data['site_id'] = $host->id;
					else $host = false;
				*/
				//\yii\helpers\VarDumper::dump($data,7,true);
					if ($id_company && $name && $date)// && $host)
					{
						//яндекс, точняк
						$data['source'] = 'yandex';
						$ar_name = explode(' ',$name);
						//$host =  filter_var('http://'.$ar_name['2'], FILTER_VALIDATE_URL);
						if($ar_name['0'] == 'rus' or $ar_name['0'] == 'msk') {
							$category = $ar_name['1'];
							$host = $ar_name['2'];
							$art = $ar_name['3'];
						}
						else {
							$category = $ar_name['0'];
							$host = $ar_name['1'];
							$art = $ar_name['2'];
						}					
						$data['id_company'] = $id_company;
						$data['name'] = $name;
						$data['date_at'] = $date;
						$data['shows'] = $row['D'];
						$data['clicks'] = $row['E'];
						$data['costs'] = $row['G'];
						
						
						
						//%region = $ar_name['0'];
						
						//$category = mb_substr(strval($ar_name['1']),0,-1);
						$category = mb_substr(strval($category),0,-1);
						$temp = Category::find()->where("name like '$category%'")->andWhere(['shop_id' => $shop_id])->one();
						if(!is_null($temp)) 
							$data['category_id'] = $temp->id;
						elseif(!empty($row['F']))
							$data['category_id'] = $row['F'];
							
						//$data['site_id'] = filter_var('http://'.$host, FILTER_VALIDATE_URL);
						if(\app\components\Tools::is_url($host) == 1) $data['host'] = $host;
															
						$temp = Tovar::findOne(['artikul' => \app\components\Tools::art($art), 'shop_id' => $shop_id]);
						if(!is_null($temp)) 
							$data['tovar_id'] = $temp->id;
						//мало ли что
						else
							$temp = Tovar::findOne(['artikul' => \app\components\Tools::art($host), 'shop_id' => $shop_id]);
						if(!is_null($temp)) 
							$data['tovar_id'] = $temp->id;
						 
						$data['shop_id'] = $shop_id;
						if(!array_key_exists('source', $data)) $data['source'] = $mdlUpload->source;
						
						$model = new Statcompany();
		//\yii\helpers\VarDumper::dump($data,7,true);				
						$model->attributes = $data;
						if ($model->validate()) {
							$verify = Statcompany::findOne([
							    'id_company' => $data['id_company'],
							    'date_at' => $data['date_at'],
							]);
							if(!is_null($verify)) {
								$verify->attributes = $data;
								$verify->save();
							}
							else {
								$model->save();
							}							
							$result[] = $data;
						} else {
						    // проверка не удалась:  $errors - это массив содержащий сообщения об ошибках
						    $errors[] = $model->errors;
						}
						/*
						$transaction = Statcompany::getDb()->beginTransaction();
						try {
						    $model->load($data);
						    $customer->save();
						    // ...другие операции с базой данных...
						    $transaction->commit();
						} catch(\Exception $e) {
						    $transaction->rollBack();
						    throw $e;
						}
						*/
					}
					$n++;
					//if($n==500) die;//break;
				}				
				
			    $provider = new ArrayDataProvider([
				    'allModels' => $result,
				    'pagination' => [
				        'pageSize' => 1000,
				    ],
				    /*'sort' => [
				        'attributes' => ['id', 'name'],
				    ],*/
				]);
				$_POST = null;
				return $this->render('importstat', [
		    		'errors' => $errors,
		    		'data' => $result,
		    		'provider' => $provider,
		    		'model'=>$mdlUpload
				]);
        		
			}	        
	    }
	           
        else {
			return $this->render('formUploadStat', ['model'=>$mdlUpload]);
		}

	}
	
	public function actionExportmsk() {
		
		$results = $errors = [];
                
        $mdl = new UtilsDownloadSend();
        
        if ($mdl->load(Yii::$app->request->post()) && $mdl->validate()) {
	        
			$fileName = 'termo-to_msk-'.date('Y-m-d').'.xls';
			  
			//$res = false;
			//$res = Orders::find()->where(['otpravlen' => '0', 'status' => '6'])->with(['client','sender' => function ($query) {
	        //	$query->andWhere(['code' => 'russianpost']);}])->all();
	        $res = Orders::find()->where(['otpravlen' => '0', 'status' => '6', 'sender_id' => $mdl->sender])->with('client','rashod', 'rashod.tovar')->all();//russianpost
	        //\yii\helpers\VarDumper::dump($res->sql,5,TRUE);die;
			  
			if(empty($res)) $errors[] = "заявок - НЕТ!";
			else {
				$objReader = \PHPExcel_IOFactory::createReader('Excel5');
				
				$objPHPExcel = $objReader->load(Yii::$app->basePath."/../lib/template/tpl_to_msk.xls");
					
				// делаем первую страницу активной
				$page = $objPHPExcel->setActiveSheetIndex(0);
				$num=2;
				foreach($res as $row) {
					$tovar = '';
					$itogo = 0;
					
					foreach($row->rashod as $rashod) {
						$itogo += $rashod->amount * $rashod->price;
						$tovar .= $rashod->tovar->name. ' ('.$rashod->amount.'шт); ';
					}
					
					$type_oplata = 'неизвестно';
					if($row['type_oplata'] ==1) $type_oplata = 'Наложенный';
					elseif($row['type_oplata'] ==2) $type_oplata = 'Предоплата';
					elseif($row['type_oplata'] ==3) $type_oplata = 'Наличными';		
					// заполняем лист
					$page->setCellValue("A$num", $num-1);
					$page->setCellValue("B$num", $row->client->postcode);
					$page->setCellValue("C$num", $row->client->region->pname);
					$page->setCellValue("D$num", $row->client->area->kname);
					$page->setCellValue("E$num", $row->client->city->kname);
					$page->setCellValue("F$num", $row->client->settlement->kname.' '.$row->client->address);
					$page->setCellValue("G$num", $row->client->fio);
					$page->setCellValue("I$num", $itogo);
					$page->setCellValue("J$num", $type_oplata);
					$page->setCellValue("K$num", $row->id);
					$page->setCellValue("L$num", $tovar);//$row->note
					$num++;
				}
	//\yii\helpers\VarDumper::dump($res,7,true);
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
		}
		return $this->render('downloadSend',['model'=>$mdl, 'errors'=>$errors]);
	}

    public function actionImportrpo() {
		$errors = [];
        $num = 0;
		$count = 0;        
        $mdlUpload = new UtilsUploadRpo();
  
      	if (Yii::$app->request->isPost) {
      		
            $mdlUpload->statFile = UploadedFile::getInstance($mdlUpload, 'statFile');
            $mdlUpload->sender = $_POST['UtilsUploadRpo']['sender'];
            
            if ($mdlUpload->validate() and $mdlUpload->upload()) {        
                
				$xls = $mdlUpload->statFile->tempName;	       		        
				
				$date = filectime($xls);
				if($date) $date = date('Y-m-d', $date);
				else $date = date('Y-m-d');
				
				$objPHPExcel = \PHPExcel_IOFactory::load($xls);
				$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);				
		//echo '<pre>';print_r($sheetData);echo '</pre>';	die;	
				//почта
				if($sheetData['1']['B'] == 'INDEXTO') {					
				
					foreach($sheetData as $row) {
						++$num;
				
						if (is_numeric($row['A'])) {
							$identif = $row['A'];
							
							$order = Orders::findOne(['identif'=>$identif, 'otpravlen'=> '0']);
							
							$summa = $row['M'] + $row['N'];
							
							if (!is_null($order)) {								
								$order->otpravlen = '1';
								$order->data_otprav = $date;
								$order->summaotp = $summa;
								if($order->save()) $count = $count + 1;
								$errors[] = ($order->errors);												
							}
							else {
								$id = (int) ($row['Q']);
								$order = Orders::findOne($id);
								if (!is_null($order)) {						
									$order->otpravlen = '1';
									$order->data_otprav = $date;
									$order->summaotp = $summa;
									$order->identif = $identif;
									if($order->save()) $count = $count + 1;
									$errors[] = ($order->errors);
								}
							}
						}							
					}
				}
				//сдэк
				elseif($sheetData['1']['A'] == 'Номер накладной') {//echo $mdlUpload->sender;// == '3') {
					foreach($sheetData as $row) {
						
						if (is_numeric($row['A'])) {
							$identif = $row['A'];
							$order = Orders::findOne(['identif'=>$identif, 'otpravlen'=> '0']);
							
							if (!is_null($order)) {									
								$date = date_create_from_format('d.m.Y', $row['B']);						
								$date = date_format($date, 'Y-m-d');
								$summa = $row['J'];
								$order->otpravlen = '1';
								$order->data_otprav = $date;
								$order->summaotp = $summa;
								if($order->save()) $count = $count + $n;
								$errors[] = ($order->errors);				
							}
						}							
					}
				}								
			}	        
	    }                
       
        return $this->render('formUploadRpo', ['model'=>$mdlUpload, 'num'=>$num, 'count'=>$count, 'errors'=>$errors]);
		
	}
    
    public function actionIndex()
    {
        return $this->render('index');
    }

	/**
	 * Возвращает сумму прописью
	 * @author runcore
	 * @uses morph(...)
	 */
	/*public static function num2str($num) {
	    $nul='ноль';
	    $ten=array(
	        array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
	        array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
	    );
	    $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
	    $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
	    $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
	    $unit=array( // Units
	        array('копейка' ,'копейки' ,'копеек',    1),
	        array('рубль'   ,'рубля'   ,'рублей'    ,0),
	        array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
	        array('миллион' ,'миллиона','миллионов' ,0),
	        array('миллиард','милиарда','миллиардов',0),
	    );
	    //
	    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
	    $out = array();
	    if (intval($rub)>0) {
	        foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
	            if (!intval($v)) continue;
	            $uk = sizeof($unit)-$uk-1; // unit key
	            $gender = $unit[$uk][3];
	            list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
	            // mega-logic
	            $out[] = $hundred[$i1]; # 1xx-9xx
	            if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
	            else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
	            // units without rub & kop
	            if ($uk>1) $out[]= self::_morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
	        } //foreach
	    }
	    else $out[] = $nul;
	    $out[] = self::_morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
	    $out[] = $kop.' '.self::_morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
	    return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
	}
*/
	/**
	 * Склоняем словоформу
	 * @ author runcore
	 */
/*	public static function _morph($n, $f1, $f2, $f5) {
	    $n = abs(intval($n)) % 100;
	    if ($n>10 && $n<20) return $f5;
	    $n = $n % 10;
	    if ($n>1 && $n<5) return $f2;
	    if ($n==1) return $f1;
	    return $f5;
	}
*/	
	//новые приключения с адресом TODO wordwrap
/*	public static function wordwrapaddress($s, &$firstLine, &$secondLine, &$thirdLine) {
		$address = $s;
		
		$firstLineLen = 50;//34;
		$secondLineLen = 42;//38;
		$thirdLineLen = 30;//26;
	
		// first line
		if (mb_strlen($address) < $firstLineLen) 
		{
			$firstLine = $address;
			return;
		}
		
		//$firstLine = utf8_wordwrap($address, $firstLineLen, "\n", true);
		
		$firstLine = mb_substr($address, 0, $firstLineLen);
		$address = mb_substr($address, $firstLineLen);

		//second line
		if (mb_strlen($address) < $secondLineLen) 
		{
			$secondLine = $address;
			return;
		}
		
		$secondLine = mb_substr($address, 0, $secondLineLen);
		$address = mb_substr($address, $secondLineLen);
		
		//third line
		$thirdLine = mb_substr($address, 0);
	}
	*/
}