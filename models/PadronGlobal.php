<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "PadronGlobal".
 *
 * @property string $CLAVEUNICA
 * @property double $CONS_ALF_POR_SECCION
 * @property string $ALFA_CLAVE_ELECTORAL
 * @property double $FECHA_NACI_CLAVE_ELECTORAL
 * @property double $LUGAR_NACIMIENTO
 * @property string $SEXO
 * @property double $DIGITO_VERIFICADOR
 * @property double $CLAVE_HOMONIMIA
 * @property string $NOMBRES
 * @property string $NOMBRE
 * @property string $APELLIDO_PATERNO
 * @property string $APELLIDO_MATERNO
 * @property string $CALLE
 * @property string $NUM_INTERIOR
 * @property string $NUM_EXTERIOR
 * @property string $COLONIA
 * @property double $CODIGO_POSTAL
 * @property double $FOLIO_NACIONAL
 * @property double $EN_LISTA_NOMINAL
 * @property double $ENTIDAD
 * @property double $DISTRITO
 * @property double $MUNICIPIO
 * @property double $SECCION
 * @property double $LOCALIDAD
 * @property double $MANZANA
 * @property double $NUM_EMISION_CREDENCIAL
 * @property integer $DISTRITOLOCAL
 * @property string $CORREOELECTRONICO
 * @property string $TELMOVIL
 * @property string $TELCASA
 * @property string $CASILLA
 * @property integer $IDPADRON
 * @property string $DOMICILIO
 * @property string $DES_LOC
 * @property string $NOM_LOC
 */
class PadronGlobal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PadronGlobal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CLAVEUNICA', 'ALFA_CLAVE_ELECTORAL', 'SEXO', 'NOMBRES', 'NOMBRE', 'APELLIDO_PATERNO', 'APELLIDO_MATERNO', 'CALLE', 'NUM_INTERIOR', 'NUM_EXTERIOR', 'COLONIA', 'CORREOELECTRONICO', 'TELMOVIL', 'TELCASA', 'CASILLA', 'DOMICILIO', 'DES_LOC', 'NOM_LOC'], 'string'],
            [['CONS_ALF_POR_SECCION', 'FECHA_NACI_CLAVE_ELECTORAL', 'LUGAR_NACIMIENTO', 'DIGITO_VERIFICADOR', 'CLAVE_HOMONIMIA', 'CODIGO_POSTAL', 'FOLIO_NACIONAL', 'EN_LISTA_NOMINAL', 'ENTIDAD', 'DISTRITO', 'MUNICIPIO', 'SECCION', 'LOCALIDAD', 'MANZANA', 'NUM_EMISION_CREDENCIAL'], 'number'],
            [['DISTRITOLOCAL', 'IDPADRON'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'CLAVEUNICA' => 'Claveunica',
            'CONS_ALF_POR_SECCION' => 'Cons  Alf  Por  Seccion',
            'ALFA_CLAVE_ELECTORAL' => 'Alfa  Clave  Electoral',
            'FECHA_NACI_CLAVE_ELECTORAL' => 'Fecha  Naci  Clave  Electoral',
            'LUGAR_NACIMIENTO' => 'Lugar  Nacimiento',
            'SEXO' => 'Sexo',
            'DIGITO_VERIFICADOR' => 'Digito  Verificador',
            'CLAVE_HOMONIMIA' => 'Clave  Homonimia',
            'NOMBRES' => 'Nombres',
            'NOMBRE' => 'Nombre',
            'APELLIDO_PATERNO' => 'Apellido  Paterno',
            'APELLIDO_MATERNO' => 'Apellido  Materno',
            'CALLE' => 'Calle',
            'NUM_INTERIOR' => 'Num  Interior',
            'NUM_EXTERIOR' => 'Num  Exterior',
            'COLONIA' => 'Colonia',
            'CODIGO_POSTAL' => 'Codigo  Postal',
            'FOLIO_NACIONAL' => 'Folio  Nacional',
            'EN_LISTA_NOMINAL' => 'En  Lista  Nominal',
            'ENTIDAD' => 'Entidad',
            'DISTRITO' => 'Distrito',
            'MUNICIPIO' => 'Municipio',
            'SECCION' => 'Seccion',
            'LOCALIDAD' => 'Localidad',
            'MANZANA' => 'Manzana',
            'NUM_EMISION_CREDENCIAL' => 'Num  Emision  Credencial',
            'DISTRITOLOCAL' => 'Distritolocal',
            'CORREOELECTRONICO' => 'Correoelectronico',
            'TELMOVIL' => 'Telmovil',
            'TELCASA' => 'Telcasa',
            'CASILLA' => 'Casilla',
            'IDPADRON' => 'Idpadron',
            'DOMICILIO' => 'Domicilio',
            'DES_LOC' => 'Des  Loc',
            'NOM_LOC' => 'Nom  Loc',
        ];
    }
}
