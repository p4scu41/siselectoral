<?php

namespace app\models;

use Yii;
use app\helpers\generarPassword;

/**
 * This is the model class for table "Usuarios".
 *
 * @property integer $IdUsuario
 * @property integer $IdEstructuraMov
 * @property string $IdPersona
 * @property string $IdPerfil
 * @property string $login
 * @property string $password
 * @property string $Estado
 * @property string $usrActualiza
 */
class Usuarios extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    // No implementados -> 'enableAutoLogin' => false
    public $authKey;
    public $accessToken;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Usuarios';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdUsuario'], 'required'],
            [['IdUsuario', 'IdEstructuraMov'], 'integer'],
            [['IdPersona', 'IdPerfil', 'login', 'password', 'Estado', 'usrActualiza'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdUsuario' => 'Id Usuario',
            'IdEstructuraMov' => 'Id Estructura Mov',
            'IdPersona' => 'Persona',
            'IdPerfil' => 'Perfil',
            'login' => 'Usuario',
            'password' => 'Contraseña',
            'Estado' => 'Status',
            'usrActualiza' => 'Usr Actualiza',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['IdUsuario'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerfil()
    {
        return $this->hasOne(Perfiles::className(), ['IdPerfil' => 'IdPerfil']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersona()
    {
        return $this->hasOne(PadronGlobal::className(), ['CLAVEUNICA' => 'IdPersona']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNodoestructura()
    {
        $nodo = DetalleEstructuraMovilizacion::find()
                ->where(['IdPersonaPuesto' => $this->IdPersona]);
        return $nodo;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        // Estado 1 Activo
        // Estado 2 Inactivo
        $user = static::find()
                ->where('IdUsuario = :IdUsuario', [':IdUsuario' => $id])
                ->andWhere('Estado = :Estado', [':Estado' => 1])
                ->one();

        return isset($user) ? $user : null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // No implementado
        return null;
    }

    /**
     * Finds user by login
     *
     * @param  string      $login
     * @return static|null
     */
    public static function findByLogin($login)
    {
        $user = static::find()
                ->where('login = :login', [':login' => $login])
                ->andWhere('Estado = :Estado', [':Estado' => 1])
                ->one();

        return isset($user) ? $user : null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->IdUsuario;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return generarPassword::checkPassword($password, $this->password);
        //return strtolower($this->password) === strtolower(md5($password));
    }
}
