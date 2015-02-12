<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "EstadoCivil".
 *
 * @property integer $ID
 * @property string $DESCRIPCION
 */
class EstadoCivil extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'EstadoCivil';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['DESCRIPCION'], 'required'],
            [['DESCRIPCION'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'DESCRIPCION' => 'Descripcion',
        ];
    }
}
