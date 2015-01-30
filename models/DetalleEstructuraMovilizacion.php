<?php

namespace app\models;

use Yii;

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
    public $tree = null;
    
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
    
    public function getTree($idNodo) {
        $this->tree = '<div id="PositionTree"><ul id="treeData" style="display: none;">';
        
        $this->tree .= $this->buildTree($idNodo);

        $this->tree .= '<ul></div>';

        return $this->tree;
    }
    
    public function buildTree($idNodo) {
        $nodo = $this->find()->where(['IdNodoEstructuraMov' => $idNodo])->one();
        $tree = '';

        $count = $this->find()
                ->where(['IdPuestoDepende' => $idNodo])
                ->count();
        
        if ($count > 0) {
            $tree .='<li class="folder" data-iconclass="glyphicon glyphicon-user">'.$nodo->IdNodoEstructuraMov.' '.$nodo->Descripcion.' ['.$count.']<ul>';
            $child = $this->find()->where(['IdPuestoDepende' => $idNodo])->all();;

            foreach ($child as $row) {
                $tree .= $this->buildTree($row->IdNodoEstructuraMov);
            }

            $tree .='</ul></li>';
        } else {
            $tree .='<li data-iconclass="fa fa-user">'.$nodo->IdNodoEstructuraMov.'</li>';
        }

        return $tree;
    }
}
