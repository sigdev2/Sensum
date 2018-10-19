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
class CCaptcha implements IModul
{   
    /////////////////////////////////////////////////////////////////////////
    const PARAM_ID = "id";
    const PARAM_ACT = "act";
    const PARAM_CAPTCHA = "captcha";
    
    /////////////////////////////////////////////////////////////////////////
    const ACT_VALID = "valid";
    const ACT_UNSET = "unset";
    
    /////////////////////////////////////////////////////////////////////////
    const RET_TRUE = "<true/>";
    const RET_FALSE = "<false/>";
    
    /////////////////////////////////////////////////////////////////////////
    const IMAGE_W = 120;
    const IMAGE_H = 40;
    const BORDER = 10;
    const LINES_COUNT = 4;
    const SYMBOLS = 5;
    const FONT_SIZE_DELTA = 1;
    const Y_POS_DELTA = 1;
    const ANGLE = 60;
    
    /////////////////////////////////////////////////////////////////////////
    const FONT_TTF = "Abagail Regular.ttf";

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
        
        if (!$bFound)
        {
            $xmloutput = self::RET_FALSE;
        }
        else
        {
            $captcha_id = null;
            
            if (!is_null($page->parameters()) && $page->parameters()->isValid())
            {
                $param = $page->parameters()->parse();
                $captcha_id = stdname::toName($param[self::PARAM_ID]);
            }
            else if (isset($_POST[self::PARAM_ID]) && !is_null($_POST[self::PARAM_ID]))
            {
                $captcha_id = stdname::toName($_POST[self::PARAM_ID]);
            }
            
            switch ((string)$_POST[self::PARAM_ACT])
            {
                case self::ACT_UNSET:
                {
                    unset($_SESSION["captcha".$captcha_id]);
                    $xmloutput = self::RET_TRUE;
                    break;
                }
                
                case self::ACT_VALID:
                {
                    if ($this->_validate(stdstr::toStr($_POST[self::PARAM_CAPTCHA]), $captcha_id))
                        $xmloutput = self::RET_TRUE;
                    else
                        $xmloutput = self::RET_FALSE;
                    break;
                }
            }
        }
        
        if (!is_null($xmloutput))
        {
            $dom = stddomxml::create();
            @$dom->loadXML($xmloutput);
            header("HTTP/1.x 200 OK");
            header("Content-Type: text/xml;charset=".stddomxml::DEFAULT_CHARSET);
            echo $dom->saveXML();
            exit;
        }
        
        $key = stdstr::rand(self::SYMBOLS);
        $len = $key->len();
        if (!is_null($captcha_id))
            $_SESSION[self::PARAM_CAPTCHA."_".$captcha_id->str()] = $key->md5();
        
        $image = imagecreate(self::IMAGE_W, self::IMAGE_H);
        imagecolorallocate($image, 255, 255, 255);
        
        $symbW = (int)((self::IMAGE_W - 2 * self::BORDER) / $len);
        $symbH = (int)(self::IMAGE_H - 2 * self::BORDER);
        
        $maxFontSize = min($symbW, $symbH);
        
        $x = self::BORDER;
        $minY = $maxFontSize + self::BORDER;
        for ($i = 0; $i < $len; $i++)
        {
            $color = imagecolorallocate($image, rand(0, 254), rand(0, 254), rand(0,254));
            $y = rand($minY, $minY - self::Y_POS_DELTA);
            $angle = rand(-((int)(self::ANGLE/2)), (int)(self::ANGLE/2));
            $fontsize = rand($maxFontSize - self::FONT_SIZE_DELTA, $maxFontSize);
        
            imagettftext($image, $fontsize, $angle, $x, $y, $color, dirname(__FILE__)."/".self::FONT_TTF, $key->at($i));
            
            $x += $symbW;
        }
        
        for ($i = 0; $i < self::LINES_COUNT; $i++)
        {
            $color = imagecolorallocate($image, rand(0, 254), rand(0, 254), rand(0,254));
            imageline($image, rand(0, self::IMAGE_W), rand(0, self::IMAGE_H), rand(0, self::IMAGE_W), rand(0, self::IMAGE_H), $color);
        }
        
        header("HTTP/1.x 200 OK");
        header("Content-type: image/jpeg");
        imagejpeg($image, '', 100);

        exit();
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
    protected function _validate($captcha, $id)
    {
        if (is_null($captcha) || is_null($id))
            return false;
            
        if (!($captcha instanceof stdstr) || !($id instanceof stdname))
            return false;
        
        if ($captcha->isEmpty() || $id->isEmpty())
            return false;

        $sessionCaptcha = $_SESSION[self::PARAM_CAPTCHA."_".$id->str()];
        if (!($sessionCaptcha instanceof stdstr))
             return false;
            
        if ($sessionCaptcha->isEmpty() || $captcha->isEmpty())
            return false;
        
        return $sessionCaptcha == $captcha;
    }
}

/////////////////////////////////////////////////////////////////////////////
?>