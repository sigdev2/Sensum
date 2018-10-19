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
require_once dirname(__FILE__)."/stdstr.php";

/////////////////////////////////////////////////////////////////////////////
class stdurl extends stdstrvalid
{
    /////////////////////////////////////////////////////////////////////////
    const URL_PREG = ".*"; // todo: validation
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct($url, /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        parent::__construct($url, $currCs, self::URL_PREG);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function toURL($str, /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        return new stdurl($str, $currCs);
    }
}

/////////////////////////////////////////////////////////////////////////////
class stdparam extends stdstrvalid
{
    /////////////////////////////////////////////////////////////////////////
    const PARAM_PREG = ".*"; // todo: validation
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct($url, /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        parent::__construct($url, $currCs, self::PARAM_PREG);
    }
    
    public function parse()
    {
        $out = new ArrayObject();
        parse_str($this->m_str, $out);
        return $out;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function toParam($str, /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        return new stdparam($str, $currCs);
    }
}

/////////////////////////////////////////////////////////////////////////////
class stdhost extends stdstrvalid
{
    /////////////////////////////////////////////////////////////////////////
    const HOST_PREG = ".*"; // todo: validation
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct($host, /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        parent::__construct($host, $currCs, self::HOST_PREG);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function toHost($str, /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        return new stdhost($str, $currCs);
    }
}

/////////////////////////////////////////////////////////////////////////////
class stdemail extends stdstrvalid 
{
    /////////////////////////////////////////////////////////////////////////
    const EMAIL_PREG = "[\p{L}\p{Nd}]+@[\p{L}\p{Nd}]+\.[\p{L}]{2,3}";
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct($email, /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        parent::__construct($email, $currCs, self::EMAIL_PREG);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function toEmail($str, /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        return new stdemail($str, $currCs);
    }
}

/////////////////////////////////////////////////////////////////////////////
?>