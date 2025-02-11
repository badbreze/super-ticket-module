<?php

namespace super\ticket\base;

use super\ticket\models\SuperMail;
use super\ticket\modules\console\helpers\EmailHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use PhpImap\Imap;

/**
 * @property array $mailIds
 */
class ImapMailBox extends \yii\base\BaseObject
{
    public SuperMail $super_mail;
    private \PhpImap\Mailbox $_connection;
    private $_count = null;
    private $_mailIds = null;

    public function init()
    {
        parent::init();

        $imapPath = EmailHelper::expressImapPath($this->super_mail);

        // Create PhpImap\Mailbox instance for all further actions
        $this->_connection = new \PhpImap\Mailbox(
            "{{$imapPath}}{$this->super_mail->folder}", // IMAP server and mailbox folder
            $this->super_mail->username, // Username for the before configured mailbox
            $this->super_mail->password, // Password for the before configured username
            false,//__DIR__, // Directory, where attachments will be saved (optional)
            'UTF-8', // Server encoding (optional)
            true, // Trim leading/ending whitespaces of IMAP path (optional)
            false // Attachment filename mode (optional; false = random filename; true = original filename)
        );

        // set some connection arguments (if appropriate)
        //$connection->setConnectionArgs(CL_EXPUNGE | OP_SECURE);
        //$connection->setImapSearchOption(SE_FREE);

        return $this;
    }

    public function getConnection() {
        return $this->_connection;
    }

    public function getCount()
    {
        if (!is_null($this->_count)) {
            return $this->_count;
        }

        $this->_count = $this->_connection->countMails();

        return $this->_count;
    }

    public function getMailIds()
    {
        if (!is_null($this->_mailIds)) {
            return $this->_mailIds;
        }

        $boxMailCheck = $this->_connection->checkMailbox();

        if (!is_object($boxMailCheck) || !isset($boxMailCheck->Nmsgs)) {
            return [];
        }

        $boxMailCount = $boxMailCheck->Nmsgs;

        if($boxMailCount == 0) {
            return [];
        }

        $messageCount = $boxMailCount >= 20 ? 20 : $boxMailCount;

        $stream = $this->_connection->getImapStream();

        $result = Imap::fetch_overview(
            $stream,
            ($boxMailCount - $messageCount + 1) . ":" . $boxMailCount
        );

        return ArrayHelper::getColumn($result, 'uid');
    }

    /**
     * TODO check mail validity/errors
     * @param $id
     * @return ImapMail
     */
    public function getMailById($id)
    {
        return new ImapMail(['mail' => $this->_connection->getMail($id)]);
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteMailById($id)
    {
        Console::stdout("Deleting email with ID: {$id}\n");

        $this->_connection->deleteMail($id);

        return $this->getMailById($id)->isDeleted;
    }

    /**
     * @param $id
     * @param $box
     * @return bool
     */
    public function moveMailToBox(ImapMail $mail, $box)
    {
        $boxPath = $this->_connection->getImapPath() . '.' . $box;
        Console::stdout("Moving email with ID: {$mail->messageId} to $boxPath\n");

        $mailBoxes = $this->_connection->getMailboxes($boxPath);

        if (count($mailBoxes) == 0) {
            Console::stdout("Creating mailbox {$box}\n");
            $this->_connection->createMailbox($box);

            $mailBoxes = $this->_connection->getMailboxes($boxPath);
        }

        if (count($mailBoxes) != 1) {
            throw new \Exception('Unable to create the mailbox dir');
        }

        //The mailbox short path
        $newPath = reset($mailBoxes)['shortpath'];

        $this->_connection->moveMail($mail->id, $newPath);

        return true;
    }
}