<?php

namespace super\ticket\helpers;

use super\ticket\models\SuperTicket;
use super\ticket\models\SuperTicketStatus;
use super\ticket\models\SuperUser;
use yii\db\Exception;

class UserHelper
{
    public static function verifyUserData($domain_id, $name, $surname = null, $email = null, $phone = null) {
        $query = SuperUser::find()
            ->andWhere(['domain_id' => $domain_id]);

        //Parsing the name as a full name
        if($surname == null) {
            $nameParts = explode(' ', $name);
            $name = $nameParts[0];
            array_shift($nameParts);
            $surname = trim(implode(' ', $nameParts));
        }

        //This must exists
        $query->andWhere(['name' => $name]);

        if(!empty($surname)) {
            $query->andWhere(['surname' => $surname]);
        }

        if(!empty($email)) {
            $query->andWhere(['email' => $email]);
        }

        if(!empty($phone)) {
            $query->andWhere(['phone' => $phone]);
        }

        $record = $query->one();

        if(empty($record) || !$record->id) {
            $record = new SuperUser([
                'domain_id' => $domain_id,
                'name' => $name,
                'surname' => $surname,
                'email' => $email,
                'phone' => $phone,
                                    ]);

            $record->save();

            if($record->hasErrors() || !$record->id) {
                throw new Exception("Unable to save the new user");
            }
        }

        return $record;
    }

    //TODO completare gestione utente standard
    public static function getUserNameById($id) {
        $superUser = SuperUser::findOne(['user_id' => $id]);

        if($superUser && $superUser->id) {
            return $superUser->fullName;
        }

        return $id;
    }
}