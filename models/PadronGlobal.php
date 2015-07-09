<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use app\helpers\ResizeImage;
use yii\web\UploadedFile;

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
 * @property string $DOMICILIO
 * @property string $DES_LOC
 * @property string $NOM_LOC
 * @property date $FECHANACIMIENTO
 * @property integer $ESTADO_CIVIL
 * @property integer $OCUPACION
 * @property integer $ESCOLARIDAD
 * @property integer $AGREGADO
 */
class PadronGlobal extends \yii\db\ActiveRecord
{
    public $foto;

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
            [['SEXO', 'NOMBRE', 'APELLIDO_PATERNO', 'APELLIDO_MATERNO', 'COLONIA', 'DOMICILIO', 'NOM_LOC', 'CODIGO_POSTAL', 'FECHANACIMIENTO', 'MUNICIPIO', 'ALFA_CLAVE_ELECTORAL'], 'required'],
            [['CLAVEUNICA', 'ALFA_CLAVE_ELECTORAL', 'SEXO', 'NOMBRE', 'APELLIDO_PATERNO', 'APELLIDO_MATERNO', 'CALLE', 'NUM_INTERIOR', 'NUM_EXTERIOR', 'COLONIA', 'CORREOELECTRONICO', 'TELMOVIL', 'TELCASA', 'CASILLA', 'DOMICILIO', 'DES_LOC', 'NOM_LOC'], 'string'],
            [['CLAVEUNICA', 'ALFA_CLAVE_ELECTORAL', 'SEXO', 'NOMBRE', 'APELLIDO_PATERNO', 'APELLIDO_MATERNO', 'CALLE', 'NUM_INTERIOR', 'NUM_EXTERIOR', 'COLONIA', 'CORREOELECTRONICO', 'TELMOVIL', 'TELCASA', 'CASILLA', 'DOMICILIO', 'DES_LOC', 'NOM_LOC'], 'trim'],
            [['ALFA_CLAVE_ELECTORAL'], 'string', 'length' => 18],
            [['CONS_ALF_POR_SECCION', 'LUGAR_NACIMIENTO', 'DIGITO_VERIFICADOR', 'CLAVE_HOMONIMIA', 'CODIGO_POSTAL', 'FOLIO_NACIONAL', 'EN_LISTA_NOMINAL', 'ENTIDAD', 'DISTRITO', 'MUNICIPIO', 'SECCION', 'LOCALIDAD', 'MANZANA', 'NUM_EMISION_CREDENCIAL'], 'number'],
            [['DISTRITOLOCAL', 'FECHA_NACI_CLAVE_ELECTORAL', 'ESTADO_CIVIL', 'OCUPACION', 'ESCOLARIDAD'], 'integer'],
            [['FECHANACIMIENTO'], 'date', 'format' => 'yyyy-MM-dd'],
            [['foto'], 'file', 'extensions' => 'jpg, png, gif', 'mimeTypes' => 'image/jpeg, image/png, image/gif']
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
            'ALFA_CLAVE_ELECTORAL' => 'Clave Electoral',
            'FECHA_NACI_CLAVE_ELECTORAL' => 'Fecha de nacimiento',
            'FECHANACIMIENTO' => 'Fecha de nacimiento',
            'LUGAR_NACIMIENTO' => 'Lugar de nacimiento',
            'SEXO' => 'Sexo',
            'DIGITO_VERIFICADOR' => 'Digito Verificador',
            'CLAVE_HOMONIMIA' => 'Clave Homonimia',
            'NOMBRE' => 'Nombre',
            'APELLIDO_PATERNO' => 'Apellido Paterno',
            'APELLIDO_MATERNO' => 'Apellido Materno',
            'CALLE' => 'Calle',
            'NUM_INTERIOR' => 'Núm Interior',
            'NUM_EXTERIOR' => 'Núm Exterior',
            'COLONIA' => 'Colonia',
            'CODIGO_POSTAL' => 'Código Postal',
            'FOLIO_NACIONAL' => 'Folio Nacional',
            'EN_LISTA_NOMINAL' => 'En Lista Nominal',
            'ENTIDAD' => 'Entidad',
            'DISTRITO' => 'Distrito',
            'MUNICIPIO' => 'Municipio',
            'SECCION' => 'Sección',
            'LOCALIDAD' => 'Localidad',
            'MANZANA' => 'Manzana',
            'NUM_EMISION_CREDENCIAL' => 'Num Emision Credencial',
            'DISTRITOLOCAL' => 'Distrito local',
            'CORREOELECTRONICO' => 'Correo electrónico',
            'TELMOVIL' => 'Tel. Móvil',
            'TELCASA' => 'Tel. Casa',
            'CASILLA' => 'Casilla',
            'DOMICILIO' => 'Domicilio',
            'DES_LOC' => 'Tipo Localidad',
            'NOM_LOC' => 'Localidad',
            'foto' => 'Foto',
            'ESTADO_CIVIL' => 'Estado Civil',
            'OCUPACION' => 'Ocupación',
            'ESCOLARIDAD' => 'Escolaridad',
            'AGREGADO' => 'Agregado'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMunicipio()
    {
        return $this->hasOne(CMunicipio::className(), ['IdMunicipio' => 'MUNICIPIO']);
    }

