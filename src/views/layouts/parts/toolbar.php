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
<header class="navbar navbar-expand flex-column flex-md-row bd-navbar mb-0 enjoy-toolbar">
    <?php if(!(Yii::$app->controller->id == 'default' && Yii::$app->controller->action->id == 'index')) : ?>
    <div class="mx-auto mr-md-auto ml-sm-0">
        <ul class="navbar-nav bd-navbar-nav flex-row">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#domains" data-toggle="dropdown">
                    <i class="fab fa-avianex"></i> <?= \super\ticket\helpers\DomainHelper::getCurrentDomainName() ?: Yii::t('super', 'General'); ?>
                </a>
                <div class="dropdown-menu">
                    <?php foreach ($domains as $availableDomain): ?>
                        <a href="<?= TicketHelper::getListUrl(null, $availableDomain->id); ?>" class="dropdown-item">
                            <i class="mdi mdi-file-pdf text-primary"></i>
                            <h6>
                                <?= $availableDomain->name; ?>
                                <br/>
                                <small class="text-muted">
                                    <?= $availableDomain->customer->name; ?>
                                </small>
                            </h6>
                        </a>
                    <?php endforeach; ?>
                </div>
            </li>
        </ul>
    </div>
    <?php endif; ?>

    <div class="navbar-brand navbar-nav-scroll mx-auto">
        <a href="/super">Super Ticket</a>
    </div>

    <div class="form-inline my-3 my-lg-0 mx-auto ml-sm-auto mr-sm-0">

        <?php if(Yii::$app->user->can('SUPER_ADMIN')) : ?>
            <div class="nav-item dropdown dropleft">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <i class="fas fa-cogs"></i>
                </a>
                <div class="dropdown-menu shadow-sm border-0" style="text-align: left">
                    <a class="dropdown-item"
                       href="/super/team">
                        <i class="fas fa-users"></i> <?= Yii::t('super', 'Teams'); ?>
                    </a>
                    <a class="dropdown-item" href="/super/sla" tabindex="-1">
                        <i class="fas fa-hourglass-half"></i> <?= Yii::t('super', 'Sla'); ?>
                    </a>
                    <a class="dropdown-item" href="/super/customer" tabindex="-1">
                        <i class="fas fa-user-tie"></i> <?= Yii::t('super', 'Customers'); ?>
                    </a>
                    <a class="dropdown-item" href="/super/domain" tabindex="-1">
                        <i class="fas fa-store"></i>  <?= Yii::t('super', 'Domains'); ?>
                    </a>
                    <a class="dropdown-item" href="/super/mails" tabindex="-1">
                        <i class="far fa-envelope"></i> <?= Yii::t('super', 'Mails'); ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <div class="dropdown">
            <input class="form-control mr-sm-2 shadow-sm bg-black rounded border-0 dropdown-toggle" type="search"
                   placeholder="<?= Yii::t('super', 'Cerca Ticket'); ?>" id="globalSearch">
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                <nav class="nav nav-pills flex-column" id="globalSearchResults">
                    <span class="dropdown-item"><?= Yii::t('super', 'Digita per iniziare la ricerca...'); ?></span>
                </nav>
            </div>
        </div>

        <div class="nav-item dropdown dropleft">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                <?php if ($profile && $profile->getAvatar()) : ?>
                    <img class="icon icon-primary rounded-circle"
                         src="<?= $profile->getAvatar()->getUrl(
                             false,
                             false,
                             true
                         ); ?>" style="max-width:25px;max-height: 25px;" alt="<?= $profile; ?>">
                <?php else: ?>
                    <i class="far fa-user-circle"></i>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu shadow-sm border-0" style="text-align: center">
                <?php if (Yii::$app->session->has('IMPERSONATOR')) : ?>
                    <a class="dropdown-item" href="/admin/security/deimpersonate">
                        <i class="fas fa-users-slash"></i> Deimpersonate
                    </a>
                <?php endif; ?>
                <a class="dropdown-item" href="/admin/security/logout" title="esci" data-method="post"
                   tabindex="-1">Logout</a>
            </div>
        </div>
        <?php /*if ($this->params['hasBlocks']) : ?>
                <input class="form-control mr-sm-2 shadow-sm bg-black rounded border-0" type="search"
                       placeholder="Cerca" id="filterBlocks">
            <?php endif;*/ ?>
    </div>
</header>