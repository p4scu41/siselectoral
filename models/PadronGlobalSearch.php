<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PadronGlobal;

/**
 * PadronGlobalSearch represents the model behind the search form about `app\models\PadronGlobal`.
 */
class PadronGlobalSearch extends PadronGlobal
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CLAVEUNICA', 'ALFA_CLAVE_ELECTORAL', 'SEXO', 'NOMBRES', 'NOMBRE', 'APELLIDO_PATERNO', 'APELLIDO_MATERNO', 'CALLE', 'NUM_INTERIOR', 'NUM_EXTERIOR', 'COLONIA', 'CORREOELECTRONICO', 'TELMOVIL', 'TELCASA', 'CASILLA', 'DOMICILIO', 'DES_LOC', 'NOM_LOC'], 'safe'],
            [['CONS_ALF_POR_SECCION', 'FECHA_NACI_CLAVE_ELECTORAL', 'LUGAR_NACIMIENTO', 'DIGITO_VERIFICADOR', 'CLAVE_HOMONIMIA', 'CODIGO_POSTAL', 'FOLIO_NACIONAL', 'EN_LISTA_NOMINAL', 'ENTIDAD', 'DISTRITO', 'MUNICIPIO', 'SECCION', 'LOCALIDAD', 'MANZANA', 'NUM_EMISION_CREDENCIAL'], 'number'],
            [['DISTRITOLOCAL', 'IDPADRON'], 'integer'],
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
        $query = PadronGlobal::find();

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
            'CONS_ALF_POR_SECCION' => $this->CONS_ALF_POR_SECCION,
            'FECHA_NACI_CLAVE_ELECTORAL' => $this->FECHA_NACI_CLAVE_ELECTORAL,
            'LUGAR_NACIMIENTO' => $this->LUGAR_NACIMIENTO,
            'DIGITO_VERIFICADOR' => $this->DIGITO_VERIFICADOR,
            'CLAVE_HOMONIMIA' => $this->CLAVE_HOMONIMIA,
            'CODIGO_POSTAL' => $this->CODIGO_POSTAL,
            'FOLIO_NACIONAL' => $this->FOLIO_NACIONAL,
            'EN_LISTA_NOMINAL' => $this->EN_LISTA_NOMINAL,
            'ENTIDAD' => $this->ENTIDAD,
            'DISTRITO' => $this->DISTRITO,
            'MUNICIPIO' => $this->MUNICIPIO,
            'SECCION' => $this->SECCION,
            'LOCALIDAD' => $this->LOCALIDAD,
            'MANZANA' => $this->MANZANA,
            'NUM_EMISION_CREDENCIAL' => $this->NUM_EMISION_CREDENCIAL,
            'DISTRITOLOCAL' => $this->DISTRITOLOCAL,
            'IDPADRON' => $this->IDPADRON,
        ]);

        $query->andFilterWhere(['like', 'CLAVEUNICA', $this->CLAVEUNICA])
            ->andFilterWhere(['like', 'ALFA_CLAVE_ELECTORAL', $this->ALFA_CLAVE_ELECTORAL])
            ->andFilterWhere(['like', 'SEXO', $this->SEXO])
            ->andFilterWhere(['like', 'NOMBRES', $this->NOMBRES])
            ->andFilterWhere(['like', 'NOMBRE', $this->NOMBRE])
            ->andFilterWhere(['like', 'APELLIDO_PATERNO', $this->APELLIDO_PATERNO])
            ->andFilterWhere(['like', 'APELLIDO_MATERNO', $this->APELLIDO_MATERNO])
            ->andFilterWhere(['like', 'CALLE', $this->CALLE])
            ->andFilterWhere(['like', 'NUM_INTERIOR', $this->NUM_INTERIOR])
            ->andFilterWhere(['like', 'NUM_EXTERIOR', $this->NUM_EXTERIOR])
            ->andFilterWhere(['like', 'COLONIA', $this->COLONIA])
            ->andFilterWhere(['like', 'CORREOELECTRONICO', $this->CORREOELECTRONICO])
            ->andFilterWhere(['like', 'TELMOVIL', $this->TELMOVIL])
            ->andFilterWhere(['like', 'TELCASA', $this->TELCASA])
            ->andFilterWhere(['like', 'CASILLA', $this->CASILLA])
            ->andFilterWhere(['like', 'DOMICILIO', $this->DOMICILIO])
            ->andFilterWhere(['like', 'DES_LOC', $this->DES_LOC])
            ->andFilterWhere(['like', 'NOM_LOC', $this->NOM_LOC]);
        
        $query->orderBy('APELLIDO_PATERNO, APELLIDO_MATERNO, NOMBRE');

        return $dataProvider;
    }
}
