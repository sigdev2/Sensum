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
class CPage
{
    /////////////////////////////////////////////////////////////////////////
    public function __construct(stdurl $query, stdtext $id, CPage $parent = null)
    {
        stderr::assert(!is_null($query));
        stderr::assert($query->isValid());
        stderr::assert(!is_null($id));
        
        $this->m_query = $query;
        $this->m_id = $id;
        $this->m_parent = $parent;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getAsDOMXML()
    {
        $dom = stddomxml::create();
        
        $xml = "<url>";
        
        $xml = $this->_getAsXMLText($xml);

        $xml .= "</url>";
        
        $dom->loadXML($xml);
        return $dom;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function query()
    {
        return $this->m_query;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function id()
    {
        return $this->m_id;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getParent()
    {
        return $this->m_parent;
    }
    
    /////////////////////////////////////////////////////////////////////////
    protected function _getAsXMLText(/*string*/ $xml)
    {
         if (!is_null($this->m_query))
            $xml .= "<mainURL>".$this->m_query->str()."</mainURL>";

        if (!is_null($this->m_id))
            $xml .= "<id>".$this->m_id."</id>";
        
        if (!is_null($this->m_parent))
        {
            $parent = "<parent>";
            $xml .= $this->m_parent->_getAsXMLText($parent)."</parent>";
        }
        
        return $xml;
    }
    
    /////////////////////////////////////////////////////////////////////////
    protected $m_parent = null;
    protected $m_query = null;
    protected $m_id = null;
}

/////////////////////////////////////////////////////////////////////////////
class CCurrentPage extends CPage
{
    /////////////////////////////////////////////////////////////////////////
    public function __construct(stdurl $query, stdtext $id, CPage $parent = null, /*bool*/ $isMain = false,
        stdparam $parameters = null, stdtext $modulId, SplFileInfo $dataFile = null, /*bool*/ $isAdmin)
    {
        parent::__construct($query, $id, $parent, $isMain);

        $isMain = (bool)$isMain;
        stderr::assert(is_null($parameters) || $parameters->isValid());
        stderr::assert(!is_null($modulId));
        $isAdmin = (bool)$isAdmin;
        
        $this->m_isMain = $isMain;
        $this->m_parameters = $parameters;
        $this->m_modulId = $modulId;
        $this->m_dataFile = $dataFile;
        $this->m_isAdmin = $isAdmin;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function isMain()
    {
        return $this->m_isMain;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function parameters()
    {
        return $this->m_parameters;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function modulId()
    {
        return $this->m_modulId;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function dataFile()
    {
        return $this->m_dataFile;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function isAdmin()
    {
        return $this->m_isAdmin;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getAsDOMXML()
    {
        $dom = parent::getAsDOMXML();
        if ($this->m_isMain)
        {
            $element = $dom->createElement("isMainURL");
            $dom->documentElement->appendChild($element);
            $element = $dom->createElement("currentHost", $_SERVER["HTTP_HOST"]);
            $dom->documentElement->appendChild($element);
        }
        
        return $dom;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private $m_isMain = false;
    private $m_parameters = null;
    private $m_modulId = null;
    private $m_dataFile = null;
    private $m_isAdmin = false;
}

/////////////////////////////////////////////////////////////////////////////
?>