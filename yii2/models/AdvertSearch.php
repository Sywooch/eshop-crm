<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Statcompany;

/**
 * StatcompanySearch represents the model behind the search form about `app\models\Statcompany`.
 */
class AdvertSearch extends Statcompany
{
	public $cnt_za;
	public $cnt_zz;
	public $date1;
	public $date2;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['date1', 'date2'], 'date','format'=>'yyyy-M-d'],
            [['date1', 'date2'], 'default', 'value' => date('Y-m-d')],
			[['shows', 'clicks', 'id_company', 'category_id', 'tovar_id', 'site_id', 'shop_id'], 'integer'],            
			[['costs'], 'number'],
			[['name', 'date_at', 'goods_art', 'host', 'source'], 'safe'],          
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
    
	/*public function attributes()
	{
		//$t = array_merge($this->attributes(), parent::attributes());
		return array_merge($t, ['tovar.name']);
	}    
    */
    public function attributeLabels()
    {
    	//return array_merge(parent::attributeLabels(),['tovar.name'=>'Tovar']);    
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
        //$query = Statcompany::find()->addSelect('date_at, id_company, statcompany.name, host, source, sum(shows) as shows, sum(clicks) as clicks, sum(costs) as costs')->where(['statcompany.shop_id'=>Yii::$app->params['user.current_shop']])->joinWith(['tovar','category'])->groupBy('statcompany.id_company');
		$query = Statcompany::find()->addSelect('statcompany.date_at, id_company, statcompany.name, host, statcompany.source, sum(shows) as shows, sum(clicks) as clicks, sum(costs) as costs')->where(['statcompany.shop_id'=>Yii::$app->params['user.current_shop']])->joinWith(['tovar','category','orders'])->groupBy('statcompany.date_at, statcompany.id_company');
		
		
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
				'defaultOrder' => ['date_at' => SORT_DESC],
            ]
        ]);
        
      /* $dataProvider->sort->attributes['tovar.name'] = [
	      // Это те таблицы, с которыми у нас установлена связь
	      // в моем случае у них есть префикс tbl_ 
			'asc' => ['tovar.name' => SORT_ASC],
			'desc' => ['tovar.name' => SORT_DESC],
		];
*/
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
		
		

        $query->andFilterWhere([
            'id' => $this->id,
            'statcompany.date_at' => $this->date_at,
            'shows' => $this->shows,
            'clicks' => $this->clicks,
            'costs' => $this->costs,
            //'id_company' => $this->id_company,
            'category_id' => $this->category_id,
            //'tovar_id' => $this->tovar_id,
            //'site_id' => $this->site_id,
            //'shop_id' => $this->shop_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'goods_art', $this->goods_art])
            ->andFilterWhere(['like', 'host', $this->host])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'id_company', $this->id_company])
            ->andFilterWhere(['like', 'tovar.name', $this->getAttribute('tovar.name')]);
            //->andFilterWhere(['like', 'tovar_id', $this->tovar->name]);

        return $dataProvider;
    }
}
