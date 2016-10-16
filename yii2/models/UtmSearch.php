<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UtmLabel;

/**
 * UtmSearch represents the model behind the search form about `app\models\UtmLabel`.
 */
class UtmSearch extends UtmLabel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id'], 'integer'],
            [['utm_campaign', 'utm_content', 'utm_source', 'utm_medium', 'utm_term', 'source_type', 'source', 'group_id', 'banner_id', 'position', 'position_type', 'region_name', 'device'], 'safe'],
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
        $query = UtmLabel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
				'defaultOrder' => ['id' => SORT_DESC],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
        ]);

        $query->andFilterWhere(['like', 'utm_campaign', $this->utm_campaign])
            ->andFilterWhere(['like', 'utm_content', $this->utm_content])
            ->andFilterWhere(['like', 'utm_source', $this->utm_source])
            ->andFilterWhere(['like', 'utm_medium', $this->utm_medium])
            ->andFilterWhere(['like', 'utm_term', $this->utm_term])
            ->andFilterWhere(['like', 'source_type', $this->source_type])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'group_id', $this->group_id])
            ->andFilterWhere(['like', 'banner_id', $this->banner_id])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'position_type', $this->position_type])
            ->andFilterWhere(['like', 'region_name', $this->region_name])
            ->andFilterWhere(['like', 'device', $this->device]);

        return $dataProvider;
    }
}
