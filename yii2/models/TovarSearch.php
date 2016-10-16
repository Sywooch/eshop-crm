<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tovar;

/**
 * TovarSearch represents the model behind the search form about `app\models\Tovar`.
 */
class TovarSearch extends Tovar
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'active'], 'integer'],
            [['artikul', 'name', 'created_at', 'updated_at', 'active', 'category_id', 'price', 'pprice'], 'safe'],
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
        $query = Tovar::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andFilterWhere(['{{tovar}}.shop_id' => Yii::$app->params['user.current_shop']]);

        $query->andFilterWhere([
            'id' => $this->id,
            'active' => $this->active,
            'category_id' => $this->category_id,
        ]);

        $query->andFilterWhere(['like', 'artikul', $this->artikul])
        	->andFilterWhere(['like', 'created_at', $this->created_at])
        	->andFilterWhere(['like', 'updated_at', $this->updated_at])
        	->andFilterWhere(['like', 'created_by', $this->created_at])
        	->andFilterWhere(['like', 'updated_by', $this->updated_at])
        	->andFilterWhere(['like', 'price', $this->price])
        	->andFilterWhere(['like', 'pprice', $this->pprice])
            ->andFilterWhere(['like', 'name', $this->name]);
       
        return $dataProvider;
    }
}
