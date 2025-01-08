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

    public $encriptionKey = null;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if(empty($this->encriptionKey)) {
            throw new \Exception("Encription Key is not configured");
        }

        $this->modules = [
            'api' => [
                'class' => modules\api\Module::class
            ],
            'console' => [
                'class' => modules\console\Module::class
            ],
        ];

        \Yii::$app->i18n->translations['super*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@vendor/badbreze/super-ticket-system/src/messages/',
            'sourceLanguage' => 'en-US',
        ];
    }
}
