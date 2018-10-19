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
class CPagesList implements IModul
{
    /////////////////////////////////////////////////////////////////////////
    const XSLT_FILE_NAME = "tpl.xsl";
    const PAGE_XSLT_FILE_NAME = "pagetpl.xsl";
    
    // use Simple XML
    /////////////////////////////////////////////////////////////////////////
    public function __construct()
    {
        
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function init(CCurrentPage $page)
    {
        $this->m_content = simplexml_load_file($page->dataFile()->getRealPath());
        
        if (!$this->m_content)
            return;

        $parameters = $page->parameters();
        if ($parameters != null)
        {   
            if (sscanf($parameters, "%d_%d", $n, $m) < 2)
                return;
            
            $article = $this->m_content->list->category[$n - 1]->item[$m - 1];
            
            $dirName = substr($page->dataFile()->getRealPath(), 0, strrpos($dataFile, "/"));
            $fullname = $page->dataFile()->getPath()."/".$article["file"];
            
            if(!is_file($fullname))
                return;
            
            $currArticle = simplexml_load_file($fullname);
        
            if(!$currArticle)
                return;
            
            $this->m_content = $currArticle;
            $this->m_bPageView = true;
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getAsDOMXML()
	{
        if (is_null($this->m_content))
            return null;
        
        $dom_sxe = dom_import_simplexml($this->m_content);
        if (!$dom_sxe)
            return null;
        
        $dom = stddomxml::create();
        $dom->appendChild($dom->importNode($dom_sxe, true));
        
	    return $dom;
	}

    /////////////////////////////////////////////////////////////////////////
    public function getXSLT()
    {
        if ($this->m_bPageView)
            return stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/".self::PAGE_XSLT_FILE_NAME));
        
        return stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/".self::XSLT_FILE_NAME));
    }
	
    /////////////////////////////////////////////////////////////////////////
    private $m_content = null;
    private $m_bPageView = false;
}
?>