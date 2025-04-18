<?php

namespace super\ticket\modules\api\models;

use super\ticket\models\SuperUser;
use Yii;

class SuperTicket extends \super\ticket\models\SuperTicket
{
    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return [
            'id',
            'subject',
            'content',
            'due_date',
            'created_at',
            'updated_at',
            'status' => function (parent $model) {
                return $model->status->toArray([
                                                   'id',
                                                   'name',
                                                   'identifier',
                                               ]);
            },
            'assignee' => function (parent $model) {
                return $model->agent ?
                    $model->agent->toArray([
                                               'id',
                                               'name',
                                               'surname',
                                               'email',
                                               'profile' => function (SuperUser $model) {
                                                   return $model ?
                                                       $model->toArray([
                                                                                 'id',
                                                                                 'name',
                                                                                 'surname',
                                                                                 'email',
                                                                             ]) :
                                                       null;
                                               },
                                           ]) :
                    null;
            },
            'team' => function (parent $model) {
                return $model->team ?
                    $model->team->toArray([
                                              'id',
                                              'name',
                                              'description',
                                          ]) :
                    null;
            },
            'priority' => function (parent $model) {
                return $model->priority ?
                    $model->priority->toArray([
                                                  'id',
                                                  'name',
                                                  'identifier',
                                              ]) :
                    null;
            },
            'source' => function (parent $model) {
                return $model->source ?
                    $model->source->toArray([
                                                'id',
                                                'name',
                                                'description',
                                            ]) :
                    null;
            },
            'creator' => function (parent $model) {
                return $model->superUser ?
                    $model->superUser->toArray([
                                              'id',
                                              'name',
                                              'surname',
                                              'email',
                                          ]) :
                    null;
            },
        ];
    }
}
