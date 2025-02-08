<?php

namespace super\ticket\db;

use super\ticket\behaviors\SoftDeleteBehavior;
use super\ticket\models\SuperMeta;
use super\ticket\models\SuperTicket;
use yii\base\ModelEvent;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\console\Application;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * @property SuperMeta[] $metadata
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * @var $_metadata mixed temporary metadata
     */
    protected $_metadata;

    /**
     * @var bool Defines if the softdelete
     */
    protected static $softDelete = true;

    /**
     * @return void
     */
    public static function enableSoftDelete()
    {
        static::$softDelete = true;
    }

    /**
     * @return void
     */
    public static function disableSoftDelete()
    {
        static::$softDelete = false;
    }

    /**
     * Checks whether or not to use softdele
     * @return bool
     */
    public static function hasSoftDelete()
    {
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
                'value' => function (ModelEvent $event) {
                    return $event->sender->created_at ?: date('Y-m-d H:i:s');
                },
            ],
            'softdelete' => [
                'class' => SoftDeleteBehavior::class
            ],
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
        $model = new static();
        $return = parent::find();

        if ($model->hasAttribute('deleted_at') && static::hasSoftDelete()) {
            $tableName = static::tableName();
            $return->andWhere([$tableName . '.deleted_at' => null]);
        }

        return $return;
    }

    public function save($runValidation = true, $attributeNames = null)
    {

        $save = parent::save($runValidation, $attributeNames);

        if (\Yii::$app instanceof Application) {
            $class = get_class($this);

            echo "\nSaving: $class ($this->id)\n\n";
            //var_dump(debug_backtrace(2));
        }

        return $save;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        //Save metadatas
        $this->storeMetadata();
    }

    public function addMetadata($name, $value)
    {
        $meta = new SuperMeta([
            'name' => $name,
            'value' => $value,
            'model_id' => $this->id,
            'model_class' => self::className(),
        ]);

        return $meta->save();
    }

    public function getMetadata()
    {
        return $this
            ->hasMany(SuperMeta::className(), ['model_id' => 'id'])
            ->andOnCondition(['model_class' => new Expression(self::className())]);
    }

    public function setMetadata($value = null)
    {
        //Drop Existing
        SuperMeta::deleteAll([
            'model_id' => $this->id,
            'model_class' => self::className(),
        ]);

        $this->_metadata = $value;

        return true;
    }

    /**
     * @return bool
     */
    protected function storeMetadata() {
        if(empty($this->_metadata)) {
            return false;
        }

        if (is_array($this->_metadata)) {
            foreach ($this->_metadata as $key => $val) {
                $this->addMetadata($key, $val);
            }
        } else {
            return $this->addMetadata('metadata', $this->_metadata);
        }

        return true;
    }

    public function getMetadataByName($name)
    {
        return $this
            ->getMetadata()
            ->andWhere(['name' => $name])
            ->all();
    }
}