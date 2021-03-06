<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\helpers\MunicipiosUsuario;

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
            'IdPersonaRepresentante' => 'Responsable',
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
                REPLACE(([PadronGlobal].[NOMBRE]+\' \'+[PadronGlobal].[APELLIDO_PATERNO]
                        +\' \'+[PadronGlobal].[APELLIDO_MATERNO]), \'\\\', \'Ñ\') AS NombreCompleto,
                CAST([PadronGlobal].[SECCION] AS INT) AS [SECCION],
                [PadronGlobal].[TELCASA],
                [PadronGlobal].[TELMOVIL],
                [PadronGlobal].[COLONIA] AS Domicilio
                ,[CMunicipio].[DescMunicipio]
                ,[DetallePromocion].[IdPErsonaPromueve]
                ,REPLACE(([personaPromueve].[NOMBRE]+\' \'+[personaPromueve].[APELLIDO_PATERNO]
                    +\' \'+[personaPromueve].[APELLIDO_MATERNO]), \'\\\', \'Ñ\') AS nombre_promueve
                ,(SELECT
                    COUNT(*)
                FROM
                    [IntegrantesOrganizaciones]
                WHERE
                    [IdOrganizacion] != '.$idOrganizacion.' AND
                    [IdPersonaIntegrante] = [PadronGlobal].[CLAVEUNICA] ) AS otrasOrganizaciones
            FROM
                [IntegrantesOrganizaciones]
            INNER JOIN
                [PadronGlobal] ON
                    [PadronGlobal].[CLAVEUNICA] = [IntegrantesOrganizaciones].[IdPersonaIntegrante] ';

        // Si no es administrador, solo mostrar lo correspondiente a su municipio
        if (strtolower(Yii::$app->user->identity->perfil->IdPerfil) != strtolower(Yii::$app->params['idAdmin'])) {
            $sqlIntegrantes .= ' AND [PadronGlobal].[MUNICIPIO] = '.Yii::$app->user->identity->persona->MUNICIPIO.' ';
        }

        // Se aplica OUTER APPLY sobre DetallePromocion porque existen personas que han sido
        // promovidos mas de una vez, por eso al hacer el cruze con la tabla promovidos
        // algunos registros se duplican, por eso se aplica el TOP 1
        $sqlIntegrantes .= ' INNER JOIN [CMunicipio] ON
                    [PadronGlobal].[MUNICIPIO] = [CMunicipio].[IdMunicipio]
            OUTER APPLY (
                SELECT TOP 1 *
                FROM [DetallePromocion]
                WHERE [DetallePromocion].[IdpersonaPromovida] = [PadronGlobal].[CLAVEUNICA]
            ) DetallePromocion
            LEFT JOIN [PadronGlobal] AS [personaPromueve] ON
                [personaPromueve].[CLAVEUNICA] = [DetallePromocion].[IdPErsonaPromueve]
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

        $dependencias['municipios'] = MunicipiosUsuario::getMunicipios();

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
        $sqlCount = 'SELECT
                    COUNT([PadronGlobal].[CLAVEUNICA]) AS total
                    FROM
                        [IntegrantesOrganizaciones]
                    INNER JOIN [PadronGlobal] ON
                        [IntegrantesOrganizaciones].[IdPersonaIntegrante] = [PadronGlobal].[CLAVEUNICA]
                    WHERE
                        [IntegrantesOrganizaciones].[IdOrganizacion] = '.$idOrganicacion.' AND ';

        if ($seccion!=null) {
            if (is_array($seccion)) {
                $sqlCount .= ' [PadronGlobal].[SECCION] IN ('.implode(',',$seccion).') AND ';
            } else {
                $sqlCount .= ' [PadronGlobal].[SECCION] = '.$seccion.' AND ';
            }
        }

        $sqlCount .= ' [PadronGlobal].[MUNICIPIO] = '.$idMuni;

        $count = Yii::$app->db->createCommand($sqlCount)->queryOne();

        return $count['total'];
    }

    /**
     * Obtiene el número de integrantes promovidos de la organización
     *
     * @param Int $idOrganicacion
     * @return Int Cantidad de integrantes de la organización
     */
    public static function getCountIntegrantesPromovidos($idOrganicacion, $idMuni, $seccion = null)
    {
        $sqlCount = 'SELECT
                    COUNT([PadronGlobal].[CLAVEUNICA]) AS total
                    FROM
                        [IntegrantesOrganizaciones]
                    INNER JOIN [PadronGlobal] ON
                        [IntegrantesOrganizaciones].[IdPersonaIntegrante] = [PadronGlobal].[CLAVEUNICA]
                    INNER JOIN [Promocion] ON
                        [Promocion].[IdpersonaPromovida] = [IntegrantesOrganizaciones].[IdPersonaIntegrante]
                    WHERE
                        [IntegrantesOrganizaciones].[IdOrganizacion] = '.$idOrganicacion.' AND ';

        if ($seccion!=null) {
            if (is_array($seccion)) {
                $sqlCount .= ' [PadronGlobal].[SECCION] IN ('.implode(',',$seccion).') AND ';
            } else {
                $sqlCount .= ' [PadronGlobal].[SECCION] = '.$seccion.' AND ';
            }
        }

        $sqlCount .= ' [PadronGlobal].[MUNICIPIO] = '.$idMuni;

        $count = Yii::$app->db->createCommand($sqlCount)->queryOne();

        return $count['total'];
    }

    public function getTotalIntegrantes()
    {
        $sqlCount = 'SELECT
                COUNT(*) AS total
            FROM
                [IntegrantesOrganizaciones]
            WHERE
                [IdOrganizacion] = '.$this->IdOrganizacion;

        $count = Yii::$app->db->createCommand($sqlCount)->queryOne();

        return $count['total'];
    }

    public function getTotalIntegrantesPromovidos()
    {
        $sqlCount = 'SELECT
                COUNT(*) AS total
            FROM
                [IntegrantesOrganizaciones]
            INNER JOIN Promocion ON
                IntegrantesOrganizaciones.IdPersonaIntegrante = Promocion.IdpersonaPromovida
            WHERE
                [IdOrganizacion] = '.$this->IdOrganizacion;

        $count = Yii::$app->db->createCommand($sqlCount)->queryOne();

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
        $sqlCount = 'SELECT
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
                    [PadronGlobal].[MUNICIPIO] = '.$idMuni;

        if ($idSeccion!=null) {
            if (is_array($idSeccion)) {
                $sqlCount .= ' AND [PadronGlobal].[SECCION] IN ('.implode(',',$idSeccion).') ';
            } else {
                $sqlCount .= ' AND [PadronGlobal].[SECCION] = '.$idSeccion.' ';
            }
        }

        $sqlCount .= ' GROUP BY
                    [PadronGlobal].[SECCION], [CSeccion].[MetaAlcanzar]
                ORDER BY
                    [SECCION]';
        
        $count = Yii::$app->db->createCommand($sqlCount)->queryAll();

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
                    ,REPLACE(([PadronGlobal].[NOMBRE]+\' \'+[PadronGlobal].[APELLIDO_PATERNO]
                        +\' \'+[PadronGlobal].[APELLIDO_MATERNO]), \'\\\', \'Ñ\') AS NOMBRE
                    ,[PadronGlobal].[FECHANACIMIENTO]
                    ,[PadronGlobal].[COLONIA]
                    ,[CMunicipio].[DescMunicipio]
                    ,[Promocion].[IdPersonaPromueve]
                    ,REPLACE(([personaPromueve].[NOMBRE]+\' \'+[personaPromueve].[APELLIDO_PATERNO]
                        +\' \'+[personaPromueve].[APELLIDO_MATERNO]), \'\\\', \'Ñ\') AS nombre_promueve
                FROM
                    [IntegrantesOrganizaciones]
                INNER JOIN [PadronGlobal] ON
                    [IntegrantesOrganizaciones].[IdPersonaIntegrante] = [PadronGlobal].[CLAVEUNICA]
                INNER JOIN [CMunicipio] ON
                    [PadronGlobal].[MUNICIPIO] = [CMunicipio].[IdMunicipio]
                LEFT JOIN [Promocion] ON
                    [Promocion].[IdpersonaPromovida] = [IntegrantesOrganizaciones].[IdPersonaIntegrante]
                LEFT JOIN [PadronGlobal] AS [personaPromueve] ON
                    [personaPromueve].[CLAVEUNICA] = [Promocion].[IdPersonaPromueve]
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
                REPLACE(([PadronGlobal].[NOMBRE]+\' \'+[PadronGlobal].[APELLIDO_PATERNO]
                        +\' \'+[PadronGlobal].[APELLIDO_MATERNO]), \'\\\', \'Ñ\') AS NombreCompleto,
                CAST([PadronGlobal].[SECCION] AS INT) AS [SECCION],
                [PadronGlobal].[MUNICIPIO],
                [PadronGlobal].[TELCASA],
                [PadronGlobal].[TELMOVIL],
                [PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]
                    +\' \'+[PadronGlobal].[NOM_LOC] As Domicilio
                ,[CMunicipio].[DescMunicipio]
            FROM
                [PadronGlobal]
            INNER JOIN [CMunicipio] ON
                    [PadronGlobal].[MUNICIPIO] = [CMunicipio].[IdMunicipio]
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

    public static function getOrgsOnMuni($idMuni, $alterna)
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
                [Organizaciones].[IdOrganizacion] 
                '.($alterna ? '' : 'NOT').' IN (
                    SELECT DISTINCT([IdOrganizacion])
                    FROM [DetalleEstructuraMovilizacion]
                    WHERE [IdOrganizacion] != -1
                ) AND
                [PadronGlobal].[MUNICIPIO] = '.$idMuni;

        $orgs = Yii::$app->db->createCommand($sqlOrgs)->queryAll();

        return $orgs;
    }

    public static function getPromotorByIntegrante($idIntegrante)
    {
        $sqlPromotores = 'SELECT
                    [IdPErsonaPromueve]
                    ,REPLACE(([PadronGlobal].[NOMBRE]+\' \'+[PadronGlobal].[APELLIDO_PATERNO]+\' \'+
                        [PadronGlobal].[APELLIDO_MATERNO]), \'\\\', \'Ñ\') AS NombreCompleto
                FROM
                    [DetallePromocion]
                INNER JOIN [PadronGlobal] ON
                    [DetallePromocion].[IdPErsonaPromueve] = [PadronGlobal].[CLAVEUNICA]
                WHERE [IdPersonaPromovida] = \''.$idIntegrante.'\'';

        $promotores = Yii::$app->db->createCommand($sqlPromotores)->queryAll();

        return $promotores;
    }

    public static function getOtrasOrganizaciones($integrante, $organizacion)
    {
        $sql = 'SELECT
            [Organizaciones].[Nombre]
        FROM [IntegrantesOrganizaciones]
        INNER JOIN [Organizaciones] On
            [Organizaciones].[IdOrganizacion] = [IntegrantesOrganizaciones].[IdOrganizacion]
        WHERE
            [IntegrantesOrganizaciones].[IdOrganizacion] != '.$organizacion.' AND
            [IntegrantesOrganizaciones].[IdPersonaIntegrante] = \''.$integrante.'\'';

        $organizaciones = Yii::$app->db->createCommand($sql)->queryAll();

        return $organizaciones;
    }

    public static function countDuplicados($idOrg = null)
    {
        $sql = 'SELECT 
            COUNT (*) AS duplicados 
        FROM ( 
            SELECT count([IdPersonaIntegrante]) AS integrante 
            FROM [SIRECIDB].[dbo].[IntegrantesOrganizaciones] '.
            ($idOrg != null ? ' WHERE IdOrganizacion = '.$idOrg : ' ').
            'GROUP BY IdPersonaIntegrante 
        ) AS resultado 
        WHERE 
            integrante > 1';

        $count = Yii::$app->db->createCommand($sql)->queryOne();

        return $count['duplicados'];
    }
}
