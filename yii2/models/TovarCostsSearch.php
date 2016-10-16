<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TovarCosts;

/**
 * TovarCostsSearch represents the model behind the search form about `app\models\TovarCosts`.
 */
class TovarCostsSearch extends TovarCosts
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tovar_id', 'current', 'active'], 'integer'],
            [['cost'], 'number'],
            [['note'], 'safe'],
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
        $query = TovarCosts::find();

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
            'cost' => $this->cost,
            'current' => $this->current,
            'active' => $this->active,
        ]);

        $query->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
