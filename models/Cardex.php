<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Cardex".
 *
 * @property integer $IdCardex
 * @property string $CLAVEUNICA
 * @property integer $idOrganizacion
 * @property string $Nota
 * @property string $FechaInsercion
 * @property string $FechaActualizacion
 * @property string $usuarioactualiza
 * @property integer $IdElementoCatalogo
 */
class Cardex extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Cardex';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['IdCardex'], 'required'],
            [['IdCardex', 'idOrganizacion', 'IdElementoCatalogo'], 'integer'],
            [['CLAVEUNICA', 'Nota', 'usuarioactualiza'], 'string'],
            [['FechaInsercion', 'FechaActualizacion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdCardex' => 'Id Cardex',
            'CLAVEUNICA' => 'Claveunica',
            'idOrganizacion' => 'Id Organizacion',
            'Nota' => 'Nota',
            'FechaInsercion' => 'Fecha Insercion',
            'FechaActualizacion' => 'Fecha Actualizacion',
            'usuarioactualiza' => 'Usuarioactualiza',
            'IdElementoCatalogo' => 'Id Elemento Catalogo',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['IdCardex'];
    }
}
