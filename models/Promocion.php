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
            ,REPLACE(([PadronGlobal].[NOMBRE] + \' \' + [PadronGlobal].[APELLIDO_PATERNO]+ \' \' +[PadronGlobal].[APELLIDO_MATERNO]), \'\\\', \'Ñ\') AS NombreCompleto
            ,REPLACE(([PadronGlobal].[NOMBRE] + \' \' + [PadronGlobal].[APELLIDO_PATERNO]+ \' \' +[PadronGlobal].[APELLIDO_MATERNO]), \'\\\', \'Ñ\') AS text
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
                ,REPLACE((PadronEnlace.NOMBRE+\' \'+PadronEnlace.APELLIDO_PATERNO+\' \'+PadronEnlace.APELLIDO_MATERNO), \'\\\', \'Ñ\') AS Enlace
                ,REPLACE((PadronRepresentante.NOMBRE+\' \'+PadronRepresentante.APELLIDO_PATERNO+\' \'+PadronRepresentante.APELLIDO_MATERNO), \'\\\', \'Ñ\') AS Representante
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

    public static function getByPromotor($promotor)
    {
        $sql = 'SELECT
            [PadronGlobal].[CLAVEUNICA],
            REPLACE(([PadronGlobal].APELLIDO_PATERNO+\' \'+[PadronGlobal].APELLIDO_MATERNO+\' \'+[PadronGlobal].NOMBRE), \'\\\', \'Ñ\') AS NOMBRECOMPLETO,
            [PadronGlobal].[SECCION],
            [PadronGlobal].[CASILLA],
            ([PadronGlobal].[DES_LOC]+\' \'+[PadronGlobal].[NOM_LOC]) AS COLONIA,
            [PadronGlobal].[SEXO],
            [Participacion]
        FROM [Promocion]
        INNER JOIN [PadronGlobal] ON
            [Promocion].[IdpersonaPromovida] = [PadronGlobal].[CLAVEUNICA]
        WHERE
            [Promocion].[IdPuesto] = '.$promotor.'
        ORDER BY NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO';

        $result = Yii::$app->db->createCommand($sql)->queryAll();

        /*foreach ($result as $index => $promovido) {
            $result[$index]['foto'] = PadronGlobal::getFotoByUID($result['CLAVEUNICA'], $result['SEXO']);
        }*/

        return $result;
    }

    public static function setParticipacion($promovidos)
    {
        if (count($promovidos)) {
            foreach ($promovidos as $promovido) {
                $query = 'UPDATE [Promocion]
                    SET [Participacion] = 1
                    WHERE [IdpersonaPromovida] = \''.$promovido.'\'';

                Yii::$app->db->createCommand($query)->execute();
            }
        }
    }

    public static function getAvanceBingo($idNodo)
    {
        $sql = 'SELECT
            COUNT([Promocion].[IdpersonaPromovida]) AS total_promovidos,
            COUNT([Promocion].[Participacion]) AS total_participacion
        FROM [Promocion]
        INNER JOIN [DetalleEstructuraMovilizacion] ON
            [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov] = [Promocion].[IdPuesto]
        WHERE [DetalleEstructuraMovilizacion].[Dependencias] LIKE \'%|'.$idNodo.'|%\' OR
            [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov] = '.$idNodo;

        $result = Yii::$app->db->createCommand($sql)->queryOne();

        return $result;
    }
    
    public static function getByDepentNodo($idNodo)
    {
        $sql = 'SELECT
            SUBSTRING([PadronGlobal].APELLIDO_PATERNO,1,1) AS Letra,
            [PadronGlobal].[CLAVEUNICA],
            REPLACE(([PadronGlobal].APELLIDO_PATERNO+\' \'+[PadronGlobal].APELLIDO_MATERNO+\' \'+[PadronGlobal].NOMBRE), \'\\\', \'Ñ\') AS NOMBRECOMPLETO,
            [PadronGlobal].[SECCION],
            [PadronGlobal].[CASILLA],
            ([PadronGlobal].[DES_LOC]+\' \'+[PadronGlobal].[NOM_LOC]) AS COLONIA,
            [PadronGlobal].[SEXO],
            [Participacion],
            REPLACE((padronPromotor.NOMBRE+\' \'+padronPromotor.APELLIDO_PATERNO+\' \'+padronPromotor.APELLIDO_MATERNO), \'\\\', \'Ñ\') AS PROMOTOR
        FROM [Promocion]
        INNER JOIN [PadronGlobal] ON
            [Promocion].[IdpersonaPromovida] = [PadronGlobal].[CLAVEUNICA]
        INNER JOIN [DetalleEstructuraMovilizacion] ON
            [Promocion].[IdPuesto] = [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
        INNER JOIN [PadronGlobal] AS padronPromotor ON
            padronPromotor.CLAVEUNICA = [Promocion].[IdPersonaPromueve]
        WHERE
            [DetalleEstructuraMovilizacion].[Dependencias] LIKE \'%|'.$idNodo.'|%\' 
            OR [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov] = '.$idNodo.'
        ORDER BY [PadronGlobal].APELLIDO_PATERNO, [PadronGlobal].APELLIDO_MATERNO, [PadronGlobal].NOMBRE';

        $result = Yii::$app->db->createCommand($sql)->queryAll();

        /*foreach ($result as $index => $promovido) {
            $result[$index]['foto'] = PadronGlobal::getFotoByUID($result['CLAVEUNICA'], $result['SEXO']);
        }*/

        return $result;
    }

    public static function statusSeccionesBingo($muni)
    {
        $sql = 'SELECT
            [CSeccion].[NumSector],
            (SELECT COUNT([Promocion].[IdpersonaPromovida])
            FROM [Promocion]
            INNER JOIN [DetalleEstructuraMovilizacion] ON
                [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov] = [Promocion].[IdPuesto]
            WHERE
                ([DetalleEstructuraMovilizacion].[Dependencias] LIKE \'%|\'+CAST(arbolEstructura.[IdNodoEstructuraMov] AS VARCHAR)+\'|%\' OR
                [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov] = arbolEstructura.[IdNodoEstructuraMov]) AND
                [Promocion].[Participacion] IS NULL) AS participacionFaltantes
        FROM
            [DetalleEstructuraMovilizacion] AS arbolEstructura
        INNER JOIN [CSeccion] ON
            arbolEstructura.[Municipio] = [CSeccion].[IdMunicipio] AND
            arbolEstructura.[IdSector] = [CSeccion].[IdSector]
        WHERE
            arbolEstructura.[IdPuesto] = 5 AND
            arbolEstructura.[Municipio] = '.$muni.'
        ORDER BY
            [CSeccion].[NumSector]';

        $result = Yii::$app->db->createCommand($sql)->queryAll();

        /*foreach ($result as $index => $promovido) {
            $result[$index]['foto'] = PadronGlobal::getFotoByUID($result['CLAVEUNICA'], $result['SEXO']);
        }*/

        return $result;
    }
    
}
