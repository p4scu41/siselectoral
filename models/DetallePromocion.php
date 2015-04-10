<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "DetallePromocion".
 *
 * @property string $IdPersonaPromovida
 * @property string $IdPErsonaPromueve
 */
class DetallePromocion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'DetallePromocion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdPersonaPromovida', 'IdPErsonaPromueve'], 'required'],
            [['IdPersonaPromovida', 'IdPErsonaPromueve'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'IdPersonaPromovida' => 'Id Persona Promovida',
            'IdPErsonaPromueve' => 'Id Persona Promueve',
        ];
    }
    
    public static function existsDetallePromocion($promovido, $promueve)
    {
        $sql = 'SELECT COUNT(*) AS TOTAL
                FROM [DetallePromocion]
                WHERE 
                    [IdPersonaPromovida] = \''.$promovido.'\' AND
                    [IdPErsonaPromueve] = \''.$promueve.'\'';

        $result = Yii::$app->db->createCommand($sql)->queryOne();

        return $result['TOTAL'];
    }
}
