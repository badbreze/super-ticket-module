<?php
namespace super\ticket\models\forms;

use super\ticket\models\SuperTicketEvent;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class TicketCommentForm extends Model
{
    public $ticket_id;
    public $body;

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
    }

    public function save() {
        if($this->validate()) {
            return SuperTicketEvent::createTicketEvent(
                $this->ticket_id,
                SuperTicketEvent::TYPE_COMMENT,
                $this->body,
                null
            );
        }

        return false;
    }
}
