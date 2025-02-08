<?php
namespace super\ticket\models\forms;

use elitedivision\amos\attachments\behaviors\FileBehavior;
use super\ticket\helpers\UserHelper;
use super\ticket\models\SuperTicketEvent;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * ContactForm is the model behind the contact form.
 */
class TicketCommentForm extends Model
{
    public $id;
    public $ticket_id;
    public $body;
    public $user_id;
    public $isNewRecord = true;
    public $recipients = [];

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'fileBehavior' => [
                'class' => FileBehavior::className()
            ]
        ]);
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['ticket_id', 'body', 'recipients'], 'required'],
            [['ticket_id'], 'integer'],
            [['body'], 'string'],
            [['id'], 'safe'],
            [['attachments'], 'file', 'maxFiles' => 0, 'maxSize' => 10240],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => Yii::t('super', 'Verification Code'),
            'recipients' => Yii::t('super', 'Recipients'),
            'body' => Yii::t('super', 'Comment'),
        ];
    }

    public function save() {
        if($this->validate()) {
            $metadata = [
                'recipients' => $this->recipients,
            ];

            return SuperTicketEvent::createTicketEvent(
                $this->ticket_id,
                SuperTicketEvent::TYPE_COMMENT,
                $this->body,
                UserHelper::getCurrentUser()->id,
                $metadata
            );
        }

        return false;
    }
}
