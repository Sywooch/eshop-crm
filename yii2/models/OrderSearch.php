<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UtmLabel;
use app\models\Orders;
use DateTime, DateInterval;
/**
 * OrderSearch represents the model behind the search form about `app\models\Order`.
 */
class OrderSearch extends Orders
{
    public $column_visible = array();
    public $column_list = array();
    public $date_order_start;
    public $date_order_end;  
    public $status_array;
    public $rashod_list;   
	//public $client;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'status', 'dostavza', 'manager_id', 'fast', 'packer_id', 'tclient', 'otpravlen', 'dostavlen', 'oplachen', 'vkasse', 'vozvrat', 'send_moskva', 'source', 'type_oplata'], 'integer'],
            [['date_at', 'created_at', 'date_order_start', 'date_order_end', 'data_duble',  'prich_double', 'prich_vozvrat', 'identif', 'category_id', 'url', 'note', 'ip_address', 'client.phone', 'client.fio', 'client.flat', 'client.fulladdress', 'utmLabel.utm_content', 'utmLabel.utm_campaign', 'utmLabel.utm_source', 'utmLabel.utm_term', 'utmLabel.source_type', 'utmLabel.utm_medium', 'utmLabel.source', 'utmLabel.group_id', 'utmLabel.banner_id', 'utmLabel.position', 'utmLabel.position_type','utmLabel.region_name','sender_id', 'b2c_id'], 'safe'],
			[['vozvrat_cost', 'summaotp', 'discount', 'summ', 'totalSumm'], 'number'],           
            //[['date_filter_start', 'date_filter_end'], 'date', 'format' => 'Y-m-d'],
            [['date_order_start', 'date_order_end', 'data_otprav',  'data_dostav', 'data_oplata', 'data_vkasse', 'data_vozvrat', 'send_moskva'], 'default', 'value' => null],
            ['status_array', 'safe'],
            //['status_array', 'each', 'rule' => ['integer']],
            //['status_array', 'exist', 'allowArray' => true, 'when' => function ($model, $attribute) {return is_array($model->$attribute);}],          
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    
    public function attributes()
	{
	    // add related fields to searchable attributes
	    /*$utmattr = UtmLabel::attributes();
	    unset($utmattr['id']);
        unset($utmattr['order_id']);*/
        $utmattr = ['utmLabel.utm_content', 'utmLabel.utm_campaign', 'utmLabel.utm_source', 'utmLabel.utm_term', 'utmLabel.source_type', 'utmLabel.utm_medium', 'utmLabel.source', 'utmLabel.group_id', 'utmLabel.banner_id', 'utmLabel.position', 'utmLabel.position_type', 'utmLabel.region_name'];
        $clientattr = ['client.phone', 'client.fio', 'client.email', 'client.region_id', 'client.area_id', 'client.city_id', 'client.settlement_id', 'client.flat', 'client.fulladdress'];
	    $ret = array_merge(parent::attributes(), $utmattr);
	   	$ret = array_merge($ret, $clientattr);
	   	//echo '<pre>';print_r($ret);echo '</pre>';die;
	    return $ret;
	}
	
    public function attributeLabels()
    {
    	$labels = parent::attributeLabels();    	
        $utmlabels = new UtmLabel;
        $utmlabels = $utmlabels->attributeLabels();        
        unset($utmlabels['id']);
        unset($utmlabels['order_id']);
        
        $ret = array_merge($labels,$utmlabels);
		
        $ret = array_merge($ret, 
            [				
                'client.phone' => 'Тел. клиента',
                'client.fio' => 'ФИО клиента',
                'client.email' => 'Email клиента',
                'client.region_id' => 'Регион клиента',
                'client.area_id' => 'Район клиента',
                'client.city_id' => 'Город клиента',
                'client.settlement_id' => 'Нас.пункт клиента',
                'client.fulladdress' => 'Полный адрес клиента',
                'client.flat' => 'Адрес, улица, кв-ра клиента',
                'date_order_start' => 'Дата заявки от',
                'date_order_end' => 'Дата заявки до',	        	
                'column_visible' => 'Показать колонки'
	    ]
	);		
	
        //echo '<pre>';print_r($labels);echo '</pre>';die;
	return $ret;
	}
    


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @id integer id client
     *
     * @return ActiveDataProvider
     */
    public function search($params, $id=null)
    {                   
        $qTotalSumm = (new \yii\db\Query())->select(['SUM(`tovar_rashod`.`price` * `tovar_rashod`.`amount`) - IFNULL(`orders`.`discount`, 0)'])->from('tovar_rashod')->where('tovar_rashod.order_id = orders.id');
        //$query = Orders::find()->select('orders.*')->addSelect(["(SELECT SUM((`tovar_rashod`.`price` * `tovar_rashod`.`amount`) - IFNULL(`orders`.`discount`, 0)) from tovar_rashod where tovar_rashod.order_id = orders.id) as totalSumm"])->groupBy('id');
        $query = Orders::find()->select('orders.*')->addSelect(['totalSumm'=>$qTotalSumm])->groupBy('{{orders}}.id');
        if(is_null($id))
        	//$query->joinWith(['client' => function($query) { $query->from(['client' => 'client']); }]);
        	$query->joinWith(['client','client.region','manager','packer','utmLabel','sender','rashod']);
        else 
        	$query->where(['client_id'=>$id])->joinWith(['client', 'client.region','manager','packer','utmLabel','sender', 'rashod']);
		
		/*if (isset($params['OrderSearch']) && isset($params['OrderSearch']['status_array']) && is_array($params['OrderSearch']['status_array'])) {
            $params['OrderSearch']['status_array'] = implode(",", $params['OrderSearch']['status_array']);
        }
		*/
		//$query->joinWith(['client' => function($query) { $query->from(['client' => 'client']); }]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        /*
        $dataProvider->sort->attributes['client.phone'] = [
            'asc' => ['client.phone' => SORT_ASC],
            'desc' => ['client.phone' => SORT_DESC]
        ];
        $dataProvider->sort->attributes['utmLabel.utm_term'] = [
            'asc' => ['utm_label.utm_term' => SORT_ASC],
            'desc' => ['utm_label.utm_term' => SORT_DESC]
        ];
        */
/*        $dataProvider->setSort([
	        'attributes' => [	           
            'client.phone' => [
	                'asc' => [Client::tableName() . '.phone' => SORT_ASC],
	                'desc' => [Client::tableName() . '.phone' => SORT_DESC],
	                //'label' => 'phone'
	            ],
	            'utm_term' => [
	                'asc' => [UtmLabel::tableName() . '.utm_term' => SORT_ASC],
	                'desc' => [UtmLabel::tableName() . '.utm_term' => SORT_DESC],
	                //'label' => 'phone'
	            ],
	         
	        ]
	    ]);
*/
		//$dataProvider->sort->defaultOrder = ['orders.id' => SORT_DESC];
		//$dataProvider->setSort(['defaultOrder' => ['orders.id'=>SORT_DESC],]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //$query->joinWith(['client']);
            return $dataProvider;
        }        
        
