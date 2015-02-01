<?php

namespace app\models;

use Yii;
use app\models\Puestos;

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
        $tree = '[';
        
        if(empty($filtros['IdPuesto'])) {
            $filtros['IdPuesto'] = $this->getMaxPuestoOnMuni($filtros['Municipio']);
        }
        
        $child = $this->find()->where($filtros)->all();;

        foreach ($child as $row) {
            $puesto = Puestos::findOne(['IdPuesto' => $row->IdPuesto]);
            
            $tree .= '{"key": "'.$row->IdNodoEstructuraMov.'", "title": "'.$puesto->Descripcion.' - '.$row->Descripcion.'", '.
                        '"data": { "puesto": "'.$puesto->Descripcion.'", "persona": "'.$row->IdPersonaPuesto.'", "iconclass": ';

            $count = $this->find()
                ->where(['IdPuestoDepende' => $row->IdNodoEstructuraMov])
                ->count();

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
            
                $tree .= '{"key": "'.$row->IdNodoEstructuraMov.'", "title": "'.$puesto->Descripcion.' - '.$row->Descripcion.'", '.
                            '"data": { "puesto": "'.$puesto->Descripcion.'", "persona": "'.$row->IdPersonaPuesto.'", "iconclass": ';
                //$tree .= '{"key": "'.$row->IdNodoEstructuraMov.'", "title": "'.$row->Descripcion.'", "data": { "persona": "'.$row->IdPersonaPuesto.'", "iconclass": ';
                
                $count = $this->find()
                    ->where(['IdPuestoDepende' => $row->IdNodoEstructuraMov])
                    ->count();
                
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
    function getMaxPuestoOnMuni($idMuni) {
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
}
