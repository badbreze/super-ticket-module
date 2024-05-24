<?php

use super\ticket\helpers\RouteHelper;

/**
 * @var $this \yii\web\View
 * @var $tickets \super\ticket\models\SuperTicket[]
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
\super\ticket\assets\EnjoyAsset::register($this);
?>

<div class="row g-0">
    <?= $this->render('../parts/navigator'); ?>
    <div class="col-sm-10 p-4">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>
                        User
                    </th>
                    <th>
                        Subject
                    </th>
                    <th>
                        Assignee
                    </th>
                    <th>
                        Priority
                    </th>
                    <th>
                        Deadline
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($dataProvider->getModels() as $ticket) : ?>
                    <tr class="linked-row"
                        data-href="<?= RouteHelper::toTicket($ticket->id, $ticket->domain_id); ?>">
                        <td class="py-2">
                            <div class="d-flex flex-row">
                                <div class="p-2 bd-highlight">
                                    <img class="ticket-avatar rounded-circle"
                                         src="https://www.gravatar.com/avatar/<?= md5(
                                             $ticket->user->email
                                         ); ?>?d=robohash"
                                         alt="avatar"
                                    />
                                </div>

                                <div class="p-2 bd-highlight">
                                    <b><?= $ticket->user->name . ' ' . $ticket->user->surname; ?></b>
                                    <div><i><?= $ticket->user->email; ?></i></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="#">
                                <?= $ticket->subject; ?>
                            </a>
                        </td>
                        <td class="py-2">
                            <div class="d-flex flex-row">
                                <div class="p-2 bd-highlight">
                                    <img class="ticket-avatar rounded-circle"
                                         src="https://www.gravatar.com/avatar/<?= md5(
                                             $ticket->agent->email
                                         ); ?>?d=robohash" alt="avatar"/>
                                </div>

                                <div class="p-2 bd-highlight">
                                    <b><?= $ticket->agent->name . ' ' . $ticket->agent->surname; ?></b>
                                    <div><i><?= $ticket->agent->email; ?></i></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($ticket->priority) : ?>
                                <span class="badge badge-warning">
                                    <?= $ticket->priority->name; ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-secondary">
                                    None
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $ticket->due_date; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?= \yii\bootstrap4\LinkPager::widget([
                                                  'pagination' => $dataProvider->getPagination(),
                                              ]); ?>
    </div>
</div>
