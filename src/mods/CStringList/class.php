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
class CStringList implements IModul
{
    /////////////////////////////////////////////////////////////////////////
    const DESCRIPTION_TAG = "description";
    const KEYWORDS_TAG = "keywords";
    const STRINGLIST_TAG = "content";
    const MESSAGE_TAG = "message";
    
    const MESSAGE_NO_VALID = "Not valid html";
    const MESSAGE_SAVED = "Change saved";
    
    const XSLT_FILE_NAME = "tpl.xsl";
    const ADMIN_XSLT_FILE_NAME = "admintpl.xsl";
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct()
    {
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function init(CCurrentPage $page)
    {
        $message = null;
        
        if ($page->isAdmin())
        {
            $this->m_isAdmin = true;
            
            if (!is_null($_POST[self::DESCRIPTION_TAG]) &&
                !is_null($_POST[self::KEYWORDS_TAG]) &&
                !is_null($_POST[self::STRINGLIST_TAG]))
            {
                $xml = "<root>";
                
                $xml .= "<".self::DESCRIPTION_TAG.">";
                $xml .= stdtext::toText($_POST[self::DESCRIPTION_TAG]);
                $xml .= "</".self::DESCRIPTION_TAG.">";
                
                $xml .= "<".self::KEYWORDS_TAG.">";
                $xml .= stdtext::toText($_POST[self::KEYWORDS_TAG]);
                $xml .= "</".self::KEYWORDS_TAG.">";
                
                $xml .= "</root>";
                
                $dom = stddomxml::create();
                $dom->loadXML($xml);
                
                $content = stddomxml::fromStr(stdxmlstr::toXML($_POST[self::STRINGLIST_TAG]), stdname::toName("div"));
                if (!is_null($content))
                {
                    $contentXML = $dom->createElement(self::STRINGLIST_TAG);
                    $dom->documentElement->appendChild($contentXML);
                    $contentXML->appendChild($dom->importNode($content->documentElement, true));
                    $dom->formatOutput = true;
                    $dom->save($page->dataFile()->getRealPath());
                }
                else
                {
                    $message = self::MESSAGE_NO_VALID;
                }
            }
        }
    
        $dom = stddomxml::fromFile($page->dataFile());
        
        if ($this->m_isAdmin)
        {
            $this->m_content = stddomxml::create();
            $this->m_content->loadXML("<".self::CONTENT_TAG_NAME."/>");
            $this->m_content->documentElement->appendChild($this->m_content->importNode($dom->documentElement, true));
            if (!is_null($message))
            {
            	$messageTag = $this->m_content->createElement(self::MESSAGE_TAG, $message);
                $this->m_content->documentElement->appendChild($messageTag);
            }
        }
        else
        {
            $content = $dom->getElementsByTagName(self::CONTENT_TAG_NAME);
            $this->m_content = $content->item(0);
        }
        
        $description = $dom->getElementsByTagName(self::DESCRIPTION_TAG);
        $description = stdtext::toText($description->item(0)->nodeValue);
        
        stddochead::addDescription($description);
        
        $keywords = $dom->getElementsByTagName(self::KEYWORDS_TAG);
        $keywords = stdtext::toText($keywords->item(0)->nodeValue);
        
        $keywords = $keywords->splitByWords();
        foreach ($keywords as $keyword)
            stddochead::addKeyword($keyword);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getAsDOMXML()
    {
        return $this->m_content;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getXSLT()
    {
        if ($this->m_isAdmin)
            return stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/".self::ADMIN_XSLT_FILE_NAME));
        
        return stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/".self::XSLT_FILE_NAME));
    }

    /////////////////////////////////////////////////////////////////////////
    private $m_content = null;
    private $m_isAdmin = false;
}

/////////////////////////////////////////////////////////////////////////////
?>