<?php
namespace super\ticket\modules\api\controllers;

use super\ticket\helpers\UserHelper;
use super\ticket\models\forms\SuperTicketBulkForm;
use super\ticket\modules\api\base\ActiveController;

/**
 * Default controller for the `super` module
 */
class TeamsController extends ActiveController
{

    public $modelClass = \super\ticket\modules\api\models\SuperTicket::class;

    public function actions()
    {
        return [];
    }

    public function actionIndex() {
        return "OK";
    }

    public function actionAddMember($team_id) {
        $team = \super\ticket\models\SuperTeam::findOne($team_id);

        $members = \Yii::$app->request->post('members');

        foreach($members as $member) {
            $user = \super\ticket\models\SuperUser::findOne($member);
            //print_r($user);die;
            $team->link('teamMembers', $user);
        }

        return true;
    }
}
