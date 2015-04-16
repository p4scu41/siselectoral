<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "seguimiento_cambios".
 *
 * @property integer $id
 * @property integer $usuario
 * @property string $tabla
 * @property string $campo
 * @property string $valor_anterior
 * @property string $nuevo_valor
 * @property string $registro
 * @property integer $accion
 * @property string $detalles
 * @property string $fecha
 */
class SeguimientoCambios extends \yii\db\ActiveRecord
{
    const INSERT = 1;
    const DELETE = 2;
    const UPDATE = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seguimiento_cambios';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usuario', 'accion'], 'integer'],
            [['tabla', 'campo', 'valor_anterior', 'nuevo_valor', 'registro', 'detalles'], 'string'],
            [['fecha'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario' => 'Usuario',
            'tabla' => 'Tabla',
            'campo' => 'Campo',
            'valor_anterior' => 'Valor Anterior',
            'nuevo_valor' => 'Nuevo Valor',
            'registro' => 'Registro',
            'accion' => 'Accion',
            'detalles' => 'Detalles',
            'fecha' => 'Fecha',
        ];
    }
}
