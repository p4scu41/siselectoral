<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "PREP_Voto".
 *
 * @property integer $id_candidato
 * @property integer $id_casilla_seccion
 * @property integer $no_votos
 * @property string $observaciones
 * @property string $created_at
 * @property string $created_by
 * @property string $updated_at
 * @property string $updated_by
 */
class PREPVoto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PREP_Voto';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_candidato', 'id_casilla_seccion', 'no_votos'], 'required'],
            [['id_candidato', 'id_casilla_seccion', 'no_votos'], 'integer'],
            [['observaciones', 'created_by', 'updated_by'], 'string'],
            [['created_at', 'updated_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_candidato' => 'Id Candidato',
            'id_casilla_seccion' => 'Id Casilla Seccion',
            'no_votos' => 'No Votos',
            'observaciones' => 'Observaciones',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id_candidato', 'id_casilla_seccion'];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = date('Y-m-d H:i:s');
                $this->created_by = Yii::$app->user->identity->IdPersona;
            } else {
                $this->updated_at = date('Y-m-d H:i:s');
                $this->updated_by = Yii::$app->user->identity->IdPersona;
            }
            
            return true;
        } else {
            return false;
        }
    }

    public static function getByCandidatos($candidatos)
    {
        $sql = 'SELECT * FROM [PREP_Voto] WHERE id_candidato IN ('.implode(',', $candidatos).')';

        $votos = Yii::$app->db->createCommand($sql)->queryAll();

        return $votos;
    }

    public static function setVoto($attrs)
    {
        $voto = self::find()
            ->where('id_candidato = '.$attrs['id_candidato'].' AND id_casilla_seccion = '.$attrs['id_casilla_seccion'])
            ->one();

        if ($voto == null) {
            $voto = new PREPVoto();
            $voto->id_candidato = $attrs['id_candidato'];
            $voto->id_casilla_seccion = $attrs['id_casilla_seccion'];
        }

        $voto->no_votos = $attrs['no_votos'];

        $voto->save();
    }

    /*public static function getResultados($candidato, $nameColum, $valueColum, $iniSeccion, $finSeccion)
    {
        $sql = 'SELECT
                [PREP_Seccion].[seccion]
                ,[PREP_Voto].[id_candidato]
                ,SUM([PREP_Voto].[no_votos]) AS no_votos
                ,SUM([CSeccion].[MetaAlcanzar]) AS meta
            FROM
                [PREP_Voto]
            INNER JOIN [PREP_Casilla_Seccion] ON
                [PREP_Casilla_Seccion].[id_casilla_seccion] = [PREP_Voto].[id_casilla_seccion]
            INNER JOIN [PREP_Seccion] ON
                [PREP_Seccion].[id_seccion] = [PREP_Casilla_Seccion].[id_seccion]
            INNER JOIN [CSeccion] ON
                [CSeccion].[IdMunicipio] = [PREP_Seccion].[municipio] AND
                [CSeccion].[NumSector] = [PREP_Seccion].[seccion]
            WHERE
                [PREP_Voto].[id_candidato] = '.$candidato.' AND
                [PREP_Seccion].['.$nameColum.'] = '.$valueColum.' AND
                [PREP_Seccion].[seccion] BETWEEN '.$iniSeccion.' AND '.$finSeccion.'
            GROUP BY
                [PREP_Seccion].[seccion], [PREP_Voto].[id_candidato]
            ORDER BY
                [PREP_Seccion].[seccion], [PREP_Voto].[id_candidato]';

        $result = Yii::$app->db->createCommand($sql)->queryAll();

        return $result;
    }*/

    public static function getResultados($nameColum, $valueColum, $zona, $iniSeccion, $finSeccion)
    {
        $sql = 'SELECT
                [PREP_Seccion].[seccion]
                ,[PREP_Voto].[id_candidato]
                ,SUM([PREP_Voto].[no_votos]) AS no_votos
                ,SUM([CSeccion].[MetaAlcanzar]) AS meta
            FROM
                [PREP_Voto]
            INNER JOIN [PREP_Casilla_Seccion] ON
                [PREP_Casilla_Seccion].[id_casilla_seccion] = [PREP_Voto].[id_casilla_seccion]
            INNER JOIN [PREP_Seccion] ON
                [PREP_Seccion].[id_seccion] = [PREP_Casilla_Seccion].[id_seccion]
            INNER JOIN [CSeccion] ON
                [CSeccion].[IdMunicipio] = [PREP_Seccion].[municipio] AND
                [CSeccion].[NumSector] = [PREP_Seccion].[seccion]
            WHERE
                1 = 1 AND
                [PREP_Seccion].['.$nameColum.'] = '.$valueColum.'
                '.($zona ? ' AND [PREP_Seccion].[zona] = '.$zona : '').'
                '.($iniSeccion ? ' AND [PREP_Seccion].[seccion] BETWEEN '.$iniSeccion.' AND '.$finSeccion : '').'
            GROUP BY
                [PREP_Seccion].[seccion], [PREP_Voto].[id_candidato]
            ORDER BY
                [PREP_Seccion].[seccion], [PREP_Voto].[id_candidato]';

        $result = Yii::$app->db->createCommand($sql)->queryAll();

        return $result;
    }
}
