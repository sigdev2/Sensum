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
interface IUserAccess
{
    /////////////////////////////////////////////////////////////////////////
    const DEFAULT_ACCESS = 0;

    /////////////////////////////////////////////////////////////////////////
    public function isAdminFor(/*int*/ $level = IUserAccess::DEFAULT_ACCESS); // bool
    
    /////////////////////////////////////////////////////////////////////////
    public function isHasAccessTo(/*int*/ $level = IUserAccess::DEFAULT_ACCESS); // bool
    
    /////////////////////////////////////////////////////////////////////////
    public function accessLevel(); // int
}

/////////////////////////////////////////////////////////////////////////////
interface IUserData
{
    /////////////////////////////////////////////////////////////////////////
    const DEFAULT_ROLE = "Гость";
    const DEFAULT_NIKNAME = self::DEFAULT_ROLE;
    
    /////////////////////////////////////////////////////////////////////////
    const ROOT_TAG = "user";
    const EMAIL_TAG = "email";
    const NIKNAME_TAG = "nikname";
    const ROLE_TAG = "role";

    // data
    
    /////////////////////////////////////////////////////////////////////////
    public function email(); // stdemail
    
    /////////////////////////////////////////////////////////////////////////
    public function nikname(); // stdtext
    
    /////////////////////////////////////////////////////////////////////////
    public function role(); // stdtext
    
    /////////////////////////////////////////////////////////////////////////
    public function id(); // int
    
    /////////////////////////////////////////////////////////////////////////
    public function data(); // DOMDocument | null
    
    // bools
    
    /////////////////////////////////////////////////////////////////////////
    public function isExist(); // bool
    
    /////////////////////////////////////////////////////////////////////////
    public function isGuest(); // bool
    
    // xml
    
    /////////////////////////////////////////////////////////////////////////
    public function getAsDOMXML(/*bool*/ $isPublic); // DOMDocument
}

/////////////////////////////////////////////////////////////////////////////
class CUserInfo extends stdcache implements IUserAccess, IUserData
{
    /////////////////////////////////////////////////////////////////////////
    public function __construct(stdemail $email = null)
    {
        $this->_setCacheConst(self::EMAIL_TAG, $email);
    }
    
    // IUserData
    
