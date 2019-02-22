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

use lispa\amos\cwh\models\CwhPubblicazioni;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CwhPubblicazioniSearch represents the model behind the search form about `lispa\amos\cwh\models\CwhPubblicazioni`.
 */
class CwhPubblicazioniSearch extends CwhPubblicazioni
{
    public function rules()
    {
        return [
            [['id', 'cwh_config_id', 'cwh_regole_pubblicazione_id'], 'integer'],
        ];
    }

    public function scenarios()
    {
// bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = CwhPubblicazioni::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'cwh_config_id' => $this->cwh_config_id,
            'cwh_regole_pubblicazione_id' => $this->cwh_regole_pubblicazione_id,
        ]);

        return $dataProvider;
    }
}
