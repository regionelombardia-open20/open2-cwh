<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh
 * @category   CategoryName
 */

namespace lispa\amos\cwh\models\search;

use lispa\amos\cwh\models\CwhConfig;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CwhConfigSearch represents the model behind the search form about `lispa\amos\cwh\models\CwhConfig`.
 */
class CwhConfigSearch extends CwhConfig
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['classname', 'raw_sql', 'tablename'], 'safe'],
        ];
    }

    public function scenarios()
    {
// bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = CwhConfig::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'classname', $this->classname])
            ->andFilterWhere(['like', 'raw_sql', $this->raw_sql])
            ->andFilterWhere(['like', 'tablename', $this->tablename]);

        return $dataProvider;
    }
}