    /////////////////////////////////////////////////////////////////////////
    public function email()
    {
        return $this->email;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function nikname()
    {
        return $this->nikname;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function role()
    {    
        return $this->role;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function id()
    {
        return $this->id;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function data()
    {
        return $this->data;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function isGuest()
    {
        return is_null($this->email) || $this->email->isEmpty() || !$this->email->isValid();
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function isExist()
    {
        if ($this->isGuest())
            return false;
        
        return $this->exist;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getAsDOMXML(/*bool*/ $isPublic)
    {   
        $xml = "<".self::ROOT_TAG.">";
        $xml .= "<".self::ROLE_TAG.">".$this->role->str()."</".self::ROLE_TAG.">";
        $xml .= "<".self::NIKNAME_TAG.">".$this->nikname->str()."</".self::NIKNAME_TAG.">";
        
        if (!$this->isGuest() && !$isPublic)
            $xml .= "<".self::EMAIL_TAG.">".$this->email->str()."</".self::EMAIL_TAG.">";
        
        $xml .= "</".self::ROOT_TAG.">";
        
        $dom = stddomxml::create();
        $dom->loadXML($xml);
        
        $data = $this->data;
        if (is_null($data))
            return $dom;

        if (!($data instanceof DOMNode))
            return $dom;
        
        if ($data instanceof DOMDocument)
            $data = $data->documentElement;
        
        $dom->documentElement->appendChild($dom->importNode($data, true));
        
        return $dom;
    }
    
    // IUserAccess
    
    /////////////////////////////////////////////////////////////////////////
    public function isAdminFor(/*int*/ $level = self::DEFAULT_ACCESS)
    {
        if ($this->isGuest())
            return false;
        
        $level = (int)$level;
        
        if ($level == self::DEFAULT_ACCESS)
            return false;
        
        return $this->accessLevel() >= $level;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function isHasAccessTo(/*int*/ $level = self::DEFAULT_ACCESS)
    {
        $level = (int)$level;
        
        if ($level == self::DEFAULT_ACCESS)
            return true;
        
        return $this->accessLevel() >= $level;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function accessLevel()
    {
        return $this->accessLevel;
    }
    
    // advanced methods

    /////////////////////////////////////////////////////////////////////////
    public function checkPass(stdstr $pass)
    {
        if ($this->isGuest())
            return false;
        
        if (is_null($pass) || $pass->isEmpty())
            return false;

        $res = dbwrapper::query(stdstr::toStr("SELECT LOGIN FROM USERS WHERE LOGIN = '".$this->email->str()."' AND PASS = '".$pass->md5()->str()."';"));

        if (!$res || $res->rowCount() <= 0)
            return false;

        return true;
    }
    
    // cache
    
    /////////////////////////////////////////////////////////////////////////
    public function cache_nikname() // stdtext
    {
        if ($this->isGuest())
            return stdtext::toText(self::DEFAULT_NIKNAME);

        $res = dbwrapper::query(stdstr::toStr("SELECT NIKNAME FROM USERS WHERE LOGIN = '".$this->email->str()."';"));
        
        if (!$res || $res->rowCount() <= 0)
            return stdtext::toText(self::DEFAULT_NIKNAME);

        $res = $res->fetchAll(PDO::FETCH_COLUMN, 0);
        return stdtext::toText($res[0]);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function cache_role() // stdtext
    {
        if ($this->isGuest())
            return stdtext::toText(self::DEFAULT_ROLE);

        $res = dbwrapper::query(stdstr::toStr("SELECT r.ROLE_NAME FROM ROLES r, USERS u WHERE r.ROLE_NAME IS NOT NULL AND r.ACCESS_LEVEL = u.ACCESS_LEVEL AND u.LOGIN = '".$this->email->str()."';"));

        if (!$res || $res->rowCount() <= 0)
            return stdtext::toText(self::DEFAULT_ROLE);

        $res = $res->fetchAll(PDO::FETCH_COLUMN, 0);
        return stdtext::toText($res[0]);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function cache_id()
    {
        if ($this->isGuest())
            return -1;

        $res = dbwrapper::query(stdstr::toStr("SELECT U_NO FROM USERS WHERE LOGIN = '".$this->email->str()."';"));

        if (!$res || $res->rowCount() <= 0)
            return -1;

        $res = $res->fetchAll(PDO::FETCH_COLUMN, 0);
        return (int)$res[0];
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function cache_data()
    {
        if ($this->isGuest())
            return null;

        $res = dbwrapper::query(stdstr::toStr("SELECT USER_DATA_XML FROM USERS WHERE u.LOGIN = '".$this->email->str()."' AND USER_DATA_XML IS NOT NULL;"));

        if (!$res || $res->rowCount() <= 0)
            return null;

        $domUserData = stddomxml::create();
        $res = $res->fetchAll(PDO::FETCH_COLUMN, 0);
        @$valid = $domUserData->loadXML($res[0]);
        if (!$valid)
            return null;

        return $domUserData;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function cache_exist()
    {
        $res = dbwrapper::query(stdstr::toStr("SELECT LOGIN FROM USERS WHERE LOGIN = '".$this->email->str()."';"));
        
        if (!$res || $res->rowCount() <= 0)
            return false;
        
        return true;
    }

    /////////////////////////////////////////////////////////////////////////
    public function cache_accessLevel()
    {
        if ($this->isGuest())
            return self::DEFAULT_ACCESS;

        $res = dbwrapper::query(stdstr::toStr("SELECT u.ACCESS_LEVEL FROM USERS u WHERE u.LOGIN = '".$this->email->str()."';"));

        if (!$res || $res->rowCount() <= 0)
            return self::DEFAULT_ACCESS;

        $res = $res->fetchAll(PDO::FETCH_COLUMN, 0);
        return (int)$res[0];
    }
}

/////////////////////////////////////////////////////////////////////////////
?>