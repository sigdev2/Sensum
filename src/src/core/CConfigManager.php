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
class CConfigManager extends stddefiner
{
    /////////////////////////////////////////////////////////////////////////
    const DEFAULT_FILE = "conf.xml";
    const CONSTS_PREFIX = "cfg_";
    const DEFAULT_URL_SEP = "?";
    
    /////////////////////////////////////////////////////////////////////////
    // consts keys
    const SITE_NAME = "site_name";
    const HOST = "host";
    const URL_SEP = "url_sep";
    const ALIAS = "alias_";
    const ADMIN_NAME = "admin_name";
    const ADMIN_EMAIL = "admin_email";
    const ADMIN_ICQ = "admin_icq";
    const PAGES_FILE = "pages_file";

    /////////////////////////////////////////////////////////////////////////
    public function __construct(SplFileInfo $cfgFile)
    {
        // load config
        if (is_null($cfgFile) || !$cfgFile->isFile())
        {
            $cfgFile = stdfile::info($_SERVER["DOCUMENT_ROOT"].self::DEFAULT_FILE);
            if (!$cfgFile->isFile())
                stderr::fatal(1001);
        }
    
        if (!($xmlConfig = simplexml_load_file($cfgFile->getRealPath())))
            stderr::fatal(1002);
            
        // todo validate XML by schema
        //  if (!valid) stderr::fatal(1003);
        
        //// set constants
        // site name
        if (!isset($xmlConfig->site))
            stderr::fatal(1004);

        self::set(self::CONSTS_PREFIX.self::SITE_NAME, stdtext::toText($xmlConfig->site["name"])->str());
        
        // host
        $host = stdhost::toHost($xmlConfig->site["host"]);
        self::set(self::CONSTS_PREFIX.self::HOST, ($host->len() > 0 && $host->isValid()) ? $host : $_SERVER["SERVER_NAME"]);
        
        // url sep
        $sep = stdsep::toSep($xmlConfig->site["url_sep"]);
        self::set(self::CONSTS_PREFIX.self::URL_SEP, $sep->len() > 0 ? $sep : self::DEFAULT_URL_SEP);

        // alias
        $aliasCount = sizeof($xmlConfig->site->alias);
        for ($i = 0; $i < $aliasCount; $i++)
        {
            $alias = stdhost::toHost($xmlConfig->site->alias[$i]);
            if ($alias->isValid())
                self::set(self::CONSTS_PREFIX.self::ALIAS.($i + 1), $alias->str());
        }
        
        // autor
        if (isset($xmlConfig->admin))
        {
            self::set(self::CONSTS_PREFIX.self::ADMIN_NAME, stdtext::toText($xmlConfig->admin["name"])->str());
            self::set(self::CONSTS_PREFIX.self::ADMIN_ICQ, (int)trim($xmlConfig->admin["icq"]));
            
            $email = stdemail::toEmail($xmlConfig->admin["email"]);
            self::set(self::CONSTS_PREFIX.self::ADMIN_EMAIL, $email->isValid() ? $email->str() : null);
        }
        else
        {
            stderr::warning(1006);
        }
        
        // init stderr
        stderr::setSiteInfo(stdtext::toText(self::get(self::CONSTS_PREFIX.self::SITE_NAME)),
                            stdemail::toEmail(self::get(self::CONSTS_PREFIX.self::ADMIN_EMAIL )));
        
        // pagesFile
        $this->m_pagesFile = stdfile::info($xmlConfig->pagesFile);
        if (!$this->m_pagesFile->isFile())
            stderr::fatal(1007);
        
        self::set(self::CONSTS_PREFIX.self::PAGES_FILE, $this->m_pagesFile->getRealPath());

        // moduls
        if (!isset($xmlConfig->moduls->modul))
        {
            stderr::fatal(1008);
        }
        else
        {
            $mdCount = sizeof($xmlConfig->moduls->modul);
            $this->m_moduls = new SplFixedArray($mdCount);
            for ($i = 0; $i < $mdCount; $i++)
            {
                $data = $xmlConfig->moduls->modul[$i];
                $modul = new CModulInfo(stdtext::toText($data["id"]),
                                        stdname::toName($data["class"]),
                                        stdfile::info($data["src"]));
                
                if ($modul->isValid())
                    $this->m_moduls[$i] = $modul;
                else
                    stderr::fatal(1009);
            }
        }
        
        // widgets
        if (isset($xmlConfig->widgetBars))
        {
            $this->m_widgets = new ArrayObject();
            foreach ($xmlConfig->widgetBars->children() as $barName => $bar)
            {
                $widgets = $bar->widget;
                $wgCount = sizeof($widgets);
                for ($i = 0; $i < $wgCount; $i++)
                {
                    $data = $widgets[$i];
                    $widget = new CWidgetInfo(stdtext::toText($data["id"]),
                                              stdname::toName($data["class"]),
                                              stdfile::info($data["src"]),
                                              (int)$data["access"],
                                              (int)$data["admin"],
                                              stdname::toName($barName),
                                              isset($data["options"]) ? stdparam::toParam($data["options"]) : null);
                    
                    if ($widget->isValid())
                        $this->m_widgets->append($widget);
                    else
                        stderr::warning(1010);
                }
            }
        }
        
        // template
        if (isset($xmlConfig->template))
        {
            // xslt File
            $this->m_xsltFile = stdfile::info($xmlConfig->template["src"]);
            if (!$this->m_xsltFile->isFile())
                stderr::fatal(1011);
            
            // admin xslt File
            $this->m_xsltAdminFile = stdfile::info($xmlConfig->template["adminSrc"]);
            if (!$this->m_xsltAdminFile->isFile())
                stderr::fatal(1012);
        }
        else
        {
            stderr::fatal(1013);
        }
        
        // head todo
        if (isset($xmlConfig->head))
        {
            stddochead::setDescriptionSep(stdsep::toSep($xmlConfig->head->description["separator"]));
            stddochead::setKeywordsSep(stdsep::toSep($xmlConfig->head->keywords["separator"]));
            stddochead::setTitleSep(stdsep::toSep($xmlConfig->head->titleSep));
            
            stddochead::addDescription(stdtext::toText($xmlConfig->head->description));
                       
            $keywords = stdtext::toText($xmlConfig->head->keywords);
            $keywords = $keywords->splitByWords();
            foreach ($keywords as $keyword)
                stddochead::addKeyword($keyword);
            
            stddochead::addTitle(stdtext::toText(self::get(self::CONSTS_PREFIX."site_name")));
        }
        else
        {
            stderr::warning(1014);
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getModuls()
    {
        return $this->m_moduls;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getWidgets(stdtext $barName = null)
    {
        if (!is_null($this->m_widgets))
            return new CWidgetInfoIter($this->m_widgets, $barName);
        
        return new CWidgetInfoIter(new ArrayObject());
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getPagesFile()
    {
        return $this->m_pagesFile;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getSiteInfoAsDOMXML()
    {
        $xml = "<siteInfo>";
        
        $xml .= "<name>".self::get(self::CONSTS_PREFIX.self::SITE_NAME)."</name>";
        $xml .= "<host>".self::get(self::CONSTS_PREFIX.self::HOST)."</host>";
        $xml .= "<url_sep>".self::get(self::CONSTS_PREFIX.self::URL_SEP)."</url_sep>";
        
        $xml .= "<aliasList>";
        $aliasKey = self::CONSTS_PREFIX.self::ALIAS;
        for ($i = 0; self::is_set($aliasKey.$i); $i++)
            $xml .= "<alias>".self::get($aliasKey.$i)."</alias>";
        $xml .= "</aliasList>";
        
        $xml .= "<admin name=\"".self::get(self::CONSTS_PREFIX.self::ADMIN_NAME)."\" email=\"".self::get(self::CONSTS_PREFIX.self::ADMIN_EMAIL)."\" icq=\"".self::get(self::CONSTS_PREFIX.self::ADMIN_ICQ)."\" />";
        
        $xml .= "</siteInfo>";
        
        $dom = stddomxml::create();
        $dom->loadXML($xml);
        
        return $dom;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getXSLTFile()
    {
        return $this->m_xsltFile;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getXSLTAdminFile()
    {
        return $this->m_xsltAdminFile;
    }

    /////////////////////////////////////////////////////////////////////////
    private $m_moduls = null;
    private $m_widgets = null;
    private $m_pagesFile = null;
    private $m_xsltFile = null;
    private $m_xsltAdminFile = null;
}

/////////////////////////////////////////////////////////////////////////////
?>