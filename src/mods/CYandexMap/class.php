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
class CYandexMap implements IModul
{
    /////////////////////////////////////////////////////////////////////////
    const DESCRIPTION_TAG = "description";
    const KEYWORDS_TAG = "keywords";
    const STRINGLIST_TAG = "content";
    const JSON_MAP_DATA_TAG = "jsonMapData";
    const MAP_ID_TAG = "mapID";
    
    const XSLT_FILE_NAME = "tpl.xsl";
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct()
    {
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function init(CCurrentPage $page)
    {
        $dom = stddomxml::fromFile($page->dataFile());
        
        $content = $dom->getElementsByTagName(self::CONTENT_TAG_NAME);
        $this->m_content = stddomxml::create();
        $this->m_content->appendChild($this->m_content->importNode($content->item(0), true));
        
        $mapId = $dom->getElementsByTagName(self::MAP_ID_TAG);
        $jsonMapData = $this->_loadMap((int)$mapId->item(0)->nodeValue);
        
        if (!is_null($jsonMapData))
        {
            $jsonMap = $this->m_content->createElement(self::JSON_MAP_DATA_TAG);
            $this->m_content->documentElement->appendChild($jsonMap);
            $jsonMap->appendChild($this->m_content->importNode($jsonMapData->documentElement, true));
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
        return stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/".self::XSLT_FILE_NAME));
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _loadMap(/*int*/ $id) /*DOMDocument | null*/
    {
        $id = (int)$id;
        
        $centerPoint = dbwrapper::query(stdstr::toStr("SELECT * FROM point WHERE id = (SELECT centerPointID from map where id = ".$id.");"));
        
        if (!$centerPoint || $centerPoint->rowCount() <= 0)
            return null;
        
        $str = "<script type=\"text/javascript\">\n";
        
        $centerPoint = $centerPoint->fetchAll(PDO::FETCH_ASSOC);
        $centerPoints["id"] = (int)$centerPoint[0]["id"];
        $centerPoints["x"] = (float)$centerPoint[0]["x"];
        $centerPoints["y"] = (float)$centerPoint[0]["y"];
        $code = "var centerPoint = ".json_encode($centerPoints).";\n";
        
        $plResults = dbwrapper::query(stdstr::toStr("SELECT placemark.id, placemark.name, placemark.text, point.x, point.y
                        FROM placemark
                        INNER JOIN point ON placemark.pointID = point.id
                        WHERE placemark.mapID = ".$id.";"));
        
        $code .= "var pointCount = ".$plResults->rowCount().";\n";
        if ($plResults && $plResults->rowCount() > 0)
        {
            $placemarks = $plResults->fetchAll(PDO::FETCH_ASSOC);
            $code .= "var placemarks = ".$this->_toJSON(new ArrayObject($placemarks)).";\n";
        }
        
        $str .= htmlspecialchars($code , ENT_NOQUOTES, "UTF-8")."</script>\n";
        
        $dom = stddomxml::create();
        $scriptTag = $dom->createElement("script", $code);
        $dom->appendChild($scriptTag);
        
        return $dom;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _toJSON(ArrayObject $arr) // string ; todo : string -> stdstr
    {
        $str = null;
        foreach($arr as $key => $item)
        {
            if (is_null($str))
                $str = "{";
            else
                $str .= ",";
            
            if ($item instanceof ArrayObject)
            {
                $item = $this->_toJSON($item);
                // todo
                $str .= $key.":".$item;
            }
            else if (is_array($item))
            {
                $item = $this->_toJSON(new ArrayObject($item));
                // todo
                $str .= $key.":".$item;
            }
            else
            {
                //todo: float must be has . 
                //if (is_numeric($item))
                    //$item = (float)$item;
                // todo spec
                $str .= $key.":".(is_string($item)?"\"":"").$item.(is_string($item)?"\"":"");
            }
        }
        
        return $str."}";        
    }

    /////////////////////////////////////////////////////////////////////////
    private $m_content = null;
    private $m_isAdmin = false;
}

/////////////////////////////////////////////////////////////////////////////
?>