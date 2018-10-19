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
class CPagesManager
{
    /////////////////////////////////////////////////////////////////////////
    public function __construct(SplFileInfo $pagesFile, /*string*/ $query, IUserAccess $currUser)
    {
        stderr::assert(!is_null($pagesFile));
        stderr::assert(!is_null($currUser));
        $query = stdstr::toStr($query);
        
        // get query and parameters
        $pos = iconv_strrpos($query->str(), CConfigManager::get("cfg_url_sep"), stdstr::DEFAULT_CHARSET);
        if ($pos === false || $pos == $query->len() - 1)
        {
            $query = $query->str();
            $parameters = null;
        }
        else
        {
            $len = $query->len();
            $parameters = stdparam::toParam(iconv_substr($query->str(), $pos + 1, $len - $pos - 1, stdstr::DEFAULT_CHARSET));
            $query = iconv_substr($query->str(), 0, $len - ($len - $pos), stdstr::DEFAULT_CHARSET);
        }
        
        // load pages file
        $xml = null;
        if(!($xml = simplexml_load_file($pagesFile->getRealPath())))
            stderr::fatal(2000);
            
        // todo: validate with schema stderr::fatal(2000); must walidate id

        // find current page
        $page = $xml->xpath("//url[@id = '".$query."']/parent::page");
        if ($page == false ||  sizeof($page) < 1)
            stderr::fatal(404);
        
        $page = $page[0];
        
        // find all parents pages
        $pages = $xml->xpath("//url[@id = '".$query."']/ancestor::page");
        $parent = null;
        if (sizeof($pages) > 1)
        {
            foreach ($pages as $node)
            {
                if ($node == $page)
                    break;

                $parent = new CPage(stdurl::toURL($node->url[0]["id"]), stdtext::toText($node["id"]), $parent);
            }
        }
        
        if (is_null($page))
            stderr::fatal(404);
        
        if (!$currUser->isHasAccessTo($page["access"]))
            stderr::fatal(404);
        
        $isMain = $page->url[0]["value"] == $query;
        
        $isAdmin = $currUser->isAdminFor($page["admin"]);
        
        $dataFile = null;
        if (isset($page["dataFile"]))
        {
            $dataFile = stdfile::info($page["dataFile"]);
            if (!$dataFile->isFile())
                stderr::fatal(2001);
        }

        $pageId = stdtext::toText($page["id"]);
        stddochead::addTitle($pageId);
        
        $this->m_currPage = new CCurrentPage(stdurl::toURL($page->url[0]["id"]), $pageId, $parent, $isMain, $parameters, stdtext::toText($page["type"]),
            $dataFile, $isAdmin);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getCurrPage()
    {
        return $this->m_currPage;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private $m_currPage = null;
}

/////////////////////////////////////////////////////////////////////////////
?>