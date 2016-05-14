<?php

namespace app\models;

use Yii;
use app\models\Puestos;
use app\models\PadronGlobal;
use app\helpers\PerfilUsuario;
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

        if (PerfilUsuario::isCapturista() || PerfilUsuario::isLecturaZona()) {
            if (empty($filtros['IdPuestoDepende'])) {
                $filtros['IdNodoEstructuraMov'] = Yii::$app->user->identity->getIdNodoEstructura();
            }
        }

        if(empty($filtros['IdPuesto']) && empty($filtros['IdPuestoDepende']) && !isset($filtros['IdNodoEstructuraMov'])) {
            $filtros['IdPuesto'] = $this->getMaxPuestoOnMuni($filtros['Municipio']);
        }

        $find = $this->find()->where($filtros);

        if ($alterna == false) {
            $find = $find->andWhere('(IdOrganizacion = -1 OR IdPuesto=5)');
        } else {
            $find = $find->andWhere('(IdOrganizacion != -1 AND IdPuesto!=5)');
        }

        if ( strtolower(Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil) == strtolower(Yii::$app->params['idDistrito']) ) {
            $find = $find->innerJoin('CSeccion', 'DetalleEstructuraMovilizacion.Municipio = CSeccion.IdMunicipio AND
                        DetalleEstructuraMovilizacion.IdSector = CSeccion.IdSector AND
                        CSeccion.DistritoLocal = '.Yii::$app->user->identity->distrito);
                /*$find->joinWith([
                'cSeccion' => function ($query) {
                    $query->onCondition([
                        'DetalleEstructuraMovilizacion.Municipio' => 'CSeccion.IdMunicipio',
                        'DetalleEstructuraMovilizacion.IdSector' => 'CSeccion.IdSector',
                        'CSeccion.DistritoLocal' => 1,
                    ]);
                },
            ]);*/
        } else if ( strtolower(Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil) == strtolower(Yii::$app->params['idDistritoFederal']) ) {
            $find = $find->innerJoin('CSeccion', 'DetalleEstructuraMovilizacion.Municipio = CSeccion.IdMunicipio AND
                        DetalleEstructuraMovilizacion.IdSector = CSeccion.IdSector AND
                        CSeccion.DistritoFederal = '.Yii::$app->user->identity->distrito);
            /*$find = $find->joinWith([
                'cSeccion' => function ($query) {
                    $query->onCondition([
                        'DetalleEstructuraMovilizacion.Municipio' => 'CSeccion.IdMunicipio',
                        'DetalleEstructuraMovilizacion.IdSector' => 'CSeccion.IdSector',
                        'CSeccion.DistritoFederal' => 1,
                    ]);
                },
            ]);*/
        }

        $child = $find->orderBy('Descripcion')->all();

        foreach ($child as $row) {
            $puesto = Puestos::findOne(['IdPuesto' => $row->IdPuesto]);

            $where = 'IdPuestoDepende = '.$row->IdNodoEstructuraMov;

            if ($alterna == false) {
                $where .= ' AND (IdOrganizacion = -1 OR IdPuesto=5)';
            } else {
                $where .= ' AND (IdOrganizacion != -1 AND IdPuesto!=5)';
            }

            $count = $this->find()->where($where)->count();
            $persona = $row->personaPuesto;
            $tree .= '{"key": "'.$row->IdNodoEstructuraMov.'", "title": "'.$puesto->Descripcion.' '.($persona ? ' - '.str_replace('\\', 'Ñ',$persona->nombreCompleto) : '').' '.
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
            $where .= ' AND (IdOrganizacion = -1 OR IdPuesto=5)';
        }

        $count = $this->find()->where($where)->count();

        if ($count > 0) {
            $child = $this->find()->where($where)->orderBy('Descripcion')->all();

            foreach ($child as $row) {
                $puesto = Puestos::findOne(['IdPuesto' => $row->IdPuesto]);
                $where = 'IdPuestoDepende = '.$row->IdNodoEstructuraMov;

                if ($alterna == false) {
                    $where .= ' AND (IdOrganizacion = -1 OR IdPuesto=5)';
                }

                $count = $this->find()->where($where)->count();

                $nombrePromotor = '';
                // Para promotor y coordinador de promotores agregar el nombre
                //if (($puesto->IdPuesto == 7 || $puesto->IdPuesto == 6) && $row->IdPersonaPuesto!='00000000-0000-0000-0000-000000000000') {
                if ($row->IdPersonaPuesto!='00000000-0000-0000-0000-000000000000') {
                    $persona = PadronGlobal::find()->where('[CLAVEUNICA] = \''.$row->IdPersonaPuesto.'\'')->one();
                    if ($persona != null) {
                        $nombrePromotor = $persona->getNombreCompleto();
                    }
                }

                $tree .= '{"key": "'.$row->IdNodoEstructuraMov.'", "title": "'.$puesto->Descripcion.' - '.$row->Descripcion.' '.str_replace('\\', 'Ñ', $nombrePromotor).
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
            $persona = $row->personaPuesto;
            $tree .= '{"key": "'.$nodo->IdNodoEstructuraMov.'", "title": "'.$nodo->Descripcion.' '.($persona ? ' - '.str_replace('\\', 'Ñ',$persona->nombreCompleto) : '').'",'.
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
                [Puestos] ON [Puestos].[IdPuesto] = [DetalleEstructuraMovilizacion].[IdPuesto] ';
        
        if ( strtolower(Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil) == strtolower(Yii::$app->params['idDistrito']) ) {
            $sql .= 'INNER JOIN [CSeccion] ON
                [DetalleEstructuraMovilizacion].[Municipio] = [CSeccion].[IdMunicipio] AND
                [DetalleEstructuraMovilizacion].[IdSector] = [CSeccion].[IdSector] AND
                [CSeccion].[DistritoLocal] = '.Yii::$app->user->identity->distrito;
        } else if ( strtolower(Yii::$app->user->identity->getPerfil()->primaryModel->IdPerfil) == strtolower(Yii::$app->params['idDistritoFederal']) ) {
            $sql .= 'INNER JOIN [CSeccion] ON
                [DetalleEstructuraMovilizacion].[Municipio] = [CSeccion].[IdMunicipio] AND
                [DetalleEstructuraMovilizacion].[IdSector] = [CSeccion].[IdSector] AND
                [CSeccion].[DistritoFederal] = '.Yii::$app->user->identity->distrito;
        }
        
        $sql .= ' WHERE
            [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.
            ' ORDER BY [Nivel] ASC';

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
            'Total' => (int)$metaPromovidosPromotor['MetaByPromotor'],
            'Ocupados' => $cantidadPromovidosPromotor['promovidos'],
            'Vacantes' => ((int)$metaPromovidosPromotor['MetaByPromotor']) - ((int)$cantidadPromovidosPromotor['promovidos']),
            'Avances %' => $avancePromovidos,
        ];

        for($i=0; $i<count($totales); $i++) {
            $totales[$i]['Ocupados'] = (int) $ocupados[$totales[$i]['IdPuesto']];
            $totales[$i]['Vacantes'] = (int) (isset($vacantes[$totales[$i]['IdPuesto']]) ? $vacantes[$totales[$i]['IdPuesto']] : 0);
            //unset($totales[$i]['IdPuesto']);

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
    public static function getNodosDependientes($parametros, $withFoto=false, $withEstrucAlter=false)
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
            WHERE '.($withEstrucAlter ? '' : '[IdOrganizacion] = -1 AND ').'
                '.$filtros.'
            ORDER BY [Puestos].[Nivel]';

        $resultPuestos = Yii::$app->db->createCommand($sqlPuestos)->queryAll();

        $result = null;

        if ($resultPuestos) {
            foreach ($resultPuestos as $nodo) {
                if($nodo['IdPersonaPuesto'] != '00000000-0000-0000-0000-000000000000') {
                    $persona = Yii::$app->db->createCommand('SELECT REPLACE(([NOMBRE]+ \' \' +[APELLIDO_PATERNO]+ \' \' +[APELLIDO_MATERNO]), \'\\\', \'Ñ\') AS NOMBRECOMPLETO, SEXO '
                                                ."FROM [PadronGlobal] WHERE [CLAVEUNICA] = '".$nodo['IdPersonaPuesto']."'")->queryOne();
                    $foto = ['foto'=>''];
                    if ($withFoto) {
                        $foto['foto'] = PadronGlobal::getFotoByUID($nodo['IdPersonaPuesto'], $persona['SEXO']);
                    }

                    if (!$persona) {
                        $persona = ['NOMBRECOMPLETO' => 'No asignado'];
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
        $nodo = DetalleEstructuraMovilizacion::find()->where('IdNodoEstructuraMov = '.$idNodo)->one();

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
                '.($nodo['IdPuesto'] == 7 ? ' OR IdNodoEstructuraMov = '.$idNodo : '').'
            GROUP BY [Puestos].[Descripcion], [Puestos].[Nivel]
            ORDER BY [Puestos].[Nivel]';

        $tablaResumen = Yii::$app->db->createCommand($sqlResumenNodo)->queryAll();

        if (count($tablaResumen) > 0) {
            $metaPromovidosPromotor = static::getMetaByPromotor($idNodo, $nodo['IdPuesto'] == 7);
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
    public static function getMetaByPromotor($idNodoPadre, $includeNodo = false)
    {
        $sql = "SELECT SUM([Meta]) AS MetaByPromotor
            FROM [DetalleEstructuraMovilizacion]
            WHERE ([IdPuesto] = 7 AND [Dependencias] LIKE '%|".$idNodoPadre."|%') "
                .($includeNodo ? "OR [IdNodoEstructuraMov] = ".$idNodoPadre : ""); // Revisar el calculo de meta de promoción

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

    /**
     * Obtiene todas las estructuras alterna dependientes del nodo superior
     *
     * @param Int $idNodo
     * @return JSON personalizado para fancytree
     */
    public function getEstrucAlterna($idMuni, $idNodo)
    {

        $sql = 'SELECT * FROM DetalleEstructuraMovilizacion WHERE (IdOrganizacion IN ('
            . 'SELECT
                    distinct [Organizaciones].[IdOrganizacion]
                FROM
                    [IntegrantesOrganizaciones]
                INNER JOIN
                    [PadronGlobal] ON
                    [PadronGlobal].[CLAVEUNICA] = [IntegrantesOrganizaciones].[IdPersonaIntegrante]
                INNER JOIN
                    [Organizaciones] ON
                    [Organizaciones].[IdOrganizacion] = [IntegrantesOrganizaciones].[IdOrganizacion]
                WHERE
                    [PadronGlobal].[MUNICIPIO] = '.$idMuni
            . ')  AND IdPuesto!=5) AND Municipio = '.$idMuni.
                ' AND IdPuestoDepende = '.$idNodo.' '.
            ' ORDER BY Descripcion';

        $alternas = Yii::$app->db->createCommand($sql)->queryAll();

        $tree = '[';

        foreach($alternas as $nodo) {
            $count = 0;
            $puesto = Puestos::find()->where(['IdPuesto'=>$nodo['IdPuesto']])->one();
            $org = Organizaciones::find()->where(['IdOrganizacion'=>$nodo['IdOrganizacion']])->one();

            if ($org) {
                $count = Organizaciones::getCountIntegrantes($nodo['IdOrganizacion'], $idMuni);
            }

            $tree .= '{"key": "'.$nodo['IdNodoEstructuraMov'].'", "title": "'.$puesto->Descripcion.' - '.$nodo['Descripcion'].' '.('['.$count.']').'", '.
                    '"data": { "IdPuesto": "'.$puesto->IdPuesto.'", "puesto": "'.$puesto->Descripcion.'", "persona": "'.$nodo['IdPersonaPuesto'].'",'.
                    ' "iconclass": "glyphicon glyphicon-user"}, "folder": true, "lazy": true},';
        }

        $tree .= ']';

        return str_replace('},]', '}]', $tree);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPuesto()
    {
        return $this->hasOne(Puestos::className(), ['IdPuesto' => 'IdPuesto']);
    }

    public static function getSeccionNodo($idNodo)
    {
        $sqlSeccion = 'SELECT CSeccion.NumSector FROM DetalleEstructuraMovilizacion
                        INNER JOIN CSeccion ON CSeccion.IdSector = DetalleEstructuraMovilizacion.IdSector
                        WHERE IdNodoEstructuraMov = '.$idNodo;
        $seccion = Yii::$app->db->createCommand($sqlSeccion)->queryOne();

        return $seccion['NumSector'];
    }
    
    public static function getSeccionesNodo($idNodo)
    {
        $sqlSecciones = 'SELECT DISTINCT [CSeccion].[NumSector]
            FROM [DetalleEstructuraMovilizacion]
            INNER JOIN [CSeccion] ON
                [DetalleEstructuraMovilizacion].[IdSector] = [CSeccion].[IdSector]
            WHERE [Dependencias] LIKE  \'%|'.$idNodo.'|%\' OR [IdNodoEstructuraMov] = '.$idNodo.'
            ORDER BY [NumSector]';
        
        $secciones = Yii::$app->db->createCommand($sqlSecciones)->queryAll();
        $secciones = ArrayHelper::map($secciones, 'NumSector', 'NumSector');

        return $secciones;
    }

    public static function getSeccionesMuni($idMuni)
    {
        $sqlSecciones = 'SELECT DISTINCT [CSeccion].[NumSector]
            FROM [DetalleEstructuraMovilizacion]
            INNER JOIN [CSeccion] ON
                [DetalleEstructuraMovilizacion].[IdSector] = [CSeccion].[IdSector]
            WHERE [Municipio] = '.$idMuni.'
            ORDER BY [NumSector]';

        $secciones = Yii::$app->db->createCommand($sqlSecciones)->queryAll();

        return $secciones;
    }

    public static function getJsmuni($idMuni)
    {
        $sqlSecciones = 'SELECT
                [IdNodoEstructuraMov]
                ,[NumSector]
                ,[CSeccion].[ZonaMunicipal]
                ,CASE
                WHEN [PadronGlobal].[CLAVEUNICA] IS NULL
                    THEN \'NO ASIGNADO\'
                    ELSE REPLACE(([PadronGlobal].[NOMBRE]+\' \'+[PadronGlobal].[APELLIDO_PATERNO]+\' \'+[PadronGlobal].[APELLIDO_MATERNO]), \'\\\', \'Ñ\')
                END AS NOMBRECOMPLETO
                ,[TELCASA]
                ,[TELMOVIL]
            FROM [DetalleEstructuraMovilizacion]
            INNER JOIN [CSeccion] ON
                [DetalleEstructuraMovilizacion].[IdSector] = [CSeccion].[IdSector]
            LEFT JOIN [PadronGlobal] ON
                [PadronGlobal].[CLAVEUNICA] = [DetalleEstructuraMovilizacion].[IdPersonaPuesto]
            WHERE [IdPuesto] = 5 AND [DetalleEstructuraMovilizacion].[Municipio] = '.$idMuni.'
            ORDER BY [NumSector]';

        $secciones = Yii::$app->db->createCommand($sqlSecciones)->queryAll();

        return $secciones;
    }

    public static function getpuestosfaltantesbyseccion($muni, $puesto)
    {
        if ($puesto == 2 || $puesto == 4 || $puesto == 8) {
            $sqlPuestos = 'SELECT
                [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov] AS idNodo,
                [Puestos].[Descripcion]+\' - \'+
                [DetalleEstructuraMovilizacion].[Descripcion] AS Puesto
            FROM
                [DetalleEstructuraMovilizacion]
            INNER JOIN [Puestos]
                ON [DetalleEstructuraMovilizacion].[IdPuesto] = [Puestos].[IdPuesto]
            WHERE
                [DetalleEstructuraMovilizacion].[Municipio] = '.$muni.' AND
                [DetalleEstructuraMovilizacion].[IdPersonaPuesto] = \'00000000-0000-0000-0000-000000000000\' AND
                [DetalleEstructuraMovilizacion].[IdPuesto] = \''.$puesto.'\'
            ORDER BY [DetalleEstructuraMovilizacion].[Descripcion]';

            $puestos = Yii::$app->db->createCommand($sqlPuestos)->queryAll();
        } else {
            $sqlPuestos = 'SELECT [DetalleEstructuraMovilizacion].[IdSector],
                [CSeccion].[NumSector] as Seccion, COUNT(*) Faltantes
                FROM [DetalleEstructuraMovilizacion]
                INNER JOIN [CSeccion] ON
                    [CSeccion].[IdMunicipio] = '.$muni.' AND
                    [CSeccion].[IdSector] = [DetalleEstructuraMovilizacion].[IdSector]
                INNER JOIN [Puestos] ON
                    [Puestos].[IdPuesto] = [DetalleEstructuraMovilizacion].[IdPuesto]
                WHERE [Municipio] = '.$muni.' AND [IdPersonaPuesto] = \'00000000-0000-0000-0000-000000000000\'
                AND [DetalleEstructuraMovilizacion].[IdPuesto] = \''.$puesto.'\'
                GROUP BY [DetalleEstructuraMovilizacion].[IdSector],[CSeccion].[NumSector]
                ORDER BY [NumSector]';

            $result = Yii::$app->db->createCommand($sqlPuestos)->queryAll();

            $puestos = [];

            if ($result != null) {
                foreach ($result as $nodo) {
                    $js =  Yii::$app->db->createCommand('SELECT [IdNodoEstructuraMov]
                        FROM [DetalleEstructuraMovilizacion]
                        WHERE [Municipio] = '.$muni.' AND [IdPuesto] = 5 AND [IdSector] = '.$nodo['IdSector'])->queryOne();

                    if ($js != null) {
                        $puestos[] = [
                            'idNodo' => $js['IdNodoEstructuraMov'],
                            'Seccion' => $nodo['Seccion'],
                            'Faltantes' => $nodo['Faltantes']
                        ];
                    } else {
                        $puestos[] = [
                            'idNodo' => 0,
                            'Seccion' => $nodo['Seccion'],
                            'Faltantes' => $nodo['Faltantes']
                        ];
                    }
                }
            }
        }

        return $puestos;
    }

    public static function existsPersonaOnEstructura($claveunica)
    {
        $sql = 'SELECT
                [Puestos].[Descripcion] as puesto,
                [DetalleEstructuraMovilizacion].[Descripcion] as estructura
            FROM [DetalleEstructuraMovilizacion]
            INNER JOIN [Puestos] ON
            [Puestos].[IdPuesto] = [DetalleEstructuraMovilizacion].[IdPuesto]
            WHERE [IdPersonaPuesto] = \''.$claveunica.'\'
            AND [IdPersonaPuesto] != \'00000000-0000-0000-0000-000000000000\'';

        $persona = Yii::$app->db->createCommand($sql)->queryOne();

        return $persona;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCSeccion()
    {
        return $this->hasMany(CSeccion::className(), ['IdMunicipio' => 'Municipio']);
    }

    /**
     * Obtiene los nodos padres de un nodo especifico
     *
     * @param type $idNodo
     * @return type
     */
    public static function getParents($idNodo, $tipo)
    {
        $parents = [];
        $nodosPadres = [];

        $query = 'SELECT [IdNodoEstructuraMov]
                ,[IdPuesto]
                ,[IdPuestoDepende]
                ,[IdPersonaPuesto]
                ,[Dependencias]
                ,[Descripcion]
            FROM [DetalleEstructuraMovilizacion]
            WHERE IdNodoEstructuraMov = ';

        $nodo = Yii::$app->db->createCommand($query.$idNodo)->queryOne();
        $parents[] = $nodo['IdPuestoDepende'];
        $ids = '';

        do {
            $idNodoPadre = array_pop($parents);

            if ($idNodoPadre != null) {
                $padre = Yii::$app->db->createCommand($query.$idNodoPadre)->queryOne();

                if ($padre != null) {
                    $nodosPadres[] = ['IdNodoEstructuraMov' => $padre['IdNodoEstructuraMov'], 'Descripcion' => $padre['Descripcion']];
                    $ids .= $padre['IdNodoEstructuraMov'].'~';
                    $parents[] = $padre['IdPuestoDepende'];
                }
            }
        } while (count($parents) != 0);

        if ($tipo) {
            $nodosPadres = substr($ids, 0, -1);  
        }

        return $nodosPadres;
    }

    public static function getPromotoresByNodo($idNodo, $participacion=true) {
        $query = 'SELECT
            [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov] AS idNodo,
            REPLACE(([NOMBRE]+ \' \' +[APELLIDO_PATERNO]+ \' \' +[APELLIDO_MATERNO]), \'\\\', \'Ñ\') AS NOMBRECOMPLETO,
            COUNT([Promocion].[IdpersonaPromovida]) AS faltantes
        FROM
            [DetalleEstructuraMovilizacion]
        INNER JOIN [PadronGlobal] ON
            [DetalleEstructuraMovilizacion].[IdPersonaPuesto] = [PadronGlobal].[CLAVEUNICA]
        INNER JOIN [Promocion] ON
            [DetalleEstructuraMovilizacion].[IdNodoEstructuraMov] = [Promocion].[IdPuesto]
            '.($participacion ? 'AND [Promocion].[Participacion] IS NULL' : '').'
        WHERE
            [DetalleEstructuraMovilizacion].[IdPersonaPuesto] != \'00000000-0000-0000-0000-000000000000\' AND
            [DetalleEstructuraMovilizacion].[IdPuesto] = 7 AND
            [DetalleEstructuraMovilizacion].[Dependencias] LIKE \'%|'.$idNodo.'|%\'
        GROUP BY
            [IdNodoEstructuraMov], [NOMBRE],[APELLIDO_PATERNO],[APELLIDO_MATERNO]
        ORDER BY
            [NOMBRE],[APELLIDO_PATERNO],[APELLIDO_MATERNO]';

        $promotores = Yii::$app->db->createCommand($query)->queryAll();

        return $promotores;
    }

    public static function getCoordMuni($idMuni)
    {
        $sql = 'SELECT * FROM [DetalleEstructuraMovilizacion]
            WHERE [IdPuesto] = 2 AND [Municipio] = '.(int)$idMuni;

        $coordMuni = Yii::$app->db->createCommand($sql)->queryOne();

        return $coordMuni;
    }

    public static function getInfoNodo($idNodo)
    {
        $sql = 'SELECT
            [CLAVEUNICA]
            ,REPLACE(([NOMBRE]+ \' \' +[APELLIDO_PATERNO]+ \' \' +[APELLIDO_MATERNO]), \'\\\', \'Ñ\') AS NOMBRECOMPLETO
            ,[SEXO]
            ,[TELCASA]
            ,[TELMOVIL]
            ,[DES_LOC]+\' \'+[NOM_LOC] AS COLONIA
            ,[NUM_INTERIOR]
            ,[NUM_EXTERIOR]
            ,[CODIGO_POSTAL]
            ,[CORREOELECTRONICO]
            ,[DOMICILIO]
            ,[IdPuesto]
            ,[IdPuestoDepende]
            ,[IdSector]
            ,[Meta]
            ,[ZonaMunicipal]
        FROM [DetalleEstructuraMovilizacion]
        INNER JOIN [PadronGlobal] ON
            [PadronGlobal].[CLAVEUNICA] = [DetalleEstructuraMovilizacion].[IdPersonaPuesto]
        WHERE [IdNodoEstructuraMov] = '.$idNodo;

        $result = Yii::$app->db->createCommand($sql)->queryOne();

        return $result;
    }

    public function getPersonaPuesto()
    {
        return $this->hasOne(PadronGlobal::className(), ['CLAVEUNICA' => 'IdPersonaPuesto']);
    }

    public static function getCoordinadorZona($zona)
    {
        $nodo = DetalleEstructuraMovilizacion::findOne(['Descripcion' => 'CZ' . str_pad($zona, 2, '0', STR_PAD_LEFT)]);

        if ($nodo) {
            return $nodo->personaPuesto;
        }
    }

}
