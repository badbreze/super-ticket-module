<?php

use super\ticket\helpers\DomainHelper;
use super\ticket\helpers\StatusHelper;
use super\ticket\helpers\TicketHelper;
use super\ticket\helpers\RouteHelper;

/**
 * @var $this \yii\web\View
 */
$profile = Yii::$app->user->identity->profile;

$currentDomain = DomainHelper::getCurrentDomain();
$domains = DomainHelper::getAvailableDomains();
$statuses = StatusHelper::getAvailableStatuses();
$currentStatus = StatusHelper::getCurrentStatus();
?>
<div class="col-sm-2 p-0">
    <div class="ticket-sidebar p-3 h-100">
        <!--div class="row mb-4">
            <div class="col text-center">
                <div class="btn-group">
                    <a href="#" type="button" class="btn btn-primary align-middle">
                        <i class="fa fa-plus mr-2"></i> Nuovo Ticket
                    </a>
                    <a href="#" type="button" class="btn btn-light align-middle">
                        <i class="fas fa-info-circle"></i>
                    </a>
                </div>
            </div>
        </div-->
        <div class="row mb-4">
            <div class="col">
                <h6 class=""><?= Yii::t('super', 'Status'); ?></h6>

                <?php foreach ($statuses as $status): ?>
                    <p>
                        <a class="<?= $status->identifier == $currentStatus->identifier ? 'active' : ''; ?>" href="<?= RouteHelper::toOrganization($currentDomain->id, $status->identifier); ?>">
                            <i class="fa fa-angle-right"></i>
                            <?= Yii::t('super', $status->name); ?>
                        </a>
                    </p>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>