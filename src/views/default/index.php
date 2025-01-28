<?php
use super\ticket\helpers\TicketHelper;

/**
 * @var $this \yii\web\View
 * @var $tickets \super\ticket\models\SuperTicket[]
 */

\super\ticket\assets\EnjoyAsset::register($this);
?>
<h4><?= Yii::t('super', 'Choose Workspace'); ?></h4>
    <div class="row pb-4">

        <?php foreach (\super\ticket\helpers\DomainHelper::getAvailableDomains() as $availableDomain) : ?>
        <div class="col-lg-3 d-flex grid-margin stretch-card">
            <div class="card sale-diffrence-border">
                <div class="card-body">
                    <h4 class="card-title mb-2">
                        <a href="<?= TicketHelper::getListUrl(null,$availableDomain->id); ?>"><?= $availableDomain->name; ?></a>
                    </h4>
                    <small class="text-muted"><?= $availableDomain->customer->name; ?></small>
                </div>
                <div class="card-footer border-info">
                    <a class="btn btn-danger font-weight-bold"
                       href="<?= TicketHelper::getListUrl(\super\ticket\models\SuperTicketStatus::STATUS_OPEN,$availableDomain->id); ?>"
                    >
                        <i class="mdi mdi-alert-box"></i>
                        <?= $availableDomain->getNewTickets()->count(); ?>
                        <?= Yii::t('super', 'New'); ?>
                    </a>
                    <a class="btn btn-success font-weight-bold"
                       href="<?= TicketHelper::getListUrl(\super\ticket\models\SuperTicketStatus::STATUS_CLOSED,$availableDomain->id); ?>"
                    >
                        <i class="mdi mdi-check"></i>
                        <?= $availableDomain->getClosedTickets()->count(); ?>
                        <?= Yii::t('super', 'Closed'); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>