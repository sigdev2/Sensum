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
class CCatalogMenu  implements IWidget
{
    /////////////////////////////////////////////////////////////////////////
    const XSLT_FILE_NAME = "tpl.xsl";
    const ADMIN_XSLT_FILE_NAME = "tpl.xsl";
    
    /////////////////////////////////////////////////////////////////////////
    // list type enum
    const CATEGORY = "category";
    const BREND = "brend";
    const DEFAULT_LIST_TYPE = self::CATEGORY;
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct()
    {
        
    }
    
    /////////////////////////////////////////////////////////////////////////
	public function init(CCurrentPage $page, /*bool*/ $isAdmin, stdparam $options = null)
	{
        $this->catalogURL = $options;
	    $this->isAdmin = $page->isAdmin();
        
        $parameters = "";
        if (!is_null($page->parameters()))
            $parameters = $page->parameters()->str();
        
        if ($parameters != "")
        {
            $arrParam = explode("_", $parameters);

            if($arrParam != false && sizeof($arrParam) == 2)
            {
                self::$currCBNO = $arrParam[0];
            }
            else if ($arrParam != false && sizeof($arrParam) == 3)
            {
                self::$currCBNO = $arrParam[0];
                self::$currGNO = $arrParam[1];
            }
            else if ($arrParam != false && sizeof($arrParam) == 4)
            {
                self::$currCBNO = $arrParam[0];
                self::$currGNO = $arrParam[1];
                self::$currPGNO = $arrParam[2];
            }
            else if ($arrParam != false && sizeof($arrParam) == 1)
            {
                $sqlProduct = "SELECT p.HIDDEN, p.NAME, p.PRICE, p.P_NO, p.ARTICUL, p.MAKER, p.COUNT, p.PROPERTY_XML, p.IS_HIT, p.IS_NEW, p.IS_REC, t.T_NO, t.TYPE_XSL, t.NAME AS T_NAME, cs.CS_NO, c.C_NO, b.B_NO, g.G_NO, pg.PG_NO, c.NAME AS C_NAME, b.NAME AS B_NAME, g.NAME AS G_NAME, pg.NAME AS PG_NAME, cs.NAME AS CS_NAME FROM PRODUCTS p LEFT OUTER JOIN COUNTSTATUS cs ON p.CS_NO = cs.CS_NO, TYPES t, CATEGORY c, BRANDS b, GROUPS g, PGROUPS pg WHERE".($this->isAdmin ? "" : " p.HIDDEN = 0 AND")." p.P_NO ='".$arrParam[0]."' AND p.PG_NO = pg.PG_NO AND pg.G_NO = g.G_NO AND (g.B_NO = b.B_NO OR g.C_NO = c.C_NO) AND t.T_NO = p.T_NO LIMIT 1;";
                
                $resProduct = dbwrapper::query(stdstr::toStr($sqlProduct));

                if (!$resProduct || $resProduct->rowCount() == 0)
                {
                    return false;
                }
                
                $product = $resProduct->fetch(PDO::FETCH_ASSOC);
                
                if ($this->listType == self::CATEGORY)
                {
                    self::$currCBNO = $product["C_NO"];
                }
                else
                {
                    self::$currCBNO = $product["B_NO"];
                }
                
                self::$currGNO = $product["G_NO"];
                self::$currPGNO = $product["PG_NO"];
            }
        }
	}
	
