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
class CAdminMail extends CCaptcha implements IModul
{
    /////////////////////////////////////////////////////////////////////////
    const ERROR_TAG = "error";
    const ERR_EMAIL = "Ошибка отправки почты! Неверно введен e-mail.";
    const ERR_TEXT = "Ошибка отправки почты! Нет содержания письма.";
    
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
            $captcha_id = null;
    
            if (isset($_POST[self::PARAM_ID]) && !is_null($_POST[self::PARAM_ID]))
                $captcha_id = stdname::toName($_POST[self::PARAM_ID]);
            
            if ($this->_validate(stdstr::toStr($_POST[self::PARAM_CAPTCHA]), $captcha_id))
            {
                $to = stdemail::toEmail($_POST["to"]);
                $email = stdemail::toEmail($_POST['email']);
            	
                if (($to->isEmpty() || !$to->isValid()) && ($email->isEmpty() || !$email->isValid()))
                {
                    $xmloutput = $this->_err(self::ERR_EMAIL);
                }
                else
                {
                    if (!$to->isEmpty() && $to->isValid())
                    {
                        $email = stdemail::toEmail(CConfigManager::get(CConfigManager::CONSTS_PREFIX.CConfigManager::ADMIN_EMAIL));
                    }
                    else if (!$email->isEmpty() && $email->isValid())
                    {
                        $to = stdemail::toEmail(CConfigManager::get(CConfigManager::CONSTS_PREFIX.CConfigManager::ADMIN_EMAIL));
                    }
                    
                    $text = stdtext::toText($_POST["text"]);
                    if ($text->isEmpty())
                    {
                        $xmloutput = $this->_err(self::ERR_TEXT);
                    }
                    else
                    {
                        if (mail($to->str(), "Почта с сайта ".$_SERVER['SERVER_NAME'], $text->str(),
                            "From:".$email->str()."\r\n"
                            ."X-Mailer: PHP/".phpversion()."\r\n"
                            ."Content-type: text/plain; charset=UTF-8"))
                            $xmloutput = self::RET_TRUE;
                    }
            	}
            }
        }
        
        if (is_null($xmloutput))
            $xmloutput = self::RET_FALSE;
        
        $dom = stddomxml::create();
        $dom->loadXML($xmloutput);
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
    
    /////////////////////////////////////////////////////////////////////////
    private function _err($err)
    {
    	return "<".self::ERROR_TAG.">".(string)$err."</".self::ERROR_TAG.">";
    }
}

/////////////////////////////////////////////////////////////////////////////
?>