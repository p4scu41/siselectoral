<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PREPSeccion;

/**
 * PREPSeccionSearch represents the model behind the search form about `app\models\PREPSeccion`.
 */
class PREPSeccionSearch extends PREPSeccion
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_seccion', 'municipio', 'zona', 'seccion', 'distrito_local', 'distrito_federal', 'activo'], 'integer'],
            [['observaciones', 'fecha_cierre'], 'safe'],
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
        $query = PREPSeccion::find();

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
            'id_seccion' => $this->id_seccion,
            'municipio' => $this->municipio,
            'zona' => $this->zona,
            'seccion' => $this->seccion,
            'distrito_local' => $this->distrito_local,
            'distrito_federal' => $this->distrito_federal,
            'activo' => $this->activo,
            'fecha_cierre' => $this->fecha_cierre,
        ]);

        $query->andFilterWhere(['like', 'observaciones', $this->observaciones]);

        return $dataProvider;
    }
}
