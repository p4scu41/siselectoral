<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ModulosSistema".
 *
 * @property string $IdModulo
 * @property string $Nombre
 */
class ModulosSistema extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ModulosSistema';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdModulo'], 'required'],
            [['IdModulo', 'Nombre'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdModulo' => 'Id Modulo',
            'Nombre' => 'Nombre',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['IdModulo'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getPermisos()
    {
        return $this->hasMany(Permisos::className(), ['IdModulo' => 'IdModulo']);
    }
}
