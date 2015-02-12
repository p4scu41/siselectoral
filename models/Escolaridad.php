<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Escolaridad".
 *
 * @property integer $ID
 * @property string $DESCRIPCION
 */
class Escolaridad extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Escolaridad';
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
