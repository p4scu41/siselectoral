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
        $sql = 'SELECT [PREP_Casilla_Seccion].*
            FROM [PREP_Casilla_Seccion]
            INNER JOIN [PREP_Seccion] ON
                [PREP_Casilla_Seccion].[id_seccion] = [PREP_Seccion].[id_seccion]
            WHERE 1 = 1 ';

        if ($params['municipio']) {
            $sql .= ' AND [PREP_Seccion].[municipio] = '.$params['municipio'];
        }

        if ($params['PREPCasillaSeccionSearch']['id_seccion']) {
            $sql .= ' AND [PREP_Casilla_Seccion].[id_seccion] = '.$params['PREPCasillaSeccionSearch']['id_seccion'];
        }

        if (isset($params['PREPCasillaSeccionSearch']['activo']) && $params['PREPCasillaSeccionSearch']['activo']!=-1) {
            $sql .= ' AND [PREP_Casilla_Seccion].[activo] = '.$params['PREPCasillaSeccionSearch']['activo'];
        }

        $sql .= ' ORDER BY [PREP_Seccion].[seccion], [PREP_Casilla_Seccion].[descripcion]';

        $query = PREPCasillaSeccion::findBySql($sql);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }
}
