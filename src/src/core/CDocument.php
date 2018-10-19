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
// check PHP version
if (version_compare(phpversion(), "5.3.0", "<"))
{
    if (version_compare(phpversion(), "5.2.4", "<"))
        die ("PHP5.2.4 and great only");
    
    class SplFixedArray extends ArrayObject
    {
        public function __construct($arg)
        {
            parent::__construct();
        }
    };
}

/////////////////////////////////////////////////////////////////////////////
// set loacle
setlocale (LC_ALL, "ru_RU.UTF-8");
iconv_set_encoding("input_encoding", "UTF-8");
iconv_set_encoding("output_encoding", "UTF-8");
iconv_set_encoding("internal_encoding", "UTF-8");

/////////////////////////////////////////////////////////////////////////////
// STDLib
require_once(dirname(__FILE__)."/../stdlib/dbwrapper.php");
require_once(dirname(__FILE__)."/../stdlib/hidesource.php");
require_once(dirname(__FILE__)."/../stdlib/stddefiner.php");
require_once(dirname(__FILE__)."/../stdlib/stddochead.php");
require_once(dirname(__FILE__)."/../stdlib/stderr.php");
require_once(dirname(__FILE__)."/../stdlib/stdhelpers.php");
require_once(dirname(__FILE__)."/../stdlib/stditer.php");
require_once(dirname(__FILE__)."/../stdlib/stdredirect.php");
require_once(dirname(__FILE__)."/../stdlib/stdstr.php");
require_once(dirname(__FILE__)."/../stdlib/stdurl.php");
require_once(dirname(__FILE__)."/../stdlib/stdcache.php");

/////////////////////////////////////////////////////////////////////////////
// Core
require_once(dirname(__FILE__)."/CUserInfo.php");
require_once(dirname(__FILE__)."/CUsersManager.php");
require_once(dirname(__FILE__)."/CContentManager.php");
require_once(dirname(__FILE__)."/CTemplateManager.php");

require_once(dirname(__FILE__)."/CModulInfo.php");
require_once(dirname(__FILE__)."/CWidgetInfo.php");

require_once(dirname(__FILE__)."/CWidgetInfoIter.php");

require_once(dirname(__FILE__)."/CConfigManager.php");

require_once(dirname(__FILE__)."/IModul.php");
require_once(dirname(__FILE__)."/IWidget.php");

require_once(dirname(__FILE__)."/CCurrentPage.php");

require_once(dirname(__FILE__)."/CModulManager.php");
require_once(dirname(__FILE__)."/CWidgetsManager.php");

require_once(dirname(__FILE__)."/CPagesManager.php");

/////////////////////////////////////////////////////////////////////////////
// error codes
require_once(dirname(__FILE__)."/error_codes.php");

/////////////////////////////////////////////////////////////////////////////
class CDocument
{
    /////////////////////////////////////////////////////////////////////////
    const OUTPUT_CHARSET = "UTF-8";
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct(/*string*/ $cfgFile, /*string*/ $query)
    {
        try
        {
            $cfgFile = (string) $cfgFile;
            $query = urldecode((string)$query);
            
            $cfg = new CConfigManager(stdfile::info($cfgFile));
            
            $result = dbwrapper::connect(stdhost::toHost("mysql:host=localhost;dbname=tests"), stdstr::toStr("root"), stdstr::toStr(""));
            if (is_string($result))
                stderr::fatal(3000);
            
            CUsersManager::init();
            $currUser = CUsersManager::getCurrUser();
            
            $pages = new CPagesManager($cfg->getPagesFile(), $query, $currUser);
            $curr = $pages->getCurrPage();
            
            $moduls = new CModulManager($curr, $cfg->getModuls());
            
            $widgets = new CWidgetsManager($curr, $cfg->getWidgets(), $currUser);
            
            $content = new CContentManager();
            $content->addXML($cfg->getSiteInfoAsDOMXML());
            $content->addXML($curr->getAsDOMXML());
            $content->addXML(stddochead::getAsDOMXML());
            $content->addXML(CUsersManager::getAsDOMXML());
            $content->addXML($moduls->getAsDOMXML());
            $content->addXML($widgets->getAsDOMXML());
            
            $templates = new CTemplateManager($cfg->getXSLTFile(), $cfg->getXSLTAdminFile(), $curr->isAdmin());
            $templates->addXMLArray($moduls->getXSLTArray());
            $templates->addXMLArray($widgets->getXSLTArray());
            
            $this->m_doc = stdxslt::compile($templates->getDoc(), $content->getDoc());
            
            //$this->m_doc = $content->getDoc();
            //$this->m_doc = $templates->getDoc();
        }
        catch (Exception $e)
        {
            stderr::except($e);
        }
    }

    /////////////////////////////////////////////////////////////////////////
    public function send()
    {
        if (!(bool)$this->m_doc)
            stderr::fatal(7000);
        
        $this->m_doc->formatOutput = true;

        header("HTTP/1.x 200 OK");
        header("Content-Type: text/html;charset=".self::OUTPUT_CHARSET);
        
        echo stderr::getFormatedWarnings()->str(self::OUTPUT_CHARSET);
 
        if (stddomxml::DEFAULT_CHARSET != self::OUTPUT_CHARSET)
            echo iconv(stddomxml::DEFAULT_CHARSET, self::OUTPUT_CHARSET, $this->m_doc->saveXML());
        else
            echo $this->m_doc->saveXML();
    }

    /////////////////////////////////////////////////////////////////////////
    private $m_doc = false;
}

/////////////////////////////////////////////////////////////////////////////
?>