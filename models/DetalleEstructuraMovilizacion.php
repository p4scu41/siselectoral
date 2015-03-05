<?php

namespace app\models;

use Yii;
use app\models\Puestos;
use app\models\PadronGlobal;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "DetalleEstructuraMovilizacion".
 *
 * @property integer $IdNodoEstructuraMov
 * @property integer $IdEstructuraMovilizacion
 * @property integer $IdPuesto
 * @property integer $IdPuestoDepende
 * @property string $IdPersonaPuesto
 * @property integer $IdSector
 * @property integer $Meta
 * @property integer $Region
 * @property integer $Municipio
 * @property string $Dependencias
 * @property integer $ZonaMunicipal
 * @property integer $ZonaDistrital
 * @property string $Descripcion
 */
class DetalleEstructuraMovilizacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'DetalleEstructuraMovilizacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdNodoEstructuraMov'], 'required'],
            [['IdNodoEstructuraMov', 'IdEstructuraMovilizacion', 'IdPuesto', 'IdPuestoDepende', 'IdSector', 'Meta', 'Region', 'Municipio', 'ZonaMunicipal', 'ZonaDistrital'], 'integer'],
            [['IdPersonaPuesto', 'Dependencias', 'Descripcion'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['IdNodoEstructuraMov'];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdNodoEstructuraMov' => 'Id Nodo Estructura Mov',
            'IdEstructuraMovilizacion' => 'Id Estructura Movilizacion',
            'IdPuesto' => 'Id Puesto',
            'IdPuestoDepende' => 'Id Puesto Depende',
            'IdPersonaPuesto' => 'Id Persona Puesto',
            'IdSector' => 'Id Sector',
            'Meta' => 'Meta',
            'Region' => 'Region',
            'Municipio' => 'Municipio',
            'Dependencias' => 'Dependencias',
            'ZonaMunicipal' => 'Zona Municipal',
            'ZonaDistrital' => 'Zona Distrital',
            'Descripcion' => 'Descripcion',
        ];
    }

    /**
     * Obtiene los nodos raices del árbol de la estructura
     *
     * @param Array $filtros
     * @return JSON Personalizado para fancytree
     */
    public function getTree($filtros, $alterna=false)
    {
        //echo var_dump($alterna);
        $filtros = array_filter($filtros);

        if(empty($filtros)) {
            return '[]';
        }

        $tree = '[';

        if(empty($filtros['IdPuesto']) && empty($filtros['IdPuestoDepende'])) {
            $filtros['IdPuesto'] = $this->getMaxPuestoOnMuni($filtros['Municipio']);
        }

        if ($alterna == false) {
            $child = $this->find()->where($filtros)->andWhere('IdOrganizacion = -1')->all();
        } else {
            $child = $this->find()->where($filtros)->andWhere('IdOrganizacion != -1')->all();
        }

        foreach ($child as $row) {
            $puesto = Puestos::findOne(['IdPuesto' => $row->IdPuesto]);

            $where = 'IdPuestoDepende = '.$row->IdNodoEstructuraMov;

            if ($alterna == false) {
                $where .= ' AND IdOrganizacion = -1';
            } else {
                $where .= ' AND IdOrganizacion != -1';
            }

            $count = $this->find()->where($where)->count();

            $tree .= '{"key": "'.$row->IdNodoEstructuraMov.'", "title": "'.$puesto->Descripcion.' - '.$row->Descripcion.' '.
                        ($count > 0 ? '['.$count.']' : '').'", '.
                        '"data": { "IdPuesto": "'.$puesto->IdPuesto.'", "puesto": "'.$puesto->Descripcion.'", "persona": "'.$row->IdPersonaPuesto.'", "iconclass": ';

            if ($count > 0) {
                $tree .= '"glyphicon glyphicon-user"}, "folder": true, "lazy": true';
            } else {
                $tree .= '"fa fa-user"}';
            }

            $tree .= '},';
        }

        $tree .= ']';

        return str_replace('},]', '}]', $tree);
    }

    /**
     * Obtiene los nodos hijos directos de un nodo especifico
     *
     * @param Int $idNodo
     * @return JSON Personalizado para fancytree
     */
    public function getBranch($idNodo, $alterna=false)
    {
        $tree = '[';
        $where = 'IdPuestoDepende = '.$idNodo;

        if ($alterna == false) {
            $where .= ' AND IdOrganizacion = -1';
        }

        $count = $this->find()->where($where)->count();

        if ($count > 0) {
            $child = $this->find()->where($where)->all();

            foreach ($child as $row) {
                $puesto = Puestos::findOne(['IdPuesto' => $row->IdPuesto]);
                $where = 'IdPuestoDepende = '.$row->IdNodoEstructuraMov;

                if ($alterna == false) {
                    $where .= ' AND IdOrganizacion = -1';
                }

                $count = $this->find()->where($where)->count();

                $tree .= '{"key": "'.$row->IdNodoEstructuraMov.'", "title": "'.$puesto->Descripcion.' - '.$row->Descripcion.' '.
                            ($count > 0 ? '['.$count.']' : '').'", '.
                            '"data": { "IdPuesto": "'.$puesto->IdPuesto.'", "puesto": "'.$puesto->Descripcion.'", "persona": "'.$row->IdPersonaPuesto.'", "iconclass": ';

                if ($count > 0) {
                   $tree .= '"glyphicon glyphicon-user"} , "folder": true, "lazy": true';
                } else {
                    $tree .= '"fa fa-user"}';
                }

                $tree .= '},';
            }
        }

        $tree .=']';

        return str_replace('},]', '}]', $tree);
    }

    /**
     * Construye todo el árbol a partir de un nodo raíz
     *
     * @param Int $idNodo
     * @return JSON personalizado para fancytree
     * @deprecated since version 1
     */
    public function buildTree($idNodo)
    {
        $nodo = $this->find()->where(['IdNodoEstructuraMov' => $idNodo])->one();
        $tree = '';

        $count = $this->find()
                ->where(['IdPuestoDepende' => $idNodo])
                ->count();

        if ($count > 0) {
            $tree .= '{"key": "'.$nodo->IdNodoEstructuraMov.'", "title": "'.$nodo->Descripcion.'",'.
                     ' "folder": true, "lazy": true, "children": [';
            $child = $this->find()->where(['IdPuestoDepende' => $idNodo])->all();

            foreach ($child as $row) {
                $tree .= $this->buildTree($row->IdNodoEstructuraMov);
            }

            $tree .= ']},';
        } else {
            $tree .= '{"key": "'.$nodo->IdNodoEstructuraMov.'", "title": "'.$nodo->Descripcion.'"},';
        }

        return str_replace('},]', '}]', $tree);
    }

    /**
     * Obtiene el ID del puesto con mayor jerarquía (basado en el nivel) dentro de un municipio,
     *
     * @param INT $idMuni ID del Municipio
     * @return INT Id del puesto
     */
    public function getMaxPuestoOnMuni($idMuni)
    {
        $sql = 'SELECT TOP (1)
                [DetalleEstructuraMovilizacion].[IdPuesto]
            FROM
                [DetalleEstructuraMovilizacion]
            INNER JOIN
                [Puestos] ON [Puestos].[IdPuesto] = [DetalleEstructuraMovilizacion].[IdPuesto]
            WHERE
                [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.'
            ORDER BY [Nivel] ASC';

        $Puesto = $this->findBySql($sql)->one();

        return $Puesto->IdPuesto;
    }

    /**
     * Obtiene el Nivel con mayor jerarquía dentro de un municipio,
     *
     * @param INT $idMuni ID del Municipio
     * @return INT Nivel
     */
    public static function getMaxNivelOnMuni($idMuni)
    {
        $sql = 'SELECT TOP (1) [Nivel]
            FROM
                [DetalleEstructuraMovilizacion]
            INNER JOIN
                [Puestos] ON [Puestos].[IdPuesto] = [DetalleEstructuraMovilizacion].[IdPuesto]
            WHERE
                [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.'
            ORDER BY [Nivel] ASC';

        $Puesto = Yii::$app->db->createCommand($sql)->queryAll();

        return $Puesto[0]['Nivel'];
    }

    /**
     * Obtiene los puestos dependientes que esten asignados a una persona
     *
     * @return Array Persona
     */
    public function getDependientes()
    {
        $nodosDependientes = $this->find()->where('IdPersonaPuesto != \'00000000-0000-0000-0000-000000000000\' AND '.
                'IdPuestoDepende = '.$this->IdNodoEstructuraMov)->all();
        $personasDependientes = array();

        if( count($nodosDependientes) > 0) {
            foreach ($nodosDependientes as $nodo) {
                //$persona = PadronGlobal::find(['CLAVEUNICA'=>$nodo->IdPersonaPuesto])->asArray()->one();
                $persona = $this->findBySql('SELECT * FROM [PadronGlobal] WHERE [CLAVEUNICA] = \''.
                                $nodo->IdPersonaPuesto.'\'')->asArray()->one();
                //$puesto = Puestos::find(['IdPuesto'=>$nodo->IdPuesto])->asArray()->one();
                $puesto = $this->findBySql('SELECT * FROM [Puestos] WHERE [IdPuesto] = \''.
                                $nodo->IdPuesto.'\'')->asArray()->one();

                $persona['puesto'] = $puesto['Descripcion'].' - '.$nodo->Descripcion;
                $persona['foto'] = PadronGlobal::getFotoByUID($persona['CLAVEUNICA'], $persona['SEXO']);

                $personasDependientes[] = $persona;
            }
        }

        return $personasDependientes;
    }

    /**
     * Obtiene la cantidad y la descripción de los nodos dependientes
     *
     * @return Array Persona
     */
    public function getCountDepen()
    {
        $cantidad = null;

        $nodosDependientes = $this->find()->where('IdPuestoDepende = '.$this->IdNodoEstructuraMov)->all();

        if( count($nodosDependientes) > 0) {
            $count = $nodosDependientes[0];
            $puesto = $this->findBySql('SELECT * FROM [Puestos] WHERE [IdPuesto] = \''.
                                $count->IdPuesto.'\'')->one();
            $descrip = explode(' ', ucwords(mb_strtolower($puesto->Descripcion)));

            if ( count($descrip)>1) {
                $descrip[0] = substr($descrip[0], 0, 1).'.';
            }

            $cantidad = Array('cantidad'=>count($nodosDependientes), 'puesto'=>implode($descrip, ' '));
        }

        return $cantidad;
    }

    /**
     * Obtiene el nodo jefe del objeto actual
     *
     * @return Array Persona
     */
    public function getJefe()
    {
        $jefe = null;
        $nodo = $this->find()->where('IdPersonaPuesto != \'00000000-0000-0000-0000-000000000000\' AND '.
                    'IdNodoEstructuraMov = '.$this->IdPuestoDepende)->one();

        if($nodo != null)
        {
            $jefe = Yii::$app->db->createCommand('SELECT * FROM [PadronGlobal] WHERE [CLAVEUNICA] = \''.
                    $nodo->IdPersonaPuesto.'\'')->queryOne();
            $puesto = Puestos::find(['IdPuesto'=>$nodo->IdPuesto])->one();

            $jefe['puesto'] = $puesto->Descripcion.' - '.$nodo->Descripcion;
            $jefe['foto'] = PadronGlobal::getFotoByUID($jefe['CLAVEUNICA'], $jefe['SEXO']);
        }

        return $jefe;
    }

    /**
     * Obtiene las secciones que coordina un jefe de sección
     *
     * @return String Secciones
     */
    public function getSecciones()
    {
        $nodosDependientes = Yii::$app->db->createCommand('SELECT SUBSTRING([Descripcion], 4, LEN([Descripcion])) AS seccion
            FROM [DetalleEstructuraMovilizacion]
            WHERE IdPuestoDepende = '.$this->IdNodoEstructuraMov.'
                ORDER BY seccion ASC')->queryAll();
        $secciones = $nodosDependientes[0]['seccion'];
        $actual = $nodosDependientes[0]['seccion'];
        // Parche para que pueda leer el ultimo nodo
        $nodosDependientes[count($nodosDependientes)] = array('seccion'=>0);
        $ultimo = 0;

        for ($i = 0; $i<count($nodosDependientes); $i++) {
            if($actual != $nodosDependientes[$i]['seccion']) {
                $secciones .= ' - '.$ultimo.', '.$nodosDependientes[$i]['seccion'];
                $actual = $nodosDependientes[$i]['seccion'];

                // Detecta números que esten solos, sin consecutivos antes y despues de su posición
                if (($actual+1) != ($nodosDependientes[$i+1]['seccion'])) {
                    $secciones .= ', '.$nodosDependientes[$i+1]['seccion'];
                    $actual = $nodosDependientes[$i+1]['seccion'];
                    $i++;
                }
            }

            $ultimo = $nodosDependientes[$i]['seccion'];
            $actual++;
        }

        $secciones = str_replace(', 0, ', '', $secciones);
        $secciones = str_replace(', 0', '', $secciones);

        return $secciones;
    }

    /**
     * Obtiene las cantidades para cada puesto en un determinado Municipio
     *
     * @param Int $idMuni
     * @return Array Tabla de puestos con su respectivas cantidads
     */
    public static function getResumen($idMuni)
    {
        $sqlTotales = 'SELECT
                [DetalleEstructuraMovilizacion].[IdPuesto],
                [Puestos].[Descripcion] as Puesto,
                COUNT(*) AS Total
            FROM
                [DetalleEstructuraMovilizacion]
            INNER JOIN [Puestos]
                ON [DetalleEstructuraMovilizacion].[IdPuesto] = [Puestos].[IdPuesto]
            WHERE
                [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.'
            GROUP BY
                [DetalleEstructuraMovilizacion].[IdPuesto],[Puestos].[Descripcion], [Puestos].[Nivel]
            ORDER BY
                [Puestos].[Nivel]';

        $sqlOcupados = 'SELECT
                [DetalleEstructuraMovilizacion].[IdPuesto],
                COUNT(*) AS ocupado
            FROM
                [DetalleEstructuraMovilizacion]
            INNER JOIN [Puestos]
                ON [DetalleEstructuraMovilizacion].[IdPuesto] = [Puestos].[IdPuesto]
            WHERE
                [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.' AND
                [DetalleEstructuraMovilizacion].[IdPersonaPuesto] != \'00000000-0000-0000-0000-000000000000\'
            GROUP BY
                [DetalleEstructuraMovilizacion].[IdPuesto],[Puestos].[Descripcion], [Puestos].[Nivel]
            ORDER BY
                [Puestos].[Nivel]';

        $sqlVacantes = 'SELECT
                [DetalleEstructuraMovilizacion].[IdPuesto],
                COUNT(*) AS vacante
            FROM
                [DetalleEstructuraMovilizacion]
            INNER JOIN [Puestos]
                ON [DetalleEstructuraMovilizacion].[IdPuesto] = [Puestos].[IdPuesto]
            WHERE
                [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.' AND
                [DetalleEstructuraMovilizacion].[IdPersonaPuesto] = \'00000000-0000-0000-0000-000000000000\'
            GROUP BY
                [DetalleEstructuraMovilizacion].[IdPuesto],[Puestos].[Descripcion], [Puestos].[Nivel]
            ORDER BY
                [Puestos].[Nivel]';

        $sqlCountPromovidos = 'SELECT COUNT([IdpersonaPromovida]) AS promovidos FROM [Promocion] WHERE [IdPuesto] IN (
            SELECT [IdNodoEstructuraMov] FROM [DetalleEstructuraMovilizacion]
            WHERE [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.')';

        $sqlMetaPromovidos = 'SELECT SUM([Meta]) AS MetaByPromotor
            FROM [DetalleEstructuraMovilizacion]
            WHERE [IdPuesto] = 7 AND [Municipio] = '.$idMuni;

        $totales = Yii::$app->db->createCommand($sqlTotales)->queryAll();
        $ocupados = ArrayHelper::map(Yii::$app->db->createCommand($sqlOcupados)->queryAll(), 'IdPuesto', 'ocupado');
        $vacantes = ArrayHelper::map(Yii::$app->db->createCommand($sqlVacantes)->queryAll(), 'IdPuesto', 'vacante');
        $cantidadPromovidosPromotor = Yii::$app->db->createCommand($sqlCountPromovidos)->queryOne();
        $metaPromovidosPromotor = Yii::$app->db->createCommand($sqlMetaPromovidos)->queryOne();

        $sumTotales = 0;
        $sumOcupados = 0;
        $sumVacantes = 0;
        $avancePromovidos = 0;

        if(count($totales) == 0) {
            return [];
        }

        if ($metaPromovidosPromotor['MetaByPromotor'] != 0) {
            $avancePromovidos = round($cantidadPromovidosPromotor['promovidos']/$metaPromovidosPromotor['MetaByPromotor']*100);
        }

        $promovidos = [
            'Puesto' => 'PROMOCIÓN',
            'Total' => $metaPromovidosPromotor['MetaByPromotor'],
            'Ocupados' => $cantidadPromovidosPromotor['promovidos'],
            'Vacantes' => ((int)$metaPromovidosPromotor['MetaByPromotor']) - ((int)$cantidadPromovidosPromotor['promovidos']),
            'Avances %' => $avancePromovidos,
        ];

        for($i=0; $i<count($totales); $i++) {
            $totales[$i]['Ocupados'] = (int) $ocupados[$totales[$i]['IdPuesto']];
            $totales[$i]['Vacantes'] = (int) $vacantes[$totales[$i]['IdPuesto']];
            unset($totales[$i]['IdPuesto']);

            $totales[$i]['Avances %'] = round(((int)$totales[$i]['Ocupados'] / (int)$totales[$i]['Total'])*100);

            $sumTotales += $totales[$i]['Total'];
            $sumOcupados += $totales[$i]['Ocupados'];
            $sumVacantes += $totales[$i]['Vacantes'];
        }

        array_push($totales, array('Puesto'=>'AVANCE ESTRUCTURA',
                                   'Total'=>$sumTotales,
                                    'Ocupados'=>$sumOcupados,
                                    'Vacantes'=>$sumVacantes,
                                    'Avances %'=>round(($sumOcupados/$sumTotales)*100)));

        array_push($totales, $promovidos);

        return $totales;
    }

    /**
     * Obtiene los puestos asociados a un Municipio
     *
     * @param type $idMuni
     * @return Array Lista de puestos disponibles en el municipio
     * @deprecated since version 1
     */
    public static function getPuestosOnMuni($idMuni)
    {
        $sqlPuestos = 'SELECT
                [DetalleEstructuraMovilizacion].[IdPuesto],
                [Puestos].[Descripcion],
                [Puestos].[Nivel]
            FROM
                [DetalleEstructuraMovilizacion]
            INNER JOIN [Puestos]
                ON [DetalleEstructuraMovilizacion].[IdPuesto] = [Puestos].[IdPuesto]
            WHERE
                [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.'
            GROUP BY
                [DetalleEstructuraMovilizacion].[IdPuesto],[Puestos].[Descripcion], [Puestos].[Nivel]
            ORDER BY
                [Puestos].[Nivel]';

        $puestos = Yii::$app->db->createCommand($sqlPuestos)->queryAll();

        return $puestos;
    }

    /**
     * Obtiene los nodos dependientes de un determinado padre
     *
     * @return Array Nodos
     */
    public static function getNodosDependientes($parametros, $withFoto=false)
    {
        $filtros = http_build_query($parametros, '', ' AND ');

        // Parche para obtener los nodos de nivel superior
        if(!isset($parametros['Nivel']) && !isset($parametros['IdPuestoDepende'])) {
            $filtros .= ' AND Nivel='.static::getMaxNivelOnMuni($parametros['Municipio']);
        } else {
            $filtros = str_replace('AND Nivel=', 'AND Nivel>=', $filtros);
        }

        $sqlPuestos = 'SELECT
                [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov],
                [DetalleEstructuraMovilizacion].[IdPuestoDepende],
                [DetalleEstructuraMovilizacion].[IdPuesto],
                [DetalleEstructuraMovilizacion].[IdPersonaPuesto],
                [Puestos].[Descripcion] AS DescripcionPuesto,
                [Puestos].[Nivel],
                [DetalleEstructuraMovilizacion].[Descripcion] as DescripcionEstructura
            FROM
                [DetalleEstructuraMovilizacion]
            INNER JOIN [Puestos]
                ON [DetalleEstructuraMovilizacion].[IdPuesto] = [Puestos].[IdPuesto]
            WHERE '.$filtros.'
            ORDER BY [Puestos].[Nivel]';

        $resultPuestos = Yii::$app->db->createCommand($sqlPuestos)->queryAll();

        $result = null;

        if ($resultPuestos) {
            foreach ($resultPuestos as $nodo) {
                if($nodo['IdPersonaPuesto'] != '00000000-0000-0000-0000-000000000000') {
                    $persona = Yii::$app->db->createCommand("SELECT ([APELLIDO_PATERNO]+ ' ' +[APELLIDO_MATERNO]+ ' ' +[NOMBRE]) AS NOMBRECOMPLETO, SEXO "
                                                ."FROM [PadronGlobal] WHERE [CLAVEUNICA] = '".$nodo['IdPersonaPuesto']."'")->queryOne();
                    $foto = ['foto'=>''];
                    if ($withFoto) {
                        $foto['foto'] = PadronGlobal::getFotoByUID($nodo['IdPersonaPuesto'], $persona['SEXO']);
                    }
                    unset($persona['SEXO']);
                    $result[] = array_merge($nodo, $persona, $foto);
                } else {
                    $foto = ['foto'=>''];
                    if ($withFoto) {
                        $foto['foto'] = PadronGlobal::getFotoByUID(null, null);
                    }
                    $result[] = array_merge($nodo, ['NOMBRECOMPLETO' => 'No asignado'], $foto);
                }
            }
        }

        return $result;
    }

    /**
     * Obtiene el resumen de las cantidades de todos los
     * nodos dependientes del idNodo proporcionado
     *
     * @param type $idNodo
     * @return JSON Tabla con el resumen de los datos
     */
    public static function getResumenNodo($idNodo)
    {
        $sqlResumenNodo = 'SELECT [Puestos].[Nivel],
                [Puestos].[Descripcion] AS Puesto
                ,COUNT([DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]) AS Total
                ,COUNT(CASE WHEN
                    [DetalleEstructuraMovilizacion].[IdPersonaPuesto]!=\'00000000-0000-0000-0000-000000000000\'
                    THEN 1 ELSE NULL END) AS Ocupados
                ,COUNT(CASE WHEN
                    [DetalleEstructuraMovilizacion].[IdPersonaPuesto]=\'00000000-0000-0000-0000-000000000000\'
                    THEN 1 ELSE NULL END) AS Vacantes
                ,CAST(ROUND(SUM(CASE WHEN
                    [DetalleEstructuraMovilizacion].[IdPersonaPuesto]!=\'00000000-0000-0000-0000-000000000000\'
                    THEN CAST(1 AS FLOAT) ELSE 0 END) /
                    CAST(COUNT([DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]) AS FLOAT) * 100, 0) AS INTEGER) AS \'Avances %\'
            FROM
                [DetalleEstructuraMovilizacion]
            INNER JOIN [Puestos]
                ON [DetalleEstructuraMovilizacion].[IdPuesto] = [Puestos].[IdPuesto]
            WHERE
                [DetalleEstructuraMovilizacion].[Dependencias] like \'%|'.$idNodo.'|%\'
            GROUP BY [Puestos].[Descripcion], [Puestos].[Nivel]
            ORDER BY [Puestos].[Nivel]';

        $tablaResumen = Yii::$app->db->createCommand($sqlResumenNodo)->queryAll();

        if (count($tablaResumen) > 0) {
            $metaPromovidosPromotor = static::getMetaByPromotor($idNodo);
            $cantidadPromovidosPromotor = static::getCountPromovidos($idNodo);
            $avancePromovidos = 0;

            if ($metaPromovidosPromotor != 0) {
                $avancePromovidos = round($cantidadPromovidosPromotor/$metaPromovidosPromotor*100);
            }

            $promovidos = [
                'Puesto' => 'PROMOCIÓN',
                'Total' => $metaPromovidosPromotor,
                'Ocupados' => $cantidadPromovidosPromotor,
                'Vacantes' => $metaPromovidosPromotor-$cantidadPromovidosPromotor,
                'Avances %' => $avancePromovidos,
            ];

            $totales = [
                'Puesto' => 'AVANCE ESTRUCTURA',
                'Total' => 0,
                'Ocupados' => 0,
                'Vacantes' => 0,
                'Avances %' => 0,
            ];

            for ($i = 0; $i < count($tablaResumen); $i++) {
                unset($tablaResumen[$i]['Nivel']);
                $totales['Total']     += $tablaResumen[$i]['Total'];
                $totales['Ocupados']  += $tablaResumen[$i]['Ocupados'];
                $totales['Vacantes']  += $tablaResumen[$i]['Vacantes'];
            }

            $totales['Avances %'] = round($totales['Ocupados'] / $totales['Total'] * 100);

            array_push($tablaResumen, $totales);
            array_push($tablaResumen, $promovidos);
        }

        return $tablaResumen;
    }

    /**
     * Función recursiva que recorre todos los nodos dependientes y los acumula en una tabla
     *
     * @param Int $idNodo ID del nodo a obtener
     * @param Array $tablaResumen Parametro refenciado a la tabla que agrupa el resumen de las cantidades de todos los puestos
     * @deprecated since version 1
     */
    public static function buildResumenNodo($idNodo, &$tablaResumen)
    {
        $sqlNodosDependientes = 'SELECT [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov]
                ,[DetalleEstructuraMovilizacion].[IdPuesto]
                ,[DetalleEstructuraMovilizacion].[IdPuestoDepende]
                ,[DetalleEstructuraMovilizacion].[IdPersonaPuesto]
                ,[Puestos].[Nivel]
                ,[Puestos].[Descripcion] AS DescripcionPuesto
            FROM
                [DetalleEstructuraMovilizacion]
            INNER JOIN [Puestos]
                ON [DetalleEstructuraMovilizacion].[IdPuesto] = [Puestos].[IdPuesto]
            WHERE
                [DetalleEstructuraMovilizacion].[IdPuestoDepende] = ';

        $nodosDependientes = Yii::$app->db->createCommand($sqlNodosDependientes.$idNodo)->queryAll();

        if (count($nodosDependientes) > 0) {
            foreach ($nodosDependientes as $nodo) {
                $ocupado = 0;
                $vacante = 0;

                if($nodo['IdPersonaPuesto'] == '00000000-0000-0000-0000-000000000000') {
                    $vacante = 1;
                } else {
                    $ocupado = 1;
                }

                $cantidadesNodo = array();
                $cantidadesNodo['Puesto']    = $nodo['DescripcionPuesto'];
                $cantidadesNodo['Total']     = (int)$tablaResumen[$nodo['Nivel']]['Total']    + 1;
                $cantidadesNodo['Ocupados']  = (int)$tablaResumen[$nodo['Nivel']]['Ocupados'] + $ocupado;
                $cantidadesNodo['Vacantes']  = (int)$tablaResumen[$nodo['Nivel']]['Vacantes'] + $vacante;

                if ($tablaResumen[$nodo['Nivel']]['Total'] != 0) {
                    $cantidadesNodo['Avances %'] = $tablaResumen[$nodo['Nivel']]['Ocupados'] / $tablaResumen[$nodo['Nivel']]['Total'] * 100;
                } else {
                    $cantidadesNodo['Avances %'] = 0;
                }

                $cantidadesNodo['Avances %'] = round($cantidadesNodo['Avances %']);

                $tablaResumen[$nodo['Nivel']] = $cantidadesNodo;

                self::buildResumenNodo($nodo['IdNodoEstructuraMov'], $tablaResumen);
            }
        }
    }

    /**
     * Obtiene la meta proyectada para cada promotor
     *
     * @param INT $idNodoPadre
     * @return INT Meta para la estructura dependiente del nodoPadre
     */
    public static function getMetaByPromotor($idNodoPadre)
    {
        $sql = "SELECT SUM([Meta]) AS MetaByPromotor
            FROM [DetalleEstructuraMovilizacion]
            WHERE ([IdPuesto] = 7 AND [Dependencias] LIKE '%|".$idNodoPadre."|%') OR
                [IdNodoEstructuraMov] = ".$idNodoPadre;

        $meta = Yii::$app->db->createCommand($sql)->queryOne();

        return (int) $meta['MetaByPromotor'];
    }

    /**
     * Obtiene la meta proyectada para cada jefe de seccion
     *
     * @param INT $idNodoPadre
     * @return INT Meta para la estructura dependiente del nodoPadre
     */
    public static function getMetaBySeccion($idNodoPadre)
    {
        $sql = "SELECT SUM([MetaAlcanzar]) AS MetaPorSeccion
            FROM [DetalleEstructuraMovilizacion]
            INNER JOIN [CSeccion]
            ON [DetalleEstructuraMovilizacion].[IdSector] = [CSeccion].[IdSector]
            WHERE ([DetalleEstructuraMovilizacion].[IdPuesto] = 5 AND
            [DetalleEstructuraMovilizacion].[Dependencias] LIKE '%|".$idNodoPadre."|%') OR
            [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov] = ".$idNodoPadre;

        $meta = Yii::$app->db->createCommand($sql)->queryOne();

        return (int) $meta['MetaPorSeccion'];
    }

     /**
     * Obtiene la meta proyectada para cada jefe de seccion
     *
     * @param INT $idNodoPadre
     * @return INT Meta para la estructura dependiente del nodoPadre
     */
    public static function getAvanceMeta($idNodoPadre)
    {
        $metaPromotor = static::getMetaByPromotor($idNodoPadre);
        $countPromocion = static::getCountPromovidos($idNodoPadre);
        $metaAvance = 0;

        if ($metaPromotor != 0) {
            $metaAvance = round($countPromocion / $metaPromotor * 100);
        }

        return $metaAvance;
    }

    /**
     * Obtiene el número de promovidos a un determinado nodo de la estructura
     *
     * @param INT $idNodoPadre
     * @return INT Número de promovidos
     */
    public static function getCountPromovidos($idNodoPadre)
    {
        $sql = "SELECT COUNT([IdpersonaPromovida]) AS promovidos FROM [Promocion] WHERE [IdPuesto] IN (
            SELECT [IdNodoEstructuraMov] FROM [DetalleEstructuraMovilizacion]
            WHERE [DetalleEstructuraMovilizacion].[Dependencias] LIKE '%|".$idNodoPadre."|%' OR
            [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov] = ".$idNodoPadre.")";

        $countPromocion = Yii::$app->db->createCommand($sql)->queryOne();

        if (!$countPromocion) {
            return 0;
        } else {
            return $countPromocion['promovidos'];
        }
    }
}
