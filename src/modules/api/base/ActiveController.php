<?php

namespace super\ticket\modules\api\base;

use yii\helpers\ArrayHelper;

class ActiveController extends \yii\rest\ActiveController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            //''
        ]);
    }
}