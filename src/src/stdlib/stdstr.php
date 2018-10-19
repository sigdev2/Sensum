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
class stdstr
{
    /////////////////////////////////////////////////////////////////////////
    const DEFAULT_CHARSET = "UTF-8";
    const I_LATIN_NUM = "QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890";

    /////////////////////////////////////////////////////////////////////////
    public function __construct($str = null, /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        if ($str instanceof stdstr)
            $this->m_str = $str->str();
        else if (!is_null($str))
        {
            $currCs = (string)$currCs;
            $str = (string)$str;
            
            if ($currCs != self::DEFAULT_CHARSET)
                $this->m_str = iconv($currCs, self::DEFAULT_CHARSET, $str);
            else
                $this->m_str = $str;
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function __toString()
    {
        return $this->m_str;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function str(/*string*/ $charset = false)
    {
        if ($charset != false)
        {
            $charset = (string)$charset;

            if(self::DEFAULT_CHARSET != $charset)
                return iconv(self::DEFAULT_CHARSET, $charset, $this->m_str);
        }
        
        return $this->m_str;
    }

    /////////////////////////////////////////////////////////////////////////
    public function isEmpty()
    {
        return $this->len() <= 0;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function charset()
    {
        return self::DEFAULT_CHARSET;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function len()
    {
        return iconv_strlen($this->m_str, self::DEFAULT_CHARSET);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function at(/*int*/ $pos)
    {
        if ($pos >= $this->len())
             return null;

        return iconv_substr($this->m_str, $pos, 1, self::DEFAULT_CHARSET);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function md5() // stdstr
    {
        return self::toStr(md5($this->m_str));
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function toStr($str, $currCs = self::DEFAULT_CHARSET)
    {
        return new stdstr($str, $currCs);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function rand(/*int*/ $len = 5, stdstr $symbols = null)
    {
        if (is_null($symbols))
             $symbols = self::toStr(self::I_LATIN_NUM);

        $len = (int)$len;
 
        $key = "";
        
        for ($i = 0; $i < $len; $i++)
            $key .= $symbols->at(rand(0, $symbols->len() - 1));
        
        return self::toStr($key);
    }
    
    /////////////////////////////////////////////////////////////////////////
    protected $m_str = "";
}

/////////////////////////////////////////////////////////////////////////////
abstract class stdsafestr extends stdstr
{   
    /////////////////////////////////////////////////////////////////////////
    public function __construct($str = null, /*string*/ $currCs = self::DEFAULT_CHARSET,
                                /*int*/ $maxLen = -1, /*string*/ $delPreg = false,
                                /*bool*/ $bTrim = true, /*bool*/ $bSpec = true)
    {
        $maxLen = (int)$maxLen;
        
        parent::__construct($str, $currCs);
        
        if ((bool)$bTrim)
            $this->m_str = trim($this->m_str); // todo: may be not cirrect
        
        if ($maxLen > 0 && iconv_strlen($this->m_str, self::DEFAULT_CHARSET) > $maxLen)
            $this->m_str = iconv_substr($this->m_str, 0, $maxLen, self::DEFAULT_CHARSET);
        
        if ($delPreg)
            $this->m_str = preg_replace("/".(string)$delPreg."/iu", "", $this->m_str);
        
        $this->m_bSpec = (bool)$bSpec;
        if ($this->m_bSpec)
            $this->m_str = htmlspecialchars($this->m_str, ENT_QUOTES, self::DEFAULT_CHARSET);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function str(/*string*/ $charset = false)
    {
        if (!$this->m_bSpec)
            return parent::str($charset);
        
        if ($charset != false)
        {
            $charset = (string)$charset;
        
            if (self::DEFAULT_CHARSET != $charset)
            {
                $str = iconv(self::DEFAULT_CHARSET, $charset, htmlspecialchars_decode($this->m_str));
                return htmlspecialchars($str, ENT_QUOTES, $charset);
            }
        }

        return $this->m_str;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function htmlLen()
    {        
        if (!$this->m_bSpec)
            return parent::len();

        return iconv_strlen($this->_normalStr(), self::DEFAULT_CHARSET);
    }
    
    /////////////////////////////////////////////////////////////////////////
    protected function _normalStr()
    {
        if (!$this->m_bSpec)
            return $this->m_str;
        
        return htmlspecialchars_decode($this->m_str);
    }
    
    /////////////////////////////////////////////////////////////////////////
    private $m_bSpec = false;
}

/////////////////////////////////////////////////////////////////////////////
abstract class stdstrvalid extends stdsafestr
{   
    /////////////////////////////////////////////////////////////////////////
    public function __construct($str = null, /*string*/ $currCs = self::DEFAULT_CHARSET,
                                /*string*/ $validPreg = ".*", /*int*/ $maxLen = -1,
                                /*string*/ $delPreg = false, /*bool*/ $bTrim = true,
                                /*bool*/ $bSpec = false)
    {
        parent::__construct($str, $currCs, $maxLen, $delPreg, $bTrim, $bSpec);
        $this->m_validPreg = (string)$validPreg;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function isValid()
    {
        return (bool)preg_match("/".$this->m_validPreg."/iu", $this->m_str);
    }
    
    /////////////////////////////////////////////////////////////////////////
    protected $m_validPreg = ".*";
}

/////////////////////////////////////////////////////////////////////////////
class stdword extends stdsafestr
{
    /////////////////////////////////////////////////////////////////////////
    const SEP_CHARS = "[^\p{L}\p{Nd}_]";
    const MAX_LEN = 75;
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct(/*string*/ $str = "", /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        parent::__construct($str, $currCs, self::MAX_LEN, self::SEP_CHARS);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function toWord($str, $currCs = self::DEFAULT_CHARSET)
    {
        return new stdword($str, $currCs);
    }
}

/////////////////////////////////////////////////////////////////////////////
class stdname extends stdsafestr
{
    /////////////////////////////////////////////////////////////////////////
    const NOT_NAME_CHARS = "[^a-zA-Z\p{Nd}_]";
    const MAX_LEN = 255;
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct(/*string*/ $str = "", /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        parent::__construct($str, $currCs, self::MAX_LEN, self::NOT_NAME_CHARS);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function toName($str, $currCs = self::DEFAULT_CHARSET)
    {
        return new stdname($str, $currCs);
    }
}

/////////////////////////////////////////////////////////////////////////////
class stdphone extends stdsafestr
{
    /////////////////////////////////////////////////////////////////////////
    const NOT_PHONES_CHARS = "[\p{L}]";
    const MAX_LEN = 255;
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct(/*string*/ $str = "", /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        parent::__construct($str, $currCs, self::MAX_LEN, self::NOT_PHONES_CHARS);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function toPhone($str, $currCs = self::DEFAULT_CHARSET)
    {
        return new stdphone($str, $currCs);
    }
}

/////////////////////////////////////////////////////////////////////////////
class stdsep extends stdsafestr
{
    /////////////////////////////////////////////////////////////////////////
    const MAX_LEN = 4;
    const NOT_SEP_CHARS = "[\p{L}\p{Nd}_]";
    const ELIDE_SEP = "...";
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct(/*string*/ $str = "", /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        parent::__construct($str, $currCs, self::MAX_LEN, self::NOT_SEP_CHARS, false);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function toSep($str, $currCs = self::DEFAULT_CHARSET)
    {
        return new stdsep($str, $currCs);
    }
}

/////////////////////////////////////////////////////////////////////////////
class stdtext extends stdsafestr
{
    /////////////////////////////////////////////////////////////////////////
    public function __construct(/*string*/ $str = "", /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        parent::__construct($str, $currCs);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function splitByWords()
    {
        $words = new ArrayObject(preg_split("/".stdword::SEP_CHARS."/iu", $this->m_str, -1, PREG_SPLIT_NO_EMPTY));
        $count = sizeof($words);
        
        for ($i = 0; $i < $count; $i++)
            $words[$i] = stdword::toWord($words[$i]);

        return $words;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function elidedText(/*int*/ $l)
    {
        $l = (int)$l;
        
        if ($this->htmlLen() <= $l)
            return clone $this;

        $sep = stdsep::toSep(stdsep::ELIDE_SEP);
        $l -= $sep->htmlLen();
        return self::toText($this->truncate($l)->str().$sep->str());
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function truncate(/*int*/ $l)
    {
        $l = (int)$l;
        
        if ($this->htmlLen() <= $l)
            return clone $this;

        $str = $this->_normalStr();
        
        $str = iconv_substr($str, 0, $l, self::DEFAULT_CHARSET);
        
        return self::toText($str);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function toText($str, $currCs = self::DEFAULT_CHARSET)
    {
        return new stdtext($str, $currCs);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function joinWords(ArrayObject $arr, stdsep $sep, /*int*/ $l = -1)
    {
        if (is_null($arr) || is_null($sep) || $arr->count() <= 0)
            return new stdtext();
        
        $l = (int)$l;
        
        $str = "";
        $iterator = $arr->getIterator();
        $htmlLen = 0;
        
        while ($iterator->valid())
        {
            $bIsValid = $iterator->current() instanceof stdword;
            if ($bIsValid)
            {
                if ($l > 0)
                {
                    $htmlLen += $iterator->current()->htmlLen();
                    if ($htmlLen >= $l)
                        break;
                }
                
                $str .= $iterator->current()->str();
            }
            
            $iterator->next();
            
            if ($bIsValid && $iterator->valid())
                $str .= $sep->str();
        }
        
        return self::toText($str);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function joinText(ArrayObject $arr, stdsep $sep)
    {
        if (is_null($arr) || is_null($sep) || $arr->count() <= 0)
            return new stdtext();
        
        $str = "";
        $iterator = $arr->getIterator();
        
        while ($iterator->valid())
        {
            $bIsValid = $iterator->current() instanceof stdtext;
            if ($bIsValid)
                $str .= $iterator->current()->str();
            
            $iterator->next();
            
            if ($bIsValid && $iterator->valid())
                $str .= $sep->str();
        }
        
        return self::toText($str);
    }
}

/////////////////////////////////////////////////////////////////////////////
class stdxmlstr extends stdstrvalid
{
    /////////////////////////////////////////////////////////////////////////
    const BS_QUOTE_IN_TAG = "\\\\";// todo: deleting only in atribute value 
    const XML_STRING = ".*"; // todo: validation
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct(/*string*/ $str = "", /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        parent::__construct($str, $currCs = self::DEFAULT_CHARSET, self::XML_STRING , -1/*, self::BS_QUOTE_IN_TAG*/);
        // todo
        //preg_replace(array("/(<.*)(=\\\\['\"])(.*>)/iu", "/(<.*)(\\\\['\"]\s)(.*>)/iu"), array("(1)(=\")(3)", "(1)(\"\s)(3)"), $this->m_str);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function addRootTag(stdname $rootName = null)
    {
        if (!is_null($rootName) && !$rootName->isEmpty())
            $this->m_str = "<".$rootName->str().">".$this->m_str."</".$rootName->str().">";
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function toXML($str, $currCs = self::DEFAULT_CHARSET)
    {
        return new stdxmlstr($str, $currCs);
    }
}

/////////////////////////////////////////////////////////////////////////////
?>