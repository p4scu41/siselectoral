<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "PREP_Tipo_Eleccion".
 *
 * @property integer $id_tipo_eleccion
 * @property string $descripcion
 */
class PREPTipoEleccion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PREP_Tipo_Eleccion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descripcion'], 'required'],
            [['descripcion'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_tipo_eleccion' => 'Id Tipo Eleccion',
            'descripcion' => 'Descripcion',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id_tipo_eleccion'];
    }
}
