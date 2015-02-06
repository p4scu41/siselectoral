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
    public function getTree($filtros) {
        if(empty(array_filter($filtros))) {
            return '[]';
        }

        $tree = '[';

        if(empty($filtros['IdPuesto'])) {
            $filtros['IdPuesto'] = $this->getMaxPuestoOnMuni($filtros['Municipio']);
        }

        $child = $this->find()->where($filtros)->all();;

        foreach ($child as $row) {
            $puesto = Puestos::findOne(['IdPuesto' => $row->IdPuesto]);

            $count = $this->find()
                ->where(['IdPuestoDepende' => $row->IdNodoEstructuraMov])
                ->count();

            $tree .= '{"key": "'.$row->IdNodoEstructuraMov.'", "title": "'.$puesto->Descripcion.' - '.$row->Descripcion.' '.
                        ($count > 0 ? '['.$count.']' : '').'", '.
                        '"data": { "puesto": "'.$puesto->Descripcion.'", "persona": "'.$row->IdPersonaPuesto.'", "iconclass": ';

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
    public function getBranch($idNodo) {
        $tree = '[';

        $count = $this->find()
                ->where(['IdPuestoDepende' => $idNodo])
                ->count();

        if ($count > 0) {
            $child = $this->find()->where(['IdPuestoDepende' => $idNodo])->all();;

            foreach ($child as $row) {
                $puesto = Puestos::findOne(['IdPuesto' => $row->IdPuesto]);

                $count = $this->find()
                    ->where(['IdPuestoDepende' => $row->IdNodoEstructuraMov])
                    ->count();

                $tree .= '{"key": "'.$row->IdNodoEstructuraMov.'", "title": "'.$puesto->Descripcion.' - '.$row->Descripcion.' '.
                            ($count > 0 ? '['.$count.']' : '').'", '.
                            '"data": { "puesto": "'.$puesto->Descripcion.'", "persona": "'.$row->IdPersonaPuesto.'", "iconclass": ';

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
     * Construye todo el arbol a partir de un nodo raíz
     *
     * @param Int $idNodo
     * @return JSON personalizado para fancytree
     * @deprecated since version 1
     */
    public function buildTree($idNodo) {
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
    public function getMaxPuestoOnMuni($idMuni) {
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
     * Obtiene los nodos dependientes
     *
     * @return Array Persona
     */
    public function getDependientes() {
        $nodosDependientes = $this->find()->where('IdPersonaPuesto != \'00000000-0000-0000-0000-000000000000\' AND '.
                'IdPuestoDepende = '.$this->IdNodoEstructuraMov)->all();
        $personasDependientes = array();

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

        return $personasDependientes;
    }

    /**
     * Obtiene la cantidad y la descripción de los nodos dependientes
     *
     * @return Array Persona
     */
    public function getCountDepen() {
        $cantidad = null;

        $count = Yii::$app->db->createCommand('SELECT COUNT(*) AS total, [IdPuesto]
            FROM [DetalleEstructuraMovilizacion]
            WHERE [IdPuestoDepende] = '.$this->IdNodoEstructuraMov.
            ' GROUP BY [IdPuesto]')->queryOne();

        if($count != null) {
            $puesto = $this->findBySql('SELECT * FROM [Puestos] WHERE [IdPuesto] = \''.
                                $count['IdPuesto'].'\'')->one();
            $descrip = explode(' ', ucwords(mb_strtolower($puesto->Descripcion)));

            if ( count($descrip)>1) {
                $descrip[0] = substr($descrip[0], 0, 1).'.';
            }
            
            $cantidad = Array('cantidad'=>$count['total'], 'puesto'=>implode($descrip, ' '));
        }

        return $cantidad;
    }

    /**
     * Obtiene el nodo jefe del objeto actual
     *
     * @return Array Persona
     */
    public function getJefe() {
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
     * Obtiene los nodos dependientes
     *
     * @param Int $idNodoEstruc IdPuestoDepende
     * @return Array Persona
     */
    public function getSecciones() {
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

    public static function getResumen($idMuni) {
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

        $totales = Yii::$app->db->createCommand($sqlTotales)->queryAll();
        $ocupados = ArrayHelper::map(Yii::$app->db->createCommand($sqlOcupados)->queryAll(), 'IdPuesto', 'ocupado');
        $vacantes = ArrayHelper::map(Yii::$app->db->createCommand($sqlVacantes)->queryAll(), 'IdPuesto', 'vacante');

        $sumTotales = 0;
        $sumOcupados = 0;
        $sumVacantes = 0;

        for($i=0; $i<count($totales); $i++) {
            $totales[$i]['Ocupados'] = (int) $ocupados[$totales[$i]['IdPuesto']];
            $totales[$i]['Vacantes'] = (int) $vacantes[$totales[$i]['IdPuesto']];
            unset($totales[$i]['IdPuesto']);


            $sumTotales += $totales[$i]['Total'];
            $sumOcupados += $totales[$i]['Ocupados'];
            $sumVacantes += $totales[$i]['Vacantes'];
        }

        array_push($totales, array('Puesto'=>'Total',
                                   'Total'=>$sumTotales,
                                    'Ocupados'=>$sumOcupados,
                                    'Vacantes'=>$sumVacantes));

        return $totales;
    }
}
