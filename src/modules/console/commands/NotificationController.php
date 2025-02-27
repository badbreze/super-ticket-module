<?php

namespace super\ticket\modules\console\commands;

use super\ticket\mail\Mailer;
use super\ticket\models\SuperTicketEvent;
use super\ticket\models\SuperTicketEventNotification;
use yii\base\Exception;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use Yii;
use yii\web\Application;

/**
 * Email console controller for the `super` module
 */
class NotificationController extends Controller
{
    public function actionIndex()
    {
        //Debug
        Console::stdout("Doing Things\n");
    }

    public function actionProcess()
    {
        $notifications = SuperTicketEventNotification::find()
        ->andWhere(['status' => SuperTicketEventNotification::STATUS_PENDING])
        ->limit(20);

        //Debug
        Console::stdout("Found {$notifications->count()} Notifications to Send\n");

        foreach ($notifications->all() as $notification) {
            try {
                Console::stdout("Processing: {$notification->id}\n");
                $this->sendNotification($notification);
            } catch (\Exception $e) {
                //Debug
                Console::stdout("Unable To Send: {$e->getMessage()} \n");
            }
        }
    }


    public function sendNotification(SuperTicketEventNotification $notification)
    {
        $event = $notification->event;
        $domainMailer = $event->ticket->domain->mailer;

        if ($domainMailer && $domainMailer->enabled) {
            $mailer = new Mailer([
                'useFileTransport' => false,
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => $domainMailer->host,
                    'username' => $domainMailer->username,
                    'password' => $domainMailer->password,
                    'port' => $domainMailer->port,
                    'encryption' => $domainMailer->encryption,
                ],
                'messageConfig' => [
                    'priority' => 3    // 1 MAX 3 NORMAL 5 LOWER
                ],
            ]);
        } else {
            throw new Exception('Mailer Not Configured for this Domain');
        }

        //Override Layout for template usage
        $mailer->htmlLayout = "@vendor/badbreze/super-ticket-system/src/views/mail/layouts/html";

        $recipients = $event->getRecipients();
        $mainRecipient = reset($recipients);

        //Drop from CC the first recipient
        unset($recipients[0]);

        if ($mainRecipient) {
            Console::stdout("To: {$mainRecipient->email}\n");

            $composition = $mailer
                ->compose("@vendor/badbreze/super-ticket-system/src/views/mail/{$event->type}", [
                    'event' => $event
                ])
                ->setFrom($domainMailer->from ?: 'no-reply@super.ticket');

            $composition->setTo($mainRecipient->email);
        } elseif (Yii::$app instanceof Application) {
            Yii::$app->session->addFlash('error', Yii::t('super', 'No Recipients For Notification'));
            return false;
        } else {
            throw new \Exception('No Recipients For Notification of ticket: ' . $event->ticket->id);
        }

        $subject = "[#T{$event->ticket_id}] - ";
        $subject .= Yii::t('super', "ticket_activity_{$event->type}", [
            'id' => $event->ticket_id,
            'subject' => $event->ticket->subject,
            'type' => $event->type,
        ]);

        Console::stdout("Subject: {$subject}\n");

        $composition
            ->setCc(ArrayHelper::map($recipients, 'email', 'fullName'))
            ->setSubject($subject);

        foreach ($event->attachments as $attachment) {
            $composition->attach($attachment->getPath(), [
                'fileName' => $attachment->name.'.'.$attachment->type
            ]);
        }

        $result = $composition->send();

        if ($result) {
            $notification->status = SuperTicketEventNotification::STATUS_SENT;
            $notification->save();
        } else {
            throw new Exception("Can't Send Notification");
        }

        return $result;
    }
}
