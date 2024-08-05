<?php
namespace super\ticket\models\forms;

use elitedivision\amos\attachments\behaviors\FileBehavior;
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
            [['ticket_id', 'body'], 'required'],
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
            'verifyCode' => 'Verification Code',
        ];

        Yii::$app->db->createCommand()->execute();
    }

    public function save() {
        if($this->validate()) {
            return SuperTicketEvent::createTicketEvent(
                $this->ticket_id,
                SuperTicketEvent::TYPE_COMMENT,
                $this->body,
                $this->user_id
            );
        }

        return false;
    }
}
