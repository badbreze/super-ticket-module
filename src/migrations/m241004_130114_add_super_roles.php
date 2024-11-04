<?php

use yii\db\Migration;

/**
 * Class m241004_130114_add_super_roles
 */
class m241004_130114_add_super_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $acl = Yii::$app->authManager;

        $user = $acl->createRole('SUPER_USER');
        $admin = $acl->createRole('SUPER_ADMIN');

        $acl->add($user);
        $acl->add($admin);
        $acl->addChild($admin, $user);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $acl = Yii::$app->authManager;

        $acl->remove($acl->getRole('SUPER_USER'));
        $acl->remove($acl->getRole('SUPER_ADMIN'));
    }
}
