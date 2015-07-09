<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PREPCandidato;

/**
 * PREPCandidatoSearch represents the model behind the search form about `app\models\PREPCandidato`.
 */
class PREPCandidatoSearch extends PREPCandidato
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_candidato', 'id_partido', 'municipio', 'distrito_local', 'distrito_federal', 'activo'], 'integer'],
            [['nombre', 'observaciones'], 'safe'],
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
        $query = PREPCandidato::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id_candidato' => $this->id_candidato,
            'id_partido' => $this->id_partido,
            'municipio' => $this->municipio,
            'distrito_local' => $this->distrito_local,
            'distrito_federal' => $this->distrito_federal,
            'activo' => $this->activo,
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'observaciones', $this->observaciones]);

        return $dataProvider;
    }
}
