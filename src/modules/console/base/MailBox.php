<?php

namespace super\ticket\modules\console\base;

use PhpImap\IncomingMail;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use PhpImap\Imap;

/**
 * This is the model class for table "{{%super_customer_role_permission_mm}}".
 *
 * @property array $mailIds
 * @property int $count
 */
class MailBox extends \yii\base\BaseObject
{
    public \PhpImap\Mailbox $connection;
    private $_count = null;
    private $_mailIds = null;

    public function getCount()
    {
        if (!is_null($this->_count)) {
            return $this->_count;
        }

        $this->_count = $this->connection->countMails();

        return $this->_count;
    }

    public function getMailIds()
    {
        if (!is_null($this->_mailIds)) {
            return $this->_mailIds;
        }

        $dateRange = date("d M Y", strToTime("-1 days"));

        $boxMailCheck = $this->connection->checkMailbox();

        if (!is_object($boxMailCheck) || !isset($boxMailCheck->Nmsgs)) {
            return [];
        }

        $boxMailCount = $boxMailCheck->Nmsgs;

        if($boxMailCount == 0) {
            return [];
        }

        $messageCount = $boxMailCount >= 20 ? 20 : $boxMailCount;

        $stream = $this->connection->getImapStream();

        $result = Imap::fetch_overview(
            $stream,
            ($boxMailCount - $messageCount + 1) . ":" . $boxMailCount
        );

        return ArrayHelper::getColumn($result, 'uid');
    }

    /**
     * @param $id
     * @return \PhpImap\IncomingMail
     */
    public function getMailById($id)
    {
        return $this->connection->getMail($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteMailById($id)
    {
        Console::stdout("Deleting email with ID: {$id}\n");

        $this->connection->deleteMail($id);

        return $this->getMailById($id)->isDeleted;
    }

    /**
     * @param $id
     * @param $box
     * @return bool
     */
    public function moveMailToBox(IncomingMail $mail, $box)
    {
        $boxPath = $this->connection->getImapPath() . '.' . $box;
        Console::stdout("Moving email with ID: {$mail->messageId} to $boxPath\n");

        $mailBoxes = $this->connection->getMailboxes($boxPath);

        if (count($mailBoxes) == 0) {
            Console::stdout("Creating mailbox {$box}");
            $this->connection->createMailbox($box);

            $mailBoxes = $this->connection->getMailboxes($boxPath);
        }

        if (count($mailBoxes) != 1) {
            throw new \Exception('Unable to create the mailbox dir');
        }

        //The mailbox short path
        $newPath = reset($mailBoxes)['shortpath'];

        $this->connection->moveMail($mail->id, $newPath);

        return true;
    }
}