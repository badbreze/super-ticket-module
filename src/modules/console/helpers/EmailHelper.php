<?php

namespace super\ticket\modules\console\helpers;

use super\ticket\base\ImapMail;
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
     * @param ImapMail $email
     * @return array
     */
    public static function getContactFromEmail(ImapMail $email) {
        $nameParts =  explode(' ', $email->fromName);
        $name = reset($nameParts);
        unset($nameParts[0]);

        $result = [
            'name' => $name,
            'surname' => trim(join(' ', $nameParts)),
            'email' => $email->fromAddress,
            'phone' => null,
            'host' => $email->fromHost,
        ];

        return $result;
    }

    public static function getFollowersFromEmail(ImapMail $email) {
        $ccs = $email->cc;
        $result = [];

        foreach ($ccs as $mail => $fullName) {
            $nameParts =  explode(' ', $fullName);
            $name = reset($nameParts);
            unset($nameParts[0]);

            $result[] = [
                'name' => $name,
                'surname' => trim(join(' ', $nameParts)),
                'email' => $mail,
                'phone' => null,
                'host' => $email->fromHost,
            ];
        }

        return $result;
    }

    /**
     * @param ImapMail $mail
     * @return int
     */
    public static function isMailTicketReffered(ImapMail $mail) {
        //TODO implementare se necessario verifiche nel corpo della mail per
        // constatarne l'appartenenza ad un ticket già esistente
        return self::getMailTicketReffered($mail)->count();
    }

    public static function getMailTicketReffered(ImapMail $mail) {
        $bySubject = self::getSubjectTicketReffered($mail);
        $byThread = self::getThreadTicketReffered($mail);

        return $bySubject;
    }

    public static function getSubjectTicketReffered(ImapMail $mail, $ignoreClosed = true) {
        $q = self::getTicketMatchesQuery($mail, $ignoreClosed);

        //TODO need a decision on how to manage this situation
        /*if ($q->count() > 1)
            throw new \Exception('Unexpected Number of Tickets on the Same Subject/user');*/

        return $q;
    }

    public static function getThreadTicketReffered(ImapMail $mail, $ignoreClosed = true) {
        $q = self::getTicketMatchesQuery($mail, $ignoreClosed);

        //TODO need a decision on how to manage this situation
        /*if ($q->count() > 1)
            throw new \Exception('Unexpected Number of Tickets on the Same Subject/user');*/

        return $q;
    }

    public static function getTicketMatchesQuery(ImapMail $mail, $ignoreClosed = true) {
        $subject = new MailSubject(['subject' => $mail->subject]);
        $subjectTicketId = self::getTicketIdFromSubject($subject);

        $q = SuperTicket::find();

        //TODO questa logica gestisce la possibilità di ignorare i ticket chiusi
        // per favoreggiare i nuovi workflow anche a parità di subject
        if($ignoreClosed) {
            $q->joinWith('status');
            $q->andWhere(['not', ['super_ticket_status.identifier' => SuperTicketStatus::STATUS_CLOSED]]);
        }

        if($subjectTicketId) {
            $q->andWhere(['super_ticket.id' => $subjectTicketId]);
        } else {
            $q->andWhere(['subject' => $subject->subject]);
        }

        /* TODO For now anyone can append self to existing ticket
        if($mail->fromAddress != false) {
            $q->joinWith('superUser');
            $q->andWhere(['super_user.email' => $mail->fromAddress]);
        }*/

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