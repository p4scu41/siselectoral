<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PREPCasillaSeccion;

/**
 * PREPCasillaSeccionSearch represents the model behind the search form about `app\models\PREPCasillaSeccion`.
 */
class PREPCasillaSeccionSearch extends PREPCasillaSeccion
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_casilla_seccion', 'id_seccion', 'id_casilla', 'cp'], 'integer'],
            [['descripcion', 'colonia', 'domicilio', 'localidad', 'repre_gral', 'tel_repre_gral', 'repre_casilla', 'tel_repre_casilla', 'observaciones'], 'safe'],
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
        $query = PREPCasillaSeccion::find();

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
            'id_casilla_seccion' => $this->id_casilla_seccion,
            'id_seccion' => $this->id_seccion,
            'id_casilla' => $this->id_casilla,
            'cp' => $this->cp,
        ]);

        $query->andFilterWhere(['like', 'descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'colonia', $this->colonia])
            ->andFilterWhere(['like', 'domicilio', $this->domicilio])
            ->andFilterWhere(['like', 'localidad', $this->localidad])
            ->andFilterWhere(['like', 'repre_gral', $this->repre_gral])
            ->andFilterWhere(['like', 'tel_repre_gral', $this->tel_repre_gral])
            ->andFilterWhere(['like', 'repre_casilla', $this->repre_casilla])
            ->andFilterWhere(['like', 'tel_repre_casilla', $this->tel_repre_casilla])
            ->andFilterWhere(['like', 'observaciones', $this->observaciones]);

        return $dataProvider;
    }
}
