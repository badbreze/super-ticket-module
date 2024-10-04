<?php

namespace super\ticket\modules\console\helpers;

use PhpImap\IncomingMail;
use super\ticket\mail\MailSubject;
use super\ticket\models\SuperMail;
use super\ticket\models\SuperTicket;
use super\ticket\models\SuperTicketStatus;
use yii\base\Exception;
use yii\helpers\Console;

class EmailHelper
{
    /**
     * @param SuperMail $source
     * @param SuperMail $mail
     * @return string
     * @throws \Exception
     */
    public static function expressImapPath(SuperMail $mail)
    {
        //The Base Imap Configuration
        $imapPath = "{$mail->host}:{$mail->port}";

        switch ($mail->type) {
            case SuperMail::TYPE_IMAP:
                {
                    $imapPath .= '/imap';
                }
                break;
            case SuperMail::TYPE_IMAP_SSL:
                {
                    $imapPath .= '/imap/ssl';
                }
                break;
            case SuperMail::TYPE_POP:
                {
                    $imapPath .= '/pop3';
                }
                break;
            case SuperMail::TYPE_POP_SSL:
                {
                    $imapPath .= '/pop3/ssl';
                }
                break;
            default:
                throw new \Exception('The MailBox Must Have One of the Supported Types');
        }

        if($mail->skip_ssl_validation === 1) {
            $imapPath .= '/novalidate-cert';
        }

        return $imapPath;
    }

    /**
     * Get a mailbox instance by source
     * @param SuperMail $mail
     * @return \PhpImap\Mailbox
     * @throws Exception
     * @throws \PhpImap\Exceptions\InvalidParameterException
     * @throws \yii\base\InvalidConfigException
     */
    public static function getMailBoxConnection(SuperMail $mail)
    {
        $imapPath = self::expressImapPath($mail);

        // Create PhpImap\Mailbox instance for all further actions
        $connection = new \PhpImap\Mailbox(
            "{{$imapPath}}{$mail->folder}", // IMAP server and mailbox folder
            $mail->username, // Username for the before configured mailbox
            $mail->password, // Password for the before configured username
            false,//__DIR__, // Directory, where attachments will be saved (optional)
            'UTF-8', // Server encoding (optional)
            true, // Trim leading/ending whitespaces of IMAP path (optional)
            false // Attachment filename mode (optional; false = random filename; true = original filename)
        );

        // set some connection arguments (if appropriate)
        //$connection->setConnectionArgs(CL_EXPUNGE | OP_SECURE);
        //$connection->setImapSearchOption(SE_FREE);

        return $connection;
    }

    /**
     * @param IncomingMail $email
     * @return array
     */
    public static function getContactFromEmail(IncomingMail $email) {
        $result = [
            'name' => $email->fromName,
            'surname' => null,
            'email' => $email->fromAddress,
            'phone' => null,
            'host' => $email->fromHost,
        ];

        return $result;
    }

    /**
     * @param IncomingMail $mail
     * @return int
     */
    public static function isMailTicketReffered(\PhpImap\IncomingMail $mail) {
        //TODO implementare se necessario verifiche nel corpo della mail per
        // constatarne l'appartenenza ad un ticket già esistente
        return self::getMailTicketReffered($mail)->count();
    }

    public static function getMailTicketReffered(\PhpImap\IncomingMail $mail) {
        $subject = new MailSubject(['subject' => $mail->subject]);

        return self::getSubjectTicketReffered($subject, $mail->fromAddress);
    }

    public static function getSubjectTicketReffered(MailSubject $subject, $sender = false, $ignoreClosed = true) {
        $q = self::getTicketMatchesQuery($subject, $sender, $ignoreClosed);

        //TODO need a decision on how to manage this situation
        /*if ($q->count() > 1)
            throw new \Exception('Unexpected Number of Tickets on the Same Subject/user');*/

        return $q;
    }

    public static function getTicketMatchesQuery(MailSubject $subject, $sender = false, $ignoreClosed = true) {
        $subjectTicketId = self::getTicketIdFromSubject($subject);

        $q = SuperTicket::find();

        //TODO questa logica gestisce la possibilità di ignorare i ticket chiusi
        // per favoreggiare i nuovi workflow anche a parità di subject
        if($ignoreClosed) {
            $q->joinWith('status');
            $q->andWhere(['<>', 'super_ticket_status.identifier', SuperTicketStatus::STATUS_CLOSED]);
        }

        if($subjectTicketId) {
            $q->andWhere(['super_ticket.id' => $subjectTicketId]);
        } else {
            $q->andWhere(['subject' => $subject->subject]);
        }

        if($sender != false) {
            $q->joinWith('superUser');
            $q->andWhere(['super_user.email' => $sender]);
        }

        return $q;
    }

    public static function getTicketIdFromSubject(MailSubject $subject) {
        $matches = null;
        if(preg_match("/\[\#T([0-9]+)\]/", $subject->subject, $matches)) {
            //Key 1 of the array will expect to contains the numeric ID
            return $matches[1];
        }

        return null;
    }
}