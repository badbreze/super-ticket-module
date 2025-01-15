<?php

namespace super\ticket\controllers;

use super\ticket\base\Controller;
use super\ticket\helpers\RouteHelper;
use super\ticket\helpers\TicketHelper;
use super\ticket\models\forms\TicketCommentForm;
use super\ticket\models\SuperDomain;
use super\ticket\models\SuperTicket;
use super\ticket\models\SuperTicketEvent;
use super\ticket\models\SuperUser;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Json;

/**
 * Default controller for the `super` module
 */
class TicketController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [
                                'disabled-platforms',
                                'get-domains',
                                'get-environments',
                                'get-ecs-services',
                                'cli',
                                'projects',
                                'scoreboard',
                                'operate',
                                'search',
                                'test',
                            ],
                            'roles' => ['SUPER_USER'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        //'action_name' => ['post', 'get']
                    ]
                ]
            ]
        );
    }

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
            //->orderBy(['super_ticket.due_date' => SORT_ASC])
            ->andWhere(['super_ticket.domain_id' => $domain_id]);

        if ($status_identifier) {
            $tickets->andWhere(['super_ticket_status.identifier' => $status_identifier]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $tickets,
            'sort' => [
                'defaultOrder' => [
                    'due_date' => SORT_ASC,
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
        $comment->recipients = ArrayHelper::getColumn($ticket->followers, 'id');

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

    public function actionAddRecipient($ticket_id) {
        $ticket = SuperTicket::findOne(['id' => $ticket_id]);

        if (!$ticket) {
            Yii::$app->session->addFlash('danger', \Yii::t('super', 'Cant Add Recipient'));
            return $this->redirect(RouteHelper::toTicket($ticket_id));
        }

        $model = new SuperUser();
        $model->domain_id = $ticket->domain_id;

        if (!$model->load(Yii::$app->request->post()) || $model->save()) {
            Yii::$app->session->addFlash('danger', \Yii::t('super', 'Cant Save Recipient'));
            return $this->redirect(RouteHelper::toTicket($ticket_id));
        }

        return $this->render('parts/recipient_modal', [
            'model' => $model,
            'ticket' => $ticket,
        ]);
    }

}
