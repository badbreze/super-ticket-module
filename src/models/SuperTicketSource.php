<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%super_ticket_source}}".
 *
 * @property int $id
 * @property string $name Name
 * @property string|null $metadata
 * @property string $source_type
 * @property integer $source_id
 * @property integer $status_id
 * @property string|null $created_at Creato il
 * @property string|null $updated_at Aggiornato il
 * @property string|null $deleted_at Cancellato il
 * @property int|null $created_by Creato da
 * @property int|null $updated_by Aggiornato da
 * @property int|null $deleted_by Cancellato da
 *
 * @property SuperTicket $superTicket
 * @property SuperMail $mail
 */
class SuperTicketSource extends ActiveRecord
{
    public const TYPE_EMAIL = 'email';
    public const TYPE_SMS = 'sms';
    public const TYPE_TELEGRAM = 'telegram';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_ticket_source}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'source_type', 'status_id'], 'required'],
            [['source_id', 'status_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['metadata'], 'string'],
            [['source_type'], 'string', 'max' => 32],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperTicket::className(), 'targetAttribute' => ['ticket_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('super', 'ID'),
            'name' => Yii::t('super', 'Name'),
            'metadata' => Yii::t('super', 'Metadata'),
            'ticket_id' => Yii::t('super', 'Agent ID'),
            'source_type' => Yii::t('super', 'Source Type'),
            'source_id' => Yii::t('super', 'Source ID'),
            'created_at' => Yii::t('super', 'Created At'),
            'updated_at' => Yii::t('super', 'Updated At'),
            'deleted_at' => Yii::t('super', 'Deleted At'),
            'created_by' => Yii::t('super', 'Created By'),
            'updated_by' => Yii::t('super', 'Updated By'),
            'deleted_by' => Yii::t('super', 'Deleted By'),
        ];
    }

    /**
     * Gets query for [[SuperTickets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperTicket()
    {
        return $this->hasOne(SuperTicket::className(), ['id' => 'ticket_id']);
    }

    public function getMail() {
        if($this->source_type != self::TYPE_EMAIL) {
            return null;
        }

        return $this->hasOne(SuperMail::className(), ['id' => 'source_id']);
    }
}
