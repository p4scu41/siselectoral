<?php

namespace app\models;

use Yii;
use yii\helpers\Url;

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
            'CONS_ALF_POR_SECCION' => 'Cons Alf Por Seccion',
            'ALFA_CLAVE_ELECTORAL' => 'Alfa Clave Electoral',
            'FECHA_NACI_CLAVE_ELECTORAL' => 'Fecha Naci Clave Electoral',
            'LUGAR_NACIMIENTO' => 'Lugar Nacimiento',
            'SEXO' => 'Sexo',
            'DIGITO_VERIFICADOR' => 'Digito Verificador',
            'CLAVE_HOMONIMIA' => 'Clave Homonimia',
            'NOMBRES' => 'Nombres',
            'NOMBRE' => 'Nombre',
            'APELLIDO_PATERNO' => 'Apellido Paterno',
            'APELLIDO_MATERNO' => 'Apellido Materno',
            'CALLE' => 'Calle',
            'NUM_INTERIOR' => 'Num Interior',
            'NUM_EXTERIOR' => 'Num Exterior',
            'COLONIA' => 'Colonia',
            'CODIGO_POSTAL' => 'Codigo Postal',
            'FOLIO_NACIONAL' => 'Folio Nacional',
            'EN_LISTA_NOMINAL' => 'En Lista Nominal',
            'ENTIDAD' => 'Entidad',
            'DISTRITO' => 'Distrito',
            'MUNICIPIO' => 'Municipio',
            'SECCION' => 'Seccion',
            'LOCALIDAD' => 'Localidad',
            'MANZANA' => 'Manzana',
            'NUM_EMISION_CREDENCIAL' => 'Num Emision Credencial',
            'DISTRITOLOCAL' => 'Distritolocal',
            'CORREOELECTRONICO' => 'Correoelectronico',
            'TELMOVIL' => 'Telmovil',
            'TELCASA' => 'Telcasa',
            'CASILLA' => 'Casilla',
            'IDPADRON' => 'Idpadron',
            'DOMICILIO' => 'Domicilio',
            'DES_LOC' => 'Des Loc',
            'NOM_LOC' => 'Nom Loc',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMunicipio()
    {
        return $this->hasOne(CMunicipio::className(), ['IdMunicipio' => 'MUNICIPIO']);
    }

    /**
     * Calcula la edad a partir de la fecha de nacimiento
     *
     * @return Int Edad
     */
    public function getEdad()
    {
        $fecha = time() - strtotime( $this->getfechaNac('Y-m-d') );
        $edad = floor($fecha / 31557600); // 60s * 60m * 24h * 365.25d

        return $edad;
    }

    /**
     * Obtiene la fecha de nacimiento, si no se encuentra la
     * obtiene de la columna ALFA_CLAVE_ELECTORAL
     *
     * @return Date Fecha de nacimiento
     */
    public function getFechaNac($format = 'd-m-Y')
    {
        $fechaNac = $this->FECHA_NACI_CLAVE_ELECTORAL;

        if(empty($fechaNac)) {
            $y = '19'.substr($this->ALFA_CLAVE_ELECTORAL, 6, 2);
            $m = substr($this->ALFA_CLAVE_ELECTORAL, 8, 2);
            $d = substr($this->ALFA_CLAVE_ELECTORAL, 10, 2);
            $fechaNac = $y.'-'.$m.'-'.$d;
        }

        $objFecha = date($format, strtotime($fechaNac));

        return $objFecha;
    }

    /**
     * Obtiene la descripción del género a partir del sexo
     *
     * @return String Género
     */
    public function getGenero()
    {
        $genero = array('H'=>'Hombre', 'M'=>'Mujer');
        return $genero[$this->SEXO];
    }

    /**
     * Obtiene la ruta del archivo de la foto a mostrar
     * Si no existe, muestra la de "desconocido"
     *
     * @return String URL de la foto a mostrar
     */
    public function getFoto()
    {
        $pathFoto = Url::to('@app/fotos/'.$this->CLAVEUNICA.'.jpg', true);

        if (!file_exists($pathFoto)) {
            $pathFoto = Url::to('@web/img/avatar/'.$this->SEXO.'.jpg', true);
        }

        $type = pathinfo($pathFoto, PATHINFO_EXTENSION);
        $image = file_get_contents($pathFoto);
        $base64Foto = 'data:image/' . $type . ';base64,' . base64_encode($image);

        return $base64Foto;
    }
}
