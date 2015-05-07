<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Promocion".
 *
 * @property integer $IdEstructuraMov
 * @property string $IdpersonaPromovida
 * @property integer $IdPuesto
 * @property string $IdPersonaPromueve
 * @property string $IdPersonaPuesto
 * @property string $FechaPromocion
 */
class Promocion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Promocion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdPuesto', 'IdPersonaPromueve', 'IdpersonaPromovida'], 'required'],
            [['IdEstructuraMov', 'IdPuesto'], 'integer'],
            [['IdpersonaPromovida', 'IdPersonaPromueve', 'IdPersonaPuesto'], 'string'],
            [['FechaPromocion'], 'safe']
        ];
    }

    /**
     * return \yii\db\ActiveQuery
     */
    public function getPersonaPromueve()
    {
        return $this->hasOne(PadronGlobal::className(), ['CLAVEUNICA' => 'IdPersonaPromueve']);
    }

    /**
     * return \yii\db\ActiveQuery
     */
    public function getPuesto()
    {
        return $this->hasOne(DetalleEstructuraMovilizacion::className(), ['IdNodoEstructuraMov' => 'IdPuesto']);
    }

    /**
     * return \yii\db\ActiveQuery
     */
    public function getPersonaPuesto()
    {
        return $this->hasOne(PadronGlobal::className(), ['CLAVEUNICA' => 'IdPersonaPuesto']);
    }

    /**
     * return \yii\db\ActiveQuery
     */
    public function getPersonaPromovida()
    {
        return $this->hasOne(PadronGlobal::className(), ['CLAVEUNICA' => 'IdpersonaPromovida']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdEstructuraMov' => 'Id Estructura Mov',
            'IdpersonaPromovida' => 'Persona Promovida',
            'IdPuesto' => 'Puesto en donde promueve',
            'IdPersonaPromueve' => 'Persona que promueve',
            'IdPersonaPuesto' => 'Nombre del promotor asignado',
            'FechaPromocion' => 'Fecha de Promoción',
        ];
    }

    public static function getListNodos($filtros)
    {
        $municipio = $filtros['municipio'];
        $seccion = $filtros['seccion'];
        $nombre = $filtros['nombre'];
        $puesto = $filtros['puesto'];
        $id = $filtros['id'];

        $sql = 'SELECT
            [PadronGlobal].[CLAVEUNICA]
            ,[PadronGlobal].[CLAVEUNICA] AS id
            ,([PadronGlobal].[NOMBRE] + \' \' + [PadronGlobal].[APELLIDO_PATERNO]+ \' \' +[PadronGlobal].[APELLIDO_MATERNO]) AS NombreCompleto
            ,([PadronGlobal].[NOMBRE] + \' \' + [PadronGlobal].[APELLIDO_PATERNO]+ \' \' +[PadronGlobal].[APELLIDO_MATERNO]) AS text
            ,[DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
            ,[DetalleEstructuraMovilizacion].[Descripcion]
        FROM
            [DetalleEstructuraMovilizacion]
        INNER JOIN [PadronGlobal] ON '
            .($municipio != null ? '[PadronGlobal].[MUNICIPIO] = '.$municipio.' AND ' : '').
            '[PadronGlobal].[CLAVEUNICA] = [DetalleEstructuraMovilizacion].[IdPersonaPuesto]
        LEFT JOIN [CSeccion] ON
            [CSeccion].[IdSector] = [DetalleEstructuraMovilizacion].[IdSector] '
            .($municipio != null ? ' AND [CSeccion].[IdMunicipio] = '.$municipio.' ' : '').
        'WHERE 1 = 1 '
            .($municipio != null ? ' AND [DetalleEstructuraMovilizacion].[Municipio] = '.$municipio.' ' : '');

        if ($seccion) {
            $sql .= ' AND [CSeccion].[NumSector] = '.$seccion;
        }

        if ($nombre) {
            $sql .= ' AND ([PadronGlobal].[NOMBRE] + \' \' + [PadronGlobal].[APELLIDO_PATERNO]+ \' \' +[PadronGlobal].[APELLIDO_MATERNO]) LIKE \'%'.$nombre.'%\'';
        }

        if ($puesto) {
            $sql .= ' AND [DetalleEstructuraMovilizacion].[Descripcion] LIKE \'%'.$puesto.'%\'';
        }

        if ($id) {
            $sql .= ' AND [PadronGlobal].[CLAVEUNICA] = \''.$id.'\'';
        }

        $sql .= ' ORDER BY NombreCompleto';

        $result = Yii::$app->db->createCommand($sql)->queryAll();

        return $result;
    }

    public static function existsPromocion($promovido)
    {
        //$puestoPromueve, $personaPuestoPromueve, $promueve
        /*$sql = 'SELECT COUNT(*) AS TOTAL
            FROM [Promocion]
            WHERE
                [IdpersonaPromovida] = "'.$promovido.'" AND
                [IdPuesto] = '.$puestoPromueve.' AND
                [IdPersonaPromueve] = "'.$promueve.'" AND
                [IdPersonaPuesto] = "'.$personaPuestoPromueve.'"';*/
        $sql = 'SELECT COUNT(*) AS TOTAL
                FROM [Promocion]
                WHERE [IdpersonaPromovida] = \''.$promovido.'\'';

        $result = Yii::$app->db->createCommand($sql)->queryOne();

        return $result['TOTAL'];
    }

    public function getCountOrganizaciones()
    {
        $sql = 'SELECT count(DISTINCT [IdOrganizacion]) AS TOTAL '
            . 'FROM [IntegrantesOrganizaciones] '
            . 'WHERE [IdPersonaIntegrante] = \''.$this->IdpersonaPromovida.'\'';

        $result = Yii::$app->db->createCommand($sql)->queryOne();

        return $result['TOTAL'];
    }

    public static function getOrganizaciones($IdpersonaPromovida)
    {
        $sql = 'SELECT [Organizaciones].[Nombre]
                ,(PadronEnlace.NOMBRE+\' \'+PadronEnlace.APELLIDO_PATERNO+\' \'+PadronEnlace.APELLIDO_MATERNO) AS Enlace
                ,(PadronRepresentante.NOMBRE+\' \'+PadronRepresentante.APELLIDO_PATERNO+\' \'+PadronRepresentante.APELLIDO_MATERNO) AS Representante
            FROM [Organizaciones]
            LEFT JOIN [PadronGlobal] AS PadronEnlace ON
                [Organizaciones].[IdPersonaEnlace] = [PadronEnlace].[CLAVEUNICA]
            LEFT JOIN [PadronGlobal] AS PadronRepresentante ON
                [Organizaciones].[IdPersonaEnlace] = [PadronRepresentante].[CLAVEUNICA]
          WHERE IdOrganizacion IN ('
            . 'SELECT [IdOrganizacion] '
            . 'FROM [IntegrantesOrganizaciones] '
            . 'WHERE [IdPersonaIntegrante] = \''.$IdpersonaPromovida.'\')';

        $result = Yii::$app->db->createCommand($sql)->queryAll();

        return $result;
    }
    
}
