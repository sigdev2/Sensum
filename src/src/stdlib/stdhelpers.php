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
require_once dirname(__FILE__)."/stderr.php";
require_once dirname(__FILE__)."/stdstr.php";

/////////////////////////////////////////////////////////////////////////////
class stddomxml
{
    /////////////////////////////////////////////////////////////////////////
    const DEFAULT_CHARSET = "UTF-8";
    const XML_VERSION = "1.0";
    
    /////////////////////////////////////////////////////////////////////////
    public static function create()
    {
        return new DOMDocument(self::XML_VERSION, self::DEFAULT_CHARSET);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function fromFile(SplFileInfo $xmlFile)
    {
        if (is_null($xmlFile) || !$xmlFile->isFile())
            return false;
            
        $dom = self::create();
        
        if (!$dom->load($xmlFile->getRealPath()))
            return false;
        
        return $dom;
    }
    
    // todo: this is for first lavel of xml, must be ierarhy copare
    /////////////////////////////////////////////////////////////////////////
    public static function unitedDOM(DOMDocument $xmlDOM, DOMDocument $repXMLDOM /*replace*/)
    {
        if (is_null($repXMLDOM))
            return $xmlDOM;
        
        if (is_null($xmlDOM))
            return $repXMLDOM;
        
        $result = clone $repXMLDOM;
        
        $childs = $xmlDOM->documentElement->childNodes;
        foreach($childs as $item)
        {
            $repChilds = $repXMLDOM->documentElement->childNodes;
            foreach($repChilds as $repItem)
            {
                if ($item->nodeName != $repItem->nodeName)
                    $result->documentElement->appendChild($result->importNode($item, true));
            }
        }
        
        return $result;
    }
    
    // if xml as str don't have root, than use $rootName
    /////////////////////////////////////////////////////////////////////////
    public static function fromStr(stdxmlstr $str, stdname $rootName = null)
    {
        if ($str->isEmpty() || !$str->isValid())
            return null;
        
        $dom = self::create();
        
        @$bSuccess = $dom->loadXML($str->str(self::DEFAULT_CHARSET));
        
        if (!$bSuccess)
        {
            $str->addRootTag($rootName);
            $bSuccess = $dom->loadXML($str->str(self::DEFAULT_CHARSET));
            
            if (!$bSuccess)
                return null;
        }
        
        return $dom;
    }
}

/////////////////////////////////////////////////////////////////////////////
class stddomdoc
{
    /////////////////////////////////////////////////////////////////////////
    public function __construct(stdname $root)
    {
        $this->m_domDoc = stddomxml::create();
        $this->m_domDoc->loadXML("<".$root->str()."/>");
    }

    /////////////////////////////////////////////////////////////////////////
    public function addXML(DOMNode $node)
    {
        if (is_null($node))
            return false;
        
        if ($node instanceof DOMDocument)
            $node = $node->documentElement;

        $this->m_domDoc->documentElement->appendChild($this->m_domDoc->importNode($node, true));
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function addXMLArray(ArrayObject $xmlArr)
    {
        if (is_null($xmlArr))
            return false;

        foreach ($xmlArr as $xml)
            $this->addXML($xml);
    }

    /////////////////////////////////////////////////////////////////////////
    public function getDoc()
    {
        $this->m_domDoc->xinclude();
        return $this->m_domDoc;
    }

    /////////////////////////////////////////////////////////////////////////
    protected $m_domDoc = false;
}

/////////////////////////////////////////////////////////////////////////////
class stdfile
{
    /////////////////////////////////////////////////////////////////////////
    public static function info(/*string*/ $path)
    {
        return new SplFileInfo(trim((string) $path));
    }
}

/////////////////////////////////////////////////////////////////////////////
class stdshdoc
{
    /////////////////////////////////////////////////////////////////////////
    const ATT_MATCH = "match";
    const ATT_NAME = "name";
    const ATT_SELECT = "select";
    
