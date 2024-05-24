<?php

namespace super\ticket\controllers\api;

/**
* This is the class for REST controller "TeamController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class TeamController extends \yii\rest\ActiveController
{
public $modelClass = 'super\ticket\models\SuperTeam';
}
