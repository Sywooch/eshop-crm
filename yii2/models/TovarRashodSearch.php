<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TovarRashod;

/**
 * TovarRashodSearch represents the model behind the search form about `app\models\TovarRashod`.
 */
class TovarRashodSearch extends TovarRashod
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'amount'], 'integer'],
            [['created_at', 'updated_at', 'tovar.name'], 'safe'],
            [['tovar_id', 'sklad_id'], 'string'],
            [['price'], 'number'],
        ];
    }
	
	public function attributes()
	{
	    // add related fields to searchable attributes	    
        $attr = ['tovar.name'];        
	    $ret = array_merge(parent::attributes(), $attr);	   	
	    return $ret;
	}
	
	public function attributeLabels()
    {
    	return array_merge(parent::attributeLabels(),
            [				
	        	'tovar_id' => 'ID товара',
	        	'tovar.name' => 'Наименование товара',	        	
	        ]
		);		 
	}
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = TovarRashod::find()->joinWith(['order', 'tovar', 'sklad'])->where(['{{tovar_rashod}}.shop_id' => Yii::$app->params['user.current_shop']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        //$query->andFilterWhere(['{{tovar_rashod}}.shop_id' => Yii::$app->params['user.current_shop']]);

        $query->andFilterWhere([
            'id' => $this->id,
            //'date_at' => $this->date_at,
            'order_id' => $this->order_id,
            'tovar_id' => $this->tovar_id,
            'price' => $this->price,
            'amount' => $this->amount,
            //'sklad_id' => $this->sklad_id,
        ]);
        
        $query->andFilterWhere(['like', 'tovar.name', $this->getAttribute('tovar.name')])
        	->andFilterWhere(['like', 'sklad_id', $this->sklad->name]);
        	
        if(!empty($this->created_at)) {
        	$date1 = yii::$app->formatter->asDate($this->created_at);
			$date2 = yii::$app->formatter->asDate($this->created_at.' + 1 days');
			$query->andFilterWhere(['>=', 'DATE(FROM_UNIXTIME(tovar_rashod.created_at))', $date1])
				->andFilterWhere(['<', 'DATE(FROM_UNIXTIME(tovar_rashod.created_at))', $date2]);
		}

        return $dataProvider;
    }
}
