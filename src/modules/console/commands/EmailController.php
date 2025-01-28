<?php

namespace super\ticket\modules\console\commands;

use elitedivision\amos\attachments\FileModule;
use super\ticket\helpers\AttachmentsHelper;
use super\ticket\helpers\StringHelper;
use super\ticket\helpers\TicketHelper;
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

print_r("\n\n\nEval Scope\n\n");

        if ($refferedToTicket->count()) {
            print_r("\n\n\nCreo Commento da Mail\n\n");
            self::createCommentFromMail($mail, $source);
        } else {
            print_r("\n\n\nCreo Nuovo Ticket da Mail\n\n");
            self::createTicketFromMail($mail, $source);
        }

        return true;
    }

    public function createTicketFromMail(\PhpImap\IncomingMail $mail, SuperMail $source)
    {
        $contact = EmailHelper::getContactFromEmail($mail);
        $relatedTickets = EmailHelper::getSubjectTicketReffered($mail, false)->all();
        $followerContacts = EmailHelper::getFollowersFromEmail($mail);

        $owner = UserHelper::getUserFromContact($source->domain_id, $contact);
        $followers = UserHelper::getUsersFromContactsArray($source->domain_id, $followerContacts);

        $subject = new MailSubject(['subject' => $mail->subject]);
        $contentParts = StringHelper::splitMailReply($mail->textHtml ?: $mail->textPlain);
        //echo "CONTENT PARTS\n";
//print_r($contentParts);
        $newTicket = new SuperTicket([
            'subject' => $subject->subject ?: 'Support',
            'content' => $contentParts[0] ?: $contentParts[1],
            'status_id' => $source->status_id,
            'priority_id' => $source->priority_id,
            'agent_id' => $source->agent_id,
            'source_id' => $source->id,
            'source_type' => SuperTicket::SOURCE_MAIL,
            'team_id' => $source->team_id,
            'domain_id' => $source->domain_id,
            'super_user_id' => $owner->id,
            'metadata' => serialize($mail)
        ]);


        //Calculate Due Date
        $newTicket->due_date = $newTicket->calculateDueDate();

        if ($mail->date) {
            $newTicket->created_at = $mail->date;
        }

        $newTicket->save();

        if ($newTicket->hasErrors()) {
            print_r($newTicket->getErrors());
            throw new Exception("Cant Save the new Ticket\n");
        }

        //Notify Assignee
        //$newTicket->updateAssignee($source->agent_id);

        //Store Original EML
        //$mail->

        //Attachments from mail
        if($mail->hasAttachments()) {
            foreach ($mail->getAttachments() as $attachment) {
                AttachmentsHelper::attachFile($attachment, $newTicket);
            }
        }

        //Opener Follows His Own Ticket
        SuperTicketFollower::follow($newTicket->id, $owner->id);

        //Link all related tickets
        foreach ($relatedTickets as $relatedTicket) {
            $newTicket->link('relatedTickets', $relatedTicket, ['type' => SuperTicketLink::TYPE_COPY]);
        }

        foreach ($followers as $follower) {
            SuperTicketFollower::follow($newTicket->id, $follower->id);
        }

        return $newTicket;
    }

    public function createCommentFromMail(\PhpImap\IncomingMail $mail, SuperMail $source)
    {
        $contact = EmailHelper::getContactFromEmail($mail);
        $followerContacts = EmailHelper::getFollowersFromEmail($mail);

        $owner = UserHelper::getUserFromContact($source->domain_id, $contact);
        $followers = UserHelper::getUsersFromContactsArray($source->domain_id, $followerContacts);

        $relatedMailQ = EmailHelper::getTicketMatchesQuery($mail);
        $relatedMail = $relatedMailQ->one();
        $contentParts = StringHelper::splitMailReply($mail->textHtml ?: $mail->textPlain);

        $comment = SuperTicketEvent::createTicketEvent(
            $relatedMail->id,
            SuperTicketEvent::TYPE_COMMENT,
            $contentParts[0],
            $owner->id
        );

        if (!$comment) {
            throw new \Exception('Cant Save Comment From Mail\n');
        }

        //Attachments from mail
        if($mail->hasAttachments()) {
            foreach ($mail->getAttachments() as $attachment) {
                echo "ATTACH:::\n";
                print_r($attachment->name);
                echo "\n:::ENDATTACH\n\n";
                AttachmentsHelper::attachFile($attachment, $comment);
            }
        }

        foreach ($followers as $follower) {
            SuperTicketFollower::follow($relatedMail->id, $follower->id);
        }

        return $comment;
    }
}
