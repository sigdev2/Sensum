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
require_once dirname(__FILE__)."/stditer.php";
require_once dirname(__FILE__)."/stdstr.php";
require_once dirname(__FILE__)."/stdurl.php";
require_once dirname(__FILE__)."/stdhelpers.php";

/////////////////////////////////////////////////////////////////////////////
class stderror
{
    /////////////////////////////////////////////////////////////////////////
    // string consts
    const DEFAULT_ERROR_NAME = "Неизвестная ошибка";
    const DEFAULT_ERROR_TEXT = "Ошибка незавестна. Код ошибки не индефицирован.";
    const DEFAULT_ERROR_CODE = 0;
    
    /////////////////////////////////////////////////////////////////////////
    const DEFAULT_CHARSET = "UTF-8";
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct(/*int*/ $code = self::DEFAULT_ERROR_CODE, stdtext $name = null, stdtext $text = null)
    {
        $this->m_code = (int)$code;
        
        if (is_null($text) || $text->isEmpty())
            $this->m_text = stdtext::toText(self::DEFAULT_ERROR_TEXT, self::DEFAULT_CHARSET);
        else
            $this->m_text = $text;

        if (is_null($name) || $name->isEmpty())
            $this->m_name = stdtext::toText(self::DEFAULT_ERROR_NAME, self::DEFAULT_CHARSET);
        else
            $this->m_name = $name;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function text()
    {
        return $this->m_text;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function name()
    {
        return $this->m_name;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function code()
    {
        return $this->m_code;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private $m_text = null;
    private $m_name = null;
    private $m_code = null;
}

/////////////////////////////////////////////////////////////////////////////
class stdtrace
{
    /////////////////////////////////////////////////////////////////////////
    const DEFAULT_FUNCTION = "Unknown";
    const DEFAULT_LINE = 0;
    const DEFAULT_CLASS = "Unknown";
    const DEFAULT_TYPE = "::";
    const DEFAULT_CHARSET = "UTF-8";
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct(stdname $function, /*int*/ $line,
                                SplFileInfo $file, stdname $class, stdsep $type, ArrayObject $args)
    {
        if (is_null($function))
            $function = stdname::toName(self::DEFAULT_FUNCTION, self::DEFAULT_CHARSET);
        if (is_null($line))
            $line = (int)self::DEFAULT_LINE;
       
        // if $file == null than store null       
       
        if (is_null($class))
            $class = stdname::toName(self::DEFAULT_CLASS, self::DEFAULT_CHARSET);
        if (is_null($type))
            $type = stdsep::toSep(self::DEFAULT_TYPE, self::DEFAULT_CHARSET);
        if (is_null($args))
            $args = new ArrayObject();

        $this->m_function = $function;
        $this->m_line = (int)$line;
        $this->m_file = $file;
        $this->m_class = $class;
        $this->m_type = $type;
        $this->m_args = $args;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getFunction()
    {
        return $this->m_function;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getLine()
    {
        return $this->m_line;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getFile()
    {
        return $this->m_file;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getClass()
    {
        return $this->m_class;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getType()
    {
        return $this->m_type;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getArgs()
    {
        return $this->m_args;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function asText()
    {
        $strText = "";
        
        if (!is_null($this->m_file))
            $strText .= "FILE: ".$this->m_file->getRealPath();
        
        $strText .= " LINE: (".$this->m_line.")";
        $strText .= " ".$this->m_class->str(self::DEFAULT_CHARSET);
        $strText .= $this->m_type->str(self::DEFAULT_CHARSET);
        $strText .= $this->m_function->str(self::DEFAULT_CHARSET);
        $strText .= "(";
        for($it = $this->m_args->getIterator(); $it->valid(); $it->next())
            $strText .= $it->current();
        $strText .= ")";
        
        return $strText;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function toTrace($backtrace = null, /*string*/ $currCs = self::DEFAULT_CHARSET)
    {
        if (is_array($backtrace))
        {
            $function = stdname::toName($backtrace["name"], $currCs);
            $line = (int)$backtrace["line"];
            $file = stdfile::info($backtrace["file"]);
            $class = stdname::toName($backtrace["class"], $currCs);
            $type = stdsep::toSep($backtrace["type"], $currCs);
            $args = new ArrayObject($backtrace["args"]);
        }
        
        return new stdtrace($function, $line, $file, $class, $type, $args);
    }
    
    /////////////////////////////////////////////////////////////////////////
    private $m_function = null;
    private $m_line = null;
    private $m_file = null;
    private $m_class = null;
    private $m_type = null;
    private $m_args = null;
}

/////////////////////////////////////////////////////////////////////////////
class stderr
{
    /////////////////////////////////////////////////////////////////////////
    // string consts
    const ASSERT_NAME = "Assert";
    const ASSERT_TEXT = "When checking the assert showed the false value.";
    const EXCEPTION_NAME = "Exception";
    const DEFAULT_USER_MESSAGE = "Please check back later, or if the problem persist, contact the webmasters.";
    const DEFAULT_SITE_NAME = "Fatal Error";
    const DEFAULT_EMAIL = "";
    const DEFAULT_CHARSET = "UTF-8";
    
    /////////////////////////////////////////////////////////////////////////
    // todo: set user message
    public static function setSiteInfo(stdtext $name, stdemail $adminEmail)
    {
        if (!is_null($name))
            self::$m_siteName = $name->str(self::DEFAULT_CHARSET);
        
        if (!is_null($adminEmail) && $adminEmail->isValid())
            self::$m_siteEmail = $adminEmail->str(self::DEFAULT_CHARSET);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function addError(/*int*/ $code, stdtext $name, stdtext $text)
    {
        $code = (int)$code;
        
        if (is_null(self::$m_errors))
            self::$m_errors = new ArrayObject();
        
        if (!is_null($code) && !is_null($name) && !is_null($text))
            self::$m_errors[$code] = new stderror($code, $name, $text);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function fatal(/*int*/ $code)
    {
        $code = (int)$code;
        
        $errObj = null;

        if (!is_null(self::$m_errors) && self::$m_errors->offsetExists($code))
            $errObj = self::$m_errors[$code];

        self::_echoErrorPage($errObj);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function warning(/*int*/ $code)
    {
        $code = (int)$code;
        
        if (is_null(self::$m_warStack))
            self::$m_warStack = new ArrayObject();
        
        self::$m_warStack->append($code);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function assert(/*bool*/ $equals)
    {
        $equals = (bool)$equals;
        $backtrace = debug_backtrace();
        $backtrace = stdtrace::toTrace($backtrace[1]);
        
        if (!$equals)
        {
            $errObj = new stderror(0, stdtext::toText(self::ASSERT_NAME, self::DEFAULT_CHARSET),
                stdtext::toText(self::ASSERT_TEXT.$backtrace->asText()));
        
            self::_echoErrorPage($errObj);
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function except(Exception $ex)
    {
        $errObj = null;
        
        if (!is_null($ex))
            $errObj = new stderror(0, stdtext::toText(self::EXCEPTION_NAME, self::DEFAULT_CHARSET),
                stdtext::toText($ex->getMessage()." Trace: ".$ex->getTraceAsString()));

        self::_echoErrorPage($errObj);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function getWarnings()
    {
        if (is_null(self::$m_warStack))
            self::$m_warStack = new ArrayObject();
        
        return new stdItByKeyArr(self::$m_errors, self::$m_warStack);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function getFormatedWarnings()
    {
        $str = "";
        $iter = self::getWarnings();
        foreach ($iter as $err)
            echo $str .= "<!-- (".$err->code().") ".$err->name()->str(self::DEFAULT_CHARSET)." : ".$err->text()->str(self::DEFAULT_CHARSET)." -->\n\n";
        
        return stdstr::toStr($str, self::DEFAULT_CHARSET);
    }
    
    /////////////////////////////////////////////////////////////////////////
    // todo: add searching form
    private static function _echoErrorPage(stderror $errObj)
    {
        if (is_null($errObj))
            $errObj = new stderror();

        header("HTTP/1.x 404 Script error");
        header("Content-Type: text/html;charset=".self::DEFAULT_CHARSET);
        
        if (!is_null(self::$m_warStack))
            echo stderr::getFormatedWarnings()->str(self::DEFAULT_CHARSET);
        
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
  <title>".self::$m_siteName." - ".$errObj->name()->str(self::DEFAULT_CHARSET)."</title>
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=".self::DEFAULT_CHARSET."\" />
  <meta http-equiv=\"content-language\" content=\"ru\" />
  <meta name=\"robots\" content=\"noindex,nofollow\" />
</head>
<body>
   <h1><a href = \"http://".$_SERVER['SERVER_NAME']."\">".self::$m_siteName."</a></h1>
   <p>URL: http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."</p>
   <hr />
   <h2>".$errObj->name()->str(self::DEFAULT_CHARSET)." ".($errObj->code() != 0 ? "(".$errObj->code().")" : "")."</h2>
   <p>
       ".$errObj->text()->str(self::DEFAULT_CHARSET)."
   </p>
   <hr />
   <i>".self::$m_userMessage." (<a href=\"mailto:".self::$m_siteEmail."\">".self::$m_siteEmail."</a>)</i>
</body>";

        die();
    }
    
    /////////////////////////////////////////////////////////////////////////
    private static $m_warStack = null;
    private static $m_errors = null;
    
    // site info
    private static $m_siteName = self::DEFAULT_SITE_NAME;
    private static $m_siteEmail = self::DEFAULT_EMAIL;
    private static $m_userMessage = self::DEFAULT_USER_MESSAGE;
}

/////////////////////////////////////////////////////////////////////////////
?>