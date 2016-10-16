<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TovarBalance;

/**
 * TovarBalanceSearch represents the model behind the search form about `app\models\TovarBalance`.
 */
class TovarBalanceSearch extends TovarBalance
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sklad_id', 'amount', 'created_at', 'updated_at', 'created_by', 'updated_by', 'shop_id'], 'integer'],
            [['tovar_id', 'tovar.name', 'tovar.artikul'], 'string'],
            [['price'], 'number'],
            //[[], 'safe']
        ];
    }
    
    public function attributes()
	{
	    // делаем поле зависимости доступным для поиска
	    return array_merge(parent::attributes(), ['tovar.name', 'tovar.artikul']);
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
        $query = TovarBalance::find()->joinWith('tovar')->where(['{{tovar_balance}}.shop_id' => Yii::$app->params['user.current_shop']]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->sort->attributes['tovar.artikul'] = [
		    'asc' => ['tovar.artikul' => SORT_ASC],
		    'desc' => ['tovar.artikul' => SORT_DESC],
		];
		$dataProvider->sort->attributes['tovar_id'] = [
		    'asc' => ['tovar.name' => SORT_ASC],
		    'desc' => ['tovar.name' => SORT_DESC],
		];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            //'tovar_id' => $this->tovar->name,
            'sklad_id' => $this->sklad_id,
            'amount' => $this->amount,
            'price' => $this->price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'shop_id' => $this->shop_id,
        ]);
        
         $query->andFilterWhere(['like', 'tovar.artikul', $this->getAttribute('tovar.artikul')])
         	->andFilterWhere(['like', 'tovar.name', $this->getAttribute('tovar_id')]);

        return $dataProvider;
    }
    
    public function searchPopup($params)
    {
        $query = TovarBalance::find()->joinWith('tovar')->where(['{{tovar_balance}}.shop_id' => Yii::$app->params['user.current_shop']])->andWhere('amount > 0');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->sort->attributes['tovar.artikul'] = [
		    'asc' => ['tovar.artikul' => SORT_ASC],
		    'desc' => ['tovar.artikul' => SORT_DESC],
		];
		$dataProvider->sort->attributes['tovar_id'] = [
		    'asc' => ['tovar.name' => SORT_ASC],
		    'desc' => ['tovar.name' => SORT_DESC],
		];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            //'tovar_id' => $this->tovar->name,
            'sklad_id' => $this->sklad_id,
            'amount' => $this->amount,
            'price' => $this->price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'shop_id' => $this->shop_id,
        ]);
        
         $query->andFilterWhere(['like', 'tovar.artikul', $this->getAttribute('tovar.artikul')])
         	->andFilterWhere(['like', 'tovar.name', $this->getAttribute('tovar_id')]);

        return $dataProvider;
    }
}
