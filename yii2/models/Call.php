<?php

namespace app\models;

use yii\base\Model;

class Call extends Model
{
    public $date_from;// = date('Y-m-d', strtotime("-1 year"));//Дата и время начала периода выборки 
    public $date_till;// = date('Y-m-d', strtotime("+1 day"));//Дата и время окончания периода выборки 
    public $direction; //направление звонка. Возможные значения: in - Входящие звонки, out - Исходящие звонки 
    public $numa;//Номер, с которого совершили звонок
    public $numb;//Номер, на который совершили звонок    
    public $login = 'lena.rop.lrf@mail.ru';//логин к сервису
    public $password = 'russoturisto26';//пароль к сервису
    public $key;    

    public function rules()
    {
        return [            
            [['date_from', 'date_till'], 'required'],
            [['date_from', 'date_till'], 'date','format'=>'yyyy-M-d'],
            [['direction', 'numa', 'numb'], 'string'], 
            [['login', 'password', 'session_key'], 'safe']
        ];
    }
    public function attributeLabels()
    {
        return [
            'date_from' => 'Дата начала выборки',
            'date_till' => 'Дата конца выборки',
            'direction' => 'Направление звонка',
            'numa' => 'Номер, с которого совершили звонок',
            'numb' => 'Номер, на который совершили звонок',
            'login' => 'Логин',
            'password' => 'Пароль',
            'session_key' => 'Ключ cессии'
        ];
    }
    
    public function getDateFrom () {
		if(!isset($this->date_from)) $this->date_from = date('Y-m-d', strtotime("-1 year"));
		return $this->date_from;
	}
	
	public function getDateTill () {
		if(!isset($this->date_till)) $this->date_till = date('Y-m-d', strtotime("+1 day"));
		return $this->date_till;
	}
	
    public function getCall($phone = null) {
    	if($phone == null) {
			$url = 'http://api.comagic.ru/api/v1/call/?session_key='.$this->getKey().'&date_from='.$this->getDateFrom().'&date_till='.$this->getDateTill();
			return $this->requestApi($url);
		}
		else {
			$ar1 = $ar2 = array();
			$phone = substr_replace($phone, '7', 0, 1);
			
			$url = 'http://api.comagic.ru/api/v1/call/?session_key='.$this->getKey().'&date_from='.$this->getDateFrom().'&date_till='.$this->getDateTill().'&numa='.$phone;			
			if($req = $this->requestApi($url)) $ar1 = $req;
			
			$url = 'http://api.comagic.ru/api/v1/call/?session_key='.$this->getKey().'&date_from='.$this->getDateFrom().'&date_till='.$this->getDateTill().'&numb='.$phone;
			if($req = $this->requestApi($url)) $ar2 = $req;
			//\yii\helpers\VarDumper::dump($ar1,10,true);die;
			return array_merge($ar1,$ar2);
		}		
	}
    /**
	* запрос к апи
	* 
	* @url string $url
	* @return array
	*/
	private function getKey() {		
		if(!isset($_SESSION['sesscall'])) {
			$sessurl = 'http://api.comagic.ru/api/login/?login='.$this->login.'&password='.$this->password;
			
			if(($ret = $this->requestApi($sessurl)) !==false) {
				$this->key = $_SESSION['sesscall'] = $ret['session_key'];
			}
			//$url = 'https://api-metrika.yandex.ru/management/v1/counters?oauth_token='.Settings::getKey('ya_metrika_token').'&field=labels';
		}
		else $this->key = $_SESSION['sesscall'];
		
		return $this->key;
	}
	
	private function requestApi($url) {		
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

}