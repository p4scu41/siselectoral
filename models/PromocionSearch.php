<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Promocion;

/**
 * PromocionSearch represents the model behind the search form about `app\models\Promocion`.
 */
class PromocionSearch extends Promocion
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdEstructuraMov', 'IdPuesto'], 'integer'],
            [['IdpersonaPromovida', 'IdPersonaPromueve', 'IdPersonaPuesto', 'FechaPromocion'], 'safe'],
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
        $query = Promocion::find();

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
            'IdEstructuraMov' => $this->IdEstructuraMov,
            'IdPuesto' => $this->IdPuesto,
            'FechaPromocion' => $this->FechaPromocion,
        ]);

        $query->andFilterWhere(['like', 'IdpersonaPromovida', $this->IdpersonaPromovida])
            ->andFilterWhere(['like', 'IdPersonaPromueve', $this->IdPersonaPromueve])
            ->andFilterWhere(['like', 'IdPersonaPuesto', $this->IdPersonaPuesto]);

        $query->orderBy('FechaPromocion DESC');

        return $dataProvider;
    }
}