<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TovarPrihod;

/**
 * TovarPrihodSearch represents the model behind the search form about `app\models\TovarPrihod`.
 */
class TovarPrihodSearch extends TovarPrihod
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'amount', 'supplier_id'], 'integer'],
            [['created_at', 'updated_at','date_at', 'doc', 'note', 'tovar.name'], 'safe'],
            [['tovar_id', 'sklad_id'], 'string'],
            [['price', 'price_sale'], 'number'],
        ];
    }
    
    public function attributes()
	{
	    // делаем поле зависимости доступным для поиска
	    return array_merge(parent::attributes(), ['tovar.name']);
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
        $query = TovarPrihod::find()->joinWith('tovar','sklad');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->attributes['tovar.name'] = [
		    'asc' => ['tovar.name' => SORT_ASC],
		    'desc' => ['tovar.name' => SORT_DESC],
		];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

		$query->andFilterWhere(['{{tovar_prihod}}.shop_id' => Yii::$app->params['user.current_shop']]);

        $query->andFilterWhere([
            'id' => $this->id,
            'tovar_id' => $this->tovar_id,
            //'date_at' => $this->date_at,
            'price' => $this->price,
            'amount' => $this->amount,
            'supplier_id' => $this->supplier_id,
            //'sklad_id' => $this->sklad_id,
        ]);

        $query->andFilterWhere(['like', 'date_at', $this->date_at])
        	->andFilterWhere(['like', 'tovar.name', $this->getAttribute('tovar.name')])
        	->andFilterWhere(['like', 'sklad_id', $this->sklad->name])
        	->andFilterWhere(['like', 'doc', $this->doc])
        	->andFilterWhere(['like', 'date_at', $this->date_at])
        	->andFilterWhere(['like', 'date(from_unixtime({{tovar_prihod}}.created_at))', $this->created_at])
        	->andFilterWhere(['like', 'updated_at', $this->updated_at])        	
            ->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