        //if(is_null($this->status) or empty($this->status))
        //	$this->status = '0';
//echo Yii::$app->params['user.current_shop'];die;
		$query->andFilterWhere(['{{orders}}.shop_id' => Yii::$app->params['user.current_shop']]);

        $query->andFilterWhere([
            'orders.id' => $this->id,
            'source' => $this->source,
            //'date' => $this->date,
            //'orders.status' => $this->status,           
            'otpravlen' => $this->otpravlen,
            'dostavlen' => $this->dostavlen,
            'oplachen' => $this->oplachen,
            'vkasse' => $this->vkasse,
            'vozvrat' => $this->vozvrat,
            'vozvrat_cost' => $this->vozvrat_cost,
            'summaotp' => $this->summaotp,
            'discount' => $this->discount,
            'dostavza' => $this->dostavza,
            'manager_id' => $this->manager_id,
            'fast' => $this->fast,
            'packer_id' => $this->packer_id,
            'client_id' => $this->client_id,
            'tclient' => $this->tclient,                     
            'type_oplata' => $this->type_oplata,
            'sklad' => $this->sklad,
            'sender_id' => $this->sender_id,
            'old_id' => $this->old_id,
            'old_id2' => $this->old_id2,
            'prich_double' =>$this->prich_double,
            'send_moskva' =>$this->send_moskva,
            'b2c_id' =>$this->b2c_id,
            
            //'summ' => $this->getTovarSumma(),
        ]);
        
        if (!empty($this->status_array)) {            
            $query->andFilterWhere(['in', 'orders.status', $this->status_array]);
        }
        else
        	$query->andFilterWhere(['orders.status' => $this->status]);
        
        //if(!is_null($id)) {
       	//echo '<pre>';print_r($this->client->region);echo '</pre>';//die;
		$query->andFilterWhere(['region_id' => $this->client->region->name]);
		//}

        //$query->andFilterWhere(['like', 'tovarsumma', $this->totalSumm])
        $query//->andFilterWhere(['like', 'prich_double', $this->prich_double])
            ->andFilterWhere(['like', 'prich_vozvrat', $this->prich_vozvrat])
            ->andFilterWhere(['like', 'identif', $this->identif])
           // ->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', '{{orders}}.note', $this->note])
            ->andFilterWhere(['like', 'ip_address', $this->ip_address]);		
		
