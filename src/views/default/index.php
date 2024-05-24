<?php
use super\ticket\helpers\TicketHelper;

/**
 * @var $this \yii\web\View
 * @var $tickets \super\ticket\models\SuperTicket[]
 */

\super\ticket\assets\EnjoyAsset::register($this);
?>
<h4>Su che Dominio Vuoi Lavorare</h4>
    <div class="row">

        <?php foreach (\super\ticket\helpers\DomainHelper::getAvailableDomains() as $availableDomain) : ?>
        <div class="col-lg-3 d-flex grid-margin stretch-card">
            <div class="card sale-diffrence-border">
                <div class="card-body">
                    <h2 class="text-dark mb-2 font-weight-bold">
                        <span class="text-danger font-weight-bold">
                            <i class="mdi mdi-alert-box"></i>
                            <?= $availableDomain->getNewTickets()->count(); ?>
                        </span>
                        <span class="text-success font-weight-bold">
                            <i class="mdi mdi-check"></i>
                            <?= $availableDomain->getResolvedTickets()->count(); ?>
                        </span>
                    </h2>
                    <h4 class="card-title mb-2">
                        <a href="<?= TicketHelper::getListUrl(null,$availableDomain->id); ?>"><?= $availableDomain->name; ?></a>
                    </h4>
                    <small class="text-muted"><?= $availableDomain->customer->name; ?></small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>