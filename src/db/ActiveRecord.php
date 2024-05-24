<?php

namespace super\ticket\db;

use super\ticket\behaviors\SoftDeleteBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * @var bool Defines if the softdelete
     */
    protected static $softDelete = true;

    /**
     * @return void
     */
    public static function enableSoftDelete() {
        static::$softDelete = true;
    }

    /**
     * @return void
     */
    public static function disableSoftDelete() {
        static::$softDelete = false;
    }

    /**
     * Checks whether or not to use softdele
     * @return bool
     */
    public static function hasSoftDelete() {
        return static::$softDelete;
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'blame' => [
                'class' => BlameableBehavior::class
            ],
            'times' => [
                'class' => TimestampBehavior::className(),
                'value' => function ($event) {
                    return date('Y-m-d H:i:s');
                },
            ],
            'softdelete' => [
                'class' => SoftDeleteBehavior::class
            ]
        ]);
    }

    /**
     * Base query, it exclude deleted elements
     *
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function find()
    {
        $model     = new static();
        $return    = parent::find();

        if ($model->hasAttribute('deleted_at') && static::hasSoftDelete()) {
            $tableName = static::tableName();
            $return->andWhere([$tableName.'.deleted_at' => null]);
        }

        return $return;
    }
}