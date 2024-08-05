<?php

namespace super\ticket\controllers;

use super\ticket\base\Controller;
use super\ticket\helpers\RouteHelper;
use super\ticket\helpers\TicketHelper;
use super\ticket\models\forms\TicketCommentForm;
use super\ticket\models\SuperDomain;
use super\ticket\models\SuperTicket;
use super\ticket\models\SuperTicketEvent;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\helpers\Json;

/**
 * Default controller for the `super` module
 */
class TicketController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        Url::remember();

        $tickets = SuperTicket::find()->all();

        return $this->render('index', [
            'tickets' => $tickets
        ]);
    }

    public function actionList($domain_id, $status_identifier = null)
    {
        Url::remember();

        $tickets = SuperTicket::find()
            ->joinWith('team')
            ->joinWith('status')
            //->orderBy(['super_ticket.created_at' => SORT_DESC])
            ->andWhere(['super_ticket.domain_id' => $domain_id]);

        if ($status_identifier) {
            $tickets->andWhere(['super_ticket_status.identifier' => $status_identifier]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $tickets,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ],
        ]);

        //$dataProvider->pagination->setPageSize(20);

        return $this->render('list', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionDetail($ticket_id)
    {
        $ticket = SuperTicket::find()->andWhere(['id' => $ticket_id])->one();

        $comment = new TicketCommentForm();
        $comment->ticket_id = $ticket_id;

        return $this->render('detail', [
            'ticket' => $ticket,
            'commentModel' => $comment,
        ]);
    }

    public function actionComment($ticket_id)
    {
        $comment = new TicketCommentForm();
        $comment->ticket_id = $ticket_id;

        if (\Yii::$app->request->isPost) {
            $comment->load(\Yii::$app->request->post());

            if (!$comment->save()) {
                \Yii::$app->session->addFlash('danger', \Yii::t('super', 'Cant Save Comment'));
            }
        }

        return $this->redirect(RouteHelper::toTicket($ticket_id));
    }

    public function actionUpdateAttribute($ticket_id, $attribute, $value)
    {
        $ticket = SuperTicket::findOne(['id' => $ticket_id]);

        if (!$ticket || !$ticket->id) {
            return $this->redirect(RouteHelper::toTicket($ticket_id));
        }

        switch ($attribute) {
            case 'assignee':
                $ticket->updateAssignee($value);
                break;
            case 'priority':
                $ticket->updatePriority($value);
                break;
            case 'status':
                $ticket->updateStatus($value);
                break;
        }

        return $this->redirect(RouteHelper::toTicket($ticket_id));
    }

    public function actionSearch($q)
    {
        $domains = SuperDomain::find()
            ->asArray();

        $response = [];

        foreach ($domains->all() as $domain) {
            $element = [
                'id' => $domain['id'],
                'name' => $domain['name'],
            ];

            $tickets = SuperTicket::find()
                ->andWhere([
                               'AND',
                               ['like', 'subject', $q],
                               ['domain_id' => $domain['id']]
                           ])
                ->asArray();

            foreach ($tickets->all() as $ticket) {
                $element['platforms'][] = [
                    'id' => $ticket['id'],
                    'name' => $ticket['subject'],
                ];
            }

            $response[] = $element;
        }

        return Json::encode($response);
    }

}
