<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "IntegrantesOrganizaciones".
 *
 * @property integer $IdOrganizacion
 * @property string $IdPersonaIntegrante
 */
class IntegrantesOrganizaciones extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'IntegrantesOrganizaciones';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdOrganizacion', 'IdPersonaIntegrante'], 'required'],
            [['IdOrganizacion'], 'integer'],
            [['IdPersonaIntegrante'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdOrganizacion' => 'Id Organizacion',
            'IdPersonaIntegrante' => 'Id Persona Integrante',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizacion()
    {
        return $this->hasMany(Organizaciones::className(), ['IdOrganizacion' => 'IdOrganizacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonaIntegrante()
    {
        return $this->hasMany(PadronGlobal::className(), ['CLAVEUNICA' => 'IdPersonaIntegrante']);
    }
}
