<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "PREP_Casilla_Seccion".
 *
 * @property integer $id_casilla_seccion
 * @property integer $id_seccion
 * @property integer $id_casilla
 * @property string $descripcion
 * @property string $colonia
 * @property string $domicilio
 * @property integer $cp
 * @property string $localidad
 * @property string $repre_gral
 * @property string $tel_repre_gral
 * @property string $repre_casilla
 * @property string $tel_repre_casilla
 * @property string $observaciones
 */
class PREPCasillaSeccion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PREP_Casilla_Seccion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_seccion', 'id_casilla', 'descripcion'], 'required'],
            [['id_seccion', 'id_casilla', 'cp'], 'integer'],
            [['descripcion', 'colonia', 'domicilio', 'localidad', 'repre_gral', 'tel_repre_gral', 'repre_casilla', 'tel_repre_casilla', 'observaciones'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_casilla_seccion' => 'Id Casilla Seccion',
            'id_seccion' => 'Sección',
            'id_casilla' => 'Casilla',
            'descripcion' => 'Descripción',
            'colonia' => 'Colonia',
            'domicilio' => 'Domicilio',
            'cp' => 'Código Postal',
            'localidad' => 'Localidad',
            'repre_gral' => 'Representante General',
            'tel_repre_gral' => 'Tel. Representante General',
            'repre_casilla' => 'Representante Casilla',
            'tel_repre_casilla' => 'Tel Representante Casilla',
            'observaciones' => 'Observaciones',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id_casilla_seccion'];
    }

    /**
     * return \yii\db\ActiveQuery
     */
    public function getSeccion()
    {
        return $this->hasOne(PREPSeccion::className(), ['id_seccion' => 'id_seccion']);
    }

    /**
     * return \yii\db\ActiveQuery
     */
    public function getCasilla()
    {
        return $this->hasOne(PREPCasilla::className(), ['id_casilla' => 'id_casilla']);
    }

    public static function getFromSeccion($idMuni, $seccion)
    {
        $sql = 'SELECT
            [PREP_Casilla_Seccion].[descripcion] AS casilla
            ,[PREP_Casilla_Seccion].[repre_casilla] AS representante
            ,[PREP_Casilla_Seccion].[tel_repre_casilla] AS tel
        FROM [PREP_Casilla_Seccion]
        INNER JOIN [PREP_Seccion] ON
            [PREP_Seccion].[id_seccion] = [PREP_Casilla_Seccion].[id_seccion]
        WHERE
            [PREP_Seccion].[municipio] = '.$idMuni.' AND
            [PREP_Seccion].[seccion] = '.$seccion;

        $casillas = Yii::$app->db->createCommand($sql)->queryAll();

        return $casillas;
    }

    public static function getWhere($where)
    {
        $sql = 'SELECT
                [PREP_Casilla_Seccion].[id_casilla_seccion]
                ,[PREP_Casilla_Seccion].[id_seccion]
                ,[PREP_Casilla_Seccion].[id_casilla]
                ,[PREP_Casilla_Seccion].[descripcion]
                ,[PREP_Casilla_Seccion].[colonia]
                ,[PREP_Casilla_Seccion].[domicilio]
                ,[PREP_Casilla_Seccion].[cp]
                ,[PREP_Casilla_Seccion].[localidad]
                ,[PREP_Casilla_Seccion].[repre_gral]
                ,[PREP_Casilla_Seccion].[tel_repre_gral]
                ,[PREP_Casilla_Seccion].[repre_casilla]
                ,[PREP_Casilla_Seccion].[tel_repre_casilla]
                ,[PREP_Casilla_Seccion].[observaciones]
                ,[PREP_Seccion].[municipio]
                ,[PREP_Seccion].[zona]
                ,[PREP_Seccion].[distrito_local]
                ,[PREP_Seccion].[distrito_federal]
                ,[PREP_Seccion].[seccion]
            FROM [PREP_Casilla_Seccion]
            INNER JOIN [PREP_Seccion] ON
                [PREP_Seccion].[id_seccion] = [PREP_Casilla_Seccion].[id_seccion]
            WHERE '.$where.'
            ORDER BY [PREP_Seccion].[seccion], [PREP_Casilla_Seccion].[descripcion]';

        $casillas = Yii::$app->db->createCommand($sql)->queryAll();

        return $casillas;
    }

}
