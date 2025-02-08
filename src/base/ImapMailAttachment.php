<?php

namespace super\ticket\base;

use PhpImap\IncomingMailAttachment;

/**
 * @property string $name
 * @property string $contents
 */
class ImapMailAttachment extends \yii\base\BaseObject
{
    public IncomingMailAttachment $attachment;

    public function getContents() {
        return $this->attachment->getContents();
    }

    public function getName() {
        return $this->attachment->name;
    }
}