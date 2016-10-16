<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Kladr;

/**
 * KladrSearch represents the model behind the search form about `app\models\Kladr`.
 */
class KladrSearch extends Kladr
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'socr', 'code', 'index', 'gninmb', 'uno', 'ocatd', 'status', 'level'], 'safe'],
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
        $query = Kladr::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'socr', $this->socr])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'index', $this->index])
            ->andFilterWhere(['like', 'gninmb', $this->gninmb])
            ->andFilterWhere(['like', 'uno', $this->uno])
            ->andFilterWhere(['like', 'ocatd', $this->ocatd])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['level'=>$this->level]);

        return $dataProvider;
    }
    	/**
	 * получить список регионов из kladr
	 * @return array
	 */
	public static function regionList() {
		$q = static::find()->where(['level'=>1])->andWhere(['REGEXP','code', "0{10}$"])->asArray()->all();	
		$qq = array_values($q);
		$r = array();
		foreach ($q as $qq) {
			if ($qq['index'])
				$r[$qq['code']] = substr($qq['code'], 0, 2) . ' ' . $qq['name'] . ' ' . $qq['socr'] . ' (' . $qq['index'] . ')';
			else
				$r[$qq['code']] = substr($qq['code'], 0, 2) . ' ' . $qq['name'] . ' ' . $qq['socr'];
		}
		ksort($r);
		return ($r);
	}

	/**
	 * получить список районов по коду региона
	 * @param int $code
	 * @return array
	 */
	public static function areaList($code) {
		if ($code == false or $code == null or empty($code))
			return array();
		$code1 = substr($code, 0, 2);
		$q = static::find()->where(['level'=>2])->andWhere(['!=','code',"$code1"])->andWhere(['REGEXP','code', "^$code1"])->all();

		$qq = array_values($q);

		foreach ($q as $qq) {
			if ($qq['index'])
				$r[$qq['code']] = $qq['name'] . ' ' . $qq['socr'] . ' (' . $qq['index'] . ')';
			else
				$r[$qq['code']] = $qq['name'] . ' ' . $qq['socr'];
		}
		asort($r);
		return ($r);
	}

	/**
	 * получить список городов по коду района
	 * @param int $code
	 * @return array
	 */
	public static function cityList($code) {
		if ($code === false or $code == null or empty($code))
			return array();
		$code1 = substr($code, 0, 5);
		$q = static::find()->where(['level'=>3])->andWhere(['REGEXP','code', "^$code1"])->all();

		$qq = array_values($q);
		foreach ($q as $qq) {
			if ($qq['index'])
				$r[$qq['code']] = $qq['name'] . ' ' . $qq['socr'] . ' (' . $qq['index'] . ')';
			else
				$r[$qq['code']] = $qq['name'] . ' ' . $qq['socr'];
		}
		asort($r);
		return ($r);
	}

	/**
	 * получить список нас.пунктов по коду района
	 * @param int $code
	 * @return array
	 */
	public static function settlementList($code) {
		if ($code === false or $code == null or empty($code))
			return array();
		$code1 = substr($code, 0, 8);
		$q = static::find()->where(['level'=>4])->andWhere(['REGEXP','code',"^$code1"])->all();
		$qq = array_values($q);
		foreach ($q as $qq) {
			if ($qq['index'])
				$r[$qq['code']] = $qq['name'] . ' ' . $qq['socr'] . ' (' . $qq['index'] . ')';
			else
				$r[$qq['code']] = $qq['name'] . ' ' . $qq['socr'];
		}
		asort($r);
		return ($r);
	}
}
