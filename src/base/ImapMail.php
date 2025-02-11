<?php

namespace super\ticket\base;

use PhpImap\IncomingMail;

/**
 * @property string $id
 * @property string $messageId
 * @property string $subject
 * @property string $content
 * @property string|null $fromName
 * @property string|null $fromAddress
 * @property string|null $fromHost
 * @property string[]|null $cc
 * @property \DateTime $date
 */
class ImapMail extends \yii\base\BaseObject
{
    public IncomingMail $mail;

    /**
     * @return ImapMailAttachment[]
     */
    public function getAttachments() {
        $attachments = [];

        foreach ($this->mail->getAttachments() as $attachment) {
            $attachments[] = new ImapMailAttachment(['attachment' => $attachment]);
        }

        return $attachments;
    }

    public function getId() {
        return $this->mail->id;
    }

    public function getMessageId() {
        return $this->mail->messageId;
    }

    public function getSubject() {
        return $this->mail->subject;
    }

    public function getContent() {
        return $this->mail->textHtml ?: $this->mail->textPlain;
    }

    public function getDate() {
        return new \DateTime($this->mail->date);
    }

    public function getFromName() {
        return $this->mail->fromName;
    }

    public function getFromAddress() {
        return $this->mail->fromAddress;
    }

    public function getFromHost() {
        return $this->mail->fromHost;
    }

    public function getCc() {
        return $this->mail->cc;
    }
}