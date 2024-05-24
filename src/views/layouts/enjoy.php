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

    <?= $this->render('parts/toolbar'); ?>

    <div class="container-scroller">
        <!-- partial -->
        <div class="container-fluid page-body-wrapper p-0">

            <div class="main-content">
                <?php if (isset($this->params['breadcrumbs'])) : ?>
                    <div>
                        <?= Breadcrumbs::widget([
                                                    'homeLink' => [
                                                        'label' => Yii::t('super', 'Dashboard'),
                                                        'url' => '/super'
                                                    ],
                                                    'activeItemTemplate' => '<li class="active"><i class="fas fa-caret-right"></i>&nbsp;{link}</li>',
                                                    'itemTemplate' => '<li><i class="fas fa-caret-right"></i>&nbsp;{link}&nbsp;</li>',
                                                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                                                ]);
                        ?>
                    </div>
                <?php endif; ?>
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