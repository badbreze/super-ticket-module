<?php

use super\ticket\helpers\RouteHelper;
use super\ticket\helpers\DomainHelper;
use super\ticket\helpers\StatusHelper;
use super\ticket\models\SuperTicket;
use yii\web\View;

/**
 * @var $this View
 * @var $ticket SuperTicket
 */

$currentDomain = DomainHelper::getCurrentDomain();
$statuses = StatusHelper::getAvailableStatuses();
?>
<div class="col-sm-2 grid-margin p-0">
    <div class="ticket-sidebar p-3 h-100">
        <h5 class="mb-4">
            <i class="fas fa-wind"></i>
            <?= Yii::t('super', 'Ticket Details'); ?>
        </h5>
        <div class="mb-2">
            <b>
                <i class="fas fa-stream"></i>
                <?= Yii::t('super', 'Status'); ?>
            </b>
            <div class="dropdown">
                <button type="button" id="chengeStatusButton" class="btn dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= Yii::t('super', $ticket->status->name) ?: '--'; ?>
                </button>
                <div class="dropdown-menu" aria-labelledby="chengeStatusButton">
                    <?php foreach ($ticket->availableStatuses as $status): ?>
                        <a href="<?= RouteHelper::updateTicketAttribute($ticket->id, 'status', $status->id); ?>"
                           data-status_id="<?= $status->id; ?>" class="dropdown-item status-changer-element">
                            <i class="mdi mdi-file-pdf text-primary"></i>
                            <?= Yii::t('super', $status->name); ?>
                        </a> <!-- dropdown-item -->
                    <?php endforeach; ?>
                </div> <!-- dropdown-menu -->
            </div> <!-- dropdown -->
        </div>
        <div class="mb-2">
            <b>
                <i class="fas fa-walking"></i>
                <?= Yii::t('super', 'Assignee'); ?>
            </b>
            <div class="dropdown">
                <button type="button" id="chengeStatusButton" class="btn dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $ticket->agent->fullName ?: Yii::t('super', 'No One'); ?>
                </button>
                <div class="dropdown-menu" aria-labelledby="chengeStatusButton">
                    <?php foreach ($ticket->availableAssignees as $assignee): ?>
                        <a href="<?= RouteHelper::updateTicketAttribute($ticket->id, 'assignee', $assignee->id); ?>"
                           data-status_id="<?= $assignee->id; ?>" class="dropdown-item status-changer-element">
                            <i class="mdi mdi-file-pdf text-primary"></i>
                            <?= $assignee->fullName; ?>
                        </a> <!-- dropdown-item -->
                    <?php endforeach; ?>
                </div> <!-- dropdown-menu -->
            </div> <!-- dropdown -->
        </div>
        <div class="mb-2">
            <b>
                <i class="fas fa-sort-numeric-up"></i>
                <?= Yii::t('super', 'Priority'); ?>
            </b>
            <div class="dropdown">
                <button type="button" id="chengeStatusButton" class="btn dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= Yii::t('super', $ticket->priority->name) ?: '--'; ?>
                </button>
                <div class="dropdown-menu" aria-labelledby="chengeStatusButton">
                    <?php foreach ($ticket->availablePriorities as $priority): ?>
                        <a href="<?= RouteHelper::updateTicketAttribute($ticket->id, 'priority', $priority->id); ?>"
                           data-status_id="<?= $priority->id; ?>" class="dropdown-item status-changer-element">
                            <i class="mdi mdi-file-pdf text-primary"></i>
                            <?= Yii::t('super', $priority->name); ?> (<?= $priority->sla->grace_period; ?>h)
                        </a> <!-- dropdown-item -->
                    <?php endforeach; ?>
                </div> <!-- dropdown-menu -->
            </div> <!-- dropdown -->
        </div>
        <div class="mb-2">
            <b>
                <i class="fas fa-exclamation-circle"></i>
                <?= Yii::t('super', 'SLA'); ?>
            </b>
            <p><?= $ticket->priority->sla ?: '--'; ?></p>
        </div>
        <div class="mb-2">
            <b>
                <i class="far fa-calendar-alt"></i>
                <?= Yii::t('super', 'Deadline'); ?>
            </b>
            <p><?= $ticket->due_date ?: '--'; ?></p>
        </div>
        <div class="mb-2">
            <b>
                <i class="fas fa-people-carry"></i>
                <?= Yii::t('super', 'Team'); ?>
            </b>
            <p><?= $ticket->team->name ?: '--'; ?></p>
        </div>
        <div class="mb-2">
            <b>
                <i class="fas fa-mail-bulk"></i>
                <?= Yii::t('super', 'Source'); ?>
            </b>
            <p><?= Yii::t('super', $ticket->source_type) ?: '--'; ?></p>
        </div>
        <div class="mb-2">
            <b>
                <i class="fas fa-quote-right"></i>
                <?= Yii::t('super', 'Followers'); ?>
            </b>
            <p>
                <ul>
                    <?php foreach ($ticket->followers as $follower): ?>
                        <li><?= $follower->superUser->fullName; ?> (<?= $follower->superUser->id; ?>[<?= $follower->superUser->domain_id; ?>])</li>
                    <?php endforeach; ?>
                </ul>
            </p>
        </div>
    </div>
</div>