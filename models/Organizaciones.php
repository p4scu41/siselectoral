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

    public static function listIntegrantes($idOrganizacion)
    {
        $sqlIntegrantes = 'SELECT
                [PadronGlobal].[CLAVEUNICA],
                ([PadronGlobal].[APELLIDO_PATERNO]+\' \'+[PadronGlobal].[APELLIDO_MATERNO]
                        +\' \'+[PadronGlobal].[NOMBRE]) AS NombreCompleto,
                CAST([PadronGlobal].[SECCION] AS INT) AS [SECCION],
                [PadronGlobal].[TELCASA],
                [PadronGlobal].[TELMOVIL],
                [PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]
                    +\' \'+[PadronGlobal].[NOM_LOC] As Domicilio
                ,[CMunicipio].[DescMunicipio]
            FROM
                [IntegrantesOrganizaciones]
            INNER JOIN
                [PadronGlobal] ON
                    [PadronGlobal].[CLAVEUNICA] = [IntegrantesOrganizaciones].[IdPersonaIntegrante]
            INNER JOIN [CMunicipio] ON
                    [PadronGlobal].[MUNICIPIO] = [CMunicipio].[IdMunicipio]
            WHERE
                [IdOrganizacion] = '.$idOrganizacion.'
            ORDER BY SECCION, NombreCompleto';

        $integrantes = Yii::$app->db->createCommand($sqlIntegrantes)->queryAll();

        return $integrantes;
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
    public static function getCountIntegrantes($idOrganicacion, $idMuni, $seccion = null)
    {
        $count = Yii::$app->db->createCommand('SELECT
                    COUNT([PadronGlobal].[CLAVEUNICA]) AS total
                    FROM
                        [IntegrantesOrganizaciones]
                    INNER JOIN [PadronGlobal] ON
                        [IntegrantesOrganizaciones].[IdPersonaIntegrante] = [PadronGlobal].[CLAVEUNICA]
                    WHERE
                        [IntegrantesOrganizaciones].[IdOrganizacion] = '.$idOrganicacion.' AND '.
                        ($seccion!=null ? '[PadronGlobal].[SECCION] = '.$seccion.' AND ' : '')
                        .'[PadronGlobal].[MUNICIPIO] = '.$idMuni)->queryOne();

        return $count['total'];
    }

    /**
     * Obtiene el número de integrantes de la organización divididos por sección
     *
     * @param Int $idOrganicacion
     * @return Array Cantidad de integrantes por seccion
     */
    public static function getCountIntegrantesBySeccion($idOrganicacion, $idMuni, $idSeccion = null)
    {
        $count = Yii::$app->db->createCommand('SELECT
                    CAST([PadronGlobal].[SECCION] AS INT) AS [SECCION]
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
                    [PadronGlobal].[MUNICIPIO] = '.$idMuni.
                    ($idSeccion!=null ? ' AND  [PadronGlobal].[SECCION] = '.$idSeccion : '')
                .'GROUP BY
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
                    ,[CMunicipio].[DescMunicipio]
                FROM
                    [IntegrantesOrganizaciones]
                INNER JOIN [PadronGlobal] ON
                    [IntegrantesOrganizaciones].[IdPersonaIntegrante] = [PadronGlobal].[CLAVEUNICA]
                INNER JOIN [CMunicipio] ON
                    [PadronGlobal].[MUNICIPIO] = [CMunicipio].[IdMunicipio]
                WHERE
                    [IntegrantesOrganizaciones].[IdOrganizacion] = '.$idOrganicacion.' AND
                    [PadronGlobal].[SECCION] = '.$idSeccion.'
                ORDER BY NOMBRE')->queryAll();

        return $listIntegrantes;
    }

    /**
     * Eliminar integrantes de la organización
     *
     * @param Int $idOrg
     * @param UID $idInte
     */
    public static function delIntegrante($idOrg, $idInte)
    {
        $sqlDelete = 'DELETE FROM [IntegrantesOrganizaciones]
                    WHERE [IdOrganizacion]='.$idOrg.' AND IdPersonaIntegrante=\''.$idInte.'\'';

        Yii::$app->db->createCommand($sqlDelete)->execute();
    }

    /**
     * Agregar integrantes de la organización
     *
     * @param Int $idOrg
     * @param UID $idInte
     */
    public static function addIntegrante($idOrg, $idInte)
    {
        $sqlInsert = 'INSERT INTO [IntegrantesOrganizaciones] ([IdOrganizacion] ,[IdPersonaIntegrante])
                    VALUES ('.$idOrg.', \''.$idInte.'\')';

        Yii::$app->db->createCommand($sqlInsert)->execute();

        $sqlIntegrante = 'SELECT
                [PadronGlobal].[CLAVEUNICA],
                ([PadronGlobal].[APELLIDO_PATERNO]+\' \'+[PadronGlobal].[APELLIDO_MATERNO]
                        +\' \'+[PadronGlobal].[NOMBRE]) AS NombreCompleto,
                CAST([PadronGlobal].[SECCION] AS INT) AS [SECCION],
                [PadronGlobal].[MUNICIPIO],
                [PadronGlobal].[TELCASA],
                [PadronGlobal].[TELMOVIL],
                [PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]
                    +\' \'+[PadronGlobal].[NOM_LOC] As Domicilio
            FROM
                [PadronGlobal]
            WHERE
                [PadronGlobal].[CLAVEUNICA] = \''.$idInte.'\'';

        $integrante = Yii::$app->db->createCommand($sqlIntegrante)->queryOne();

        // Se asigna al Jefe de Seción
        $sqlAsignaJS = 'UPDATE [DetalleEstructuraMovilizacion] SET [IdOrganizacion] = '.$idOrg.'
            WHERE [IdNodoEstructuraMov] = (SELECT TOP 1
                [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
            FROM
                [DetalleEstructuraMovilizacion]
            INNER JOIN
                [CSeccion] ON
                    [CSeccion].[IdMunicipio] = '.$integrante['MUNICIPIO'].' AND
                    [DetalleEstructuraMovilizacion].[IdSector] = [CSeccion].[IdSector] AND
                    [CSeccion].[NumSector] = '.$integrante['SECCION'].'
            WHERE [IdPuesto] = 5 AND [Municipio] = '.$integrante['MUNICIPIO'].')';

        Yii::$app->db->createCommand($sqlAsignaJS)->execute();

        return $integrante;
    }

    public static function getOrgsOnMuni($idMuni)
    {
        $sqlOrgs = 'SELECT
                distinct [Organizaciones].[IdOrganizacion],
                [Organizaciones].*
            FROM
                [IntegrantesOrganizaciones]
            INNER JOIN
                [PadronGlobal] ON
                [PadronGlobal].[CLAVEUNICA] = [IntegrantesOrganizaciones].[IdPersonaIntegrante]
            INNER JOIN
                [Organizaciones] ON
                [Organizaciones].[IdOrganizacion] = [IntegrantesOrganizaciones].[IdOrganizacion]
            WHERE
                [PadronGlobal].[MUNICIPIO] = '.$idMuni;

        $orgs = Yii::$app->db->createCommand($sqlOrgs)->queryAll();

        return $orgs;
    }
}