    /////////////////////////////////////////////////////////////////////////
    const TAG_TPL = "template";
    const TAG_CALL_TPL = "call-template";
    const TAG_APPLY_TPL = "apply-templates";
    
    /////////////////////////////////////////////////////////////////////////
    const XSLT_VAERSION = "1.0";
    const XSLT_NS = "http://www.w3.org/1999/XSL/Transform";
    
    ////////////////////////////////////////////////////////////////////////
    public function __construct()
    {
        $this->m_domDoc = stddomxml::create();
        $this->m_domDoc->loadXML(
            "<xsl:stylesheet version=\"".self::XSLT_VAERSION."\" xmlns:xsl=\"".self::XSLT_NS."\"/>");
    }
     
    ////////////////////////////////////////////////////////////////////////
    public function newTpl(/*string*/ $attr, /*string*/ $value)
    {
        $attr = (string)$attr;
        stderr::assert($attr == self::ATT_MATCH || $attr == self::ATT_NAME);
        
        $value = (string) $value;
        
        $tpl = $this->m_domDoc->createElementNS(self::XSLT_NS, "xsl:".self::TAG_TPL, "");
        $tpl->setAttribute($attr, $value);
        
        return $tpl;
    }
    
    ////////////////////////////////////////////////////////////////////////
    public function newCallTpl(/*string*/ $name)
    {
        $name = (string) $name;
        
        $tpl = $this->m_domDoc->createElementNS(self::XSLT_NS, self::TAG_CALL_TPL, "");
        $tpl->setAttribute(self::ATT_NAME, $name);
        
        return $tpl;
    }
    
    ////////////////////////////////////////////////////////////////////////
    public function newApplyTpl(/*string*/ $select)
    {
        $name = (string) $name;
        
        $tpl = $this->m_domDoc->createElementNS(self::XSLT_NS, self::TAG_APPLY_TPL, "");
        $tpl->setAttribute(self::ATT_SELECT, $select);
        
        return $tpl;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function appendChild(DOMNode $node)
    {
        $this->m_domDoc->documentElement->appendChild($node);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function doc()
    {
        return $this->m_domDoc;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function allCalls(DOMDocument $xslt)
    {
        if (is_null($xslt) || !($xslt instanceof DOMDocument))
            return null;
        
        $tpls = $xslt->getElementsByTagName(stdshdoc::TAG_TPL);
            
        if ($tpls->length <= 0)
            return null;

        $calls = new ArrayObject();
        
        foreach ($tpls as $tpl)
        {
            $tplName = $tpl->attributes->getNamedItem(stdshdoc::ATT_NAME)->value;
            
            if ((bool)$tplName)
            {
                $tplCall = $this->newCallTpl($tplName);
                
                // todo: optimizate
                stdarr::appendUnic($calls, $tplCall);
            }
        }
        
        return $calls;
    }
     
    /////////////////////////////////////////////////////////////////////////
    protected $m_domDoc = null;
}

/////////////////////////////////////////////////////////////////////////////
class stdxslt
{
    /////////////////////////////////////////////////////////////////////////
    public static function compile(DOMDocument $xslTemplate, DOMDocument $xmlDocument)
    {
        stderr::assert(!is_null($xslTemplate));
        stderr::assert(!is_null($xmlDocument));
        
        // init XSLT-processor
        $xslt = new XSLTProcessor();
        
        $xslt->importStyleSheet($xslTemplate);
        
        $result = $xslt->transformToDoc($xmlDocument);
        
        return $result;
    }
}

/////////////////////////////////////////////////////////////////////////////
class stdarr
{
    /////////////////////////////////////////////////////////////////////////
    public static function appendUnic(ArrayObject $arr, $object)
    {
        if (is_null($arr) || is_null($object))
            return false;
        
        // todo: optimization and correct == (for DOMDocument this don't work)
        //foreach ($arr as $obj)
        //{
        //    if ($obj == $object)
        //        return false;
        //}
        
        $arr->append($object);
        return true;
    }
}

/////////////////////////////////////////////////////////////////////////////
?>