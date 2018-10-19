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
class CLogin  implements IWidget
{
    const XSLT_FILE_NAME = "tpl.xsl";
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct()
    {
        
    }
    
    /////////////////////////////////////////////////////////////////////////
	public function init(CCurrentPage $page, /*bool*/ $isAdmin, stdparam $options = null)
	{
	    
	}
	
    /////////////////////////////////////////////////////////////////////////
	public function getAsDOMXML()
	{
	    return null;
	}
	
	/////////////////////////////////////////////////////////////////////////
	public function getXSLT()
	{
	    return stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/".self::XSLT_FILE_NAME));
	}
    
    /////////////////////////////////////////////////////////////////////////
	public function generateXSLT()
	{
        return null;
    }
}

/////////////////////////////////////////////////////////////////////////////
?>