<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Ocupacion".
 *
 * @property integer $ID
 * @property string $DESCRIPCION
 */
class Ocupacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Ocupacion';
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