		$query->andFilterWhere(['like', 'client.fio', $this->getAttribute('client.fio')]);
		$query->andFilterWhere(['LIKE', 'client.phone', $this->getAttribute('client.phone')]);
		$query->andFilterWhere(['LIKE', 'client.region', $this->getAttribute('client.region')]);
		$query->andFilterWhere(['LIKE', 'client.fulladdress', $this->client->fulladdress]);
		$query->andFilterWhere(['LIKE', 'client.flat', $this->getAttribute('client.flat')]);
		$query->andFilterWhere(['LIKE', 'utm_label.utm_term', $this->getAttribute('utmLabel.utm_term')]);
		$query->andFilterWhere(['LIKE', 'utm_label.utm_campaign', $this->getAttribute('utmLabel.utm_campaign')]);
		$query->andFilterWhere(['LIKE', 'utm_label.utm_content', $this->getAttribute('utmLabel.utm_content')]);
		$query->andFilterWhere(['LIKE', 'utm_label.utm_source', $this->getAttribute('utmLabel.utm_source')]);
		$query->andFilterWhere(['LIKE', 'utm_label.utm_medium', $this->getAttribute('utmLabel.utm_medium')]);
		$query->andFilterWhere(['LIKE', 'utm_label.utm_term', $this->getAttribute('utmLabel.utm_term')]);
		$query->andFilterWhere(['LIKE', 'utm_label.source_type', $this->getAttribute('utmLabel.source_type')]);
		$query->andFilterWhere(['LIKE', 'utm_label.source', $this->getAttribute('utmLabel.source')]);
		$query->andFilterWhere(['LIKE', 'utm_label.group_id', $this->getAttribute('utmLabel.group_id')]);
		$query->andFilterWhere(['LIKE', 'utm_label.banner_id', $this->getAttribute('utmLabel.banner_id')]);
		$query->andFilterWhere(['LIKE', 'utm_label.position', $this->getAttribute('utmLabel.position')]);
		$query->andFilterWhere(['LIKE', 'utm_label.position_type', $this->getAttribute('utmLabel.position_type')]);
		$query->andFilterWhere(['LIKE', 'utm_label.region_name', $this->getAttribute('utmLabel.region_name')]);
		if(!empty($this->data_otprav)) 
			$query->andFilterWhere(['between', 'DATE(orders.data_otprav)', (\yii::$app->formatter->asDate($this->data_otprav).' 00:00:00'), (yii::$app->formatter->asDate($this->data_otprav).' 23:59:59')]);
		//echo '<pre>';print_r($this);echo '</pre>';
		if(!empty($this->date_order_start)) {
			//echo $this->date_order_start;
			$query->andFilterWhere(['between', 'DATE(orders.date_at)', (\yii::$app->formatter->asDate($this->date_order_start).' 00:00:00'), (yii::$app->formatter->asDate($this->date_order_end).' 23:59:59')]);
		}
		else if(!empty($this->date_at)) {
			$date1 = \yii::$app->formatter->asDate($this->date_at);//date('Y-m-d', strtotime($this->created_at));
			$date2 = \yii::$app->formatter->asDate($this->date_at.' + 1 days');//date("Y-m-d", strtotime($this->created_at.' + 1 days'));
			//echo $date1;			echo $date2;die;
			
			//Yii::info($date1, 'test'); // NULL
			$query->andFilterWhere(['>=', 'DATE(orders.date_at)', $date1])
				  ->andFilterWhere(['<', 'DATE(orders.date_at)', $date2]);		
		}
		
        return $dataProvider;
    }
    
    public static function getTotalSumma($dataProvider, $fieldName){
        $itogo = 0;

        foreach ($dataProvider as $item){
        	$rashod = $item->rashod;
        	//$itogo += $rashod['summ'];
        	//echo '<pre>';print_r($rashod->summ);echo '</pre>';
        	foreach($rashod as $rashod) {
				$itogo += $rashod->summ;//$rashod->amount * $rashod->price;			
			}          
        }
        
        return $itogo;
    }
    
    /**
	* 
	* 
	* @return
	*/
	public function generateColumn() {

		$list = $this->attributeLabels();
		unset($list['column_visible']);
		unset($list['date_order_start']);
        unset($list['date_order_end']);
		$this->column_list = $list;
		
		//echo '<pre>';print_r($this->attributes);echo '</pre>';
		
		if (isset($_POST['OrderSearch']['column_visible'])) {

			$this->column_visible = array();			
			foreach ($list as $key => $field) {
				if (in_array($key, $_POST['OrderSearch']['column_visible'])) {
					array_push($this->column_visible, $key);
				}
			}
			
			//if (!isset(Yii::$app->request->cookies['column_visible'])) {
			    Yii::$app->response->cookies->add(new \yii\web\Cookie([
			        'name' => 'column_visible',
			        'value' => serialize($this->column_visible),
			        'expire' => time() + 86400 * 365,
			    ]));
			//}
			
//			Dumper::d($this->column_visible);	die;
			//Yii::app()->request->cookies['column_visible'] = new CHttpCookie('column_visible', serialize($this->column_visible));
			//Yii::app()->request->cookies['column_visible']->expire = time() + 60 * 60 * 24 * 30;
//			$this->column_visible= $column;
		}
		else {
			if(isset(Yii::$app->request->cookies['column_visible'])) {
				//echo 'nnnnnnnnnnnnn';
				$this->column_visible = unserialize(Yii::$app->request->cookies['column_visible']);
			}
			else {
				//echo 'yyyyyyyyyyyyyyy';
				$this->column_visible = array('id', 'status', 'date_at', 'client_id','url');
			}
		}
		
			/*$this->column_visible = ()) ? 
				(unserialize(Yii::$app->request->cookies['column_visible']->getValue)) :
				(array('id', 'status', 'created_at', 'clientPhone','url'));
				*/
	}
}