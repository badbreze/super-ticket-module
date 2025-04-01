<?php

namespace super\ticket\mail;

use yii\helpers\ArrayHelper;
use Yii;
use yii\web\View;

class Mailer extends \yii\swiftmailer\Mailer
{
    /**
     * @var \yii\base\View|array view instance or its array configuration.
     */
    private $_view = [];
    /**
     * @var string the directory containing view files for composing mail messages.
     */
    private $_viewPath;

    private $_message;

    /**
     * Renders the specified view with optional parameters and layout.
     * The view will be rendered using the [[view]] component.
     * @param string $view the view name or the [path alias](guide:concept-aliases) of the view file.
     * @param array $params the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @param string|bool $layout layout view name or [path alias](guide:concept-aliases). If false, no layout will be applied.
     * @return string the rendering result.
     */
    public function render($view, $params = [], $layout = false)
    {
        $output = $this->getView()->render($view, $params, $this);
        if ($layout !== false) {
            $layoutParams = ArrayHelper::merge($params, ['content' => $output, 'message' => $this->_message]);

            return $this->getView()->render($layout, $layoutParams, $this);
        }

        return $output;
    }


    /**
     * Creates view instance from given configuration.
     * @param array $config view configuration.
     * @return View view instance.
     */
    protected function createView(array $config)
    {
        if (!array_key_exists('class', $config)) {
            $config['class'] = View::className();
        }

        return Yii::createObject($config);
    }

    /**
     * Creates a new message instance.
     * The newly created instance will be initialized with the configuration specified by [[messageConfig]].
     * If the configuration does not specify a 'class', the [[messageClass]] will be used as the class
     * of the new message instance.
     * @return MessageInterface message instance.
     */
    protected function createMessage()
    {
        $config = $this->messageConfig;

        if (!array_key_exists('class', $config)) {
            $config['class'] = $this->messageClass;
        }

        $config['mailer'] = $this;

        return Yii::createObject($config);
    }

    /**
     * Saves the message as a file under [[fileTransportPath]].
     * @param MessageInterface $message
     * @return bool whether the message is saved successfully
     */
    protected function saveMessage($message)
    {
        $path = Yii::getAlias($this->fileTransportPath);

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        if ($this->fileTransportCallback !== null) {
            $file = $path . '/' . call_user_func($this->fileTransportCallback, $this, $message);
        } else {
            $file = $path . '/' . $this->generateMessageFileName();
        }

        file_put_contents($file, $message->toString());

        return true;
    }

}