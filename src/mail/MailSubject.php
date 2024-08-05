<?php

namespace super\ticket\mail;

use yii\base\BaseObject;

/**
 * @property string $subject The Parsed Subject
 * @property boolean $isReply Is This Subject a Reply
 * @property boolean $isForwarded Is This Subject a Forwarded Mail
 */
class MailSubject extends BaseObject
{
    private $_subject;
    private $_is_reply = false;
    private $_is_forwarded = false;

    public function getSubject() {
        return $this->_subject;
    }

    public function setSubject($subject) {
        $reList = 'R:|RE:|Re:|re:|RIS:|Ris:|ris:|RIF:|Rif:|rif:|Ré:|ré:|réponse:';
        $fwList = 'I:|i:|F:|f:|FW:|Fw:|fw';

        $rpRx = "/^\s*({$reList}|{$fwList})\s*/";
        $reRx = "/^($reList)\s*/m";
        $fwRx = "/^($fwList)\s*/m";


        //Try Match Replies
        preg_match_all($reRx, $subject, $replyMatches, PREG_SET_ORDER, 0);

        if(is_array($replyMatches) && count($replyMatches) && count($replyMatches[0])) {
            $this->_is_reply = true;
        }

        //Try Match Forwards
        preg_match_all($fwRx, $subject, $forwardMatches, PREG_SET_ORDER, 0);
        if(is_array($forwardMatches) && count($forwardMatches) && count($forwardMatches[0])) {
            $this->_is_reply = true;
        }

        $result = $subject;

        do {
            $result = preg_replace($rpRx, '', $result);
            preg_match_all($rpRx, $result, $replaceMatches, PREG_SET_ORDER, 0);
        } while(is_array($replaceMatches) && count($replaceMatches) && count($replaceMatches[0]));

        return $this->_subject = $result;
    }

    public function getIsReply() {
        return $this->_is_reply;
    }

    public function getIsForwarded() {
        return $this->_is_forwarded;
    }
}