<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "PREP_Seccion".
 *
 * @property integer $id_seccion
 * @property integer $municipio
 * @property integer $zona
 * @property integer $seccion
 * @property integer $distrito_local
 * @property integer $distrito_federal
 * @property string $observaciones
 * @property integer $activo
 * @property string $fecha_cierre
 */
class PREPSeccion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PREP_Seccion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['municipio', 'zona', 'seccion'], 'required'],
            [['municipio', 'zona', 'seccion', 'distrito_local', 'distrito_federal', 'activo'], 'integer'],
            [['observaciones'], 'string'],
            [['fecha_cierre'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_seccion' => 'Id Seccion',
            'municipio' => 'Municipio',
            'zona' => 'Zona',
            'seccion' => 'Sección',
            'distrito_local' => 'Distrito Local',
            'distrito_federal' => 'Distrito Federal',
            'observaciones' => 'Observaciones',
            'activo' => 'Activo',
            'fecha_cierre' => 'Fecha Cierre',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id_seccion'];
    }

    /**
     * return \yii\db\ActiveQuery
     */
    public function getCmunicipio()
    {
        return $this->hasOne(CMunicipio::className(), ['IdMunicipio' => 'municipio']);
    }

    public static function getByMunicipio($muni)
    {
        $sql = 'SELECT [id_seccion],[seccion]
            FROM [PREP_Seccion]
            WHERE [municipio] = '.(int)$muni.'
            ORDER BY [seccion]';

        $secciones = Yii::$app->db->createCommand($sql)->queryAll();

        return $secciones;
    }

}
