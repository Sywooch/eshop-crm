<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Sklad;

/**
 * TovarSearch represents the model behind the search form about `app\models\Tovar`.
 */
class TovarOstatokSearch extends Model//Sklad //\yii\db\ActiveRecord
{
    public $t_art;
    public $s_id; 
    public $s_name, $t_id, $t_name, $t_price, $cat_id, $cat_name, $ostatok;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['s_id', 'cat_id', 't_id'], 'integer'],
            [['t_price', 'ostatok'], 'number'],
            [['t_art', 's_name', 't_name', 'cat_name'], 'string'],
        ];
    }
	
	public function attributeLabels()
    {	
        return [				
        	's_id' => 'Склад ID',
        	'cat_id' => 'Категория ID',
        	't_id' => 'Товар ID',
        	't_art' => 'Артикул',
        	's_name' => 'Склад',
        	't_name' => 'Товар',
        	'cat_name' => 'Категория',
        	't_price' => 'Цена продажи',
        	'ostatok' => 'Остаток',
		];
	}

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {	
		//$this->s_id = \app\models\Sklad::defaultId();
		$this->load($params);
		
		$where = 'where 1=1';//"(ostatok > 0)"
		$current_shop = Yii::$app->params['user.current_shop'];
        //$where .= " AND (`tovar`.active='1')";  

        if ($this->validate()) {
			foreach ($this->attributes() as $name)
	        { // Формируем where-часть запроса, указывая заполненные атрибуты
	            if ($this->$name != '') {	               
	                if(substr($name, -2) === 'id')
	                	$where .= ' AND '.$name." = '{$this->$name}'";
	                else 
	                	$where .= ' AND '.$name." LIKE '%{$this->$name}%'";
	            }
	        }	        
		}       
   
        /*$sql = "SELECT * FROM
         (SELECT `tovar`.`artikul` AS `t_art`, `tovar`.`name` AS `t_name`, `tovar`.`id` AS `t_id`, `tovar`.`price` AS `t_price`, `sklad`.`id` AS `s_id`, `sklad`.`name` AS `s_name`, `category`.`id` AS `cat_id`, `category`.`name` AS `cat_name`, IFNULL((tovar_prihod.amount - tovar_rashod.amount), 0) as ostatok
         FROM `sklad`
         INNER JOIN `tovar_prihod` ON `sklad`.`id` = `tovar_prihod`.`sklad_id`
         INNER JOIN `tovar_rashod` ON `sklad`.`id` = `tovar_rashod`.`sklad_id`
         INNER JOIN `tovar` ON `tovar_prihod`.`tovar_id` = `tovar`.`id`
         INNER JOIN `category` ON `tovar`.`category_id` = `category`.`id`
         INNER JOIN `orders` ON `orders`.`id` = `tovar_rashod`.`order_id`
         INNER JOIN `sklad_shops` ON `sklad_shops`.`sklad_id` = `sklad`.`id`
         WHERE `sklad_shops`.`shop_id` = '$current_shop' AND (`tovar`.shop_id='$current_shop') AND (`orders`.`status`='6') $where_active
         GROUP BY `sklad`.`id`, `tovar`.`name`) c
        WHERE $where";
        */
        /*$sql = "select *, IFNULL((cnt_prihod - cnt_rashod), 0) as ostatok
		from 
		(select `tovar`.`artikul` AS `t_art`, `tovar`.`name` AS `t_name`, `tovar`.`id` AS `t_id`, `tovar`.`price` AS `t_price`, `sklad`.`id` AS s_id, `sklad`.`name` AS `s_name`, `category`.`id` AS `cat_id`, `category`.`name` AS `cat_name`,								
			(select IFNULL(sum(amount), 0) from tovar_prihod where tovar_prihod.tovar_id = tovar.id and tovar_prihod.sklad_id = sklad.id) as cnt_prihod,		
			(select IFNULL(sum(amount), 0) from tovar_rashod,orders where tovar_rashod.tovar_id = tovar.id and tovar_rashod.order_id = orders.id and orders.status=6 and tovar_rashod.sklad_id = sklad.id) as cnt_rashod			
			from tovar, sklad, sklad_shops, category		
			where tovar.shop_id = $current_shop
			and (sklad_shops.shop_id = $current_shop and sklad_shops.sklad_id = sklad.id)
			and tovar.category_id = category.id
			AND (`tovar`.active='1')				
			group by tovar.id, sklad.id1) a $where";
        */
        
        $sql = "select *, IFNULL((cnt_prihod - cnt_rashod), 0) as ostatok
		from 
		(select `tovar`.`artikul` AS `t_art`, `tovar`.`name` AS `t_name`, `tovar`.`id` AS `t_id`, `tovar`.`price` AS `t_price`, `sklad`.`id` AS s_id, `sklad`.`name` AS `s_name`, `category`.`id` AS `cat_id`, `category`.`name` AS `cat_name`,								
			(select IFNULL(sum(amount), 0) from tovar_prihod where tovar_prihod.tovar_id = tovar.id and tovar_prihod.sklad_id = sklad.id) as cnt_prihod,		
			(select IFNULL(sum(amount), 0) from tovar_rashod,orders where tovar_rashod.tovar_id = tovar.id and tovar_rashod.order_id = orders.id and orders.status=6 and orders.otpravlen=1 and tovar_rashod.sklad_id = sklad.id) as cnt_rashod			
			from tovar, sklad, category		
			where tovar.shop_id = $current_shop
			and sklad.shop_id = $current_shop
			and tovar.category_id = category.id
			AND (`tovar`.active='1')			
			group by tovar.id, sklad.id
			) a
		$where";//order by tovar.name asc, sklad.id asc
		
		/*$w_s_prih = $w_cat_prih = $w_cat_rash = $w_s_rash = $w_cat = '';
		if(!empty($this->s_id)) {
			$w_s_prih = 'and tp.sklad_id = '.$this->s_id;
			$w_s_rash = 'and tr.sklad_id = '.$this->s_id;
			$w_s = 'tovar.shop_id = '. $current_shop .'';
		}
		if(!empty($this->cat_id)) {
			//$w_cat_prih = 'and tp.tovar.category_id = '.$this->cat_id;
			//$w_cat_rash = 'and tr.tovar.category_id = '.$this->cat_id;
			$w_cat = 'and tovar.category_id = '.$this->cat_id;
		}
		
		$sql = "select *, IFNULL((cnt_prihod - cnt_rashod), 0) as ostatok
		from 
		(select `tovar`.`artikul` AS `t_art`, `tovar`.`name` AS `t_name`, `tovar`.`id` AS `t_id`, `tovar`.`price` AS `t_price`, 								
			0 as cnt_prihod,		
			0 as cnt_rashod			
			from tovar		
			where tovar.shop_id = $current_shop			
			$w_cat
			AND (`tovar`.active='1')			
			group by tovar.id
			) a
		$where";*/
		//(select IFNULL(sum(amount), 0) from tovar_prihod tp where tp.tovar_id = tovar.id $w_s_prih) as cnt_prihod,		
		//	(select IFNULL(sum(amount), 0) from tovar_rashod tr, orders where tr.tovar_id = tovar.id and tr.order_id = orders.id and orders.status=6 and orders.otpravlen=1 $w_s_rash) as cnt_rashod
		
    //echo $sql;
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
		        'attributes' => ['s_id', 'cat_id', 't_id', 't_art', 's_name', 't_name', 'cat_name', 't_price', 'ostatok'],
		        'defaultOrder' => ['t_name'=>SORT_ASC],
		    ],
		]);
		//$dataProvider->setSort(['defaultOrder' => ['t_name'=>SORT_ASC],]);
       
		
        return $dataProvider;
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchforpopup($params)
    {	
		$this->s_id = \app\models\Sklad::defaultId();
		$this->load($params);
		
		$where = 'where 1=1';//"(ostatok > 0)"
		$current_shop = Yii::$app->params['user.current_shop'];
        //$where .= " AND (`tovar`.active='1')";  

        if ($this->validate()) {
			foreach ($this->attributes() as $name)
	        { // Формируем where-часть запроса, указывая заполненные атрибуты
	            if ($this->$name != '') {	               
	                if(substr($name, -2) === 'id')
	                	$where .= ' AND '.$name." = '{$this->$name}'";
	                else 
	                	$where .= ' AND '.$name." LIKE '%{$this->$name}%'";
	            }
	        }	        
		}       
        $sql = "select * from 
        (select `tovar`.`artikul` AS `t_art`, `tovar`.`name` AS `t_name`, `tovar`.`id` AS `t_id`, `tovar`.`price` AS `t_price`, `sklad`.`id` AS s_id, `sklad`.`name` AS `s_name`, `category`.`id` AS `cat_id`, `category`.`name` AS `cat_name`
			from tovar, sklad, category		
			where tovar.shop_id = $current_shop
			and sklad.shop_id = $current_shop
			and tovar.category_id = category.id
			AND (`tovar`.active='1')			
			group by tovar.id, sklad.id	) a	
			$where	
		";//order by tovar.name asc, sklad.id asc
    
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
		        'attributes' => ['s_id', 'cat_id', 't_id', 't_art', 's_name', 't_name', 'cat_name', 't_price', 'ostatok'],
		        'defaultOrder' => ['t_name'=>SORT_ASC],
		    ],
		]);
		//$dataProvider->setSort(['defaultOrder' => ['t_name'=>SORT_ASC],]);
       
		
        return $dataProvider;
    }
}
