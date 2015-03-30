<?php

namespace app\models;

use Yii;

/**
 * Clase que genera los reportes
 */
class Reporte extends \yii\db\ActiveRecord
{
    /**
     * Obtiene el avance por sección de un determinado Municipio
     *
     * @param Int $idMuni
     * @return Array
     */
    public static function avanceSeccional($idMuni)
    {
        $sql = 'SELECT
                    [CSeccion].[NumSector] AS \'Sección\',
                    CASE
                        WHEN tblPadron.[CLAVEUNICA] IS NULL
                        THEN \'NO ASIGNADO\'
                        ELSE (tblPadron.[APELLIDO_PATERNO]+\' \'+tblPadron.[APELLIDO_MATERNO]+\' \'+tblPadron.[NOMBRE])
                    END AS Responsable,
                    [CSeccion].MetaAlcanzar AS Meta,
                    ROUND((
                        SELECT
                            COUNT(*) as Avance
                        FROM [Promocion]
                        INNER JOIN [PadronGlobal] ON
                            [PadronGlobal].[CLAVEUNICA] = [Promocion].[IdpersonaPromovida]
                        WHERE [PadronGlobal].[MUNICIPIO] = '.$idMuni.' AND '.
                            '[PadronGlobal].[SECCION] = [CSeccion].[NumSector]
                    ) / [CSeccion].MetaAlcanzar * 100, 0) AS \'Avance %\'
                FROM
                    [DetalleEstructuraMovilizacion]
                LEFT JOIN [PadronGlobal] AS tblPadron ON
                    tblPadron.[CLAVEUNICA] = [DetalleEstructuraMovilizacion].[IdPersonaPuesto]
                INNER JOIN [CSeccion] ON
                    [CSeccion].[IdMunicipio] = [DetalleEstructuraMovilizacion].[Municipio] AND
                    [CSeccion].[IdSector] = [DetalleEstructuraMovilizacion].[IdSector]
                WHERE
                    [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.'
                ORDER BY
                    [CSeccion].[NumSector] ASC';

        $result = Yii::$app->db->createCommand($sql)->queryAll();

        return $result;
    }

    /**
     * Convierte Array de datos a una tabla HTML
     *
     * @param Array $arrayDatos Arreglo asociativo con los datos
     * @param Array $omitirCentrado Columnas que no deben centrarse
     * @param String $class clase css
     * @param String $options optiones html
     * @return String Tabla HTML
     */
    public function arrayToHtml(
        $arrayDatos,
        $omitirCentrado = [],
        $class = 'table table-condensed table-bordered table-hover',
        $options = 'border="1" cellpadding="1" cellspacing="1"'
    ) {
        $htmlTable = '<table class="'.$class.'" '.$options.'><thead><tr>';

        if (count($arrayDatos) == 0) {
            $htmlTable .= '<th class="text-center">No se encontrados datos en la búsqueda</th></tr></thead><tbody>';
        } else {
            // Encabezado
            $primeraFila = array_shift($arrayDatos);
            $encabezado = array_keys($primeraFila);
            array_unshift($arrayDatos, $primeraFila);

            foreach ($encabezado as $columna) {
                $htmlTable .= '<th class="text-center">'.(mb_check_encoding($columna, 'UTF-8') ?
                                    $columna : utf8_encode($columna)).'</th>';
            }

            $htmlTable .= '</tr></thead><tbody>';

            // Cuerpo
            foreach ($arrayDatos as $fila) {
                $htmlTable .= '<tr>';
                $i = 1;
                foreach ($fila as $columna) {
                    if (count($omitirCentrado)) {
                        if (in_array($i, $omitirCentrado)) {
                            $htmlTable .= '<td>'.$columna.'</td>';
                        } else {
                            $htmlTable .= '<td class="text-center">'.$columna.'</td>';
                        }
                    } else {
                        $htmlTable .= '<td class="text-center">'.$columna.'</td>';
                    }
                    $i++;
                }
                $htmlTable .= '</tr>';
            }
        }

        $htmlTable .= '</tbody></table>';

        return $htmlTable;
    }

    /**
     * Función recursiva que obtiene los datos de un nodo de la estructura
     *
     * @param type $idNodo
     * @return type
     */
    public static function nodoEstructuraJSON($idNodo, $puestos)
    {
        $sqlNodo = 'SELECT
                    [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
                    ,[Puestos].[Descripcion] AS DescripcionPuesto
                    ,[DetalleEstructuraMovilizacion].[Descripcion] AS DescripcionNodo
                    ,[CSeccion].[NumSector] as Seccion
                    ,CASE
                        WHEN [PadronGlobal].[CLAVEUNICA] IS NULL
                        THEN \'NO ASIGNADO\'
                        ELSE ([PadronGlobal].[APELLIDO_PATERNO]+\' \'+[PadronGlobal].[APELLIDO_MATERNO]+\' \'
                            +[PadronGlobal].[NOMBRE])
                    END AS Responsable
                    ,[PadronGlobal].[TELCASA]
                    ,[PadronGlobal].[TELMOVIL]
                    ,[PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]+\' \'
                        +[PadronGlobal].[NOM_LOC] As Domicilio
                FROM
                    [DetalleEstructuraMovilizacion]
                LEFT JOIN
                    [PadronGlobal] ON
                    [PadronGlobal].[CLAVEUNICA] = [DetalleEstructuraMovilizacion].[IdPersonaPuesto]
                INNER JOIN
                    [Puestos] ON
                    [DetalleEstructuraMovilizacion].[IdPuesto] = [Puestos].[IdPuesto]
                LEFT JOIN
                    [CSeccion] ON
                    [DetalleEstructuraMovilizacion].[Municipio]  = [CSeccion].[IdMunicipio] AND
                    [DetalleEstructuraMovilizacion].[IdSector] = [CSeccion].[IdSector]
                WHERE
                    [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov] = '.$idNodo;

        if (count($puestos)) {
            $sqlNodo .= ' AND [DetalleEstructuraMovilizacion].[IdPuesto] IN ('.implode(',', $puestos).')';
        }

        $estructura = '';

        $nodo = Yii::$app->db->createCommand($sqlNodo)->queryOne();

        if (!$nodo) {
            return $estructura;
        }

        $sqlDatosEstructura = 'SELECT
                    \'MetaEstructura\' AS Meta,
                    COUNT([IdEstructuraMovilizacion]) AS MetaEstructura
                FROM
                    [DetalleEstructuraMovilizacion]
                WHERE
                    [Dependencias] LIKE \'%|'.$idNodo.'|%\'
                UNION
                SELECT
                    \'AvanceEstructura\' AS Meta,
                    COUNT([IdEstructuraMovilizacion]) AS AvanceEstructura
                FROM
                    [DetalleEstructuraMovilizacion]
                WHERE
                    [Dependencias] LIKE \'%|'.$idNodo.'|%\'
                    AND IdPersonaPuesto != \'00000000-0000-0000-0000-000000000000\'';

        $metaEstructura = [
            'meta' => '',
            'avance' => ''
        ];

        $datosEstructura = Yii::$app->db->createCommand($sqlDatosEstructura)->queryAll();

        if (count($datosEstructura)) {
            $metaEstructura['meta'] = $datosEstructura[0]['MetaEstructura'];
            $metaEstructura['avance'] = ($metaEstructura['meta'] != 0 ?
                round($datosEstructura[1]['MetaEstructura']/$metaEstructura['meta']*100) : 0);
        }

        $sqlDatosPromocion = 'SELECT
                    \'MetaPromocion\' AS Meta,
                    SUM([Meta]) AS MetaPromocion
                FROM
                    [DetalleEstructuraMovilizacion]
                WHERE
                    [IdNodoEstructuraMov] = '.$idNodo.' OR
                    [Dependencias] LIKE \'%|'.$idNodo.'|%\'
                UNION
                SELECT
                    \'AvancePromocion\' AS Meta,
                    COUNT([Promocion].[IdpersonaPromovida]) AS AvancePromocion
                FROM
                    [Promocion]
                INNER JOIN
                    [DetalleEstructuraMovilizacion] ON
                        [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov] = [Promocion].[IdPuesto]
                WHERE
                    [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov] = '.$idNodo.'  OR
                    [DetalleEstructuraMovilizacion].[Dependencias] LIKE \'%|'.$idNodo.'|%\'';

        $metaPromocion = [
            'meta' => '',
            'avance' => ''
        ];

        $datosPromocion = Yii::$app->db->createCommand($sqlDatosPromocion)->queryAll();

        if (count($datosPromocion)) {
            $metaPromocion['meta'] = $datosPromocion[1]['MetaPromocion'];
            $metaPromocion['avance'] = ($datosPromocion['meta'] != 0 ?
                                            round($datosPromocion[0]['MetaPromocion']/$datosPromocion['meta']*100) : 0);
        }

        $estructura .= '{ "Puesto": "'.$nodo['DescripcionPuesto'].'", '
                        .'"Descripción": "'.$nodo['DescripcionNodo'].'",'
                        .'"Nombre": "'.str_replace('\\', 'Ñ', $nodo['Responsable']).'",'
                        .'"Sección": "'.$nodo['Seccion'].'",'
                        .'"Meta Estruc": "'.$metaEstructura['meta'].'",'
                        .'"% Avance Estruc": "'.$metaEstructura['avance'].'",'
                        .'"Meta Promo": "'.$metaPromocion['meta'].'",'
                        .'"% Avance Promo": "'.$metaPromocion['avance'].'",'
                        .'"Tel. Celular": "'.$nodo['TELMOVIL'].'", '
                        .'"Tel. Casa": "'.$nodo['TELCASA'].'", '
                        .'"Domicilio": "'.str_replace('\\', 'Ñ', $nodo['Domicilio']).'" },';

        $sqlNodosDependientes = 'SELECT
                        [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
                    FROM
                        [DetalleEstructuraMovilizacion]
                    WHERE
                        [DetalleEstructuraMovilizacion].[IdPuestoDepende] = '.$nodo['IdNodoEstructuraMov'];

        $child = Yii::$app->db->createCommand($sqlNodosDependientes)->queryAll();

        if (count($child) > 0) {
            foreach ($child as $row) {
                $estructura .= self::nodoEstructuraJSON($row['IdNodoEstructuraMov'], $puestos);
            }
        }

        return $estructura;
    }

    /**
     * Obtiene el reporte de la estructura
     *
     * @param Int $idMuni
     * @param Int|Null $idNodo
     * @param Array Int $puestos
     * @return JSON
     */
    public static function estructura($idMuni, $idNodo = null, $puestos = [])
    {
        $nodos = '';
        if ($idNodo == null) {
            $sqlPuesto = 'SELECT MIN([IdPuesto]) AS [IdPuesto] FROM [DetalleEstructuraMovilizacion]'.
                            ' WHERE [Municipio] = '.$idMuni;
            $puesto = Yii::$app->db->createCommand($sqlPuesto)->queryOne();

            if ($puesto['IdPuesto'] == null) {
                return [];
            }

            $sqlNodos = 'SELECT
                        [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
                    FROM
                        [DetalleEstructuraMovilizacion]
                    WHERE
                        [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.
                        ' AND [DetalleEstructuraMovilizacion].[IdPuesto] = '.$puesto['IdPuesto'];

            $nodos = Yii::$app->db->createCommand($sqlNodos)->queryAll();
        } else {
            $nodos = [ ['IdNodoEstructuraMov' => $idNodo] ];
        }

        $estructura = '[';

        if (count($nodos)) {
            foreach ($nodos as $nodo) {
                $estructura .= self::nodoEstructuraJSON($nodo['IdNodoEstructuraMov'], $puestos);
            }
        }

        $estructura .= ']';

        return json_decode(str_replace('},]', '}]', $estructura), true);
    }

    /**
     * Obtiene el listado de promovidos con sus respectivos promotores
     *
     * @param Int $idMuni
     * @param Int|Null $idNodo
     * @param Array Int $puestos
     * @return JSON
     */
    public static function promovidos($idMuni, $idNodo = null)
    {
        $sqlPromotores = 'SELECT
                [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov],
                [PadronGlobal].[CLAVEUNICA],
                CASE
                    WHEN [PadronGlobal].[CLAVEUNICA] IS NULL
                    THEN \'NO ASIGNADO\'
                    ELSE ([PadronGlobal].[APELLIDO_PATERNO]+\' \'+[PadronGlobal].[APELLIDO_MATERNO]
                        +\' \'+[PadronGlobal].[NOMBRE])
                END AS Responsable,
                [PadronGlobal].[TELCASA],
                [PadronGlobal].[TELMOVIL],
                [PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]
                    +\' \'+[PadronGlobal].[NOM_LOC] As Domicilio
            FROM 
                [DetalleEstructuraMovilizacion]
            LEFT JOIN
                [PadronGlobal] ON 
                    [PadronGlobal].[CLAVEUNICA] = [DetalleEstructuraMovilizacion].[IdPersonaPuesto] AND
                    [PadronGlobal].[MUNICIPIO] = '.$idMuni.'
            WHERE 
                [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.' AND
                [DetalleEstructuraMovilizacion].[IdPuesto] = 7 AND
                [DetalleEstructuraMovilizacion].[IdPersonaPuesto] != \'00000000-0000-0000-0000-000000000000\' ';

        if ($idNodo != null) {
            $sqlPromotores .= ' AND [DetalleEstructuraMovilizacion].[Dependencias] LIKE \'%|'.$idNodo.'|%\'';
        }
        
        $promotores = Yii::$app->db->createCommand($sqlPromotores)->queryAll();

        if (count($promotores) == 0) {
            return json_decode('[]');
        } else {
            $reporte = '[';

            foreach ($promotores as $promotor) {
                $reporte .= '{ "Nombre": "<b>'.str_replace('\\', 'Ñ', $promotor['Responsable']).'</b>",'
                                .'"Tel. Celular": "<b>'.$promotor['TELMOVIL'].'</b>", '
                                .'"Tel. Casa": "<b>'.$promotor['TELCASA'].'</b>", '
                                .'"Domicilio": "<b>'.str_replace('\\', 'Ñ', $promotor['Domicilio']).'</b>" },';

                $sqlPromovidos = 'SELECT
                        [Promocion].[IdpersonaPromovida]
                        ,[Promocion].[IdPuesto]
                        ,[Promocion].[IdPersonaPromueve]
                        ,[Promocion].[IdPersonaPuesto]
                        ,([PadronGlobal].[APELLIDO_PATERNO]+\' \'+[PadronGlobal].[APELLIDO_MATERNO]
                            +\' \'+[PadronGlobal].[NOMBRE]) AS Persona,
                        [PadronGlobal].[TELCASA],
                        [PadronGlobal].[TELMOVIL],
                        [PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]
                            +\' \'+[PadronGlobal].[NOM_LOC] As Domicilio
                    FROM
                        [Promocion]
                    INNER JOIN
                        [PadronGlobal] ON
                        [PadronGlobal].[CLAVEUNICA] = [Promocion].[IdpersonaPromovida] AND
                        [PadronGlobal].[MUNICIPIO] = '.$idMuni.'
                    WHERE [Promocion].[IdPuesto] = '.$promotor['IdNodoEstructuraMov'];

                $promovidos = Yii::$app->db->createCommand($sqlPromovidos)->queryAll();

                if (count($promovidos)) {
                    foreach ($promovidos as $promovido) {
                        $reporte .= '{ "Nombre": "'.str_replace('\\', 'Ñ', $promovido['Persona']).'",'
                                .'"Tel. Celular": "'.$promovido['TELMOVIL'].'", '
                                .'"Tel. Casa": "'.$promovido['TELCASA'].'", '
                                .'"Domicilio": "'.str_replace('\\', 'Ñ', $promovido['Domicilio']).'" },';
                    }
                } else {
                    $reporte .= '{ "Nombre": "Sin Promovidos",'
                                .'"Tel. Celular": "", '
                                .'"Tel. Casa": "", '
                                .'"Domicilio": "" },';
                }
            }

            $reporte .= ']';
        }

        return json_decode(str_replace('},]', '}]', $reporte), true);
    }
}
