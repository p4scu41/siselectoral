<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Puestos".
 *
 * @property integer $IdPuesto
 * @property string $Descripcion
 * @property string $Siglas
 * @property integer $Nivel
 */
class Puestos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Puestos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdPuesto'], 'required'],
            [['IdPuesto', 'Nivel'], 'integer'],
            [['Descripcion', 'Siglas'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdPuesto' => 'Id Puesto',
            'Descripcion' => 'Descripcion',
            'Siglas' => 'Siglas',
            'Nivel' => 'Nivel',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['IdPuesto'];
    }
}
