<?php

namespace super\ticket\helpers;

use super\ticket\models\SuperUser;
use yii\console\Application;
use yii\db\Exception;

class UserHelper
{
    /**
     * Get the current SuperUser profile
     * @return array|\yii\db\ActiveRecord|null
     * @throws \yii\base\InvalidConfigException
     */
    public static function getCurrentUser()
    {
        if(\Yii::$app instanceof Application) {
            return null;
        }

        $query = SuperUser::find()
            ->andWhere(['user_id' => \Yii::$app->user->id])
            ->andWhere(['not', ['user_id' => null]]);

        return $query->one();
    }

    /**
     * Check if the current logged use is available on super ticket system
     * @return mixed|null
     * @throws \yii\base\InvalidConfigException
     */
    public static function isCurrentUserAvailable()
    {
        return self::getCurrentUser()->id;
    }

    public static function parseAndGetUser($domain_id, $name, $surname = null, $email = null, $phone = null)
    {
        $query = SuperUser::find()
            ->andWhere(['domain_id' => [$domain_id, null]])
            ->orderBy(['domain_id' => SORT_ASC]);

        //Parsing the name as a full name
        if ($surname == null) {
            $nameParts = explode(' ', $name);
            $name = $nameParts[0];
            array_shift($nameParts);
            $surname = trim(implode(' ', $nameParts));
        }

        if (!empty($email)) {
            $query->andWhere(['email' => $email]);
        } else if (!empty($phone)) {
            $query->andWhere(['phone' => $phone]);
        } else if (!empty($name)) {
            $query->andWhere(['name' => $name]);

            if (!empty($surname)) {
                $query->andWhere(['surname' => $surname]);
            }
        } else {
            throw new \Exception("No Data To Obtain User");
        }

        $record = $query->one();

        if (empty($record) || !$record->id) {
            $record = new SuperUser([
                'domain_id' => $domain_id,
                'name' => $name ?: $email,
                'surname' => $surname,
                'email' => $email,
                'phone' => $phone,
            ]);

            $record->save();

            if ($record->hasErrors() || !$record->id) {
                $errors = json_encode($record->getErrors());
                throw new Exception("Unable to save the new user: {$errors}");
            }
        }

        return $record;
    }

    /**
     * @param $domain_id
     * @param array $contact
     * @return array|SuperUser|\yii\db\ActiveRecord|null
     * @throws Exception
     */
    public static function getUserFromContact($domain_id, array $contact)
    {
        return UserHelper::parseAndGetUser(
            $domain_id,
            $contact['name'],
            $contact['surname'],
            $contact['email'],
            $contact['phone']
        );
    }

    /**
     * @param $domain_id
     * @param array $contacts
     * @return array
     * @throws Exception
     */
    public static function getUsersFromContactsArray($domain_id, array $contacts)
    {
        $users = [];

        foreach ($contacts as $contact) {

            $users[] = self::getUserFromContact($domain_id, $contact);
        }

        return $users;
    }

    //TODO completare gestione utente standard
    public static function getUserNameById($id)
    {
        $superUser = SuperUser::findOne(['user_id' => $id]);

        if ($superUser && $superUser->id) {
            return $superUser->fullName;
        }

        return $id;
    }
}