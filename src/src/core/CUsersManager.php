<?php
/* UTF-8
   Copyright 2010-2018 SigDev

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License. */

/////////////////////////////////////////////////////////////////////////////
require_once(dirname(__FILE__)."/../stdlib/hidesource.php");

/////////////////////////////////////////////////////////////////////////////
class CUsersManager
{
    // login is email
    /////////////////////////////////////////////////////////////////////////
    const ID_LOGIN = "currUser";
    
    /////////////////////////////////////////////////////////////////////////
    const MIN_USER_PASS_LEN = 6;
    const MAX_USER_PASS_LEN = 80;
    
    const MIN_USER_NIKNAME_LEN = 3;
    const MAX_USER_NIKNAME_LEN = 50;
    
    const USER_ONLINE_TIME = 500; // in sec
    
    const USERS_COUNT_TAG = "count";
    const ROOT_USERS_TAG = "users";
    const ONLINE_USERS_TAG = "online";
    
    /////////////////////////////////////////////////////////////////////////
    public static function init()
    {
        session_start();
        
        // validate login
        if (is_null($_SESSION[self::ID_LOGIN]) || !($_SESSION[self::ID_LOGIN] instanceof CUserInfo))
        {
            self::$m_currUser = new CUserInfo();
        }
        else
        {
            self::$m_currUser = $_SESSION[self::ID_LOGIN];
        }
        
        self::_updateCurr();
        
        return self::$m_currUser->isGuest();
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function isInit()
    {
        return !is_null(self::$m_currUser);
    }

    /////////////////////////////////////////////////////////////////////////
    public static function logout()
    {
        if (!self::isInit())
            return false;
        
        if (!self::getCurrUser()->isGuest())
        {
            self::$m_currUser = new CUserInfo();
            self::_updateCurr();
        }
        
        session_unset();
        session_destroy();
        
        return true;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function login(stdemail $email, stdstr $pass)
    {
        if (!self::isInit())
            return false;

        if (!self::getCurrUser()->isGuest())
            return false;

        $user = new CUserInfo($email);
        if (!$user->isExist())
            return false;
  
        if (!$user->checkPass($pass))
            return false;

        self::$m_currUser = $user;
        $_SESSION[self::ID_LOGIN] = $user;

        self::_updateCurr();
        
        return true;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function getCurrUser()
    {
        if (is_null(self::$m_currUser))
            self::$m_currUser = new CUserInfo();
        
        return self::$m_currUser;
    }

    /////////////////////////////////////////////////////////////////////////
    static public function registration(stdemail $loginEmail, stdstr $pass, stdtext $nikname, DOMNode $domUserData = null)
    {
        if (is_null($nikname) || $nikname->len() < self::MIN_USER_NIKNAME_LEN || $nikname->len() > self::MAX_USER_NIKNAME_LEN)
            return false;

        if (is_null($pass) || $pass->len() < self::MIN_USER_PASS_LEN || $pass->len() > self::MAX_USER_PASS_LEN)
            return false;
            
        $user = new CUserInfo($loginEmail);
        if ($user->isGuest() || $user->isExist())
            return false;
        
        // todo: this save dom xml to data base don't safe, use stdxmlstr
        $userData = is_null($domUserData) ? "NULL" : "'".$domUserData->saveXML()."'";
        $query = stdstr::toStr("INSERT INTO USERS VALUES(0, '".$loginEmail->str()."', '".$nikname->str()."', '".$pass->md5()->str()."', ".CUserInfo::DEFAULT_ACCESS.", ".$userData.");");
        
        if (!dbwrapper::exec($query))
            return false;
        
        return true;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function getAsDOMXML()
    {
        $dom = stddomxml::create();
        $dom->loadXML("<".self::ROOT_USERS_TAG."/>");
        
        $count = 0;
        $res = dbwrapper::query(stdstr::toStr("SELECT LOGIN FROM SESSIONS WHERE LOGIN IS NOT NULL;"));
        if ((bool)$res && $res->rowCount() > 0)
        {
            $res = new ArrayObject($res->fetchAll(PDO::FETCH_COLUMN, 0));
            $count = $res->count();
            
            $online = null;
            foreach ($res as $login)
            {
                if (self::getCurrUser()->isGuest() || self::getCurrUser()->email()->str() != $login)
                {
                    $user = new CUserInfo(stdemail::toEmail($login));
                    $user = $user->getAsDOMXML(true);
                    
                    stderr::assert($user instanceof DOMNode);
    
                    if ($user instanceof DOMDocument)
                        $user = $user->documentElement;
                    
                    if (is_null($online))
                    {
                        $online = $dom->createElement(self::ONLINE_USERS_TAG);
                        $dom->documentElement->appendChild($online);
                    }
                    
                    $online->appendChild($dom->importNode($user, true));
                }
            }
        }
        
        $currUser = self::getCurrUser()->getAsDOMXML(false);
        
        stderr::assert($currUser instanceof DOMNode);
        
        if ($currUser instanceof DOMDocument)
            $currUser = $currUser->documentElement;
        
        $dom->documentElement->appendChild($dom->importNode($currUser, true));
        
        $dom->documentElement->appendChild($dom->createElement(self::USERS_COUNT_TAG, $count));
        
        return $dom;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function isExist(stdemail $email)
    {
        $user = new CUserInfo($email);
        return $user->isExist();
    }
    
    /////////////////////////////////////////////////////////////////////////
    private static function _updateCurr()
    {
        // delete old sessions
        $past = time() - self::USER_ONLINE_TIME;
        dbwrapper::exec(stdstr::toStr("DELETE FROM SESSIONS WHERE LAST_TIME < '".$past."'"));
        
        // update time for users
        $userLogin = self::getCurrUser()->isGuest() ? "NULL" : "'.".self::getCurrUser()->email()->str().".'";
        $res = dbwrapper::query(stdstr::toStr("SELECT SESSION_ID, LOGIN FROM SESSIONS WHERE SESSION_ID = '".session_id()."';"));
        if ((bool)$res && $res->rowCount() > 0)
        {
            $res = new ArrayObject($res->fetchAll());

            if ($res->count() > 1)
                dbwrapper::exec(stdstr::toStr("DELETE FROM SESSIONS WHERE SESSION_ID = '".session_id()."' LIMIT ".($res->count() - 1).";"));
            
            dbwrapper::exec(stdstr::toStr("UPDATE SESSIONS SET LAST_TIME = ".time().", LOGIN = ".$userLogin." WHERE SESSION_ID = '".session_id()."';"));
        }
        else
        {
            dbwrapper::exec(stdstr::toStr("INSERT INTO SESSIONS VALUES(".$userLogin.", '".session_id()."', ".time().");"));
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    private static $m_currUser = null;
}

/////////////////////////////////////////////////////////////////////////////
?>