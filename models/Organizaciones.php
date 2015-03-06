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

    /**
     * Obtiene el número de integrantes de la organización
     *
     * @param Int $idOrganicacion
     * @return Int Cantidad de integrantes de la organización
     */
    public static function getCountIntegrantes($idOrganicacion, $idMuni)
    {
        $count = Yii::$app->db->createCommand('SELECT
                    COUNT([PadronGlobal].[CLAVEUNICA]) AS total
                    FROM
                        [IntegrantesOrganizaciones]
                    INNER JOIN [PadronGlobal] ON
                        [IntegrantesOrganizaciones].[IdPersonaIntegrante] = [PadronGlobal].[CLAVEUNICA]
                    WHERE
                        [IntegrantesOrganizaciones].[IdOrganizacion] = '.$idOrganicacion.' AND
                        [PadronGlobal].[MUNICIPIO] = '.$idMuni)->queryOne();

        return $count['total'];
    }

    /**
     * Obtiene el número de integrantes de la organización divididos por sección
     *
     * @param Int $idOrganicacion
     * @return Array Cantidad de integrantes por seccion
     */
    public static function getCountIntegrantesBySeccion($idOrganicacion, $idMuni)
    {
        $count = Yii::$app->db->createCommand('SELECT
                    [PadronGlobal].[SECCION]
                    ,COUNT([PadronGlobal].[CLAVEUNICA]) AS total
                FROM
                    [IntegrantesOrganizaciones]
                INNER JOIN [PadronGlobal] ON
                    [IntegrantesOrganizaciones].[IdPersonaIntegrante] = [PadronGlobal].[CLAVEUNICA]
                WHERE
                    [IntegrantesOrganizaciones].[IdOrganizacion] = '.$idOrganicacion.' AND
                    [PadronGlobal].[MUNICIPIO] = '.$idMuni.'
                GROUP BY
                    [PadronGlobal].[SECCION]
                ORDER BY
                    [SECCION]')->queryAll();

        return $count;
    }
}
