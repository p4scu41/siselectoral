<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Permisos".
 *
 * @property string $IdPerfilUsuario
 * @property string $IdModulo
 * @property integer $IdEstructuraMov
 * @property integer $TipoPermiso
 * @property string $usrActualiza
 * @property string $FechaActualiza
 */
class Permisos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Permisos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdPerfilUsuario', 'IdModulo'], 'required'],
            [['IdPerfilUsuario', 'IdModulo', 'usrActualiza'], 'string'],
            [['IdEstructuraMov', 'TipoPermiso'], 'integer'],
            [['FechaActualiza'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdPerfilUsuario' => 'Id Perfil Usuario',
            'IdModulo' => 'Id Modulo',
            'IdEstructuraMov' => 'Id Estructura Mov',
            'TipoPermiso' => 'Tipo Permiso',
            'usrActualiza' => 'Usr Actualiza',
            'FechaActualiza' => 'Fecha Actualiza',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['IdPerfilUsuario', 'IdModulo', 'TipoPermiso'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getPerfil()
    {
        return $this->hasOne(Perfiles::className(), ['IdPerfil' => 'IdPerfilUsuario']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getModulo()
    {
        return $this->hasOne(ModulosSistema::className(), ['IdModulo' => 'IdModulo']);
    }
}
