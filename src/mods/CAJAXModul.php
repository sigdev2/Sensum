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
require_once dirname(__FILE__)."/../stdlib/hidesource.php";

/////////////////////////////////////////////////////////////////////////////
class CAJAXModul implements IModul
{
    /////////////////////////////////////////////////////////////////////////
    const PARAM_ACT = "act";
    
    /////////////////////////////////////////////////////////////////////////
    const RET_TRUE = "<true/>";
    const RET_FALSE = "<false/>";

    /////////////////////////////////////////////////////////////////////////
    public function __construct()
    {
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function init(CCurrentPage $page)
    {
        // tudo: testing if it is this server
        
        $xmloutput = null;
        
        $xmloutput = $this->_getResponse((string)$_POST[self::PARAM_ACT]);
        
        if (!is_null($xmloutput))
        {
            $dom = stddomxml::create();
            @$dom->loadXML($xmloutput);
            header("HTTP/1.x 200 OK");
            header("Content-Type: text/xml;charset=".stddomxml::DEFAULT_CHARSET);
            echo $dom->saveXML();
        }
        
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
    protected function _getResponse(/*string*/ $act) // xml as string of null if is non XML
    {
        return null;
    }
}

/////////////////////////////////////////////////////////////////////////////
?>