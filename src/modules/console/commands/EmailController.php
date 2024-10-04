<?php

namespace super\ticket\modules\console\commands;

use elitedivision\amos\attachments\FileModule;
use super\ticket\helpers\AttachmentsHelper;
use super\ticket\helpers\UserHelper;
use super\ticket\mail\MailSubject;
use super\ticket\models\SuperMail;
use super\ticket\models\SuperTicket;
use super\ticket\models\SuperTicketEvent;
use super\ticket\models\SuperTicketFollower;
use super\ticket\models\SuperTicketLink;
use super\ticket\modules\console\base\MailBox;
use super\ticket\modules\console\helpers\EmailHelper;
use yii\base\Exception;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Email console controller for the `super` module
 */
class EmailController extends Controller
{
    public function actionIndex()
    {
        //Debug
        Console::stdout("Doing Things\n");
    }

    public function actionProcess()
    {
        $mailSourcesQuery = SuperMail::find();

        //Debug
        Console::stdout("Found {$mailSourcesQuery->count()} Mail Sources\n");

        foreach ($mailSourcesQuery->all() as $mailSource) {
            try {
                Console::stdout("Processing: {$mailSource->id}\n");
                $this->processMailBox($mailSource);
            } catch (\Exception $e) {
                //Debug
                Console::stdout("Unable To Process Mailbox: {$e->getMessage()}\n");
            }
        }
    }

    public function processMailBox(SuperMail $source)
    {
        $mailbox = new MailBox([
            'connection' => EmailHelper::getMailBoxConnection($source)
        ]);

        foreach ($mailbox->mailIds as $mail_id) {
            $mail = $mailbox->getMailById($mail_id);

            $transaction = \Yii::$app->db->beginTransaction();

            try {
                self::evaluateMailScope($mail, $source);

                if ($source->move_path && !$mailbox->moveMailToBox($mail, $source->move_path)) {
                    print_r("Currently inside: " . $mailbox->getMailById($mail_id)->mailboxFolder . "\n");
                    throw new Exception("Unable to move the mail to the new box\n");
                }

                $transaction->commit();
            } catch (\Exception $e) {
                Console::stdout("Unable to complete mail processing: {$e->getMessage()}");
                Console::stdout($e->getTraceAsString());

                $transaction->rollBack();
            }

            Console::stdout("Mail parsed successiful: {$mail_id}\n");
        }
    }

    public function evaluateMailScope(\PhpImap\IncomingMail $mail, SuperMail $source)
    {
        $refferedToTicket = EmailHelper::getMailTicketReffered($mail);

        if ($refferedToTicket->count()) {
            self::createCommentFromMail($mail, $source);
        } else {
            self::createTicketFromMail($mail, $source);
        }

        return true;
    }

    public function createTicketFromMail(\PhpImap\IncomingMail $mail, SuperMail $source)
    {
        $contact = EmailHelper::getContactFromEmail($mail);
        $subject = new MailSubject(['subject' => $mail->subject]);

        $relatedTickets = EmailHelper::getSubjectTicketReffered(
            $subject,
            $mail->fromAddress,
            false)
            ->all();

        $user = UserHelper::parseAndGetUser(
            $source->domain_id,
            $contact['name'],
            $contact['surname'],
            $contact['email'],
            $contact['phone']
        );

        $newTicket = new SuperTicket([
            'subject' => $mail->subject,
            'content' => $mail->textHtml ?: $mail->textPlain,
            'status_id' => $source->status_id,
            'priority_id' => $source->priority_id,
            'agent_id' => $source->agent_id,
            'source_id' => $source->id,
            'source_type' => SuperTicket::SOURCE_MAIL,
            'team_id' => $source->team_id,
            'domain_id' => $source->domain_id,
            'super_user_id' => $user->id,
            'metadata' => serialize($mail)
        ]);

        if ($mail->date) {
            $newTicket->created_at = $mail->date;
        }

        $newTicket->save();

        if ($newTicket->hasErrors()) {
            print_r($newTicket->getErrors());
            throw new Exception("Cant Save the new Ticket\n");
        }

        //Attachments from mail
        if($mail->hasAttachments()) {
            foreach ($mail->getAttachments() as $attachment) {
                AttachmentsHelper::attachFile($attachment, $newTicket);
            }
        }

        //Opener Follows His Own Ticket
        SuperTicketFollower::follow($newTicket->id, $user->id);

        //Link all related tickets
        foreach ($relatedTickets as $relatedTicket) {
            $newTicket->link('relatedTickets', $relatedTicket, ['type' => SuperTicketLink::TYPE_COPY]);
        }

        return $newTicket;
    }

    public function createCommentFromMail(\PhpImap\IncomingMail $mail, SuperMail $source)
    {
        $contact = EmailHelper::getContactFromEmail($mail);

        $user = UserHelper::parseAndGetUser(
            $source->domain_id,
            $contact['name'],
            $contact['surname'],
            $contact['email'],
            $contact['phone']
        );

        $subject = new MailSubject(['subject' => $mail->subject]);

        $relatedMailQ = EmailHelper::getTicketMatchesQuery($subject, $mail->fromAddress);
        $relatedMail = $relatedMailQ->one();

        $comment = SuperTicketEvent::createTicketEvent(
            $relatedMail->id,
            SuperTicketEvent::TYPE_COMMENT,
            $mail->textHtml ?: $mail->textPlain,
            $user->id
        );

        if (!$comment) {
            throw new \Exception('Cant Save Comment From Mail\n');
        }

        //Attachments from mail
        if($mail->hasAttachments()) {
            foreach ($mail->getAttachments() as $attachment) {
                echo "ATTACH:::\n";
                print_r($attachment);
                echo "\n:::ENDATTACH\n\n";
                AttachmentsHelper::attachFile($attachment, $comment);
            }
        }

        return $comment;
    }
}
