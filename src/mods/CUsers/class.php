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
require_once dirname(__FILE__)."/../../src/stdlib/hidesource.php";

/////////////////////////////////////////////////////////////////////////////
require_once dirname(__FILE__)."/../CCaptcha/class.php";

/////////////////////////////////////////////////////////////////////////////
class CUsers extends CCaptcha implements IModul
{   
    /////////////////////////////////////////////////////////////////////////
    const PARAM_LOGIN = "loginEmail";
    const PARAM_PASS = "pass";
    const PARAM_NIKNAME = "nikname";
    const PARAM_DATA = "userData";
    
    /////////////////////////////////////////////////////////////////////////
    const ACT_REG = "reg";
    const ACT_EXIST = "exist";
    const ACT_LOGIN = "login";
    const ACT_LOGOUT = "logout";
    const ACT_FAILD_LOGIN_COUNT = "failed_login_count";
    
    /////////////////////////////////////////////////////////////////////////
    const RET_NOEXIST = "<noexist/>";
    const RET_EXIST = "<exist/>";
    
    /////////////////////////////////////////////////////////////////////////
    const MAX_LOGIN_COUNT = 3;
    const ID_LOGIN_COUNT = "loginCount";

    /////////////////////////////////////////////////////////////////////////
    public function __construct()
    {
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function init(CCurrentPage $page)
    {
        $xmloutput = null;
        
        $bFound = ($_SERVER["HTTP_HOST"] == CConfigManager::get(CConfigManager::CONSTS_PREFIX.CConfigManager::HOST));
        if (!$bFound)
        {
            $aliasKey = CConfigManager::CONSTS_PREFIX.CConfigManager::ALIAS;
            for ($i = 0; CConfigManager::is_set($aliasKey.$i); $i++)
                if (($bFound = ($_SERVER["HTTP_HOST"] == CConfigManager::get($aliasKey.$i))))
                    break;                
        }
        
        if ($bFound)
        {
            switch((string)$_POST[self::PARAM_ACT])
            {
                case self::ACT_REG:
                {
                    $captcha_id = null;
            
                    if (isset($_POST[self::PARAM_ID]) && !is_null($_POST[self::PARAM_ID]))
                        $captcha_id = stdname::toName($_POST[self::PARAM_ID]);
                    
                    if ($this->_validate(stdstr::toStr($_POST[self::PARAM_CAPTCHA]), $captcha_id))
                    {
                        $email = stdemail::toEmail($_POST[self::PARAM_LOGIN]);
                        $pass = stdstr::toStr($_POST[self::PARAM_PASS]);
                        $nikname = stdtext::toText($_POST[self::PARAM_NIKNAME]);
                        
                        $userData = null;
                        if ((bool)$_POST[self::PARAM_DATA])
                        {
                            $xml = stddomxml::create();
                            @$valid = $xml->loadXML($_POST[self::PARAM_DATA]);
                            if ($valid)
                                $userData = $xml;
                        }
                        
                        if (CUsersManager::registration($email, $pass, $nikname, $userData))
                            $xmloutput = self::RET_TRUE;
                    }
    
                    break;
                }
                
                case self::ACT_EXIST:
                {
                    $email = stdemail::toEmail($_POST[self::PARAM_LOGIN]);
                    if (CUsersManager::isExist($email))
                        $xmloutput = self::RET_EXIST;
                    else
                        $xmloutput = self::RET_NOEXIST;
    
                    break;
                }
                
                case self::ACT_LOGOUT:
                {
                    if (CUsersManager::logout())
                        $xmloutput = self::RET_TRUE;
                    
                    break;
                }
                
                case self::ACT_LOGIN:
                {
                    if (!isset($_SESSION[self::ID_LOGIN_COUNT]))
                        $_SESSION[self::ID_LOGIN_COUNT] = 0;
                    
                    $captcha_id = null;
            
                    if (isset($_POST[self::PARAM_ID]) && !is_null($_POST[self::PARAM_ID]))
                        $captcha_id = stdname::toName($_POST[self::PARAM_ID]);
                    
                    // todo
                    $bValid = true;//$_SESSION[self::ID_LOGIN_COUNT] <= self::MAX_LOGIN_COUNT || $this->_validate(stdstr::toStr($_POST[self::PARAM_CAPTCHA]), $captcha_id);
                    
                    if ($bValid)
                    {
                        $email = stdemail::toEmail($_POST[self::PARAM_LOGIN]);
                        $pass = stdstr::toStr($_POST[self::PARAM_PASS]);
                        
                        if (CUsersManager::login($email, $pass))
                        {
                            $_SESSION[self::ID_LOGIN_COUNT] = 0;
                            $xmloutput = CUsersManager::getCurrUser()->getAsDOMXML()->saveXML();
                        }
                    }
                    
                    if (is_null($xmloutput))
                    {
                        $_SESSION[self::ID_LOGIN_COUNT] += 1;
                    }
                    
                    break;
                }
                
                case self::ACT_FAILD_LOGIN_COUNT:
                {
                    // todo
                    //if (!isset($_SESSION[self::ID_LOGIN_COUNT]))
                    //    $_SESSION[self::ID_LOGIN_COUNT] = 0;
                    
                    //if ($_SESSION[self::ID_LOGIN_COUNT] <= self::MAX_LOGIN_COUNT)
                        $xmloutput = self::RET_TRUE;
                    //else
                    //    $xmloutput = self::RET_FALSE;
    
                    break;
                }
            }
        }

        if (is_null($xmloutput))
            $xmloutput = self::RET_FALSE;
        
        $dom = stddomxml::create();
        @$dom->loadXML($xmloutput);
        header("HTTP/1.x 200 OK");
        header("Content-Type: text/xml;charset=".stddomxml::DEFAULT_CHARSET);
        echo $dom->saveXML();
        exit;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getAsDOMXML()
    {
        return null;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getXSLT()
    {
        return null;
    }
}

/////////////////////////////////////////////////////////////////////////////
?>