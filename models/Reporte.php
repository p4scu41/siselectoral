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
                        THEN \'Sin Asignación\'
                        ELSE (tblPadron.[APELLIDO_PATERNO]+\' \'+tblPadron.[APELLIDO_MATERNO]+\' \'+tblPadron.[NOMBRE])
                    END AS Responsable,
                    [CSeccion].MetaAlcanzar AS Meta,
                    ROUND((
                        SELECT
                            COUNT(*) as Avance
                        FROM [Promocion]
                        INNER JOIN [PadronGlobal] ON
                            [PadronGlobal].[CLAVEUNICA] = [Promocion].[IdpersonaPromovida]
                        WHERE [PadronGlobal].[MUNICIPIO] = 51 AND [PadronGlobal].[SECCION] = [CSeccion].[NumSector]
                    ) / [CSeccion].MetaAlcanzar * 100, 0) AS \'Avance %\'
                FROM
                    [DetalleEstructuraMovilizacion]
                LEFT JOIN [PadronGlobal] AS tblPadron ON
                    tblPadron.[CLAVEUNICA] = [DetalleEstructuraMovilizacion].[IdPersonaPuesto]
                INNER JOIN [CSeccion] ON
                    [CSeccion].[IdMunicipio] = [DetalleEstructuraMovilizacion].[Municipio] AND
                    [CSeccion].[IdSector] = [DetalleEstructuraMovilizacion].[IdSector]
                WHERE
                    [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.' AND
                    tblPadron.[MUNICIPIO] = '.$idMuni.'
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
                $htmlTable .= '<th class="text-center">'.utf8_encode($columna).'</th>';
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

}
