<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "CMunicipio".
 *
 * @property integer $IdMunicipio
 * @property integer $IdRegion
 * @property string $DescMunicipio
 * @property string $NumDistrito
 * @property string $IDMPIOANT
 * @property integer $numsec
 * @property integer $numloc
 * @property integer $totln
 * @property integer $DistritoLocal
 */
class CMunicipio extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'CMunicipio';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdMunicipio'], 'required'],
            [['IdMunicipio', 'IdRegion', 'numsec', 'numloc', 'totln', 'DistritoLocal'], 'integer'],
            [['DescMunicipio', 'NumDistrito', 'IDMPIOANT'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdMunicipio' => 'Id Municipio',
            'IdRegion' => 'Id Region',
            'DescMunicipio' => 'Desc Municipio',
            'NumDistrito' => 'Num Distrito',
            'IDMPIOANT' => 'Idmpioant',
            'numsec' => 'Numsec',
            'numloc' => 'Numloc',
            'totln' => 'Totln',
            'DistritoLocal' => 'Distrito Local',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['IdMunicipio'];
    }
}
