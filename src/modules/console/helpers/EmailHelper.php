<?php

namespace super\ticket\modules\console\helpers;

use PhpImap\IncomingMail;
use super\ticket\models\SuperMail;
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
        Console::stdout("Obtaining connection for: {$mail->name} <{$mail->address}>\n");

        $imapPath = self::expressImapPath($mail);

        Console::stdout("Imap Path: {{$imapPath}}{$mail->folder}. with: {$mail->username} & {$mail->password}\n");
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
}