<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

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
    public static function avanceSeccional($nameColumn, $valueColumn, $zona, $muni = null)
    {
        /*$sql = 'SELECT
            [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
            ,[CSeccion].[NumSector] AS \'Sección\'
            ,CASE
                WHEN [PadronGlobal].[CLAVEUNICA] IS NULL
                THEN \'NO ASIGNADO\'
                        ELSE REPLACE(([PadronGlobal].[NOMBRE]+\' \'+[PadronGlobal].[APELLIDO_PATERNO]+\' \'+[PadronGlobal].[APELLIDO_MATERNO]), \'\\\', \'Ñ\')
            END AS Responsable
            ,[CSeccion].[MetaAlcanzar] AS Meta
            ,(SELECT COUNT(*) FROM
                [Promocion]
            INNER JOIN [PadronGlobal] AS padronPromovidos ON
                [Promocion].[IdpersonaPromovida] = padronPromovidos.[CLAVEUNICA]
            WHERE
                padronPromovidos.[SECCION] = [CSeccion].[NumSector]
            ) AS Avance
        FROM
            [DetalleEstructuraMovilizacion]
        INNER JOIN [CSeccion] ON
            [CSeccion].[IdSector] = [DetalleEstructuraMovilizacion].[IdSector]
        LEFT JOIN [PadronGlobal] ON
            [PadronGlobal].[CLAVEUNICA] = [DetalleEstructuraMovilizacion].[IdPersonaPuesto]
        WHERE
            [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.' AND
            [DetalleEstructuraMovilizacion].[IdPuesto] = 5
        ORDER BY [CSeccion].[NumSector]';*/

        $sql = 'SELECT
            [CSeccion].[NumSector] AS \'Sección\'
            ,CASE
                WHEN [PadronGlobal].[CLAVEUNICA] IS NULL
                THEN \'NO ASIGNADO\'
                ELSE REPLACE(([PadronGlobal].[NOMBRE]+\' \'+[PadronGlobal].[APELLIDO_PATERNO]+\' \'+[PadronGlobal].[APELLIDO_MATERNO]), \'\', \'Ñ\')
            END AS Responsable
            ,(SELECT SUM([Meta])
            FROM [DetalleEstructuraMovilizacion]
            WHERE [IdPuesto] = 7 AND ([Dependencias] LIKE \'%|\'+CAST(arbolEstructura.[IdNodoEstructuraMov] AS VARCHAR)+\'|%\' OR
            [IdNodoEstructuraMov] = arbolEstructura.[IdNodoEstructuraMov])
            ) AS Meta
            ,(SELECT COUNT([IdpersonaPromovida]) FROM [Promocion] WHERE [IdPuesto] IN (
            SELECT [IdNodoEstructuraMov] FROM [DetalleEstructuraMovilizacion]
            WHERE [DetalleEstructuraMovilizacion].[Dependencias] LIKE \'%|\'+CAST(arbolEstructura.[IdNodoEstructuraMov] AS VARCHAR)+\'|%\' OR
            [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov] = arbolEstructura.[IdNodoEstructuraMov])
            ) AS Avance
            ,\'\' as \'Avance %\'
            ,[PadronGlobal].[TELMOVIL] AS \'Tel Móvil\'
            ,[PadronGlobal].[DOMICILIO]+\' \'+[PadronGlobal].[COLONIA] AS \'Domicilio\'
        FROM
            [DetalleEstructuraMovilizacion] AS arbolEstructura
        INNER JOIN [CSeccion] ON
            [CSeccion].[IdSector] = arbolEstructura.[IdSector]
        LEFT JOIN [PadronGlobal] ON
            [PadronGlobal].[CLAVEUNICA] = arbolEstructura.[IdPersonaPuesto]
        INNER JOIN [PREP_Seccion] ON
            [PREP_Seccion].[seccion] = [CSeccion].[NumSector] AND
            [PREP_Seccion].[municipio] = [CSeccion].[IdMunicipio]
        WHERE
            [PREP_Seccion].['.$nameColumn.'] = '.$valueColumn.' AND
            '.($zona!='' && $zona!=0 ? ' [PREP_Seccion].[zona] = '.$zona.' AND ' : '').'
            arbolEstructura.[IdPuesto] = 5
        ORDER BY [CSeccion].[NumSector]';

        $result = Yii::$app->db->createCommand($sql)->queryAll();
        $sumaMeta = 0;
        $sumaAvance = 0;
        $sumaDuplicados = 0;

        foreach ($result as &$fila) {
            $duplicados = 0;
            $nodo = DetalleEstructuraMovilizacion::findOne(['Descripcion' => 'CZ' . str_pad($zona, 2, '0', STR_PAD_LEFT)]);

            if ($nodo) {
                $duplicados = count(Reporte::promovidosDuplicados($muni, $nodo->IdNodoEstructuraMov));
            }

            $sumaDuplicados +=  $duplicados;
            //$fila['Duplicados'] = $duplicados;
            $fila['Avance %'] = round(($fila['Avance'] / $fila['Meta']) * 100);

            $sumaMeta += $fila['Meta'];
            $sumaAvance += $fila['Avance'];
        }

        if (count($result)) {
            array_push($result, [
                'Sección' => '',
                'Responsable' => 'TOTAL',
                'Meta' => number_format($sumaMeta),
                'Avance' => number_format($sumaAvance),
                'Avance %' => round(($sumaAvance / $sumaMeta) * 100),
                'Tel Móvil' => '',
                'Domicilio' => ''
            ]);
        }

        return $result;
    }

    /**
     * Convierte Array de datos a una tabla HTML
     *
     * @param Array $arrayDatos Arreglo asociativo con los datos
     * @param Array $omitirCentrado Columnas que no deben centrarse
     * @param Array $omitirColumnas Columnas que no se deben mostrar
     * @param Array $metadatos Metadatos que se deben agregar a las columnas
     * @param String $class clase css
     * @param String $options optiones html
     * @return String Tabla HTML
     */
    public function arrayToHtml(
        $arrayDatos,
        $omitirCentrado = [],
        $omitirColumnas = [],
        $metadatos = [],
        $class = 'table table-condensed table-bordered table-hover',
        $options = 'border="1" cellpadding="1" cellspacing="1"',
        $header = []
    ) {
        $htmlTable = '<table class="'.$class.'" '.$options.'><thead><tr>';

        if (count($arrayDatos) == 0) {
            $htmlTable .= '<th class="text-center">No se encontrados datos en la búsqueda</th></tr></thead><tbody>';
        } else {
            // Encabezado
            if (!empty($header)) {
                $encabezado = $header;
            } else {
                $primeraFila = array_shift($arrayDatos);
                $encabezado = array_keys($primeraFila);
                array_unshift($arrayDatos, $primeraFila);
            }

            $j = 1;
            foreach ($encabezado as $columna) {
                if (!in_array($j, $omitirColumnas)) {
                    $htmlTable .= '<th class="text-center">'.(mb_check_encoding($columna, 'UTF-8') ?
                                    $columna : utf8_encode($columna)).'</th>';
                }
                $j++;
            }

            $htmlTable .= '</tr></thead><tbody>';

            // Cuerpo
            foreach ($arrayDatos as $fila) {
                $htmlTable .= '<tr ';
                    if (count($metadatos)) {
                        foreach ($metadatos as $columMetadato) {
                            if (isset($fila[$columMetadato])) {
                                $htmlTable .= ' data-'.$columMetadato.'="'.$fila[$columMetadato].'" ';
                            }
                        }
                    }
                $htmlTable .= '>';
                $i = 1;
                foreach ($fila as $columna) {
                    if (!in_array($i, $omitirColumnas)) {
                        if (count($omitirCentrado)) {
                            if (in_array($i, $omitirCentrado)) {
                                $htmlTable .= '<td>'.$columna.'</td>';
                            } else {
                                $htmlTable .= '<td class="text-center">'.$columna.'</td>';
                            }
                        } else {
                            $htmlTable .= '<td class="text-center">'.$columna.'</td>';
                        }
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
    public static function nodoEstructuraJSON($idNodo, $puestos, $espacios = '', $includeID = false)
    {
        $agregarEspacios = $espacios;
        $sqlNodo = 'SELECT
                    [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
                    ,[Puestos].[Descripcion] AS DescripcionPuesto
                    ,[DetalleEstructuraMovilizacion].[Descripcion] AS DescripcionNodo
                    ,[CSeccion].[NumSector] as Seccion
                    ,CASE
                        WHEN [PadronGlobal].[CLAVEUNICA] IS NULL
                        THEN \'NO ASIGNADO\'
                        ELSE REPLACE(([PadronGlobal].[NOMBRE]+\' \'+[PadronGlobal].[APELLIDO_PATERNO]+\' \'
                            +[PadronGlobal].[APELLIDO_MATERNO]), \'\\\', \'Ñ\')
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
            /*$metaEstructura['avance'] = ($metaEstructura['meta'] != 0 ?
                round($datosEstructura[1]['MetaEstructura']/$metaEstructura['meta']*100) : 0);*/
            $metaEstructura['avance'] = $datosEstructura[1]['MetaEstructura'];
        }

        $metaPromocion = [
            'meta' => DetalleEstructuraMovilizacion::getMetaByPromotor($idNodo),
            'avance' => DetalleEstructuraMovilizacion::getCountPromovidos($idNodo)
        ];

        /*$sqlDatosPromocion = 'SELECT
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

        $datosPromocion = Yii::$app->db->createCommand($sqlDatosPromocion)->queryAll();

        if (count($datosPromocion)) {
            $metaPromocion['meta'] = $datosPromocion[1]['MetaPromocion'];
            //$metaPromocion['avance'] = ($datosPromocion[1]['MetaPromocion'] != 0 ?
                //round($datosPromocion[0]['MetaPromocion']/$datosPromocion[1]['MetaPromocion']*100) : 0);
            $metaPromocion['avance'] = $datosPromocion[0]['MetaPromocion'];
        }*/

        $agregarEspacios .= $espacios;

        $estructura .= '{ '.($includeID ? '"id": "'.$nodo['IdNodoEstructuraMov'].'", ' : '').'
                        "Puesto": "'.$agregarEspacios.$nodo['DescripcionPuesto'].'", '
                        .'"Descripción": "'.$nodo['DescripcionNodo'].'",'
                        .'"Nombre": "'.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $nodo['Responsable'])).'",'
                        .'"Sección": "'.$nodo['Seccion'].'",'
                        .'"Meta Estruc": "'.$metaEstructura['meta'].'",'
                        .'"Vacantes": "'.($datosEstructura[0]['MetaEstructura']-$datosEstructura[1]['MetaEstructura']).'",'
                        .'"Avance Estruc": "'.$metaEstructura['avance'].'",'
                        .'"Meta Promo": "'.$metaPromocion['meta'].'",'
                        .'"Avance Promo": "'.$metaPromocion['avance'].'",'
                        .'"Tel. Celular": "'.$nodo['TELMOVIL'].'", '
                        .'"Tel. Casa": "'.$nodo['TELCASA'].'" },';
                        //.'"Domicilio": "'.str_replace('\\', 'Ñ', $nodo['Domicilio']).'" },';

        $sqlNodosDependientes = 'SELECT
                        [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
                    FROM
                        [DetalleEstructuraMovilizacion]
                    LEFT JOIN
                        [PadronGlobal] ON
                        [PadronGlobal].[CLAVEUNICA] = [DetalleEstructuraMovilizacion].[IdPersonaPuesto]
                    WHERE
                        [DetalleEstructuraMovilizacion].[IdPuestoDepende] = '.$nodo['IdNodoEstructuraMov'].'
                     ORDER BY [PadronGlobal].[NOMBRE], [PadronGlobal].[APELLIDO_PATERNO], [PadronGlobal].[APELLIDO_MATERNO]';

        $child = Yii::$app->db->createCommand($sqlNodosDependientes)->queryAll();

        if (count($child) > 0) {
            foreach ($child as $row) {
                $estructura .= self::nodoEstructuraJSON($row['IdNodoEstructuraMov'], $puestos, $agregarEspacios, $includeID);
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
    public static function estructura($idMuni, $idNodo = null, $puestos = [], $includeID = false)
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
                $estructura .= self::nodoEstructuraJSON($nodo['IdNodoEstructuraMov'], $puestos, '&nbsp', $includeID);
            }
        }

        $estructura .= ']';

        return json_decode(str_replace('},]', '}]', $estructura), true);
    }

    public function promovidos($idMuni, $idNodo = null, $tipoPromovido, $incluir_domicilio)
    {
        $reporte = '';

        if ($tipoPromovido == 1) {
            $reporte = self::promovidosEfectivos($idMuni, $idNodo, $incluir_domicilio);
        } else {
            $reporte = self::promovidosIntentos($idMuni, $idNodo, $incluir_domicilio);
        }

        return $reporte;
    }

    /**
     * Obtiene el listado de promovidos efectivos con sus respectivos promotores
     *
     * @param Int $idMuni
     * @param Int|Null $idNodo
     * @return JSON
     */
    public static function promovidosEfectivos_back($idMuni, $idNodo = null, $incluir_domicilio)
    {
        $sqlPromotores = 'SELECT
                DISTINCT([Promocion].[IdPersonaPromueve])
                ,[DetalleEstructuraMovilizacion].[Descripcion] AS descripcionPuesto
                ,REPLACE(([PadronGlobal].[NOMBRE] + \' \' +
                    [PadronGlobal].[APELLIDO_PATERNO] + \' \' +
                    [PadronGlobal].[APELLIDO_MATERNO]
                ), \'\\\', \'Ñ\') AS nombrePersonaPromueve
                ,[PadronGlobal].[TELCASA]
                ,[PadronGlobal].[TELMOVIL]
                ,[PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]
                    +\' \'+[PadronGlobal].[NOM_LOC] As Domicilio,
                [CSeccion].[NumSector]
            FROM
                [Promocion]
            INNER JOIN [PadronGlobal] ON
                [PadronGlobal].[CLAVEUNICA] = [Promocion].[IdPersonaPuesto] AND
                [PadronGlobal].[MUNICIPIO] = '.$idMuni.'
            INNER JOIN [DetalleEstructuraMovilizacion] ON
                [DetalleEstructuraMovilizacion].[IdPersonaPuesto] = [PadronGlobal].[CLAVEUNICA] AND
                [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.' ';

        if ($idNodo != null) {
            $sqlPromotores .= ' AND ([DetalleEstructuraMovilizacion].[Dependencias] LIKE \'%|'.$idNodo.'|%\' '.
                                ' OR [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov] = '.$idNodo.')';
        }

        $sqlPromotores .= ' INNER JOIN [CSeccion] ON
                [CSeccion].[IdMunicipio] = [DetalleEstructuraMovilizacion].[Municipio] AND
                [CSeccion].[IdSector] = [DetalleEstructuraMovilizacion].[IdSector]
            ORDER BY descripcionPuesto, nombrePersonaPromueve';

        $promotores = Yii::$app->db->createCommand($sqlPromotores)->queryAll();

        if (count($promotores) == 0) {
            return json_decode('[]');
        } else {
            $reporte = '[{}';

            foreach ($promotores as $promotor) {
                $reporte .= '{ "Nombre": "<b>'.$promotor['descripcionPuesto'].' '.str_replace('\\', 'Ñ', $promotor['nombrePersonaPromueve']).' - Sección '.$promotor['NumSector'].'</b>",'
                                .'"Tel. Celular": "<b>'.$promotor['TELMOVIL'].'</b>", '
                                .'"Tel. Casa": "<b>'.$promotor['TELCASA'].'</b>", '
                                .($incluir_domicilio ? '"Domicilio": "<b>'.str_replace('\\', 'Ñ', $promotor['Domicilio']).'</b>" ,' : '')
                                .'"Promovido Por": "" },';

                $sqlPromovidos = 'SELECT
                        REPLACE(([PadronGlobal].[NOMBRE]+\' \'+[PadronGlobal].[APELLIDO_PATERNO]
                            +\' \'+[PadronGlobal].[APELLIDO_MATERNO]), \'\\\', \'Ñ\') AS Persona,
                        [PadronGlobal].[TELCASA],
                        [PadronGlobal].[TELMOVIL],
                        [PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]
                            +\' \'+[PadronGlobal].[NOM_LOC] As Domicilio
                    FROM
                        [Promocion]
                    INNER JOIN
                        [PadronGlobal] ON
                        [PadronGlobal].[CLAVEUNICA] = [Promocion].[IdPersonaPromovida] AND
                        [PadronGlobal].[MUNICIPIO] = '.$idMuni.'
                    WHERE [Promocion].[IdPErsonaPromueve] = \''.$promotor['IdPersonaPromueve'].'\'
                        ORDER BY Persona';

                $promovidos = Yii::$app->db->createCommand($sqlPromovidos)->queryAll();

                if (count($promovidos)) {
                    $countPromovidos = 1;
                    foreach ($promovidos as $promovido) {
                        $reporte .= '{ "Nombre": "'.$countPromovidos.'. '.str_replace('\\', 'Ñ', $promovido['Persona']).'",'
                                .'"Tel. Celular": "'.$promovido['TELMOVIL'].'", '
                                .'"Tel. Casa": "'.$promovido['TELCASA'].'", '
                                .($incluir_domicilio ? '"Domicilio": "'.str_replace('\\', 'Ñ', $promovido['Domicilio']).'",' : '')
                                .'"Promovido Por": " --- " },';
                        $countPromovidos++;
                    }
                } else {
                    $reporte .= '{ "Nombre": "Sin Promovidos",'
                                .'"Tel. Celular": "", '
                                .'"Tel. Casa": "", '
                                .($incluir_domicilio ? '"Domicilio": "", ' : '')
                                .'"Promovido Por": "" },';
                }

                $sqlPromovidosOtros = 'SELECT
                        REPLACE(([PadronGlobal].[NOMBRE]+\' \'+[PadronGlobal].[APELLIDO_PATERNO]
                            +\' \'+[PadronGlobal].[APELLIDO_MATERNO]), \'\\\', \'Ñ\') AS Persona,
                        [PadronGlobal].[TELCASA],
                        [PadronGlobal].[TELMOVIL],
                        [PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]
                            +\' \'+[PadronGlobal].[NOM_LOC] As Domicilio,
                        REPLACE(([padronPromueve].[NOMBRE] + \' \' +
                            [padronPromueve].[APELLIDO_PATERNO] + \' \' +
                            [padronPromueve].[APELLIDO_MATERNO]
                        ), \'\\\', \'Ñ\') AS nombrePersonaPromueve,
                        [DetalleEstructuraMovilizacion].[Descripcion] AS descripcionPuesto
                    FROM
                        [Promocion]
                    INNER JOIN
                        [PadronGlobal] AS [padronPromueve] ON
                        [padronPromueve].[CLAVEUNICA] = [Promocion].[IdPErsonaPromueve] AND
                        [padronPromueve].[MUNICIPIO] = '.$idMuni.'
                    INNER JOIN
                        [PadronGlobal] ON
                        [PadronGlobal].[CLAVEUNICA] = [Promocion].[IdPersonaPromovida]
                    INNER JOIN
                        [DetalleEstructuraMovilizacion] ON
                        [DetalleEstructuraMovilizacion].[IdPersonaPuesto] = [Promocion].[IdPErsonaPromueve]
                    WHERE
                        [Promocion].[IdPersonaPuesto] = \''.$promotor['IdPersonaPromueve'].'\' AND
                        [Promocion].[IdPErsonaPromueve] != \''.$promotor['IdPersonaPromueve'].'\'
                        ORDER BY Persona';

                $promovidosOtros = Yii::$app->db->createCommand($sqlPromovidosOtros)->queryAll();

                if (count($promovidosOtros)) {
                    foreach ($promovidosOtros as $promovido) {
                        $reporte .= '{ "Nombre": "'.$countPromovidos.'. '.str_replace('\\', 'Ñ', $promovido['Persona']).'",'
                                .'"Tel. Celular": "'.$promovido['TELMOVIL'].'", '
                                .'"Tel. Casa": "'.$promovido['TELCASA'].'", '
                                .($incluir_domicilio ? '"Domicilio": "'.str_replace('\\', 'Ñ', $promovido['Domicilio']).'",' : '')
                                .'"Promovido Por": "'.$promovido['descripcionPuesto'].' '.str_replace('\\', 'Ñ', $promovido['nombrePersonaPromueve']).'" },';
                        $countPromovidos++;
                    }
                }

                $reporte .= '{ "Nombre": " &nbsp; ",'
                            .'"Tel. Celular": " &nbsp; ", '
                            .'"Tel. Casa": " &nbsp; ", '
                            .($incluir_domicilio ? '"Domicilio": " &nbsp; ", ' : '')
                            .'"Promovido Por": " &nbsp; " },';
            }

            $reporte .= ']';
        }

        return json_decode(str_replace('},]', '}]', $reporte), true);
    }

    /**
     * Obtiene el listado de promovidos efectivos con sus respectivos promotores
     *
     * @param Int $idMuni
     * @param Int|Null $idNodo
     * @return JSON
     */
    public static function promovidosEfectivos($idMuni, $idNodo = null, $incluir_domicilio)
    {
        $sqlPromocion = 'SELECT
            Promocion.IdPersonaPromueve
            ,Promocion.IdPersonaPuesto
            ,Promocion.IdpersonaPromovida
            ,DetalleEstructuraMovilizacion.Descripcion
            ,(tblPersonaPuesto.NOMBRE+\' \'+tblPersonaPuesto.APELLIDO_PATERNO+\' \'+
                tblPersonaPuesto.APELLIDO_MATERNO) AS personaPuesto
            ,CSeccion.NumSector
            ,tblPersonaPuesto.TELCASA
            ,tblPersonaPuesto.TELMOVIL
            ,tblPersonaPromueve.TELCASA AS TELCASA_PersonaPromueve
            ,tblPersonaPromueve.TELMOVIL AS TELMOVIL_PersonaPromueve
            ,tblEstructuraPromotor.Descripcion as puestoPromotor
            ,(tblPersonaPromueve.NOMBRE+\' \'+tblPersonaPromueve.APELLIDO_PATERNO+\' \'+
                tblPersonaPromueve.APELLIDO_MATERNO) AS personaPromueve
            ,(tblPersonaPromovida.NOMBRE+\' \'+tblPersonaPromovida.APELLIDO_PATERNO+\' \'+
                tblPersonaPromovida.APELLIDO_MATERNO) AS personaPromovida
            ,(tblPersonaPromovida.DOMICILIO+\', #\'+tblPersonaPromovida.NUM_INTERIOR) AS Domicilio
            ,(tblPersonaPromovida.COLONIA) AS Colonia
            ,PREP_Seccion.zona
        FROM
            Promocion
        INNER JOIN DetalleEstructuraMovilizacion ON
            DetalleEstructuraMovilizacion.IdNodoEstructuraMov = Promocion.IdPuesto AND
            DetalleEstructuraMovilizacion.Municipio = '.$idMuni.'
            '.($idNodo != null ? 'AND (DetalleEstructuraMovilizacion.Dependencias LIKE \'%|'.$idNodo.'|%\'
            OR DetalleEstructuraMovilizacion.IdNodoEstructuraMov = '.$idNodo.')' : '').'
        INNER JOIN PadronGlobal AS tblPersonaPromovida ON
            Promocion.IdpersonaPromovida = tblPersonaPromovida.CLAVEUNICA
        INNER JOIN PadronGlobal AS tblPersonaPromueve ON
            Promocion.IdPersonaPromueve = tblPersonaPromueve.CLAVEUNICA
        INNER JOIN PadronGlobal AS tblPersonaPuesto ON
            Promocion.IdPersonaPuesto = tblPersonaPuesto.CLAVEUNICA
        INNER JOIN CSeccion ON
            DetalleEstructuraMovilizacion.IdSector = CSeccion.IdSector AND
            DetalleEstructuraMovilizacion.Municipio = CSeccion.IdMunicipio
        INNER JOIN DetalleEstructuraMovilizacion AS tblEstructuraPromotor ON
            tblEstructuraPromotor.IdPersonaPuesto = Promocion.IdPersonaPromueve
        LEFT JOIN PREP_Seccion ON
            PREP_Seccion.municipio = tblPersonaPromueve.MUNICIPIO AND
            PREP_Seccion.seccion = CSeccion.IdSector
        ORDER BY
            CSeccion.NumSector, personaPuesto, personaPromovida';

        $sqlOrganizacion = 'SELECT
                Organizaciones.IdOrganizacion,
                Organizaciones.Nombre
            FROM
                IntegrantesOrganizaciones
            INNER JOIN Organizaciones ON
                IntegrantesOrganizaciones.IdOrganizacion = Organizaciones.IdOrganizacion
            WHERE
                IdPersonaIntegrante = ';
        /* INNER JOIN DetalleEstructuraMovilizacion ON
            (DetalleEstructuraMovilizacion.IdPersonaPuesto = Organizaciones.IdPersonaEnlace OR
            DetalleEstructuraMovilizacion.IdPersonaPuesto = Organizaciones.IdPersonaRepresentante) AND
            DetalleEstructuraMovilizacion.IdPersonaPuesto != \'00000000-0000-0000-0000-000000000000\'*/

        $promocion = Yii::$app->db->createCommand($sqlPromocion)->queryAll();

        if (count($promocion) == 0) {
            return json_decode('[]');
        } else {
            $reporte = '[{"total": "'.count($promocion).'"}, ';
            $anteriorPromotor = '';
            $countPromovidos = 0;

            foreach ($promocion as $promotor) {

                if ($anteriorPromotor != $promotor['personaPuesto']) {
                    if ($countPromovidos != 0) {
                        $reporte .= '{ "Nombre": " &nbsp; ",'
                            .'"Tel. Celular": " &nbsp; ", '
                            .'"Tel. Casa": " &nbsp; ", '
                            .($incluir_domicilio ? '"Domicilio": " &nbsp; ", ' : '')
                            .'"Colonia": " &nbsp; ",'
                            .'"Promovido Por": " &nbsp; ",'
                            .'"Organización": " &nbsp; " },';
                    }
                    $infoPromotor = PadronGlobal::find()->where('CLAVEUNICA = \''.$promotor['IdPersonaPuesto'].'\'')->one();

                    $reporte .= '{ "Nombre": "<b>'.$promotor['Descripcion'].' '.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['personaPuesto'])).' - Sección '.$promotor['NumSector'].'</b>",'
                        .'"Tel. Celular": "<b>'.$infoPromotor->TELMOVIL.'</b>", '
                        .'"Tel. Casa": "<b>'.$infoPromotor->TELCASA.'</b>", '
                        .($incluir_domicilio ? '"Domicilio": "<b>'.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $infoPromotor->DOMICILIO)).'</b>" ,' : '')
                        .'"Colonia": "'.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $infoPromotor->COLONIA)).'",'
                        .'"Promovido Por": " &nbsp; ",'
                        .'"Organización": "" },';

                    $anteriorPromotor = $promotor['personaPuesto'];
                    $countPromovidos = 0;
                }

                $countPromovidos++;
                $listOrganizaciones = Yii::$app->db->createCommand($sqlOrganizacion.'\''.$promotor['IdpersonaPromovida'].'\'')->queryAll();
                $organizaciones = [];
                $organizaciones = ' &nbsp; ';

                if (count($listOrganizaciones)) {
                    $organizaciones = implode(', ', ArrayHelper::map($listOrganizaciones, 'IdOrganizacion', 'Nombre'));
                }

                $nodoEstructura = DetalleEstructuraMovilizacion::findOne(['IdPersonaPuesto' => $promotor['IdPersonaPromueve']]);
                $seccional = '';

                if ($nodoEstructura) {
                    $querySeccional = 'SELECT NumSector FROM CSeccion WHERE IdMunicipio = '.$nodoEstructura->Municipio.' AND IdSector = '.$nodoEstructura->IdSector;
                    $seccional = Yii::$app->db->createCommand($querySeccional)->queryScalar();
                }

                $reporte .= '{ "Nombre": "'.$countPromovidos.'. '.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['personaPromovida'])).'",'
                    .'"Tel. Celular": "'.$promotor['TELMOVIL_PersonaPromueve'].'", '
                    .'"Tel. Casa": "'.$promotor['TELCASA_PersonaPromueve'].'", '
                    .($incluir_domicilio ? '"Domicilio": "'.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['Domicilio'])).'",' : '')
                    .'"Colonia": "'.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['Colonia'])).'",'
                    //.'"Promovido Por": "'.($promotor['personaPuesto']!=$promotor['personaPromueve'] ? $promotor['puestoPromotor'].' '.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['personaPromueve'])) : ' &nbsp; ').'",'
                    .'"Promovido Por": "'.($promotor['puestoPromotor'].' '.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['personaPromueve']))).' - Z'.($nodoEstructura ? $nodoEstructura->ZonaMunicipal : '').' '.($seccional ? '- S'.$seccional : '').'",'
                    .'"Organización": "'.$organizaciones.'" },';
            }

            $reporte .= ']';
        }

        //echo (str_replace('},]', '}]', $reporte)).'**************************************';

        return json_decode(str_replace('},]', '}]', $reporte), true);
    }

    /**
     * Obtiene el listado de promotores con sus respectivos promovidos
     *
     * @param Int $idMuni
     * @param Int|Null $idNodo
     * @return JSON
     */
    public static function promotoresPromovidos($idMuni, $idNodo = null, $incluir_domicilio, $excluir_activistas = false)
    {
        $sqlPromocion = 'SELECT
            [Promocion].[IdpersonaPromovida]
            ,[Promocion].[IdPuesto]
            ,[Promocion].[IdPersonaPromueve]
            ,[Promocion].[IdPersonaPuesto]
            ,[Promocion].[Participacion]

            ,(padronPersonaPromovida.NOMBRE+\' \'+padronPersonaPromovida.APELLIDO_PATERNO+\' \'+padronPersonaPromovida.APELLIDO_MATERNO) AS nombrePersonaPromovida
            ,padronPersonaPromovida.COLONIA AS COLONIAPersonaPromovida
            ,padronPersonaPromovida.DOMICILIO AS DOMICILIOPersonaPromovida
            ,estructuraPersonaPromueve.Descripcion AS puestoPersonaPromueve

            ,(padronPersonaPromueve.NOMBRE+\' \'+padronPersonaPromueve.APELLIDO_PATERNO+\' \'+padronPersonaPromueve.APELLIDO_MATERNO) AS nombrePersonaPromueve
            ,padronPersonaPromueve.COLONIA AS COLONIAPersonaPromueve
            ,padronPersonaPromueve.DOMICILIO AS DOMICILIOPersonaPromueve
            ,padronPersonaPromueve.TELMOVIL AS TELMOVILPersonaPromueve
            ,padronPersonaPromueve.TELCASA AS TELCASAPersonaPromueve
            ,estructuraPersonaPromueve.IdSector AS seccionPersonaPromueve
            ,estructuraPersonaPromueve.ZonaMunicipal AS ZonaPersonaPromueve

            ,(padronPersonaPuesto.NOMBRE+\' \'+padronPersonaPuesto.APELLIDO_PATERNO+\' \'+padronPersonaPuesto.APELLIDO_MATERNO) AS nombrePersonaPuesto
            ,padronPersonaPuesto.TELMOVIL AS TELMOVILPersonaPuesto
            ,padronPersonaPuesto.TELCASA AS TELCASAPersonaPuesto
            ,estructuraPersonaPuesto.Descripcion AS puestoPersonaPuesto
            ,estructuraPersonaPuesto.IdSector AS seccionPersonaPuesto
            ,estructuraPersonaPuesto.ZonaMunicipal AS ZonaPersonaPuesto
        FROM
            [Promocion]
        INNER JOIN [PadronGlobal] AS padronPersonaPromovida ON
            padronPersonaPromovida.CLAVEUNICA = [Promocion].[IdpersonaPromovida]
        INNER JOIN [PadronGlobal] AS padronPersonaPromueve ON
            padronPersonaPromueve.CLAVEUNICA = [Promocion].[IdPersonaPromueve]
        INNER JOIN [PadronGlobal] AS padronPersonaPuesto ON
            padronPersonaPuesto.CLAVEUNICA = [Promocion].[IdPersonaPuesto]
        INNER JOIN [DetalleEstructuraMovilizacion] AS estructuraPersonaPromueve ON
            estructuraPersonaPromueve.IdPersonaPuesto = [Promocion].[IdPersonaPromueve]
        INNER JOIN [DetalleEstructuraMovilizacion] AS estructuraPersonaPuesto ON
            estructuraPersonaPuesto.IdPersonaPuesto = [Promocion].[IdPersonaPuesto]
        WHERE
            estructuraPersonaPromueve.Municipio = '.$idMuni.'
            '.($idNodo != null ? ' AND (estructuraPersonaPromueve.Dependencias LIKE \'%|'.$idNodo.'|%\' OR
            estructuraPersonaPromueve.IdNodoEstructuraMov = '.$idNodo.')' : ' ').
            ($excluir_activistas ? ' AND estructuraPersonaPromueve.IdPuesto != 7 ' : ' ').
        ' ORDER BY
            seccionPersonaPromueve, nombrePersonaPromueve, nombrePersonaPromovida';

        $sqlOrganizacion = 'SELECT
                Organizaciones.IdOrganizacion,
                Organizaciones.Nombre
            FROM
                IntegrantesOrganizaciones
            INNER JOIN Organizaciones ON
                IntegrantesOrganizaciones.IdOrganizacion = Organizaciones.IdOrganizacion
            WHERE
                IdPersonaIntegrante = ';
        /* INNER JOIN DetalleEstructuraMovilizacion ON
            (DetalleEstructuraMovilizacion.IdPersonaPuesto = Organizaciones.IdPersonaEnlace OR
            DetalleEstructuraMovilizacion.IdPersonaPuesto = Organizaciones.IdPersonaRepresentante) AND
            DetalleEstructuraMovilizacion.IdPersonaPuesto != \'00000000-0000-0000-0000-000000000000\'*/

        $promocion = Yii::$app->db->createCommand($sqlPromocion)->queryAll();

        if (count($promocion) == 0) {
            return json_decode('[]');
        } else {
            $reporte = '[{"total": "'.count($promocion).'"}, ';
            $anteriorPromotor = '';
            $countPromovidos = 0;

            foreach ($promocion as $promotor) {

                if ($anteriorPromotor != $promotor['nombrePersonaPromueve']) {
                    if ($countPromovidos != 0) {
                        $reporte .= '{ "Nombre": " &nbsp; ",'
                            .'"Tel. Celular": " &nbsp; ", '
                            .'"Tel. Casa": " &nbsp; ", '
                            .($incluir_domicilio ? '"Domicilio": " &nbsp; ", ' : '')
                            .'"Colonia": " &nbsp; ",'
                            .'"Asignado a": " &nbsp; ",'
                            .'"Organización": " &nbsp; " },';
                    }
                    //$infoPromotor = PadronGlobal::find()->where('CLAVEUNICA = \''.$promotor['IdPersonaPromueve'].'\'')->one();

                    $reporte .= '{ "Nombre": "<b>'.$promotor['puestoPersonaPromueve'].' '.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['nombrePersonaPromueve'])).($promotor['seccionPersonaPromueve']!='' && $promotor['seccionPersonaPromueve']!='-1' ? ' - Sección '.$promotor['seccionPersonaPromueve'] : '').'</b>",'
                        .'"Tel. Celular": "<b>'.$promotor['TELMOVILPersonaPromueve'].'</b>", '
                        .'"Tel. Casa": "<b>'.$promotor['TELCASAPersonaPromueve'].'</b>", '
                        .($incluir_domicilio ? '"Domicilio": "<b>'.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['DOMICILIOPersonaPromueve'])).'</b>" ,' : '')
                        .'"Colonia": "'.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['COLONIAPersonaPromueve'])).'",'
                        .'"Asignado a": " &nbsp; ",'
                        .'"Organización": "" },';

                    $anteriorPromotor = $promotor['nombrePersonaPromueve'];
                    $countPromovidos = 0;
                }

                $countPromovidos++;
                $listOrganizaciones = Yii::$app->db->createCommand($sqlOrganizacion.'\''.$promotor['IdpersonaPromovida'].'\'')->queryAll();
                $organizaciones = [];
                $organizaciones = ' &nbsp; ';

                if (count($listOrganizaciones)) {
                    $organizaciones = implode(', ', ArrayHelper::map($listOrganizaciones, 'IdOrganizacion', 'Nombre'));
                }

                /*$nodoEstructura = DetalleEstructuraMovilizacion::findOne(['IdPersonaPuesto' => $promotor['IdPersonaPuesto']]);
                $seccional = '';

                if ($nodoEstructura) {
                    $querySeccional = 'SELECT NumSector FROM CSeccion WHERE IdMunicipio = '.$nodoEstructura->Municipio.' AND IdSector = '.$nodoEstructura->IdSector;
                    $seccional = Yii::$app->db->createCommand($querySeccional)->queryScalar();
                }*/

                $reporte .= '{ "Nombre": "'.$countPromovidos.'. '.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['nombrePersonaPromovida'])).'",'
                    .'"Tel. Celular": "'.$promotor['TELMOVILPersonaPuesto'].'", '
                    .'"Tel. Casa": "'.$promotor['TELCASAPersonaPuesto'].'", '
                    .($incluir_domicilio ? '"Domicilio": "'.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['DOMICILIOPersonaPromovida'])).'",' : '')
                    .'"Colonia": "'.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['COLONIAPersonaPromovida'])).'",'
                    //.'"Asignado a": "'.($promotor['personaPuesto']!=$promotor['personaPromueve'] ? $promotor['puestoPromotor'].' '.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['personaPromueve'])) : ' &nbsp; ').'",'
                    .'"Asignado a": "'.($promotor['puestoPersonaPuesto'].' '.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['nombrePersonaPuesto']))).' - Z'.$promotor['ZonaPersonaPuesto'].' '.($promotor['seccionPersonaPuesto'] != '' && $promotor['seccionPersonaPuesto'] != '-1' ? '- S'.$promotor['seccionPersonaPuesto'] : '').'",'
                    .'"Organización": "'.$organizaciones.'" },';
            }

            $reporte .= ']';
        }

        //echo (str_replace('},]', '}]', $reporte)).'**************************************';

        return json_decode(str_replace('},]', '}]', $reporte), true);
    }

    /**
     * Obtiene el listad de promovidos duplicados
     *
     * @param  int $idMuni
     * @param  int $idNodo
     * @return array
     */
    public static function promovidosDuplicados($idMuni, $idNodo = null)
    {
        $sqlDuplicados = 'SELECT
                *, COUNT(IdpersonaPromovida) AS duplicados
            FROM
                (SELECT
                    Promocion.IdpersonaPromovida
                    ,(tblPersonaPromovida.NOMBRE+\' \'+tblPersonaPromovida.APELLIDO_PATERNO+\'  \'+
                        tblPersonaPromovida.APELLIDO_MATERNO) AS personaPromovida
                FROM
                    Promocion
                INNER JOIN DetalleEstructuraMovilizacion ON
                    DetalleEstructuraMovilizacion.IdNodoEstructuraMov = Promocion.IdPuesto AND
                    DetalleEstructuraMovilizacion.Municipio = '.$idMuni.'
                    '.($idNodo != null ? 'AND (DetalleEstructuraMovilizacion.Dependencias LIKE \'%|'.$idNodo.'|%\'
                    OR DetalleEstructuraMovilizacion.IdNodoEstructuraMov = '.$idNodo.')' : '').'
                INNER JOIN PadronGlobal AS tblPersonaPromovida ON
                    Promocion.IdpersonaPromovida = tblPersonaPromovida.CLAVEUNICA
                INNER JOIN CSeccion ON
                    DetalleEstructuraMovilizacion.IdSector = CSeccion.IdSector AND
                    DetalleEstructuraMovilizacion.Municipio = CSeccion.IdMunicipio
                INNER JOIN DetalleEstructuraMovilizacion AS tblEstructuraPromotor ON
                    tblEstructuraPromotor.IdPersonaPuesto = Promocion.IdPersonaPromueve
                ) AS promovidos
            GROUP BY
                IdpersonaPromovida, personaPromovida
            HAVING COUNT(IdpersonaPromovida) > 1';

        return Yii::$app->db->createCommand($sqlDuplicados)->queryAll();
    }

    /**
     * Obtiene el listado de intentos de promoción con sus respectivos promotores
     *
     * @param Int $idMuni
     * @param Int|Null $idNodo
     * @return JSON
     */
    public static function promovidosIntentos($idMuni, $idNodo = null, $incluir_domicilio)
    {
        $sqlPromocion = 'SELECT
            DetallePromocion.IdPErsonaPromueve
            ,DetallePromocion.IdPersonaPromovida
            ,DetalleEstructuraMovilizacion.Descripcion
            ,(tblPersonaPuesto.NOMBRE+\' \'+tblPersonaPuesto.APELLIDO_PATERNO+\' \'+
                tblPersonaPuesto.APELLIDO_MATERNO) AS personaPuesto
            ,CSeccion.NumSector
            ,tblPersonaPuesto.TELCASA
            ,tblPersonaPuesto.TELMOVIL
            ,tblEstructuraPromotor.Descripcion as puestoPromotor
            ,(tblPersonaPromueve.NOMBRE+\' \'+tblPersonaPromueve.APELLIDO_PATERNO+\' \'+
                tblPersonaPromueve.APELLIDO_MATERNO) AS personaPromueve
            ,(tblPersonaPromovida.NOMBRE+\' \'+tblPersonaPromovida.APELLIDO_PATERNO+\' \'+
                tblPersonaPromovida.APELLIDO_MATERNO) AS personaPromovida
            ,(tblPersonaPromovida.DOMICILIO+\', #\'+tblPersonaPromovida.NUM_INTERIOR) AS Domicilio
            ,(tblPersonaPromovida.COLONIA) AS Colonia
        FROM
            DetallePromocion
        INNER JOIN DetalleEstructuraMovilizacion ON
            DetalleEstructuraMovilizacion.IdPersonaPuesto = DetallePromocion.IdPErsonaPromueve AND
            DetalleEstructuraMovilizacion.Municipio = '.$idMuni.'
            '.($idNodo != null ? 'AND (DetalleEstructuraMovilizacion.Dependencias LIKE \'%|'.$idNodo.'|%\'
            OR DetalleEstructuraMovilizacion.IdNodoEstructuraMov = '.$idNodo.')' : '').'
        INNER JOIN PadronGlobal AS tblPersonaPromovida ON
            DetallePromocion.IdPersonaPromovida = tblPersonaPromovida.CLAVEUNICA
        INNER JOIN PadronGlobal AS tblPersonaPromueve ON
            DetallePromocion.IdPErsonaPromueve = tblPersonaPromueve.CLAVEUNICA
        INNER JOIN PadronGlobal AS tblPersonaPuesto ON
            DetallePromocion.IdPErsonaPromueve = tblPersonaPuesto.CLAVEUNICA
        INNER JOIN CSeccion ON
            DetalleEstructuraMovilizacion.IdSector = CSeccion.IdSector AND
            DetalleEstructuraMovilizacion.Municipio = CSeccion.IdMunicipio
        INNER JOIN DetalleEstructuraMovilizacion AS tblEstructuraPromotor ON
            tblEstructuraPromotor.IdPersonaPuesto = DetallePromocion.IdPErsonaPromueve
        ORDER BY
            CSeccion.NumSector, personaPuesto, personaPromovida';

        $sqlOrganizacion = 'SELECT
                Organizaciones.IdOrganizacion,
                Organizaciones.Nombre
            FROM
                IntegrantesOrganizaciones
            INNER JOIN Organizaciones ON
                IntegrantesOrganizaciones.IdOrganizacion = Organizaciones.IdOrganizacion
            WHERE
                IdPersonaIntegrante = ';

        $promocion = Yii::$app->db->createCommand($sqlPromocion)->queryAll();

        if (count($promocion) == 0) {
            return json_decode('[]');
        } else {
            $reporte = '[';
            $anteriorPromotor = '';
            $countPromovidos = 0;

            foreach ($promocion as $promotor) {

                if ($anteriorPromotor != $promotor['personaPuesto']) {
                    if ($countPromovidos != 0) {
                        $reporte .= '{ "Nombre": " &nbsp; ",'
                            .'"Tel. Celular": " &nbsp; ", '
                            .'"Tel. Casa": " &nbsp; ", '
                            .($incluir_domicilio ? '"Domicilio": " &nbsp; ", ' : '')
                            .'"Colonia": " &nbsp; ",'
                            .'"Promovido Por": " &nbsp; ",'
                            .'"Organización": " &nbsp; " },';
                    }

                    $infoPromotor = PadronGlobal::find()->where('CLAVEUNICA = \''.$promotor['IdPErsonaPromueve'].'\'')->one();

                    $reporte .= '{ "Nombre": "<b>'.$promotor['Descripcion'].' '.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['personaPuesto'])).' - Sección '.$promotor['NumSector'].'</b>",'
                        .'"Tel. Celular": "<b>'.$infoPromotor->TELMOVIL.'</b>", '
                        .'"Tel. Casa": "<b>'.$infoPromotor->TELCASA.'</b>", '
                        .($incluir_domicilio ? '"Domicilio": "<b>'.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $infoPromotor->DOMICILIO)).'</b>" ,' : '')
                        .'"Colonia": "'.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $infoPromotor->COLONIA)).'",'
                        .'"Promovido Por": " &nbsp; ",'
                        .'"Organización": "" },';

                    $anteriorPromotor = $promotor['personaPuesto'];
                    $countPromovidos = 0;
                }

                $countPromovidos++;
                $listOrganizaciones = Yii::$app->db->createCommand($sqlOrganizacion.'\''.$promotor['IdPErsonaPromueve'].'\'')->queryAll();
                $organizaciones = ' &nbsp; ';

                if (count($listOrganizaciones)) {
                    $organizaciones = implode(', ', ArrayHelper::map($listOrganizaciones, 'IdOrganizacion', 'Nombre'));
                }

                $reporte .= '{ "Nombre": "'.$countPromovidos.'. '.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['personaPromovida'])).'",'
                    .'"Tel. Celular": "'.$promotor['TELMOVIL'].'", '
                    .'"Tel. Casa": "'.$promotor['TELCASA'].'", '
                    .($incluir_domicilio ? '"Domicilio": "'.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['Domicilio'])).'",' : '')
                    .'"Colonia": "'.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['Colonia'])).'",'
                    .'"Promovido Por": "'.($promotor['personaPuesto']!=$promotor['personaPromueve'] ? $promotor['puestoPromotor'].' '.preg_replace("'\s+'", ' ',str_replace('\\', 'Ñ', $promotor['personaPromueve'])) : ' &nbsp; ').'",'
                    .'"Organización": "'.$organizaciones.'" },';
            }

            $reporte .= ']';
        }

        return json_decode(str_replace('},]', '}]', $reporte), true);
    }
    /*public static function promovidosIntentos($idMuni, $idNodo = null, $incluir_domicilio)
    {
        $sqlPromotores = 'SELECT
                [DetalleEstructuraMovilizacion].[Descripcion] AS descripcionPuesto
                ,REPLACE(([PadronGlobal].[NOMBRE] + \' \' +
                    [PadronGlobal].[APELLIDO_PATERNO] + \' \' +
                    [PadronGlobal].[APELLIDO_MATERNO]
                ), \'\\\', \'Ñ\') AS Responsable
                ,[DetallePromocion].[IdPErsonaPromueve]
                ,[PadronGlobal].[TELCASA]
                ,[PadronGlobal].[TELMOVIL]
                ,[PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]
                    +\' \'+[PadronGlobal].[NOM_LOC] As Domicilio
            FROM
                [DetallePromocion]
            INNER JOIN [PadronGlobal] ON
                [PadronGlobal].[CLAVEUNICA] = [DetallePromocion].[IdPErsonaPromueve] AND
                [PadronGlobal].[MUNICIPIO] = '.$idMuni.'
            INNER JOIN [DetalleEstructuraMovilizacion] ON
                [DetalleEstructuraMovilizacion].[IdPersonaPuesto] = [PadronGlobal].[CLAVEUNICA] AND
                [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.' ';

        if ($idNodo != null) {
            $sqlPromotores .= ' AND [DetalleEstructuraMovilizacion].[Dependencias] LIKE \'%|'.$idNodo.'|%\' ';
        }

        $sqlPromotores .= ' ORDER BY Responsable ';

        $promotores = Yii::$app->db->createCommand($sqlPromotores)->queryAll();

        if (count($promotores) == 0) {
            return json_decode('[]');
        } else {
            $reporte = '[';

            foreach ($promotores as $promotor) {
                $reporte .= '{ "Nombre": "<b>'.$promotor['descripcionPuesto'].' '.str_replace('\\', 'Ñ', $promotor['Responsable']).'</b>",'
                                .'"Tel. Celular": "<b>'.$promotor['TELMOVIL'].'</b>", '
                                .'"Tel. Casa": "<b>'.$promotor['TELCASA'].'</b>" },';
                                //.'"Domicilio": "<b>'.str_replace('\\', 'Ñ', $promotor['Domicilio']).'</b>" },';

                $sqlPromovidos = 'SELECT
                        REPLACE(([PadronGlobal].[NOMBRE]+\' \'+[PadronGlobal].[APELLIDO_PATERNO]
                            +\' \'+[PadronGlobal].[APELLIDO_MATERNO]), \'\\\', \'Ñ\') AS Persona,
                        [PadronGlobal].[TELCASA],
                        [PadronGlobal].[TELMOVIL],
                        [PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]
                            +\' \'+[PadronGlobal].[NOM_LOC] As Domicilio
                    FROM
                        [DetallePromocion]
                    INNER JOIN
                        [PadronGlobal] ON
                        [PadronGlobal].[CLAVEUNICA] = [DetallePromocion].[IdPersonaPromovida] AND
                        [PadronGlobal].[MUNICIPIO] = '.$idMuni.'
                    WHERE [DetallePromocion].[IdPErsonaPromueve] = \''.$promotor['IdPErsonaPromueve'].'\'
                    ORDER BY Persona';

                $promovidos = Yii::$app->db->createCommand($sqlPromovidos)->queryAll();

                if (count($promovidos)) {
                    $countPromovidos = 1;
                    foreach ($promovidos as $promovido) {
                        $reporte .= '{ "Nombre": "'.$countPromovidos.'. '.str_replace('\\', 'Ñ', $promovido['Persona']).'",'
                                .'"Tel. Celular": "'.$promovido['TELMOVIL'].'", '
                                .'"Tel. Casa": "'.$promovido['TELCASA'].'" },';
                                //.'"Domicilio": "'.str_replace('\\', 'Ñ', $promovido['Domicilio']).'" },';
                        $countPromovidos++;
                    }
                } else {
                    $reporte .= '{ "Nombre": "Sin Promovidos",'
                                .'"Tel. Celular": "", '
                                .'"Tel. Casa": "" },';
                                //.'"Domicilio": "" },';
                }

                $reporte .= '{ "Nombre": " &nbsp; ",'
                            .'"Tel. Celular": " &nbsp; ", '
                            .'"Tel. Casa": " &nbsp; " },';
                            //.'"Domicilio": " &nbsp; ", '
                            //.'"Promovido Por": " &nbsp; " },';
            }

            $reporte .= ']';
        }

        return json_decode(str_replace('},]', '}]', $reporte), true);
    }*/

    public static function auditoria($idMuni, $idNodo, $puestos)
    {
        $datos = Reporte::estructura($idMuni, $idNodo, $puestos, true);
        $reporte = [];

        foreach ($datos as $fila) {
            $objAuditoria = AuditoriaEstructura::findOne(['IdNodoEstructuraMov' => $fila['id']]);
            if ($objAuditoria) {
                $fecha = new \DateTime($objAuditoria['Fecha']);
            }

            $rowAuditoria = [
                'Puesto' => $fila['Puesto'],
                'Descripción' => $fila['Descripción'],
                'Nombre' => $fila['Nombre'],
                'Estructura' => $objAuditoria ? ($objAuditoria['Puesto'] ? 'Si' : 'No') : '',
                'Persona' => $objAuditoria ? ($objAuditoria['Persona'] ? 'Si' : 'No') : '',
                'Sección' => $objAuditoria ? ($objAuditoria['Seccion'] ? 'Si' : 'No') : '',
                'Celular' => $objAuditoria ? ($objAuditoria['Celular'] ? 'Si' : 'No') : '',
                'Observaciones' => $objAuditoria ? $objAuditoria['Observaciones'] : '',
                'Fecha' => $objAuditoria ? $fecha->format('d-m-Y h:i a') : '',
            ];

            $reporte[] = $rowAuditoria;
        }

        return $reporte;
    }
}
