<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Perfiles".
 *
 * @property string $IdPerfil
 * @property string $Nombre
 * @property string $Descripcion
 * @property integer $Visualizar
 */
class Perfiles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Perfiles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdPerfil'], 'required'],
            [['IdPerfil', 'Nombre', 'Descripcion'], 'string'],
            [['Visualizar'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdPerfil' => 'Id Perfil',
            'Nombre' => 'Nombre',
            'Descripcion' => 'Descripcion',
            'Visualizar' => 'Visualizar',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['IdPerfil'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getPermisos()
    {
        return $this->hasMany(Permisos::className(), ['IdPerfilUsuario' => 'IdPerfil']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getUsuarios()
    {
        return $this->hasMany(Usuarios::className(), ['IdPerfil' => 'IdPerfil']);
    }
}