    public function getNombreCompleto()
    {
        return str_replace('\\', 'Ñ', $this->NOMBRE.' '.$this->APELLIDO_PATERNO.' '.$this->APELLIDO_MATERNO);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstado_civil()
    {
        return $this->hasOne(ElementoCatalogo::className(), ['IdElementoCatalogo' => 'ESTADO_CIVIL']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOcupacion()
    {
        return $this->hasOne(ElementoCatalogo::className(), ['IdElementoCatalogo' => 'OCUPACION']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscolaridad()
    {
        return $this->hasOne(ElementoCatalogo::className(), ['IdElementoCatalogo' => 'ESCOLARIDAD']);
    }

    /**
     * Calcula la edad a partir de la fecha de nacimiento
     *
     * @return Int Edad
     */
    public function getEdad()
    {
        $fecha = time() - strtotime($this->getfechaNac('Y-m-d'));
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
        $fechaNac = $this->FECHANACIMIENTO;

        if (empty($fechaNac)) {
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
     * Si no existe, muestra la de una en base al sexo
     *
     * @return String URL de la foto a mostrar
     */
    public function getFoto()
    {
        $this->foto = static::getFotoByUID($this->CLAVEUNICA, $this->SEXO);

        return static::getFotoByUID($this->CLAVEUNICA, $this->SEXO);
        //return $this->foto;
    }

    /**
     * Obtiene la ruta del archivo de la foto a mostrar
     * Si no existe, muestra la de una en base al sexo
     *
     * @param UID $uid CLAVEUNICA
     * @param Char $sexo SEXO
     * @return String URL de la foto a mostrar
     */
    public static function getFotoByUID($uid, $sexo)
    {
        $pathFoto = Url::to('@app/fotos/'.strtolower($uid).'.jpg');

        if (!file_exists($pathFoto)) {
            $pathFoto = Url::to('@app/web/img/avatar/'.$sexo.'.jpg');

            if (!file_exists($pathFoto)) {
                $pathFoto = Url::to('@app/web/img/avatar/U.jpg');
            }
        }

        // Obtener nuevas dimensiones
        list($ancho, $alto) = getimagesize($pathFoto);

        if ($ancho > 200 || $alto > 250) {
             // Redimensionar
            ResizeImage::smart_resize_image($pathFoto, null, 200, 250, false, $pathFoto, false, false, 100);
        }

        $type = pathinfo($pathFoto, PATHINFO_EXTENSION);
        $imageByte = file_get_contents($pathFoto);
        $base64Foto = 'data:image/' . $type . ';base64,' . base64_encode($imageByte);

        return $base64Foto;
    }

    public static function newUID()
    {
        $uid = Yii::$app->db->createCommand('SELECT NEWID() as newUID')->queryOne();

        return $uid['newUID'];
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getPuesto()
    {
        return $this->hasOne(Puestos::className(), ['IdPuesto' => 'IdPuesto'])
            ->viaTable('DetalleEstructuraMovilizacion', ['IdPersonaPuesto' => 'CLAVEUNICA']);
    }
}
