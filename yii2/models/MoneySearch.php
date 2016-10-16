<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Money;

/**
 * MoneySearch represents the model behind the search form about `app\models\Money`.
 */
class MoneySearch extends Money
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'item_id', 'method_id'], 'integer'],
            [['created_at', 'updated_at', 'created_by', 'updated_by', 'date_at', 'type', 'note'], 'safe'],
            [['summa'], 'number'],
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
        $query = Money::find();

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
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'summa' => $this->summa,
            'item_id' => $this->item_id,
            'method_id' => $this->method_id,
        ]);

        $query->andFilterWhere(['like', 'created_at', $this->created_at])
        	->andFilterWhere(['like', 'updated_at', $this->updated_at])
        	->andFilterWhere(['like', 'date_at', $this->date_at])
        	->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
