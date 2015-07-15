<?php

namespace app\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "PREP_Partido".
 *
 * @property integer $id_partido
 * @property string $nombre
 * @property string $nombre_corto
 * @property string $propietario
 * @property string $suplente
 * @property string $observaciones
 * @property string $color
 */
class PREPPartido extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PREP_Partido';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'nombre_corto', 'propietario', 'suplente', 'observaciones', 'color'], 'string'],
            [['nombre_corto'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_partido' => 'Id Partido',
            'nombre' => 'Nombre',
            'nombre_corto' => 'Nombre Corto',
            'propietario' => 'Propietario',
            'suplente' => 'Suplente',
            'observaciones' => 'Observaciones',
            'color' => 'Color',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id_partido'];
    }

    public function afterSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->id_partido = Yii::$app->db->getLastInsertID();
            }

            return true;
        } else {
            return false;
        }
    }

    public function getLogo()
    {
        $pathLogo = Url::to('@app/partidos', true).'/'.$this->id_partido.'.jpg';
        $logoPartido = null;

        if ( file_exists($pathLogo) ) {
            $type = pathinfo($pathLogo, PATHINFO_EXTENSION);
            $imageByte = file_get_contents($pathLogo);
            $logoPartido = 'data:image/' . $type . ';base64,' . base64_encode($imageByte);
        }

        return $logoPartido;
    }
}
