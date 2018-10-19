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
require_once dirname(__FILE__)."/stdurl.php";

/////////////////////////////////////////////////////////////////////////////
class stdredirect
{
    /////////////////////////////////////////////////////////////////////////
    const DEFAULT_CHARSET = "UTF-8";
    
    /////////////////////////////////////////////////////////////////////////
    public static function movedPermanently(stdurl $url)
    {   
        self::_locate(301, $url);
    }

    /////////////////////////////////////////////////////////////////////////
    public static function found(stdurl $url)
    {
        self::_locate(302, $url);
    }

    /////////////////////////////////////////////////////////////////////////
    public static function seeOther(stdurl $url)
    {
        self::_locate(303, $url);
    }

    /////////////////////////////////////////////////////////////////////////
    public static function temporaryRedirect(stdurl $url)
    {
        self::_locate(307, $url);
    }

    /////////////////////////////////////////////////////////////////////////
    private static function _locate(/*int*/ $code, stdurl $url)
    {
        $code = (int)$code;
        
        if (is_null($url))
            $url = "/";
        else
            $url = $url->str(self::DEFAULT_CHARSET);
        
        header("Content-Type: text/html;charset=".self::DEFAULT_CHARSET);
        header("Location: ".$url, true, $code);
        die();        
    }
}

/////////////////////////////////////////////////////////////////////////////