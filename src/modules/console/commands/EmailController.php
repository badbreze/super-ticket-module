<?php

namespace super\ticket\modules\console\commands;

use super\ticket\base\ImapMail;
use super\ticket\helpers\AttachmentsHelper;
use super\ticket\helpers\StringHelper;
use super\ticket\helpers\UserHelper;
use super\ticket\mail\MailSubject;
use super\ticket\models\SuperMail;
use super\ticket\models\SuperTicket;
use super\ticket\models\SuperTicketEvent;
use super\ticket\models\SuperTicketFollower;
use super\ticket\models\SuperTicketLink;
use super\ticket\base\ImapMailBox;
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

        $time = microtime();

        //Debug
        Console::stdout("Found {$mailSourcesQuery->count()} Mail Sources\n");

        foreach ($mailSourcesQuery->all() as $mailSource) {
            try {
                Console::stdout("Processing: {$mailSource->name} ({$mailSource->address})\n");
                $this->processMailBox($mailSource);
            } catch (\Exception $e) {
                //Debug
                Console::stdout("Unable To Process Mailbox: {$e->getMessage()}\n");
            }
        }
    }

    public function processMailBox(SuperMail $source)
    {
        $mailbox = new ImapMailBox([
            'super_mail' => $source
        ]);

        $time = microtime();

        //Debug
        Console::stdout("Found: {$mailbox->count} mails\n");

        foreach ($mailbox->mailIds as $mail_id) {
            $mail = $mailbox->getMailById($mail_id);

            //Debug
            Console::stdout("Process: {$mail_id}\n");
            Console::stdout("Subject: {$mail->subject}\n");

            $transaction = \Yii::$app->db->beginTransaction();

            try {
                self::evaluateMailScope($mail, $source);

                if ($source->move_path && !$mailbox->moveMailToBox($mail, $source->move_path)) {
                    print_r("Currently inside: " . $mailbox->getMailById($mail_id)->mailboxFolder . "\n");
                    throw new Exception("Unable to move the mail to the new box\n");
                }

                Console::stdout("Moved Mail: {$mail_id}\n");

                $transaction->commit();
            } catch (\Exception $e) {
                Console::stdout("Unable to complete mail processing: {$e->getMessage()}\n");
                Console::stdout($e->getTraceAsString());

                $transaction->rollBack();
            }
        }
    }

    public function evaluateMailScope(ImapMail $mail, SuperMail $source)
    {
        $refferedToTicket = EmailHelper::getMailTicketReffered($mail);

        print_r("\nChoosing Mail Scope...\n");

        if ($refferedToTicket->count()) {
            Console::stdout("\nThis Mail is a Comment\n");
            self::createCommentFromMail($mail, $source);
        } else {
            Console::stdout("\nThis Mail is a New Ticket\n");
            self::createTicketFromMail($mail, $source);
        }

        return true;
    }

    public function createTicketFromMail(ImapMail $mail, SuperMail $source)
    {
        $contact = EmailHelper::getContactFromEmail($mail);
        $relatedTickets = EmailHelper::getSubjectTicketReffered($mail, false)->all();
        $followerContacts = EmailHelper::getFollowersFromEmail($mail);

        $owner = UserHelper::getUserFromContact($source->domain_id, $contact);
        $followers = UserHelper::getUsersFromContactsArray($source->domain_id, $followerContacts);

        $subject = new MailSubject(['subject' => $mail->subject]);
        $contentParts = StringHelper::splitMailReply($mail->content);

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
            'metadata' => [
                'id' => $mail->messageId,
                'date' => $mail->date->format('Y-m-d H:i:s'),
                'cc' => json_encode($mail->cc),
            ]
        ]);

        //Calculate Due Date
        $newTicket->due_date = $newTicket->calculateDueDate();

        if ($mail->date) {
            $newTicket->created_at = $mail->date->format('Y-m-d H:i:s');
        }

        $newTicket->save();
        Console::stdout("Saved ticket {$newTicket->id}!\n");

        if ($newTicket->hasErrors()) {
            print_r($newTicket->getErrors());
            throw new Exception("Cant Save the new Ticket\n");
        }

        //Notify Assignee
        $newTicket->updateAssignee($source->agent_id);

        //Attachments from mail
        if(!empty($mail->getAttachments())) {
            foreach ($mail->getAttachments() as $attachment) {
                AttachmentsHelper::attachFile($attachment, $newTicket);
            }
        }

        //Opener Follows His Own Ticket
        Console::stdout("Make Opener Follow: {$owner->id}\n");
        SuperTicketFollower::follow($newTicket->id, $owner->id);

        //Agent follows ticket
        Console::stdout("Make Agent Follow: {$newTicket->agent_id}\n");
        SuperTicketFollower::follow($newTicket->id, $newTicket->agent_id);

        //Link all related tickets
        foreach ($relatedTickets as $relatedTicket) {
            Console::stdout("Linking to ticket: {$relatedTicket->id}\n");
            $newTicket->link('relatedTickets', $relatedTicket, ['type' => SuperTicketLink::TYPE_COPY]);
        }

        foreach ($followers as $follower) {
            Console::stdout("Adding Followers: {$follower->id}\n");
            SuperTicketFollower::follow($newTicket->id, $follower->id);
        }

        return $newTicket;
    }

    public function createCommentFromMail(ImapMail $mail, SuperMail $source)
    {
        $contact = EmailHelper::getContactFromEmail($mail);
        $followerContacts = EmailHelper::getFollowersFromEmail($mail);

        $owner = UserHelper::getUserFromContact($source->domain_id, $contact);
        $followers = UserHelper::getUsersFromContactsArray($source->domain_id, $followerContacts);

        $ticketRelatedToMailQ = EmailHelper::getTicketMatchesQuery($mail);
        $ticketRelatedToMail = $ticketRelatedToMailQ->one();
        $contentParts = StringHelper::splitMailReply($mail->content);

        $comment = SuperTicketEvent::createTicketEvent(
            $ticketRelatedToMail->id,
            SuperTicketEvent::TYPE_COMMENT,
            $contentParts[0],
            $owner->id,
            [
                'recipients' => $owner->id
            ]
        );

        if (!$comment) {
            throw new \Exception('Cant Save Comment From Mail\n');
        }

        //Attachments from mail
        if(!empty($mail->getAttachments())) {
            foreach ($mail->getAttachments() as $attachment) {
                AttachmentsHelper::attachFile($attachment, $comment);
            }
        }

        foreach ($followers as $follower) {
            Console::stdout("Adding Followers Of Comment: {$follower->id}\n");
            SuperTicketFollower::follow($ticketRelatedToMail->id, $follower->id);
        }

        return $comment;
    }
}
