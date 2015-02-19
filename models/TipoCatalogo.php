<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "TipoCatalogo".
 *
 * @property integer $IdTipoCatalogo
 * @property string $Descripcion
 */
class TipoCatalogo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'TipoCatalogo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdTipoCatalogo'], 'integer'],
            [['Descripcion'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdTipoCatalogo' => 'Id Tipo Catalogo',
            'Descripcion' => 'Descripcion',
        ];
    }
}
