<?php

namespace super\ticket\helpers;

use elitedivision\amos\attachments\FileModule;
use super\ticket\base\ImapMailAttachment;
use yii\helpers\FileHelper;

class AttachmentsHelper
{
    public static function attachFile(ImapMailAttachment $attachment, $owner, $attribute = 'attachments', $dropOriginFile = true) {
        if(empty($attachment) || empty($attachment->getContents())) {
            return null;
        }

        /**
         * @var $module FileModule
         */
        $module = \Yii::$app->getModule('attachments');

        $attachtempDir = self::getUserDirPath($module, 'mail_attach');

        //Write file
        file_put_contents($attachtempDir.$attachment->name, $attachment->getContents());

        if($module) {
            try {
                return $module->attachFile($attachtempDir.$attachment->name, $owner, $attribute, $dropOriginFile);
            } catch (\Exception $exception) {
                \Yii::error("Attachment skipped {$attachment->name} cause {$exception->getMessage()}");
            }
        }

        return false;
    }

    public static function getUserDirPath(FileModule $fileModule, $suffix = '')
    {
        $userDirPath = $fileModule->getTempPath() . DIRECTORY_SEPARATOR . $suffix;

        //Try dir creation
        FileHelper::createDirectory($userDirPath, 0777);

        //Check if the dir has been created
        if(!is_dir($userDirPath)) {
            throw new \Exception("Unable to create Upload Direcotory '{$userDirPath}'");
        }

        return $userDirPath . DIRECTORY_SEPARATOR;
    }
}