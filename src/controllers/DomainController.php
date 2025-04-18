<?php

namespace super\ticket\controllers;

use super\ticket\base\Controller;
use dmstr\bootstrap\Tabs;
use super\ticket\models\SuperDomain;
use super\ticket\models\SuperMailer;
use super\ticket\models\SuperTicket;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\HttpException;

/**
 * This is the class for controller "DomainController".
 */
class DomainController extends Controller
{
    /**
     * @var boolean whether to enable CSRF validation for the actions in this controller.
     * CSRF validation is enabled only when both this property and [[Request::enableCsrfValidation]] are true.
     */
    public $enableCsrfValidation = false;

    /**
     * Creates a new SuperDomain model.
     * @return mixed
     */
    public function actionCreate($customer_id)
    {
        $model = new SuperDomain;
        $mailer = new SuperMailer();

        //Set Customer
        $model->customer_id = $customer_id;

        $mailer->mail_template = '{{event.superUser.email}} {{content}}';

        try {
            $model->load(\Yii::$app->request->post());
            $mailer->load(\Yii::$app->request->post());

            if ($model->save()) {
                $mailer->domain_id = $model->id;

                if ($mailer->save()) {
                    return $this->redirect(['/super/customer/update', 'id' => $customer_id]);
                }
            }
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            $model->addError('_exception', $msg);
        }

        return $this->render('create', [
            'model' => $model,
            'mailer' => $mailer
        ]);
    }

    /**
     * Updates an existing SuperDomain model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $mailer = $model->mailer ?: new SuperMailer();

        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            $mailer->load(\Yii::$app->request->post());
            $mailer->domain_id = $model->id;

            if ($model->save() && $mailer->save()) {
                return $this->redirect(Url::previous());
            }
        }

        return $this->render('update', [
            'model' => $model,
            'mailer' => $mailer,
        ]);
    }

    /**
     * Deletes an existing SuperDomain model.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $model = $this->findModel($id);
            $model->delete();
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            \Yii::$app->getSession()->addFlash('error', $msg);
            return $this->redirect(Url::previous());
        }

// TODO: improve detection
        $isPivot = strstr('$id', ',');
        if ($isPivot == true) {
            return $this->redirect(Url::previous());
        } elseif (isset(\Yii::$app->session['__crudReturnUrl']) && \Yii::$app->session['__crudReturnUrl'] != '/') {
            Url::remember(null);
            $url = \Yii::$app->session['__crudReturnUrl'];

            return $this->redirect($url);
        } else {
            return $this->redirect(['/super/customer/update', 'id' => $model->customer_id]);
        }
    }

    /**
     * Finds the SuperDomain model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SuperDomain the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SuperDomain::findOne($id)) !== null) {
            return $model;
        } else {
            throw new HttpException(404, 'The requested page does not exist.');
        }
    }
}
