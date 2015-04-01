<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

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
            [['Nombre', 'IdPersonaRepresentante', 'IdPersonaEnlace', 'IdMunicipio', 'idTipoOrganizacion'], 'required'],
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
            'IdPersonaRepresentante' => 'Representante',
            'IdPersonaEnlace' => 'Enlace',
            'IdMunicipio' => 'Municipio',
            'Observaciones' => 'Observaciones',
            'idTipoOrganizacion' => 'Tipo',
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
    public function getRepresentante()
    {
        return $this->hasOne(PadronGlobal::className(), ['CLAVEUNICA' => 'IdPersonaRepresentante']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnlace()
    {
        return $this->hasOne(PadronGlobal::className(), ['CLAVEUNICA' => 'IdPersonaEnlace']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMunicipio()
    {
        return $this->hasOne(CMunicipio::className(), ['IdMunicipio' => 'IdMunicipio']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoOrganizacion()
    {
        return $this->hasOne(ElementoCatalogo::className(), ['IdElementoCatalogo' => 'idTipoOrganizacion']);
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
     * Devuelve un array con municipios y tipos
     * necesarios para los combos en el formulario
     * 
     * @return Array
     */
    public static function getDependencias()
    {
        $dependencias = [
            'municipios' => null,
            'tipos' => null
        ];

        $dependencias['municipios'] = ArrayHelper::map(
            CMunicipio::find()
                ->select(['IdMunicipio', 'DescMunicipio'])
                ->orderBy('DescMunicipio')
                ->all(),
            'IdMunicipio',
            'DescMunicipio'
        );

        $dependencias['tipos'] = ArrayHelper::map(
                ElementoCatalogo::find()->where(['IdTipoCatalogo'=>4])
                ->select(['IdElementoCatalogo', 'Descripcion'])
                ->orderBy('Descripcion')
                ->all(),
            'IdElementoCatalogo',
            'Descripcion'
        );

        return $dependencias;
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
                    ,[CSeccion].[MetaAlcanzar]
                    ,COUNT([PadronGlobal].[CLAVEUNICA]) AS total
                FROM
                    [IntegrantesOrganizaciones]
                INNER JOIN [PadronGlobal] ON
                    [IntegrantesOrganizaciones].[IdPersonaIntegrante] = [PadronGlobal].[CLAVEUNICA]
                INNER JOIN [CSeccion] ON
                    [CSeccion].[NumSector] = [PadronGlobal].[SECCION] AND
                    [CSeccion].[IdMunicipio] = [PadronGlobal].[MUNICIPIO]
                WHERE
                    [IntegrantesOrganizaciones].[IdOrganizacion] = '.$idOrganicacion.' AND
                    [PadronGlobal].[MUNICIPIO] = '.$idMuni.'
                GROUP BY
                    [PadronGlobal].[SECCION], [CSeccion].[MetaAlcanzar]
                ORDER BY
                    [SECCION]')->queryAll();

        return $count;
    }

    /**
     * Obtiene el número de integrantes de la organización divididos por sección
     *
     * @param Int $idOrganicacion
     * @return Array Cantidad de integrantes por seccion
     */
    public static function getListIntegrantesFromSeccion($idOrganicacion, $idSeccion)
    {
        $listIntegrantes = Yii::$app->db->createCommand('SELECT
                    [PadronGlobal].[CLAVEUNICA]
                    ,[PadronGlobal].[SEXO]
                    ,([PadronGlobal].[NOMBRE]+\' \'+[PadronGlobal].[APELLIDO_PATERNO]
                        +\' \'+[PadronGlobal].[APELLIDO_MATERNO]) AS NOMBRE
                    ,[PadronGlobal].[FECHANACIMIENTO]
                    ,[PadronGlobal].[COLONIA]
                FROM
                    [IntegrantesOrganizaciones]
                INNER JOIN [PadronGlobal] ON
                    [IntegrantesOrganizaciones].[IdPersonaIntegrante] = [PadronGlobal].[CLAVEUNICA]
                WHERE
                    [IntegrantesOrganizaciones].[IdOrganizacion] = '.$idOrganicacion.' AND
                    [PadronGlobal].[SECCION] = '.$idSeccion.'
                ORDER BY NOMBRE')->queryAll();

        return $listIntegrantes;
    }
}
