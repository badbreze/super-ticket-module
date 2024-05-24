<?php

namespace super\ticket\modules\console\commands;

use PhpImap\IncomingMail;
use super\ticket\helpers\UserHelper;
use super\ticket\models\SuperMail;
use super\ticket\models\SuperTicket;
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
            $contact = EmailHelper::getContactFromEmail($mail);

            $user = UserHelper::verifyUserData(
                $source->domain_id,
                $contact['name'],
                $contact['surname'],
                $contact['email'],
                $contact['phone']
            );

            $transaction = \Yii::$app->db->beginTransaction();

            try {
                $newTicket = new SuperTicket([
                                                 'subject' => $mail->subject,
                                                 'content' => $mail->textHtml ?: $mail->textPlain,
                                                 'status_id' => $source->status_id,
                                                 'source_id' => $source->id,
                                                 'source_type' => SuperTicket::SOURCE_MAIL,
                                                 'team_id' => $source->team_id,
                                                 'domain_id' => $source->domain_id,
                                                 'user_id' => $user->id,
                                                 'metadata' => serialize($mail)
                                             ]);

                $newTicket->save();

                if($newTicket->hasErrors()) {
                    print_r($newTicket->getErrors());
                    throw new Exception("Cant Save the new Ticket\n");
                }

                if($source->move_path && !$mailbox->moveMailToBox($mail, $source->move_path)) {
                    print_r("Currently inside: ".$mailbox->getMailById($mail_id)->mailboxFolder."\n");
                    throw new Exception("Unable to move the mail to the new box\n");
                }

                $transaction->commit();
            } catch (\Exception $e) {
                Console::stdout("Unable to complete mail processing: {$e->getMessage()}");

                $transaction->rollBack();
            }

            Console::stdout("Mail parsed successiful: {$mail_id}");
        }
    }
}
