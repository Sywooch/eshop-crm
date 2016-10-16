<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sms;

/**
 * SmsSearch represents the model behind the search form about `app\models\Sms`.
 */
class SmsSearch extends Sms
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'status'], 'integer'],
            [['sms_id', 'event', 'msg', 'note', 'shop_id', 'created_at'], 'safe'],
            [['cost'], 'number'],
            [['phone'], 'string'],
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
        $query = Sms::find()->joinWith(['order'])->where(['{{sms}}.shop_id' => Yii::$app->params['user.current_shop']]);

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
            'order_id' => $this->order_id,
            'status' => $this->status,
            'cost' => $this->cost,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'sms_id', $this->sms_id])
            ->andFilterWhere(['like', 'event', $this->event])
            ->andFilterWhere(['like', 'msg', $this->msg])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['=', 'DATE(FROM_UNIXTIME(sms.created_at)', $this->created_at])
            ->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
