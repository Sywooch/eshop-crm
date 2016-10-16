<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Price;

/**
 * PriceSearch represents the model behind the search form about `app\models\Price`.
 */
class PriceSearch extends Price
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tovar_id', 'category_id'], 'integer'],
            [['artikul', 'name', 'created_at', 'updated_at'], 'safe'],            
            //[['price'], 'number'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Price::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tovar_id' => $this->tovar_id,
            'price' => $this->price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category_id' => $this->category_id,
            'active' => $this->active,
        ]);

        $query->andFilterWhere(['like', 'artikul', $this->artikul])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
