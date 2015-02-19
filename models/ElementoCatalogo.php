<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ElementoCatalogo".
 *
 * @property integer $IdElementoCatalogo
 * @property integer $IdTipoCataogo
 * @property string $Clave
 * @property string $Descripcion
 * @property integer $Valor1
 * @property double $Valor2
 * @property string $Valor3
 */
class ElementoCatalogo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ElementoCatalogo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdElementoCatalogo', 'IdTipoCataogo', 'Valor1'], 'integer'],
            [['Clave', 'Descripcion', 'Valor3'], 'string'],
            [['Valor2'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdElementoCatalogo' => 'Id Elemento Catalogo',
            'IdTipoCataogo' => 'Id Tipo Cataogo',
            'Clave' => 'Clave',
            'Descripcion' => 'Descripcion',
            'Valor1' => 'Valor1',
            'Valor2' => 'Valor2',
            'Valor3' => 'Valor3',
        ];
    }
}
