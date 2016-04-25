<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "AuditoriaEstructura".
 *
 * @property integer $IdNodoEstructuraMov
 * @property integer $Puesto
 * @property integer $Persona
 * @property integer $Seccion
 * @property integer $Celular
 * @property string $Observaciones
 * @property string $Fecha
 */
class AuditoriaEstructura extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'AuditoriaEstructura';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdNodoEstructuraMov'], 'required'],
            [['IdNodoEstructuraMov', 'Puesto', 'Persona', 'Seccion', 'Celular'], 'integer'],
            [['Observaciones'], 'string'],
            [['Fecha'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdNodoEstructuraMov' => 'Id Nodo Estructura Mov',
            'Puesto' => 'Puesto',
            'Persona' => 'Persona',
            'Seccion' => 'Seccion',
            'Celular' => 'Celular',
            'Observaciones' => 'Observaciones',
            'Fecha' => 'Fecha',
        ];
    }
}
