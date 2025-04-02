<?php
namespace super\ticket\models\forms;

use elitedivision\amos\attachments\behaviors\FileBehavior;
use super\ticket\helpers\UserHelper;
use super\ticket\models\SuperTicketEvent;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

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
    public $type = SuperTicketEvent::TYPE_COMMENT;

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
            [['body', 'type'], 'string'],
            [['id'], 'safe'],
            [['attachments'], 'file', 'maxFiles' => 0, 'maxSize' => 1024*1024*10],
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

            //What a shame
            $_FILES['SuperTicketEvent'] = $_FILES['TicketCommentForm'];
            UploadedFile::reset();

            $event = SuperTicketEvent::createTicketEvent(
                $this->ticket_id,
                $this->type,
                $this->body,
                UserHelper::getCurrentUser()->id,
                $metadata
            );

            return $event;
        }

        return false;
    }
}
