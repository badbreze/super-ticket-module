<?php

use yii\widgets\Breadcrumbs;


/**
 * @var $this \yii\web\View
 * @var $content string
 * @var $profile UserProfile
 */

\super\ticket\assets\EnjoyAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?php $this->head() ?>
    </head>

    <body>
    <?php $this->beginBody() ?>

    <div class="container-scroller">
        <!-- partial -->
        <div class="container-fluid page-body-wrapper p-0">

            <div class="spectate-content">
                <div class="content-wrapper px-3">
                    <?= $content; ?>
                </div>
                <!-- content-wrapper ends -->
            </div>
        </div>
        <!-- page-body-wrapper ends -->
        <?= $this->render('parts/footer'); ?>
    </div>
    <?php $this->endBody() ?>
    </body>

    </html>
<?php $this->endPage() ?>