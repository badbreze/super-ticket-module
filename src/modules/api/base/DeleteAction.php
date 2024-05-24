<?php
namespace super\ticket\modules\api\base;
use yii\web\ServerErrorHttpException;

class DeleteAction extends \yii\rest\DeleteAction
{
    /**
     * @inheridoc
     */
    public function run($id)
    {
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        if ($model->delete() === false && count($model->getErrors())) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        \Yii::$app->getResponse()->setStatusCode(204);
    }
}