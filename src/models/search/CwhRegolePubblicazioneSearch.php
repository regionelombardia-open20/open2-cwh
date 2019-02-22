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

use lispa\amos\cwh\models\CwhRegolePubblicazione;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CwhRegolePubblicazioneSearch represents the model behind the search form about `lispa\amos\cwh\models\CwhRegolePubblicazione`.
 */
class CwhRegolePubblicazioneSearch extends CwhRegolePubblicazione
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nome', 'codice'], 'safe'],
        ];
    }

    public function scenarios()
    {
// bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = CwhRegolePubblicazione::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'codice', $this->codice]);

        return $dataProvider;
    }
}
