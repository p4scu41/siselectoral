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
    public static function avanceSeccional($idMuni) {
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
                        WHERE [PadronGlobal].[MUNICIPIO] = '.$idMuni.' AND [PadronGlobal].[SECCION] = [CSeccion].[NumSector]
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
    public function arrayToHtml($arrayDatos, $omitirCentrado=null, $class='table table-condensed table-bordered table-hover', $options='border="1" cellpadding="1" cellspacing="1"')
    {
        $htmlTable = '<table class="'.$class.'" '.$options.'><thead><tr>';

        if (count($arrayDatos) == 0) {
            $htmlTable .= '<th class="text-center">No se encontrados datos en la búsqueda</th></tr></thead><tbody>';
        } else {
            // Encabezado
            $primeraFila = array_shift($arrayDatos);
            $encabezado = array_keys($primeraFila);
            array_unshift($arrayDatos, $primeraFila);

            foreach ($encabezado as $columna) {
                $htmlTable .= '<th class="text-center">'.htmlentities(utf8_encode($columna)).'</th>';
            }

            $htmlTable .= '</tr></thead><tbody>';

            // Cuerpo
            foreach ($arrayDatos as $fila) {
                $htmlTable .= '<tr>';
                $i = 1;
                foreach ($fila as $columna) {
                    if ( in_array($i, $omitirCentrado) ) {
                        $htmlTable .= '<td>'.$columna.'</td>';
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
     *
     *
     * @param Int $idMuni
     * @return Array
     */
    /*public static function estructura($idMuni)
    {
        $sqlPuesto = 'SELECT MIN([IdPuesto]) AS [IdPuesto] FROM [DetalleEstructuraMovilizacion] WHERE [Municipio] = '.$idMuni;
        $puesto = Yii::$app->db->createCommand($sqlPuesto)->queryOne();

        $sqlNodos = 'SELECT
                    [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
                    ,[DetalleEstructuraMovilizacion].[IdPuesto]
                    ,[Puestos].[Descripcion] AS DescripcionPuesto
                    ,[DetalleEstructuraMovilizacion].[IdPuestoDepende]
                    ,[DetalleEstructuraMovilizacion].[IdPersonaPuesto]
                    ,[PadronGlobal].[APELLIDO_PATERNO]+\' \'+[PadronGlobal].[APELLIDO_MATERNO]+\' \'+[PadronGlobal].[NOMBRE] AS Responsable
                    ,[DetalleEstructuraMovilizacion].[Dependencias]
                    ,[DetalleEstructuraMovilizacion].[Descripcion] AS DescripcionNodo
                    ,[PadronGlobal].[TELCASA]
                    ,[PadronGlobal].[TELMOVIL]
                    ,[PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]+\' \'+[PadronGlobal].[NOM_LOC] As Domicilio
                FROM
                    [DetalleEstructuraMovilizacion]
                LEFT JOIN
                    [PadronGlobal] ON
                    [PadronGlobal].[CLAVEUNICA] = [DetalleEstructuraMovilizacion].[IdPersonaPuesto]
                INNER JOIN
                    [Puestos] ON
                    [DetalleEstructuraMovilizacion].[IdPuesto] = [Puestos].[IdPuesto]
                WHERE
                    [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.' AND [DetalleEstructuraMovilizacion].[IdPuesto] = '.$puesto['IdPuesto'];

        $nodos = Yii::$app->db->createCommand($sqlNodos)->queryAll();
        $arrayEstructura = [];

        foreach ($nodos as $nodo) {
            $elemento = [
                'Puesto'       => $nodo['DescripcionPuesto'],
                'Nombre'       => $nodo['Responsable'],
                'Tel. Celular' => $nodo['TELMOVIL'],
                'Tel. Casa'    => $nodo['TELCASA'],
                'Domicilio'    => $nodo['Domicilio'],
            ];

            array_push($arrayEstructura, $elemento);

            $sqlNodosDependientes = 'SELECT
                    [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
                    ,[DetalleEstructuraMovilizacion].[IdPuesto]
                    ,[Puestos].[Descripcion] AS DescripcionPuesto
                    ,[DetalleEstructuraMovilizacion].[IdPuestoDepende]
                    ,[DetalleEstructuraMovilizacion].[IdPersonaPuesto]
                    ,[PadronGlobal].[APELLIDO_PATERNO]+\' \'+[PadronGlobal].[APELLIDO_MATERNO]+\' \'+[PadronGlobal].[NOMBRE] AS Responsable
                    ,[DetalleEstructuraMovilizacion].[Dependencias]
                    ,[DetalleEstructuraMovilizacion].[Descripcion] AS DescripcionNodo
                    ,[PadronGlobal].[TELCASA]
                    ,[PadronGlobal].[TELMOVIL]
                    ,[PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]+\' \'+[PadronGlobal].[NOM_LOC] As Domicilio
                FROM
                    [DetalleEstructuraMovilizacion]
                LEFT JOIN
                    [PadronGlobal] ON
                    [PadronGlobal].[CLAVEUNICA] = [DetalleEstructuraMovilizacion].[IdPersonaPuesto]
                INNER JOIN
                    [Puestos] ON
                    [DetalleEstructuraMovilizacion].[IdPuesto] = [Puestos].[IdPuesto]
                WHERE
                    [DetalleEstructuraMovilizacion].[IdPuestoDepende] = '.$nodo['IdNodoEstructuraMov'];

            $nodosDependientes = Yii::$app->db->createCommand($sqlNodosDependientes)->queryAll();

            foreach ($nodosDependientes as $nodoHijo) {
                $elemento = [
                    'Puesto'       => $nodoHijo['DescripcionPuesto'],
                    'Nombre'       => $nodoHijo['Responsable'],
                    'Tel. Celular' => $nodoHijo['TELMOVIL'],
                    'Tel. Casa'    => $nodoHijo['TELCASA'],
                    'Domicilio'    => $nodoHijo['Domicilio'],
                ];

                array_push($arrayEstructura, $elemento);

                self::nodosHijos(&$arrayEstructura, $nodoHijo['IdNodoEstructuraMov']);
            }
        }

        return $arrayEstructura;
    }*/

    /**
     *
     *
     * @param type $arrayEstructura
     * @param type $idNodo
     */
    /*public static function nodosHijos(&$arrayEstructura, $idNodo)
    {
        $sqlNodos = 'SELECT
                    [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
                    ,[DetalleEstructuraMovilizacion].[IdPuesto]
                    ,[Puestos].[Descripcion] AS DescripcionPuesto
                    ,[DetalleEstructuraMovilizacion].[IdPuestoDepende]
                    ,[DetalleEstructuraMovilizacion].[IdPersonaPuesto]
                    ,[PadronGlobal].[APELLIDO_PATERNO]+\' \'+[PadronGlobal].[APELLIDO_MATERNO]+\' \'+[PadronGlobal].[NOMBRE] AS Responsable
                    ,[DetalleEstructuraMovilizacion].[Dependencias]
                    ,[DetalleEstructuraMovilizacion].[Descripcion] AS DescripcionNodo
                    ,[PadronGlobal].[TELCASA]
                    ,[PadronGlobal].[TELMOVIL]
                    ,[PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]+\' \'+[PadronGlobal].[NOM_LOC] As Domicilio
                FROM
                    [DetalleEstructuraMovilizacion]
                LEFT JOIN
                    [PadronGlobal] ON
                    [PadronGlobal].[CLAVEUNICA] = [DetalleEstructuraMovilizacion].[IdPersonaPuesto]
                INNER JOIN
                    [Puestos] ON
                    [DetalleEstructuraMovilizacion].[IdPuesto] = [Puestos].[IdPuesto]
                WHERE
                    [DetalleEstructuraMovilizacion].[IdPuestoDepende] = '.$idNodo;

        $nodos = Yii::$app->db->createCommand($sqlNodos)->queryAll();

        if (count($nodosDependientes)) {
            foreach ($nodos as $nodo) {
                $elemento = [
                    'Puesto'       => $nodo['DescripcionPuesto'],
                    'Nombre'       => $nodo['Responsable'],
                    'Tel. Celular' => $nodo['TELMOVIL'],
                    'Tel. Casa'    => $nodo['TELCASA'],
                    'Domicilio'    => $nodo['Domicilio'],
                ];

                array_push($arrayEstructura, $elemento);

                $sqlNodosDependientes = 'SELECT
                        [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
                        ,[DetalleEstructuraMovilizacion].[IdPuesto]
                        ,[Puestos].[Descripcion] AS DescripcionPuesto
                        ,[DetalleEstructuraMovilizacion].[IdPuestoDepende]
                        ,[DetalleEstructuraMovilizacion].[IdPersonaPuesto]
                        ,[PadronGlobal].[APELLIDO_PATERNO]+\' \'+[PadronGlobal].[APELLIDO_MATERNO]+\' \'+[PadronGlobal].[NOMBRE] AS Responsable
                        ,[DetalleEstructuraMovilizacion].[Dependencias]
                        ,[DetalleEstructuraMovilizacion].[Descripcion] AS DescripcionNodo
                        ,[PadronGlobal].[TELCASA]
                        ,[PadronGlobal].[TELMOVIL]
                        ,[PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]+\' \'+[PadronGlobal].[NOM_LOC] As Domicilio
                    FROM
                        [DetalleEstructuraMovilizacion]
                    LEFT JOIN
                        [PadronGlobal] ON
                        [PadronGlobal].[CLAVEUNICA] = [DetalleEstructuraMovilizacion].[IdPersonaPuesto]
                    INNER JOIN
                        [Puestos] ON
                        [DetalleEstructuraMovilizacion].[IdPuesto] = [Puestos].[IdPuesto]
                    WHERE
                        [DetalleEstructuraMovilizacion].[IdPuestoDepende] = '.$nodo['IdNodoEstructuraMov'];

                $nodosDependientes = Yii::$app->db->createCommand($sqlNodosDependientes)->queryAll();

                if (count($nodosDependientes)) {
                    foreach ($nodosDependientes as $nodoHijo) {
                        $elemento = [
                            'Puesto'       => $nodoHijo['DescripcionPuesto'],
                            'Nombre'       => $nodoHijo['Responsable'],
                            'Tel. Celular' => $nodoHijo['TELMOVIL'],
                            'Tel. Casa'    => $nodoHijo['TELCASA'],
                            'Domicilio'    => $nodoHijo['Domicilio'],
                        ];

                        array_push($arrayEstructura, $elemento);

                        return true;

                        //self::nodosHijos(&$arrayEstructura, $nodoHijo['IdNodoEstructuraMov']);
                    }
                }
            }
        }
    }*/

    /**
     *
     * @param type $idNodo
     * @return type
     */
    public static function nodoEstructuraJSON($idNodo)
    {
        $sqlNodo = 'SELECT
                    [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
                    ,[Puestos].[Descripcion] AS DescripcionPuesto
                    ,[CSeccion].[NumSector] as Seccion
                    ,CASE
                        WHEN [PadronGlobal].[CLAVEUNICA] IS NULL
                        THEN \'NO ASIGNADO\'
                        ELSE ([PadronGlobal].[APELLIDO_PATERNO]+\' \'+[PadronGlobal].[APELLIDO_MATERNO]+\' \'+[PadronGlobal].[NOMBRE])
                    END AS Responsable
                    ,[PadronGlobal].[TELCASA]
                    ,[PadronGlobal].[TELMOVIL]
                    ,[PadronGlobal].[DOMICILIO]+\', \'+[PadronGlobal].[DES_LOC]+\' \'+[PadronGlobal].[NOM_LOC] As Domicilio
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
        $estructura = '';

        $nodo = Yii::$app->db->createCommand($sqlNodo)->queryOne();

        $estructura .= '{ "Puesto": "'.$nodo['DescripcionPuesto'].'", '
                        .'"Nombre": "'.str_replace('\\', 'Ñ', $nodo['Responsable']).'",'
                        .'"Sección": "'.$nodo['Seccion'].'",'
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
                $estructura .= self::nodoEstructuraJSON($row['IdNodoEstructuraMov']);
            }
        }

        return $estructura;
    }

    public static function estructura($idMuni)
    {
        $sqlPuesto = 'SELECT MIN([IdPuesto]) AS [IdPuesto] FROM [DetalleEstructuraMovilizacion] WHERE [Municipio] = '.$idMuni;
        $puesto = Yii::$app->db->createCommand($sqlPuesto)->queryOne();

        $sqlNodos = 'SELECT
                    [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
                FROM
                    [DetalleEstructuraMovilizacion]
                WHERE
                    [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.' AND [DetalleEstructuraMovilizacion].[IdPuesto] = '.$puesto['IdPuesto'];

        $nodos = Yii::$app->db->createCommand($sqlNodos)->queryAll();
        $estructura = '[';

        if (count($nodos)) {
            foreach ($nodos as $nodo) {
                $estructura .= self::nodoEstructuraJSON($nodo['IdNodoEstructuraMov']);
            }
        }

        $estructura .= ']';

        return json_decode(str_replace('},]', '}]', $estructura), true);
    }

}
