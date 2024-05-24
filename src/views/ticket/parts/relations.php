<?php

use super\ticket\helpers\TicketHelper;
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
<?php if ($ticket->links || $ticket->dependantTickets) : ?>
    <div class="preview-list">
        <p class="mb-0 font-weight-medium float-left">Relations</p>
        <?php if ($ticket->links) : ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Link Type</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ticket->links as $link) : ?>
                        <tr>
                            <td>
                                <a href="<?= TicketHelper::getTicketDetailUrl($link->relatedTicket); ?>">
                                    <?= $link->related_ticket_id; ?>
                                </a>
                            </td>
                            <td><?= $link->type; ?></td>
                            <td><label class="badge badge-danger"><?= $link->relatedTicket->status->name; ?></label>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; // ticket->links ?>
        <?php if ($ticket->dependantTickets) : ?>
            <p class="mb-0 font-weight-medium float-left">Depends on this</p>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ticket->dependantTickets as $dependantTicket) : ?>
                        <tr>
                            <td>
                                <a href="<?= TicketHelper::getTicketDetailUrl($dependantTicket); ?>">
                                    <?= $dependantTicket->id; ?>
                                </a>
                            </td>
                            <td><label class="badge badge-danger"><?= $dependantTicket->status->name; ?></label></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; // ticket->dependantTickets ?>
    </div>
<?php endif; ?>