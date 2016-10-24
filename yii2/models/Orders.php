<?php
namespace app\models;

use Yii;
use app\models\Client;
use app\models\Category;
use app\models\Sms;
use app\models\Senders;
use app\modules\user\models\User;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use app\components\LiveInform;
/**
 * This is the model class for table "order".
 *
 * @property string $id
 * @property string $created_at
 * @property integer $status
 * @property string $dublicate
 * @property string $otpravlen
 * @property string $dostavlen
 * @property string $oplachen
 * @property string $vkasse
 * @property string $vozvrat
 * @property string $vozvrat_cost
 * @property string $prich_double
 * @property string $prich_vozvrat
 * @property string $summaotp
 * @property string $discount
 * @property string $identif
 * @property integer $dostavza
 * @property string $manager_id
 * @property string $category_id
 * @property integer $fast
 * @property string $packer_id
 * @property string $url
 * @property integer $client_id
 * @property integer $tclient
 * @property string $note
 * @property string $ip_address
 */
class Orders extends \yii\db\ActiveRecord
{
	public $price_old = array();
	public $price_new = array();
	public $cnt_all;
	public $summ;  
	public $totalSumm;
/*
	public function init(){		
		$this->shop_id = Yii::$app->params['user.current_shop'];
		parent::init();
	}
*/
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders';
    }
    
    public function behaviors()
	{
	    return [
	        TimestampBehavior::className(),
	        BlameableBehavior::className(),
	    ];
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source'],'required'],
            [['date_at', 'created_at', 'updated_at', 'ip_address', 'sklad', 'updated_by', 'created_by',  'old_id', 'old_id2'], 'safe'],
            [['status', 'dostavza', 'manager_id', 'category_id', 'fast', 'packer_id', 'client_id', 'tclient', 'otpravlen', 'dostavlen', 'oplachen', 'vkasse', 'vozvrat', 'source', 'sender_id', 'type_oplata', 'send_moskva'], 'integer'],
            [['status', 'dostavza', 'manager_id', 'category_id', 'fast', 'packer_id', 'client_id', 'tclient', 'otpravlen', 'dostavlen', 'oplachen', 'vkasse', 'vozvrat', 'source', 'sender_id', 'type_oplata'], 'filter', 'filter' => 'intval'],
            [['vozvrat_cost', 'summaotp', 'discount', 'totalSumm'], 'number'],
            [['identif', 'prich_double', 'prich_vozvrat', 'note'], 'string'],
           // [['client_id'], 'required'],                        
            [['url'], 'string', 'max' => 255],
            ['data_otprav', 'required',
            	'when' => function($model) {return $model->otpravlen == '1';},
            	'whenClient' => "function (attribute, value) {
		        	if ($('#orders-otpravlen:checked').length) return true;
		    	}",
		    ],
		    ['data_dostav', 'required',
            	'when' => function($model) {return $model->dostavlen == '1';},
            	'whenClient' => "function (attribute, value) {
		        	if ($('#orders-dostavlen:checked').length) return true;
		    	}",
		    ],
		    ['data_oplata', 'required',
            	'when' => function($model) {return $model->oplachen == '1';},
            	'whenClient' => "function (attribute, value) {
		        	if ($('#orders-oplachen:checked').length) return true;
		    	}",
		    ],
		    ['data_vkasse', 'required',
            	'when' => function($model) {return $model->vkasse == '1';},
            	'whenClient' => "function (attribute, value) {
		        	if ($('#orders-vkasse:checked').length) return true;
		    	}",
		    ],
		    ['data_vozvrat', 'required',
            	'when' => function($model) {return $model->vozvrat == '1';},
            	'whenClient' => "function (attribute, value) {
		        	if ($('#orders-vozvrat:checked').length) return true;
		    	}",
		    ],		    
            [['created_at', 'dostavza', 'data_duble', 'manager_id', 'category_id', 'fast', 'packer_id', 'client_id', 'tclient', 'data_otprav',  'data_dostav', 'data_oplata', 'data_vkasse', 'data_vozvrat', 'ip_address', 'url', 'identif', 'prich_double', 'prich_vozvrat', 'note', 'vozvrat_cost', 'summaotp', 'discount', 'updated_by', 'updated_at', 'sender_id', 'old_id', 'old_id2'], 'default', 'value' => null],
            [['identif', 'prich_double', 'prich_vozvrat', 'note', 'vozvrat_cost', 'summaotp', 'discount'], 'trim'],
			[['status'], 'required', 'requiredValue'=>'6',
				'when' => function($model) {return $model->otpravlen == '1';},
            	'whenClient' => "function (attribute, value) {
		        	if ($('#orders-otpravlen:checked').length) return true;
		    	}",
		    	'message' => 'Состояние заявки должно быть "Заказ"',
		    ],
           [['status'], 'required', 'requiredValue'=>'6',
            	'when' => function($model) {return $model->dostavlen == '1';},
            	'whenClient' => "function (attribute, value) {
		        	if ($('#orders-dostavlen:checked').length) return true;
		    	}",
		    	'message' => 'Состояние заявки должно быть "Заказ"',
		    ],
		   [['status'], 'required', 'requiredValue'=>'6',
            	'when' => function($model) {return $model->oplachen == '1';},
            	'whenClient' => "function (attribute, value) {
		        	if ($('#orders-oplachen:checked').length) return true;
		    	}",
		    	'message' => 'Состояние заявки должно быть "Заказ"',
		    ],
		   [['status'], 'required', 'requiredValue'=>'6',
            	'when' => function($model) {return $model->vkasse == '1';},
            	'whenClient' => "function (attribute, value) {
		        	if ($('#orders-vkasse:checked').length) return true;
		    	}",
		    	'message' => 'Состояние заявки должно быть "Заказ"',
		    ],
		   [['status'], 'required', 'requiredValue'=>'6',
            	'when' => function($model) {return $model->vozvrat == '1';},
            	'whenClient' => "function (attribute, value) {
		        	if ($('#orders-vozvrat:checked').length) return true;
		    	}",
		    	'message' => 'Состояние заявки должно быть "Заказ"',
		    ],
		    [['status'], 'required', 'requiredValue'=>'7',
            	'when' => function($model) {return $model->prich_double > '0';},
            	'whenClient' => "function (attribute, value) {
		        	if ($('#orders-prich_double:checked').length) return true;
		    	}",
		    	'message' => 'Состояние заявки должно быть "Отказ"',
		    ],
        ];
    }
 /*   
    public function checkExist() {
		if((!empty($this->otpravlen) and empty($this->data_otprav)) or (empty($this->otpravlen) and !empty($this->data_otprav)))
		{
			$errorMsg= 'Галка должна быть с датой';
			$this->addError('otpravlen',$errorMsg);
			//$this->addError('data_otprav',$errorMsg);
		}
	}
*/
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date_at' => 'Дата',
            'created_at' => 'Cоздан',
            'updated_at' => 'Изменен',
            'created_by' => 'Кто создал',
            'updated_by' => 'Кто изменил',
            'status' => 'Состояние',
            'data_duble' => 'Дата дубля',          
            'otpravlen' => 'Отправлен',
            'data_otprav' => 'Дата отправки',
            'dostavlen' => 'Доставлен',
            'data_dostav' => 'Дата доставки',
            'oplachen' => 'Оплачен',
            'data_oplata' => 'Дата оплаты',
            'vkasse' => 'В кассе',
            'data_vkasse' => 'Дата в кассе',
            'vozvrat' => 'Возврат',
            'data_vozvrat' => 'Дата возврата',
            'vozvrat_cost' => 'Возврат руб',
            'prich_double' => 'Причина дубля/отказа',
            'prich_vozvrat' => 'Возврат причина',
            'summaotp' => 'Цена доставки',
            'discount' => 'Скидка на заказ',
            'identif' => 'Почтов. ИД',
            'dostavza' => 'Доставка за счет клиента',
            'manager_id' => 'Менеджер',
            'category_id' => 'Категория',
            'fast' => 'Быстро!',
            'packer_id' => 'Упаковщик',
            'url' => 'Ссылка',
            'client_id' => 'Клиент ID',            
            'tclient' => 'Тип клиента',
            'note' => 'Примечание',
            'ip_address' => 'IP адрес',            
            'type_oplata' => 'Тип оплаты',
            'sklad' => 'Со склада',
            'source' => 'Источник',
            'sender_id'=> 'Отправить через',
            'summ' => 'Сумма',
            'old_id'=>'ID оригинал',
            'old_id2'=>'ID cтарый',
            'totalSumm' => 'Cумма всего',
            'send_moskva' => 'Отгрузка через Москву',
            'b2c_id' => 'Внут. код фулфилмента',
        ];
    }
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }
    public function getManager()
    {
        return $this->hasOne(User::className(), ['id' => 'manager_id'])->from(User::tableName() . ' AS manager');
    }
    public function getPacker()
    {
        return $this->hasOne(User::className(), ['id' => 'packer_id']);
    }
    public function getUtmLabel()
    {
        return $this->hasOne(UtmLabel::className(), ['order_id' => 'id'])->inverseOf('order');
    }
    public function getRashod()
    {
        return $this->hasMany(TovarRashod::className(), ['order_id' => 'id'])->select('*')
        //->addSelect('(SELECT SUM(price*amount) from tovar_rashod where tovar_rashod.order_id = orders.id) as summ')
        ->addSelect('(price*amount) as summ');
       // ->inverseOf('order');
    }
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
    public function getSms()
    {
        return $this->hasMany(Sms::className(), ['order_id' => 'id'])->inverseOf('order');
    }
    public function getSmsDostav()
    {
        return $this->hasOne(Sms::className(), ['order_id' => 'id'])->where(['order_status'=>'dostav']);
    }
    public function getSender()
    {
        return $this->hasOne(Senders::className(), ['id' => 'sender_id']);
    }
    /**
	* 
	* 
	* @return
	*/
    public function countRegular()
    {
		return $n = self::find()->where(['client_id' =>$this->client_id])->count();		
		//return $n;
		//echo $phone;
	}
    /**
	* 
	* @param undefined $type
	* @param undefined $item
	* 
	* @return array
	*/
    public static function itemAlias($type, $item=false) {
    	$_yesno = ['0' => 'Нет', '1' => 'Да'];
		$_items = array(
			'status' => array(
				'1' => 'Новый',
				'2' => 'Дубль',
				'3' => 'Недозвон',
				'4' => 'В работе',
				'5' => 'Техподдержка',//'Обработан',				
				'6' => 'Заказ',
				'7' => 'Отказ',	
				'8'	=> 'Тест',
				'9'	=> 'Консультация',
				//'9' => 'Техподдержка',
			),
			'otpravlen'	=> $_yesno,
			'dostavlen'	=> $_yesno,
			'oplachen'	=> $_yesno,
			'vkasse'	=> $_yesno,
			'vozvrat'	=> $_yesno,
			'dostavza'	=> $_yesno,
			'fast'		=> $_yesno,
			'send_moskva'=> $_yesno,
			'source'	=> [
				'1' => 'Форма на сайте',
				'2' => 'Звонок телефона',
				'3' => 'Живосайт'
			],
			'type_oplata' => [
				'1' => 'Наложенный',
				'2' => 'Предоплата',
				'3' => 'Наличными',
			],
			'prich_double' =>[
				'1' => 'Не устраивает срок доставки',
				'2' => 'Не устраивает цена доставки',
				'3' => 'Дорого',
				'4' => 'Хочу кредит',
				'5' => 'Нашел дешевле',//'Обработан',				
				'6' => 'Передумал',
				'7' => 'Уже купил',
				'8' => 'Не подходит',
			],
			/*'sender' => [
				'russianpost' => 'Почта России',
				'cdek' => 'СДЭК',
				'self' => 'Самовывоз',
			]*/
		);
		//запретить менеджерам дубли и прочее
		if (isset(Yii::$app->user) and (Yii::$app->user->can('manager')) and !Yii::$app->user->can('orderDouble'))
		{
			$status_disable = [
			    '2' => ['disabled' => true],
			    '3' => ['disabled' => true],
			    '5' => ['disabled' => true],
			    '8' => ['disabled' => true],
			];
			$_items['status_disable'] = $status_disable;
		}
		if ($item === false)
			return isset($_items[$type]) ? $_items[$type] : false;
		else
			return isset($_items[$type][$item]) ? $_items[$type][$item] : false;
	}
    
	public function getClientPhone() {
	    return $this->client->phone;
	}
	
	public function getTovarSumma() {
		$itogo = 0;
		foreach($this->rashod as $rashod) {
			$itogo += abs($rashod->amount) * $rashod->price;
		}
		if($this->discount < $itogo) $itogo = $itogo - $this->discount;
		return $itogo;
	}
