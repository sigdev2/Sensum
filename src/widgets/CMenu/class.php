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
class CMenu  implements IWidget
{
    /////////////////////////////////////////////////////////////////////////
    const XSLT_FILE_NAME = "tpl.xsl";
    const TPL_NAME = "menu";
    
    /////////////////////////////////////////////////////////////////////////
    // options
    const OPT_ID = "id";
    const OPT_MODE = "mode";
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct()
    {
        
    }
    
    /////////////////////////////////////////////////////////////////////////
	public function init(CCurrentPage $page, /*bool*/ $isAdmin, stdparam $options = null)
	{ 
        $opt = $options->parse();
        $this->m_id = stdtext::toText($opt[self::OPT_ID]);
        if ($this->m_id->isEmpty())
            return;
            
        $this->m_mode = stdname::toName($opt[self::OPT_MODE]);
        
        $pagesFile = stdfile::info(CConfigManager::get("cfg_pages_file"));
        stderr::assert($pagesFile instanceof SplFileInfo);
        
        $xml = simplexml_load_file($pagesFile->getRealPath());
        
        $menus = $xml->xpath("//menu[@id = '".$this->m_id->str()."']");
        if ($menus != false &&  sizeof($menus) > 0)
            $this->m_menu = $menus[0];
	}
	
    /////////////////////////////////////////////////////////////////////////
	public function getAsDOMXML()
	{
	    if (is_null($this->m_menu))
            return null;
        
        // todo: generate new menu xml without system infomations in menu xml (access, admin, etc ...)
        
        $dom = stddomxml::create();
        $menuNode = $dom->importNode(dom_import_simplexml($this->m_menu), true);
        $menuNode = $dom->appendChild($menuNode);
        
        if (!is_null($this->m_mode) && !$this->m_mode->isEmpty())
        {
            $mode = $dom->createElement(self::OPT_MODE, $this->m_mode->str());
            $menuNode->appendChild($mode);
        }
        
        return $dom;
	}
    
	/////////////////////////////////////////////////////////////////////////
	public function getXSLT()
	{
	    $xslt = stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/".self::XSLT_FILE_NAME));
        
        return $xslt;
	}
    
    /////////////////////////////////////////////////////////////////////////
	public function generateXSLT()
	{
        $sh = new stdshdoc();
        $tpl = $sh->newTpl(stdshdoc::ATT_NAME, $this->m_id->str());
        $applyTpl = $sh->newApplyTpl("//".self::TPL_NAME."[@id = '".$this->m_id."']");
        $tpl->appendChild($applyTpl);
        
        $sh->appendChild($tpl);
        
        return $sh->doc();
    }
    
    /////////////////////////////////////////////////////////////////////////
    private $m_menu = null;
    private $m_mode = null;
    private $m_id = null;
}

/////////////////////////////////////////////////////////////////////////////
?>