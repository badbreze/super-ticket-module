<?php
namespace super\ticket;

/**
 * super module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'super\ticket\controllers';


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->modules = [
            'api' => [
                'class' => modules\api\Module::class
            ]
        ];
    }
}
