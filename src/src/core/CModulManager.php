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
class CModulManager
{   
    /////////////////////////////////////////////////////////////////////////
    public function __construct(CCurrentPage $page, /*IteratorAggregate*/ SplFixedArray $moduls)
    {
        stderr::assert(!is_null($page));
        stderr::assert(!is_null($moduls));
        
        $modulInfo = null;
        foreach ($moduls as $info)
        {
            if ($info->id() == $page->modulId())
            {
                $modulInfo = $info;
                break;
            }
        }
            
        if (is_null($modulInfo))
            stderr::fatal(4000);
        
        include_once($modulInfo->src()->getRealPath());
        
        $class = $modulInfo->class_name()->str();
        $this->modul = new $class;
        
        if (!($this->modul instanceof IModul))
            stderr::fatal(4001);
        
        $this->modul->init($page);
    }

    /////////////////////////////////////////////////////////////////////////
    public function getAsDOMXML()
    {
        $dom = $this->modul->getAsDOMXML();
        
        if (is_null($dom) || !$dom)
            stderr::fatal(4004);
        
        if ($dom instanceof DOMDocument)
            $dom = $dom->documentElement;
        
        if (!($dom instanceof DOMNode))
            stderr::fatal(4004);

        if ($dom->nodeName != IModul::CONTENT_TAG_NAME)
            stderr::fatal(4004);
        
        return $dom;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getXSLTArray()
    {
        $xslt = $this->modul->getXSLT(); 
        if (is_null($xslt) || !$xslt || !($xslt instanceof DOMDocument))
            stderr::fatal(4005);
        
        // todo: template validation by dtd w3c 4005
        
        $tpls = $xslt->documentElement->childNodes;
        
        if ($tpls->length <= 0)
            stderr::fatal(4005);
        
        $arr = new ArrayObject();
        $bTplFind = false;
        foreach ($tpls as $tpl)
        {
            if ($tpl->attributes)
                if ($tpl->attributes->getNamedItem(stdshdoc::ATT_MATCH)->value == IModul::CONTENT_TAG_NAME)
                    $bTplFind = true;
            
            $arr->append($tpl);
        }
        
        if ($bTplFind == false)
            stderr::fatal(4005);

        return $arr;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private $m_modul = null;
}

/////////////////////////////////////////////////////////////////////////////
?>