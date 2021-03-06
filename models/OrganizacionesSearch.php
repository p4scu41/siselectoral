<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Organizaciones;

/**
 * OrganizacionesSearch represents the model behind the search form about `app\models\Organizaciones`.
 */
class OrganizacionesSearch extends Organizaciones
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdOrganizacion', 'IdMunicipio', 'idTipoOrganizacion'], 'integer'],
            [['Nombre', 'Siglas', 'IdPersonaRepresentante', 'IdPersonaEnlace', 'Observaciones'], 'safe'],
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
        $query = Organizaciones::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // Si no es administrador, solo mostrar lo correspondiente a su municipio
        if (strtolower(Yii::$app->user->identity->perfil->IdPerfil) != strtolower(Yii::$app->params['idAdmin'])) {
            $this->IdMunicipio = Yii::$app->user->identity->persona->MUNICIPIO;
        }

        $query->andFilterWhere([
            'IdOrganizacion' => $this->IdOrganizacion,
            'IdMunicipio' => $this->IdMunicipio,
            'idTipoOrganizacion' => $this->idTipoOrganizacion,
        ]);

        $query->andFilterWhere(['like', 'Nombre', $this->Nombre])
            ->andFilterWhere(['like', 'Siglas', $this->Siglas])
            ->andFilterWhere(['like', 'IdPersonaRepresentante', $this->IdPersonaRepresentante])
            ->andFilterWhere(['like', 'IdPersonaEnlace', $this->IdPersonaEnlace])
            ->andFilterWhere(['like', 'Observaciones', $this->Observaciones]);

        return $dataProvider;
    }
}