    /////////////////////////////////////////////////////////////////////////
	public function getAsDOMXML()
	{
	    $listType = $_POST["catalogType"]; /*CATEGORY or BREND*/
        if ($listType != self::CATEGORY && $listType != self::BREND)
        {
            if ($_SESSION["catalogType"])
            {
                $listType = $_SESSION["catalogType"];
            }
            else
            {
                $listType = self::DEFAULT_LIST_TYPE;
                $_SESSION["catalogType"] = self::DEFAULT_LIST_TYPE;
            }
        }
        else
        {
            $_SESSION["catalogType"] = $listType;
        }
        
        $xmlCatalogList = "<catalogList>\n<listType>".$listType."</listType>\n";
        
        $sqlCat = "SELECT NAME";

        $tag = "";
        if ($listType == self::CATEGORY)
        {
            $tag = self::CATEGORY;
            $number = "C_NO";
            $sqlCat .= ", ".$number;
            $sqlCat .= " FROM CATEGORY";
        }
        else
        {
            $tag = self::BREND;
            $number = "B_NO";
            $sqlCat .= ", ".$number;
            $sqlCat .= " FROM BRANDS";
        }
        
        $sqlCat .= " ORDER BY NAME ASC;";
        
        $resCat = dbwrapper::query(stdstr::toStr($sqlCat));

        if ($resCat && $resCat->rowCount() > 0)
        {
            while ($cbObject = $resCat->fetch(PDO::FETCH_ASSOC))
            {
                if (self::$currCBNO != false && $cbObject[$number] == self::$currCBNO)
                {
                    $sqlGroups = "SELECT NAME, G_NO FROM GROUPS WHERE ".($this->isAdmin ? "" : " HIDDEN = 0 AND");
                    
                    $sqlGroups .= "  ".$number." = ".self::$currCBNO;

                    $sqlGroups .= " ORDER BY G_NO ASC;";
                    
                    $resGroups = dbwrapper::query(stdstr::toStr($sqlGroups));
                    
                    if ($resGroups && $resGroups->rowCount() > 0)
                    {
                        $xmlCatalogList .= "<".$tag." name=\"".$cbObject["NAME"]."\" num=\"".$cbObject[$number]."\"  current=\"1\">\n";
                        
                        while ($group = $resGroups->fetch(PDO::FETCH_ASSOC))
                        {
                            if (self::$currGNO != false && $group["G_NO"] == self::$currGNO)
                            {
                                $sqlSubGroups = "SELECT NAME, PG_NO FROM PGROUPS WHERE".($this->isAdmin ? "" : " HIDDEN = 0 AND")." G_NO = ".self::$currGNO." ORDER BY PG_NO ASC;";
                                
                                $resSubGroups = dbwrapper::query(stdstr::toStr($sqlSubGroups));
                    
                                if ($resSubGroups && $resSubGroups->rowCount() > 0)
                                {
                                    $xmlCatalogList .= "<group name=\"".$group["NAME"]."\"  num=\"".$group["G_NO"]."\" current=\"1\">\n";
                                
                                    while ($subGroup = $resSubGroups->fetch(PDO::FETCH_ASSOC))
                                    {
                                        if (self::$currPGNO != false && $subGroup["PG_NO"] == self::$currPGNO)
                                        {
                                            $xmlCatalogList .= "<subgroup name=\"".$subGroup["NAME"]."\"  num=\"".$subGroup["PG_NO"]."\" current=\"1\"/>\n";
                                        }
                                        else
                                        {
                                            $xmlCatalogList .= "<subgroup name=\"".$subGroup["NAME"]."\"  num=\"".$subGroup["PG_NO"]."\"/>\n";
                                        }
                                    }
                                    
                                    $xmlCatalogList .= "</group>\n";
                                }
                                else
                                {
                                    $xmlCatalogList .= "<group name=\"".$group["NAME"]."\"  num=\"".$group["G_NO"]."\" current=\"1\"/>\n";
                                }
                            }
                            else
                            {
                                $xmlCatalogList .= "<group name=\"".$group["NAME"]."\"  num=\"".$group["G_NO"]."\"/>\n";
                            }
                        }
                        
                        $xmlCatalogList .= "</".$tag.">\n";
                    }
                    else
                    {
                        $xmlCatalogList .= "<".$tag." name=\"".$cbObject["NAME"]."\" num=\"".$cbObject[$number]."\" current=\"1\"/>\n";
                    }
                }
                else
                {
                    $xmlCatalogList .= "<".$tag." name=\"".$cbObject["NAME"]."\" num=\"".$cbObject[$number]."\" />\n";
                }
            }
        }
        
        $xmlCatalogList .= "<catalogURL>".$this->catalogURL."</catalogURL>";
        $xmlCatalogList .= "</catalogList>";
        
        $domCatalogList = stddomxml::create();
        $domCatalogList->loadXML($xmlCatalogList);
        
        return $domCatalogList;
	}
	
	/////////////////////////////////////////////////////////////////////////
	public function getXSLT()
	{
        if ($this->isAdmin)
	        return stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/".self::ADMIN_XSLT_FILE_NAME));
        
        return stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/".self::XSLT_FILE_NAME));
	}
    
    /////////////////////////////////////////////////////////////////////////
	public function generateXSLT()
	{
        return null;
    }
    
    /////////////////////////////////////////////////////////////////////////
    static private $currCBNO = false;  // current category or brand number in BD
    static private $currGNO = false;   // current group number in BD
    static private $currPGNO = false;  // current subgroup number in BD
    private $isAdmin = false;
    private $catalogURL = false;
}

/////////////////////////////////////////////////////////////////////////////
?>