<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Organizaciones".
 *
 * @property integer $IdOrganizacion
 * @property string $Nombre
 * @property string $Siglas
 * @property string $IdPersonaRepresentante
 * @property string $IdPersonaEnlace
 * @property integer $IdMunicipio
 * @property string $Observaciones
 * @property integer $idTipoOrganizacion
 */
class Organizaciones extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Organizaciones';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdOrganizacion'], 'required'],
            [['IdOrganizacion', 'IdMunicipio', 'idTipoOrganizacion'], 'integer'],
            [['Nombre', 'Siglas', 'IdPersonaRepresentante', 'IdPersonaEnlace', 'Observaciones'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdOrganizacion' => 'Id Organizacion',
            'Nombre' => 'Nombre',
            'Siglas' => 'Siglas',
            'IdPersonaRepresentante' => 'Id Persona Representante',
            'IdPersonaEnlace' => 'Id Persona Enlace',
            'IdMunicipio' => 'Id Municipio',
            'Observaciones' => 'Observaciones',
            'idTipoOrganizacion' => 'Id Tipo Organizacion',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['IdOrganizacion'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIntegrantes()
    {
        return $this->hasMany(PadronGlobal::className(), ['CLAVEUNICA' => 'IdPersonaIntegrante'])
                ->viaTable('IntegrantesOrganizaciones', ['IdOrganizacion' => 'IdOrganizacion']);
    }
}