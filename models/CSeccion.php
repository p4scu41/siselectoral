<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "CSeccion".
 *
 * @property integer $IdSector
 * @property integer $IdMunicipio
 * @property string $NumSector
 * @property string $Domicilio
 * @property integer $MetaAlcanzar
 * @property integer $DistritoFederal
 * @property integer $IdTipoElec
 * @property integer $DistritoLocal
 * @property integer $TotalPadron
 * @property integer $TotalParticipacion
 * @property string $PorParticipacion
 * @property integer $SeccionAnterior
 * @property integer $ZonaMunicipal
 */
class CSeccion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'CSeccion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdSector'], 'required'],
            [['IdSector', 'IdMunicipio', 'MetaAlcanzar', 'DistritoFederal', 'IdTipoElec', 'DistritoLocal', 'TotalPadron', 'TotalParticipacion', 'SeccionAnterior', 'ZonaMunicipal'], 'integer'],
            [['NumSector', 'Domicilio'], 'string'],
            [['PorParticipacion'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdSector' => 'Id Sector',
            'IdMunicipio' => 'Id Municipio',
            'NumSector' => 'Num Sector',
            'Domicilio' => 'Domicilio',
            'MetaAlcanzar' => 'Meta Alcanzar',
            'DistritoFederal' => 'Distrito Federal',
            'IdTipoElec' => 'Id Tipo Elec',
            'DistritoLocal' => 'Distrito Local',
            'TotalPadron' => 'Total Padron',
            'TotalParticipacion' => 'Total Participacion',
            'PorParticipacion' => 'Por Participacion',
            'SeccionAnterior' => 'Seccion Anterior',
            'ZonaMunicipal' => 'Zona Municipal',
        ];
    }
}
