<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "PREP_Casilla".
 *
 * @property integer $id_casilla
 * @property string $descripcion
 */
class PREPCasilla extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PREP_Casilla';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descripcion'], 'required'],
            [['descripcion'], 'string'],
            [['descripcion'], 'trim']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_casilla' => 'Id Casilla',
            'descripcion' => 'Descripción',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id_casilla'];
    }
}
