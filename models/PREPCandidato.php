<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "PREP_Candidato".
 *
 * @property integer $id_candidato
 * @property integer $id_partido
 * @property integer $id_tipo_eleccion
 * @property integer $municipio
 * @property string $nombre
 * @property integer $distrito_local
 * @property integer $distrito_federal
 * @property integer $activo
 * @property integer $orden
 * @property string $observaciones
 */
class PREPCandidato extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PREP_Candidato';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_partido', 'id_tipo_eleccion'], 'required'],
            [['id_partido', 'municipio', 'id_tipo_eleccion', 'distrito_local', 'distrito_federal', 'activo', 'orden'], 'integer'],
            [['nombre', 'observaciones'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_candidato' => 'Id Candidato',
            'id_partido' => 'Partido',
            'id_tipo_eleccion' => 'Tipo de Elección',
            'municipio' => 'Municipio',
            'nombre' => 'Nombre',
            'distrito_local' => 'Distrito Local',
            'distrito_federal' => 'Distrito Federal',
            'activo' => 'Activo',
            'observaciones' => 'Observaciones',
            'orden' => 'Orden'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id_candidato'];
    }

    /**
     * return \yii\db\ActiveQuery
     */
    public function getPartido()
    {
        return $this->hasOne(PREPPartido::className(), ['id_partido' => 'id_partido']);
    }

    /**
     * return \yii\db\ActiveQuery
     */
    public function getCmunicipio()
    {
        return $this->hasOne(CMunicipio::className(), ['IdMunicipio' => 'municipio']);
    }

    /**
     * return \yii\db\ActiveQuery
     */
    public function getTipoEleccion()
    {
        return $this->hasOne(PREPTipoEleccion::className(), ['id_tipo_eleccion' => 'id_tipo_eleccion']);
    }
}
