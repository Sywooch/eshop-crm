<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Statmetrika;

/**
 * StatmetrikaSearch represents the model behind the search form about `app\models\Statmetrika`.
 */
class StatmetrikaSearch extends Statmetrika
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'visits', 'page_views', 'new_visitors', 'visit_time'], 'integer'],
            [['date_at', 'host', 'label'], 'safe'],
            [['denial', 'depth'], 'number'],
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
        $query = Statmetrika::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'date_at' => $this->date_at,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'visits' => $this->visits,
            'page_views' => $this->page_views,
            'new_visitors' => $this->new_visitors,
            'denial' => $this->denial,
            'depth' => $this->depth,
            'visit_time' => $this->visit_time,
        ]);

        $query->andFilterWhere(['like', 'host', $this->host])
            ->andFilterWhere(['like', 'label', $this->label]);

        return $dataProvider;
    }
}