/*	
	public function getTovarList() {
		return $list = \app\models\Price::find()->with('tovar')->all();
		
	}
*/
	public function saveTovar() {
		$return = '';
		$oldarray = $old = $newarray = $new = $need_del = $need_add = array();
		//\yii\helpers\VarDumper::dump($_REQUEST,5,true);die;
		$oldarray = TovarRashod::find()->where(['order_id'=>$this->id])->indexBy('id')->asArray()->all();
		if (isset($_POST['tovar_list'])) 
			$newarray = $_POST['tovar_list'];
		
		//приведем массивы к единому виду
		foreach($oldarray as $item){
			if(array_key_exists($item['tovar_id'].'_'.$item['sklad_id'], $old)) {
				$old[$item['tovar_id'].'_'.$item['sklad_id']]['amount'] = $old[$item['tovar_id'].'_'.$item['sklad_id']]['amount'] + $item['amount'];
			}
			else {
				$old[$item['tovar_id'].'_'.$item['sklad_id']] = $item;
			}
		}
		foreach($newarray as $item){
			if(array_key_exists($item['tovar_id'].'_'.$item['sklad_id'], $new)) {
				$new[$item['tovar_id'].'_'.$item['sklad_id']]['amount'] = $new[$item['tovar_id'].'_'.$item['sklad_id']]['amount'] + $item['amount'];
			}
			else {
				$new[$item['tovar_id'].'_'.$item['sklad_id']] = $item;
			}
		}		
		$need_del = array_diff_key($old, $new);
		$need_add = array_diff_key($new, $old);
		
		if((count($old) == count($new)) and count($need_add) <1) $need_add = $new;
		/*
		\yii\helpers\VarDumper::dump($old,5,true);
		\yii\helpers\VarDumper::dump($new,5,true);
		\yii\helpers\VarDumper::dump($need_del, 5, true);
		\yii\helpers\VarDumper::dump($need_add, 5, true);
		die;
		*/
		foreach ($need_add as $add) {					
			if (($tr_id = TovarRashod::findOne(['tovar_id' => $add['tovar_id'], 'order_id' => $this->id, 'sklad_id' => $add['sklad_id']])) !== null) {	
			//\yii\helpers\VarDumper::dump($tr_id,5,true);die;
				$tr_id->amount = $add['amount'];
				if ($tr_id->save()) $return .= 'Товар '.$tr_id->tovar->name.' обновлен. ';
				else $return .= 'Товар '.$tr_id->tovar->name.' НЕ обновлен. '.print_r($tr_id->firstErrors, true);
			}							
			else {
				$tovar = Tovar::findOne([$add['tovar_id'], ]);					
				//добавим в расход
				$newmodel = new TovarRashod;
				$newmodel->order_id = $this->id;					
				$newmodel->tovar_id = $tovar->id;
				$newmodel->sklad_id = $add['sklad_id'];//$balance->sklad_id;
				$newmodel->price = $tovar->price;
				$newmodel->pprice = $tovar->pprice;
				$newmodel->amount = $add['amount'];
				if ($newmodel->save()) $return .= 'Новый товар '.$tovar->name.' сохранен. ';
				else $return .= 'Новый товар '.$tovar->name.' НЕ сохранен. Ошибки: '.print_r($newmodel->firstErrors, true).' ';	
			}
		
		}
		/*foreach ($need_add as $add) {
			if (array_key_exists('rashod_id', $add)) {			
				if (($tr_id = TovarRashod::findOne(['id' => $add['rashod_id'], 'order_id' => $this->id])) !== null) {	
				//\yii\helpers\VarDumper::dump($tr_id,5,true);die;
					$tr_id->amount = $add['amount'];
					if ($tr_id->save()) $return .= 'Товар '.$tr_id->tovar->name.' обновлен. ';
					else $return .= 'Товар '.$tr_id->tovar->name.' НЕ обновлен. '.print_r($tr_id->firstErrors, true);
				}
				else $return .= 'Не найден расход ID='.$id.'. ';
			}
			else {
				$tovar = Tovar::findOne([$add['tovar_id'], ]);					
				//добавим в расход
				$newmodel = new TovarRashod;
				$newmodel->order_id = $this->id;					
				$newmodel->tovar_id = $tovar->id;
				$newmodel->sklad_id = $add['sklad_id'];//$balance->sklad_id;
				$newmodel->price = $tovar->price;
				$newmodel->pprice = $tovar->pprice;
				$newmodel->amount = $add['amount'];
				if ($newmodel->save()) $return .= 'Новый товар '.$tovar->name.' сохранен. ';
				else $return .= 'Новый товар '.$tovar->name.' НЕ сохранен. Ошибки: '.print_r($newmodel->firstErrors, true).' ';	
			}
		
		}*/
		if (!empty($need_del)) {
			$iddel = array();
			$numdel =0;
			foreach ($need_del as $del) {				
				if(array_key_exists('created_at', $del))
					$iddel[]=$del['id'];
				else
					$iddel[]=$del['rashod_id'];
			}
			$numdel = TovarRashod::deleteAll(['id'=>$iddel, 'order_id'=>$this->id]);
			$return .= 'Удалено старых товаров: '.$numdel.'. ';
		}

		return $return;
	}
	
	/*
	public function getCall(){
		$url = 'http://api.comagic.ru/api/login/?login=lena.rop.lrf@mail.ru&password=russoturisto26';
		
		$list=array();
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL,$url);
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$call = curl_exec ($ch);
		curl_close($ch);

		$return = json_decode($call,true);
		if($return['success'] == 'true')
			return $return['data'];
		else 
			return false;
	}
*/
	public function beforeSave($insert)
	{
	    if (parent::beforeSave($insert)) {
	        if((!isset($this->shop_id) or is_null($this->shop_id) or empty($this->shop_id)) and (Yii::$app->params['user.current_shop'] >0)) $this->shop_id = Yii::$app->params['user.current_shop'];
	        return true;
	    } else {
	        return false; 
	    }
	}
	/**
	* 
	* @param undefined $insert
	* @param undefined $changedAttributes
	* 
	* 'accept' => 'Принят',				
	* 'shipped' => 'Отгружен',
	* 'delivered' => 'Доставлен',
	* 'delivered3day' => 'Повтор доставки',
	* 'posrtrack' => 'Трекинг',
	* 
	* @return
	*/
	public function afterSave($insert, $changedAttributes)
	{   	   
	    parent::afterSave($insert, $changedAttributes);
	    //\yii\helpers\VarDumper::dump($changedAttributes,10,true);
	    //\yii\helpers\VarDumper::dump($this->attributes,10,true);
	    //\yii\helpers\VarDumper::dump($this->oldAttributes,10,true);
	    //\yii\helpers\VarDumper::dump($this,10,true);
	    //die;
        //echo "</pre>";die;*/
        if($insert) {        	
			$sms = new Sms();
	    	$msg = $sms->sendSms($this, 'raw');
	    	//else error on console
	    	if(isset(Yii::$app->session)){
				if($msg !==false) Yii::$app->session->addFlash('success', $msg);
	    		else Yii::$app->session->addFlash('error', 'Смс не сохранена');	
			}	
		}            
        if(($changedAttributes['status'] == '1' or $changedAttributes['status'] == '4') and $this->status == '6') {	  
	    	$sms = new Sms();
	    	$msg = $sms->sendSms($this, 'accept');
	    	if($msg !==false) Yii::$app->session->addFlash('success', $msg);
	    	else Yii::$app->session->addFlash('error', 'Смс не сохранена');
		}
	    if($changedAttributes['otpravlen'] == '0' and $this->otpravlen == '1' and $this->status == '6') {	  
	    	//sms
	    	$sms = new Sms();
	    	$msg = $sms->sendSms($this, 'shipped');	    
	    	if($msg !==false) Yii::$app->session->addFlash('success', $msg);
	    	else Yii::$app->session->addFlash('error', 'Смс не сохранена');
	    	
	    	//ostatok
	        foreach($this->rashod as $rashod) {	
		    	$msg = TovarBalance::calc($rashod->tovar_id, $rashod->sklad_id, $rashod->amount, $this->shop_id, '-');
		    	Yii::$app->session->addFlash('info', $msg);	    	
		    }
		}		
		if($changedAttributes['dostavlen'] == '0' and $this->dostavlen == '1' and $this->status == '6') {	  
	    	$sms = new Sms();
	    	$msg = $sms->sendSms($this, 'delivered');	    
	    	if($msg !==false) Yii::$app->session->addFlash('success', $msg);
	    	else Yii::$app->session->addFlash('error', 'Смс не сохранена');
		}
		if(strlen(trim($changedAttributes['identif'])) == 0 and strlen($this->identif) >5 and $this->status == '6') {
			if($this->sender->code == 'russianpost') {
				$li = new LiveInform();
				$li->phone = $this->client->phone;
				$li->order_id = $this->id;
				$li->tracking = $this->identif;
		    	$msg = $li->add();
		    	if(array_key_exists('success',$msg)) Yii::$app->session->addFlash('success', $msg['success']);
		    	else Yii::$app->session->addFlash('error', $msg['error']);
			}			
		}
	}
}
