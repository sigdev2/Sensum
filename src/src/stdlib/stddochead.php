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
require_once dirname(__FILE__)."/stdhelpers.php";

/////////////////////////////////////////////////////////////////////////////
class stddochead
{
    /////////////////////////////////////////////////////////////////////////
    const DEFAULT_KEYWORDS_SEP = " ";
    const DEFAULT_DESCRIPTION_SEP = " ";
    const DEFAULT_TITLE_SEP = " ";
    const DEFAULT_CHARSET = "UTF-8";
    
    const MAX_KEYWORDS_LEN = 150;
    const MAX_DESCRIPTION_LEN = 150;
    const MAX_TITLE_LEN = 75;
    
    /////////////////////////////////////////////////////////////////////////
    public static function addKeyword(stdword $word)
    {
        if (is_null(self::$m_keywords))
            self::$m_keywords = new ArrayObject();
        
        if (!is_null($word))
            self::$m_keywords->append($word);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function addDescription(stdtext $text)
    {
        if (is_null(self::$m_description))
            self::$m_description = new ArrayObject();
        
        if (!is_null($text))
            self::$m_description->append($text);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function addTitle(stdtext $text)
    {
        if (is_null(self::$m_title))
            self::$m_title = new ArrayObject();

        if (!is_null($text))
            self::$m_title->append($text);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function clearKeywords()
    {
        self::$m_keywords = null;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function clearDescription()
    {
        self::$m_description = null;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function clearTitle()
    {
        self::$m_title = null;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function setKeywordsSep(stdsep $sep)
    {
        if (!is_null($sep))
            self::$m_keywordsSep = $sep;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function setDescriptionSep(stdsep $sep)
    {
        if (!is_null($sep))
            self::$m_descriptionSep = $sep;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function setTitleSep(stdsep $sep)
    {
        if (!is_null($sep))
            self::$m_titleSep = $sep;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function getKeywordSep()
    {
        return self::$m_keywordsSep;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function getDescriptionSep()
    {
        return self::$m_descriptionSep;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function getTitleSep()
    {
        return self::$m_titleSep;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function getAsDOMXML()
    {
        // todo: correct elide in center by parts
        $xml = "<head>";
        
        if (!is_null(self::$m_keywords))
        {
            if (is_null(self::$m_keywordsSep))
                self::$m_keywordsSep = stdsep::toSep($sep, self::DEFAULT_KEYWORDS_SEP);

            $keywords = stdtext::joinWords(self::$m_keywords, self::$m_keywordsSep, self::MAX_KEYWORDS_LEN);
            $xml .= "<keywords>";
            $xml .= $keywords->str(self::DEFAULT_CHARSET);
            $xml .= "</keywords>";
        }
        
        if (!is_null(self::$m_description))
        {
            if (is_null(self::$m_descriptionSep))
                self::$m_descriptionSep = stdsep::toSep($sep, self::DEFAULT_DESCRIPTION_SEP);

            $description = stdtext::joinText(self::$m_description, self::$m_descriptionSep);
            $xml .= "<description>";
            $xml .= $description->elidedText(self::MAX_DESCRIPTION_LEN)->str(self::DEFAULT_CHARSET);
            $xml .= "</description>";
        }
        
        if (!is_null(self::$m_title))
        {
            if (is_null(self::$m_titleSep))
                self::$m_titleSep = stdsep::toSep($sep, self::DEFAULT_TITLE_SEP);

            $title = stdtext::joinText(self::$m_title, self::$m_titleSep);
            $xml .= "<title>";
            $xml .= $title->elidedText(self::MAX_TITLE_LEN)->str(self::DEFAULT_CHARSET);
            $xml .= "</title>";
        }
        
        $xml .= "</head>";
        $domDoc = stddomxml::create();
        $domDoc->loadXML($xml);
        
        return $domDoc;
    }

    /////////////////////////////////////////////////////////////////////////
    private static $m_keywords = null;
    private static $m_description = null;
    private static $m_title = null;
    private static $m_keywordsSep = null;
    private static $m_descriptionSep = null;
    private static $m_titleSep = null;
}

/////////////////////////////////////////////////////////////////////////////
?>