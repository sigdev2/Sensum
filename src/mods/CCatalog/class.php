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
require_once dirname(__FILE__)."/../../src/excellib/reader.php";

/////////////////////////////////////////////////////////////////////////////
class CCatalog implements IModul
{
    /////////////////////////////////////////////////////////////////////////
    const PARAM_P_NO = "P_NO";
    const PARAM_NAME = "NAME";
    const PARAM_PRICE = "PRICE";
    const PARAM_ARTICUL = "ARTICUL";
    const PARAM_MAKER = "MAKER";
    const PARAM_COUNT = "COUNT";
    const PARAM_HIDDEN = "HIDDEN";
    const PARAM_IS_HIT = "IS_HIT";
    const PARAM_IS_NEW = "IS_NEW";
    const PARAM_IS_REC = "IS_REC";
    const PARAM_PROPERTY_XML = "PROPERTY_XML";
    const PARAM_T_NO = "T_NO";
    const PARAM_CS_NO = "CS_NO";
    const PARAM_IMG = "IMG";

    const PARAM_PG_NO = "PG_NO";
    
    const PARAM_G_NO = "G_NO";
    
    const PARAM_C_NO = "C_NO";
    const PARAM_B_NO = "B_NO";
    
    const PARAM_C_NAME = "C_NAME";
    const PARAM_B_NAME = "B_NAME";
    
    const PARAM_G_NAME = "G_NAME";
    const PARAM_PG_NAME = "PG_NAME";

    const PARAM_EXCEL_FILE = "xlsfile";
    const PARAM_EXCEL_PAGE = "page";
    
    const PARAM_ACT = "act";
    
    const ACT_ADD = "add";
    const ACT_REMOVE = "del";
    const ACT_UPDATE = "upd";
    const ACT_NEW_GROUP = "addGroup";
    const ACT_NEW_SUB_GROUP = "addSubGroup";
    const ACT_NEW_CATEGORY = "addCategory";
    const ACT_NEW_BRAND = "addBrand";
    const ACT_REM_GROUP = "delGroup";
    const ACT_REM_SUB_GROUP = "delSubGroup";
    const ACT_REM_CATEGORY = "delCategory";
    const ACT_REM_BRAND = "delBrand";
    
    const ACT_EXCEL = "excel";
    
    const TAG_TYPES = "types";
    const TAG_CSS = "countStatuss";
    const TAG_PGS = "subGroups";
    
    const TAG_TYPE = "type";
    const TAG_CS = "countStatus";
    const TAG_PG = "subGroup";
    
    const TAG_GROUPS = "groups";
    const TAG_CATEGS = "categories";
    const TAG_BRANDS = "brands";
    
    const TAG_GROUP = "group";
    const TAG_CATEG = "category";
    const TAG_BRAND = "brand";
    
    /////////////////////////////////////////////////////////////////////////
    const XSLT_FILE_NAME = "tpl.xsl";
    const ADMIN_XSLT_FILE_NAME = "admintpl.xsl";
    const DEFAULT_TYPE_XSLT = "default.xsl";
    const TYPE_XSLT_ADMIN_PREFIX = "admin_";
    const DEFAULT_UPLOAD_DIR = "/";
    
    /////////////////////////////////////////////////////////////////////////
    // list type enum
    const CATEGORY = "category";
    const BREND = "brend";
    const DEFAULT_LIST_TYPE = self::CATEGORY;
    
    /////////////////////////////////////////////////////////////////////////
    // sort enum
    const BY_NAME = "by name";
    const BY_PRICE = "by price";
    const BY_COUNT = "by count";
    const BY_ARTICUL = "by articul";
    const BY_MAKER = "by maker";
    const DEFAULT_SORT_BY = self::BY_PRICE;
    //
    const ASC  = "ASC";
    const DESC = "DESC";
    const DEFAULT_SORT_TYPE = self::ASC;
    
    /////////////////////////////////////////////////////////////////////////
    // page size enum
    const SIZE4 = 4;
    const SIZE6 = 6;
    const SIZE8 = 8;
    const SIZE10 = 10;
    const DEFAULT_PAGE_SIZE = self::SIZE6;
    
    /////////////////////////////////////////////////////////////////////////
    const DEFAULT_CHARSET = "UTF-8";
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct()
    {
        $this->content = stddomxml::create();
        $this->content->loadXML("<content />");
        
        if ($_SESSION["catalogType"])
        {
            $this->listType = $_SESSION["catalogType"];
        }
        else
        {
            $this->listType = self::DEFAULT_LIST_TYPE;
            $_SESSION["catalogType"] = self::DEFAULT_LIST_TYPE;
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function init(CCurrentPage $page)
    {   
        $this->isAdmin = $page->isAdmin();
        $this->m_pageDir = $page->dataFile()->getPath()."/";
        $message = null;
        
        if ($this->isAdmin && (bool)$_POST[self::PARAM_ACT])
        {
            switch ((string)$_POST[self::PARAM_ACT])
            {
                case self::ACT_ADD:
                {
                    
                    $this->_createProduct($_POST[self::PARAM_NAME], $_POST[self::PARAM_PRICE],
                                   $_POST[self::PARAM_ARTICUL], $_POST[self::PARAM_MAKER],
                                   $_POST[self::PARAM_COUNT], $_POST[self::PARAM_HIDDEN],
                                   $_POST[self::PARAM_IS_HIT], $_POST[self::PARAM_IS_NEW],
                                   $_POST[self::PARAM_IS_REC], $_POST[self::PARAM_PROPERTY_XML],
                                   $_POST[self::PARAM_T_NO], $_POST[self::PARAM_PG_NO],
                                   $_POST[self::PARAM_CS_NO], $_FILES[self::PARAM_IMG]);
                    break;
                }
                
                case self::ACT_UPDATE:
                {
                    $this->_updateProduct($_POST[self::PARAM_P_NO], $_POST[self::PARAM_NAME], $_POST[self::PARAM_PRICE],
                                   $_POST[self::PARAM_ARTICUL], $_POST[self::PARAM_MAKER],
                                   $_POST[self::PARAM_COUNT], $_POST[self::PARAM_HIDDEN],
                                   $_POST[self::PARAM_IS_HIT], $_POST[self::PARAM_IS_NEW],
                                   $_POST[self::PARAM_IS_REC], $_POST[self::PARAM_PROPERTY_XML],
                                   $_POST[self::PARAM_T_NO], $_POST[self::PARAM_PG_NO],
                                   $_POST[self::PARAM_CS_NO], $_FILES[self::PARAM_IMG]);
                    break;
                }
                
                case self::ACT_REMOVE:
                {
                    $this->_removeProduct($_POST[self::PARAM_P_NO]);
                    break;
                }
                
                case self::ACT_NEW_GROUP:
                {
                    $HIDDEN = !is_null($_POST[self::PARAM_HIDDEN]) && (int)$_POST[self::PARAM_HIDDEN] >= 1 ? 1 : 0;
                    $NAME = stdtext::toText($_POST[self::PARAM_G_NAME])->str();
                    
                    $C_NO = !is_null($_POST[self::PARAM_C_NO]) && $this->_isRealCategory($_POST[self::PARAM_C_NO]) ? (int)$_POST[self::PARAM_C_NO] : "NULL";
                    $B_NO = !is_null($_POST[self::PARAM_B_NO]) && $this->_isRealBrand($_POST[self::PARAM_B_NO]) ? (int)$_POST[self::PARAM_B_NO] : "NULL";
                    
                    if ($NAME != "" && ($C_NO != "NULL" || $B_NO != "NULL"))
                        dbwrapper::query(stdstr::toStr("INSERT INTO GROUPS VALUES (0, '".$NAME."', ".$HIDDEN.", ".$B_NO.", ".$C_NO.");"));
                    
                    break;
                }
                
                case self::ACT_NEW_SUB_GROUP:
                {
                    $HIDDEN = !is_null($_POST[self::PARAM_HIDDEN]) && (int)$_POST[self::PARAM_HIDDEN] >= 1 ? 1 : 0;
                    $NAME = stdtext::toText($_POST[self::PARAM_PG_NAME])->str();
                    $G_NO = (int)$_POST[self::PARAM_G_NO];
                    
                    if ($this->_isRealGroup($G_NO) && $NAME != "")
                        dbwrapper::query(stdstr::toStr("INSERT INTO PGROUPS VALUES (0, '".$NAME."', ".$HIDDEN.", ".$G_NO.");"));
                    
                    break;
                }
                
                case self::ACT_NEW_CATEGORY:
                {
                    if ($_POST[self::PARAM_C_NAME])
                        dbwrapper::query(stdstr::toStr("INSERT INTO CATEGORY VALUES (0, '".stdtext::toText($_POST[self::PARAM_C_NAME])->str()."');"));
                    break;
                }
                
                case self::ACT_NEW_BRAND:
                {
                    if ($_POST[self::PARAM_B_NAME])
                        dbwrapper::query(stdstr::toStr("INSERT INTO BRANDS VALUES (0, '".stdtext::toText($_POST[self::PARAM_B_NAME])->str()."');"));
                    break;
                }
                
                case self::ACT_REM_GROUP:
                {
                    $G_NO = (int) $_POST[self::PARAM_G_NO];
                    
                    if ($this->_isRealGroup($G_NO))
                        dbwrapper::query(stdstr::toStr("DELETE FROM GROUPS WHERE G_NO = ".(int)$G_NO." LIMIT 1;"));
                    
                    break;
                }
                
                case self::ACT_REM_SUB_GROUP:
                {
                    $PG_NO = (int) $_POST[self::PARAM_PG_NO];
                    
                    if ($this->_isRealSubGroup($PG_NO))
                        dbwrapper::query(stdstr::toStr("DELETE FROM PGROUPS WHERE PG_NO = ".(int)$PG_NO." LIMIT 1;"));
                    
                    break;
                }
                
                case self::ACT_REM_CATEGORY:
                {
                    $C_NO = (int) $_POST[self::PARAM_C_NO];
                    
                    if ($this->_isRealCategory($C_NO))
                        dbwrapper::query(stdstr::toStr("DELETE FROM CATEGORY WHERE C_NO = ".(int)$C_NO." LIMIT 1;"));
                    
                    break;
                }
                
                case self::ACT_REM_BRAND:
                {
                    $B_NO = (int) $_POST[self::PARAM_B_NO];
                    
                    if ($this->_isRealBrand($B_NO))
                        dbwrapper::query(stdstr::toStr("DELETE FROM BRANDS WHERE B_NO = ".(int)$B_NO." LIMIT 1;"));
                    
                    break;
                }
                
                case self::ACT_EXCEL:
                {
                    $PG_NO = (int) $_POST[self::PARAM_PG_NO];
                    
                    if ($this->_isRealSubGroup($PG_NO))
                        $this->_readExcel((int)$_POST[self::PARAM_EXCEL_PAGE],  $_FILES[self::PARAM_EXCEL_FILE]["tmp_name"], $PG_NO);
                    
                    break;
                }
            }
        }
        
        $parameters = "";
        if (!is_null($page->parameters()))
            $parameters = $page->parameters()->str();
        
        if ($parameters == "")
        {
            $this->_insertProductsListToContentXML(0);
            
            $this->_insertCurrPosInCatalog();
        }
        else
        {
            $arrParam = explode("_", $parameters);

            if($arrParam != false && sizeof($arrParam) == 2)
            {
                self::$currCBNO = $arrParam[0];
                $this->_insertProductsListToContentXML($arrParam[1]);
                
                $this->_insertCurrPosInCatalog();
            }
            else if ($arrParam != false && sizeof($arrParam) == 3)
            {
                self::$currCBNO = $arrParam[0];
                self::$currGNO = $arrParam[1];
                
                $this->_insertProductsListToContentXML($arrParam[2]);
                
                $this->_insertCurrPosInCatalog();
            }
            else if ($arrParam != false && sizeof($arrParam) == 4)
            {
                self::$currCBNO = $arrParam[0];
                self::$currGNO = $arrParam[1];
                self::$currPGNO = $arrParam[2];
                
                $this->_insertProductsListToContentXML($arrParam[3]);
                
                $this->_insertCurrPosInCatalog();
            }
            else if ($arrParam != false && sizeof($arrParam) == 1)
            {
                $sqlProduct = $this->_selectFromProduct($this->isAdmin, (int)$arrParam[0]);

                $this->_selectFromProduct($this->isAdmin, "p.P_NO =".(int)$arrParam[0]);
                
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
                
                $this->_insertProductDataToContentXML($product);
                
                if ((bool)$product["TYPE_XSL"])
                    $this->productView = $product["TYPE_XSL"];
            }
        }
        
        if ($this->isAdmin)
        {
            $types = $this->content->createElement(self::TAG_TYPES);
            $this->content->documentElement->appendChild($types);
            $res = dbwrapper::query(stdstr::toStr("SELECT T_NO, NAME FROM TYPES;"));
            
            if ((bool)$res && $res->rowCount() > 0)
            {
                while ($type = $res->fetch(PDO::FETCH_ASSOC))
                {
                    $tag = $this->content->createElement(self::TAG_TYPE, $type["NAME"]);
                    $tag->setAttribute("T_NO", $type["T_NO"]);
                    $types->appendChild($tag);
                }
            }            
            
            $pGroup = $this->content->createElement(self::TAG_PGS);
            $this->content->documentElement->appendChild($pGroup);
            $res = dbwrapper::query(stdstr::toStr("SELECT PG_NO, NAME FROM PGROUPS;"));
    
            if ((bool)$res && $res->rowCount() > 0)
            {
                while ($subgr = $res->fetch(PDO::FETCH_ASSOC))
                {
                    $tag = $this->content->createElement(self::TAG_PG, $subgr["NAME"]);
                    $tag->setAttribute("PG_NO", $subgr["PG_NO"]);
                    $pGroup->appendChild($tag);
                }
            }
            
            $pCS = $this->content->createElement(self::TAG_CSS);
            $this->content->documentElement->appendChild($pCS);
            $res = dbwrapper::query(stdstr::toStr("SELECT CS_NO, NAME FROM COUNTSTATUS;"));
    
            if ((bool)$res && $res->rowCount() > 0)
            {
                while ($cstatus = $res->fetch(PDO::FETCH_ASSOC))
                {
                    $tag = $this->content->createElement(self::TAG_CS, $cstatus["NAME"]);
                    $tag->setAttribute("CS_NO", $cstatus["CS_NO"]);
                    $pGroup->appendChild($tag);
                }
            }
            
            $groups = $this->content->createElement(self::TAG_GROUPS);
            $this->content->documentElement->appendChild($groups);
            $res = dbwrapper::query(stdstr::toStr("SELECT G_NO, NAME FROM GROUPS;"));
    
            if ((bool)$res && $res->rowCount() > 0)
            {
                while ($group = $res->fetch(PDO::FETCH_ASSOC))
                {
                    $tag = $this->content->createElement(self::TAG_GROUP, $group["NAME"]);
                    $tag->setAttribute("G_NO", $group["G_NO"]);
                    $groups->appendChild($tag);
                }
            }
            
            $categories = $this->content->createElement(self::TAG_CATEGS);
            $this->content->documentElement->appendChild($categories);
            $res = dbwrapper::query(stdstr::toStr("SELECT C_NO, NAME FROM CATEGORY;"));
    
            if ((bool)$res && $res->rowCount() > 0)
            {
                while ($category = $res->fetch(PDO::FETCH_ASSOC))
                {
                    $tag = $this->content->createElement(self::TAG_CATEG, $category["NAME"]);
                    $tag->setAttribute("C_NO", $category["C_NO"]);
                    $categories->appendChild($tag);
                }
            }
            
            $brands = $this->content->createElement(self::TAG_BRANDS);
            $this->content->documentElement->appendChild($brands);
            $res = dbwrapper::query(stdstr::toStr("SELECT B_NO, NAME FROM BRANDS;"));
    
            if ((bool)$res && $res->rowCount() > 0)
            {
                while ($brand = $res->fetch(PDO::FETCH_ASSOC))
                {
                    $tag = $this->content->createElement(self::TAG_BRAND, $brand["NAME"]);
                    $tag->setAttribute("B_NO", $brand["B_NO"]);
                    $brands->appendChild($tag);
                }
            }
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getAsDOMXML()
	{
	    return $this->content;
	}

    /////////////////////////////////////////////////////////////////////////
    public function getXSLT()
    {
        if ((bool)$this->productView)
        {
            if ($this->isAdmin)
            {
                $dom = stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/types/".self::TYPE_XSLT_ADMIN_PREFIX.$this->productView));
                if (!$dom)
                    $dom = stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/types/admin.xsl"));
            }
            else
            {
                $dom = stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/types/".$this->productView));
                
                if (!$dom)
                    $dom = stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/types/".self::DEFAULT_TYPE_XSLT));
            }
            
            return $dom;
        }

        if ($this->isAdmin)
            return stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/".self::ADMIN_XSLT_FILE_NAME));
        else
            return stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/".self::XSLT_FILE_NAME));
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _getSortBy()
    {
        $sortBy = htmlspecialchars($_POST["catalogSortBy"]);
        if ($sortBy != self::BY_NAME &&
            $sortBy != self::BY_PRICE &&
            $sortBy != self::BY_COUNT &&
            $sortBy != self::BY_ARTICUL &&
            $sortBy != self::BY_MAKER)
        {
            if ($_SESSION["catalogSortBy"])
            {
                $sortBy = $_SESSION["catalogSortBy"];
            }
            else
            {
                $sortBy = self::DEFAULT_SORT_BY;
                $_SESSION["catalogSortBy"] = self::DEFAULT_SORT_BY;
            }
        }
        else
        {
            $_SESSION["catalogSortBy"] = $sortBy;
        }
        
        return $sortBy;
    }
        
    /////////////////////////////////////////////////////////////////////////
    private function _getSortByColumnName($sortBy)
    {
        switch($sortBy)
        {
            case self::BY_NAME:
                return "NAME";
            case self::BY_PRICE:
                return "PRICE";
            case self::BY_COUNT:
                return "COUNT";
            case self::BY_ARTICUL:
                return "ARTICUL";
            case self::BY_MAKER:
                return "MAKER";
        }
        
        return self::DEFAULT_SORT_BY;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _getSortType()
    {
        $sortType = htmlspecialchars($_POST["catalogSortType"]);
        if ($sortType != self::ASC &&
            $sortType != self::DESC)
        {
            if ($_SESSION["catalogSortType"])
            {
                $sortType = $_SESSION["catalogSortType"];
            }
            else
            {
                $sortType = self::DEFAULT_SORT_TYPE;
                $_SESSION["catalogSortType"] = self::DEFAULT_SORT_TYPE;
            }
        }
        else
        {
            $_SESSION["catalogSortType"] = $sortType;
        }
        
        return $sortType;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _getPageSize()
    {
        $pageSize = (int)($_POST["catalogPageSize"]);
        if ($pageSize != self::SIZE4 &&
            $pageSize != self::SIZE6 &&
            $pageSize != self::SIZE8 &&
            $pageSize != self::SIZE10)
        {
            if ($_SESSION["catalogPageSize"])
            {
                $pageSize = $_SESSION["catalogPageSize"];
            }
            else
            {
                $pageSize = self::DEFAULT_PAGE_SIZE;
                $_SESSION["catalogPageSize"] = self::DEFAULT_PAGE_SIZE;
            }
        }
        else
        {
            $_SESSION["catalogPageSize"] = $pageSize;
        }
        
        return $pageSize;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _insertProductsListToContentXML($page)
    {
        $pageSize = $this->_getPageSize();
        
        $sqlPageCount = "SELECT COUNT(NAME)/".$pageSize." AS PAGE_COUNT FROM PRODUCTS".($this->isAdmin ? "" : " WHERE HIDDEN = 0");

        $sqlProducts = "SELECT * FROM PRODUCTS".($this->isAdmin ? "" : " WHERE HIDDEN = 0");
        
        $sqlAappendWhere = "";
        if (self::$currCBNO)
        {
            $cbno = "";
            $gno = "";
            $pgno = "";
            
            if ($this->listType == self::CATEGORY)
                $cbno = " AND g.C_NO = (SELECT C_NO FROM CATEGORY WHERE C_NO = ".(int)self::$currCBNO.")";
            else
                $cbno = " AND g.B_NO = (SELECT B_NO FROM BRANDS WHERE B_NO = ".(int)self::$currCBNO.")";
            
            if (self::$currGNO)
            {
                $gno = " AND g.G_NO = ".(int)self::$currGNO;
        
                if (self::$currPGNO)
                {
                    $pgno = " AND pg.PG_NO = ".(int)self::$currPGNO;
                }
            }
            
            $sqlAappendWhere = ($this->isAdmin ? " WHERE " : " AND ")."PG_NO = (SELECT PG_NO FROM PGROUPS pg, GROUPS g WHERE pg.G_NO = g.G_NO".$pgno.$gno.$cbno.")";
        }
        
        $sortBy = $this->_getSortBy();
        $sortType = $this->_getSortType();
        $sqlProducts .= $sqlAappendWhere." ORDER BY ".$this->_getSortByColumnName($sortBy)." ".$sortType;
        $sqlPageCount .= $sqlAappendWhere.";";
        
        $res = dbwrapper::query(stdstr::toStr($sqlPageCount));

        if (!$res || $res->rowCount() == 0)
        {
            $pageCount = $pageSize;
        }
        else
        {
            $pageCount = $res->fetch(PDO::FETCH_ASSOC);
            $pageCount = $pageCount["PAGE_COUNT"];
        }
        
        if ($pageCount < $page)
        {
            $page = 0;
        }
        
        $elemPageCount = $this->content->createElement("pageCount", (int)$pageCount);
        $elemPageSize = $this->content->createElement("pageSize", $pageSize);
        $elemSortBy = $this->content->createElement("sortBy", $sortBy);
        $elemSortType = $this->content->createElement("sortType", $sortType);
        $elemPage = $this->content->createElement("page", $page);
        
        $this->content->documentElement->appendChild($elemPageCount);
        $this->content->documentElement->appendChild($elemPageSize);
        $this->content->documentElement->appendChild($elemSortBy);
        $this->content->documentElement->appendChild($elemSortType);
        $this->content->documentElement->appendChild($elemPage);
        
        $posStart = $page * $pageSize;
 
        $sqlProducts .= " LIMIT ".$posStart.", ".$pageSize.";";
        
        $res = dbwrapper::query(stdstr::toStr($sqlProducts));

        if (!$res || $res->rowCount() == 0)
        {
            return false;
        }
        
        while ($product = $res->fetch(PDO::FETCH_ASSOC))
            $this->_insertProductDataToContentXML($product);
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _insertProductDataToContentXML($product /*PDO::FETCH_ASSOC*/)
    {
        $pNode = $this->content->createElement("product");
        $this->content->documentElement->appendChild($pNode);
        foreach ($product as $key => $value)
        {
            if ($key == "PROPERTY_XML")
            {
                $domXML = stddomxml::create();
                if ($value)
                {
                    if (@$domXML->loadXML($value));
                    {
                        $property = $this->content->createElement($key);
                        $property->appendChild($this->content->importNode($domXML->documentElement, true));
                        $pNode->appendChild($property);
                    }
                }
            }
            else
            {
                $element = $this->content->createElement($key, $value);
                $pNode->appendChild($element);
            }
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _insertCurrPosInCatalog()
    {
        if (self::$currCBNO)
        {
            $cbNum = $this->content->createElement("cbNum", self::$currCBNO);
            $this->content->documentElement->appendChild($cbNum);
        }
        if (self::$currGNO)
        {
            $gNum = $this->content->createElement("gNum", self::$currGNO);
            $this->content->documentElement->appendChild($gNum);
        }
        if (self::$currPGNO)
        {
            $pgNum = $this->content->createElement("pgNum", self::$currPGNO);
            $this->content->documentElement->appendChild($pgNum);
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _isRealProduct(/*int*/$P_NO)
    {
        $res = dbwrapper::query(stdstr::toStr("SELECT P_NO FROM PRODUCTS WHERE P_NO = ".(int)$P_NO.";"));
        if (!$res || $res->rowCount() == 0)
            return false;
        
        return true;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _isRealBrand(/*int*/$B_NO)
    {
        $res = dbwrapper::query(stdstr::toStr("SELECT B_NO FROM BRANDS WHERE B_NO = ".(int)$B_NO.";"));
        if (!$res || $res->rowCount() == 0)
            return false;
        
        return true;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _isRealCategory(/*int*/$C_NO)
    {
        $res = dbwrapper::query(stdstr::toStr("SELECT C_NO FROM CATEGORY WHERE C_NO = ".(int)$C_NO.";"));
        if (!$res || $res->rowCount() == 0)
            return false;
        
        return true;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _isRealGroup(/*int*/$G_NO)
    {
        $res = dbwrapper::query(stdstr::toStr("SELECT G_NO FROM GROUPS WHERE G_NO = ".(int)$G_NO.";"));
        if (!$res || $res->rowCount() == 0)
            return false;
        
        return true;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _isRealSubGroup(/*int*/$PG_NO)
    {
        $res = dbwrapper::query(stdstr::toStr("SELECT PG_NO FROM PGROUPS WHERE PG_NO = ".(int)$PG_NO.";"));
        if (!$res || $res->rowCount() == 0)
            return false;
        
        return true;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _updateProduct($P_NO, $NAME, $PRICE, $ARTICUL, $MAKER,
        $COUNT, $HIDDEN, $IS_HIT, $IS_NEW, $IS_REC, $PROPERTY_XML, $T_NO, $PG_NO,
        $CS_NO, $IMG)
    {
        if (!$this->_isRealProduct($P_NO))
            return false;
 
        $NAME = (string) $NAME;
        $PRICE = (int)$PRICE;
        $ARTICUL = (string) $ARTICUL;
        $MAKER = (string) $MAKER;
        $COUNT = (int) $COUNT;
        
        if ($HIDDEN)
            $HIDDEN = !is_null($HIDDEN) && (int)$HIDDEN >= 1 ? 1 : 0;
        if ($IS_HIT)
            $IS_HIT = !is_null($IS_HIT) && (int)$IS_HIT >= 1 ? 1 : 0;
        if ($IS_NEW)
            $IS_NEW = !is_null($IS_NEW) && (int)$IS_NEW >= 1 ? 1 : 0;
        if ($IS_REC)
            $IS_REC = !is_null($IS_REC) && (int)$IS_REC >= 1 ? 1 : 0;
        
        // todo: PROPERTY_XML attributes and safe text (sql-inections)
        $domXml = stddomxml::create();
        @$bIsXML = $domXml->loadXML(stdstr::toStr(stripslashes(trim((string)$PROPERTY_XML)))->str());
        
        if ($PROPERTY_XML)
        {
            if ($bIsXML)
            {
                $PROPERTY_XML = "'".$domXml->saveXML()."'";
            }
            else
                $PROPERTY_XML = null;
        }
        else
        {
            $PROPERTY_XML = "NULL";
        }
        
        if ($T_NO)
            $T_NO = (int) $T_NO;
        
        if ($PG_NO)
            $PG_NO = (int) $PG_NO;
        
        if ($CS_NO)
            $CS_NO = !is_null($CS_NO) && (int)$CS_NO >= 1 ? (int)$CS_NO : "NULL";
        else
            $CS_NO = "NULL";
        
        $res = dbwrapper::query(stdstr::toStr("SELECT * FROM PRODUCTS WHERE P_NO = ".(int)$P_NO.";"));
        if (!$res || $res->rowCount() == 0)
            return false;
        
        $res = $res->fetch(PDO::FETCH_ASSOC);
        
        if ($NAME != "" && $NAME != $res["NAME"])
            dbwrapper::exec(stdstr::toStr("UPDATE PRODUCTS SET NAME = '".$NAME."' WHERE P_NO = ".$P_NO.";"));
        if ($PRICE != $res["PRICE"])
            dbwrapper::exec(stdstr::toStr("UPDATE PRODUCTS SET PRICE = ".$PRICE." WHERE P_NO = ".$P_NO.";"));
        if ($ARTICUL != "" && $ARTICUL != $res["ARTICUL"])
            dbwrapper::exec(stdstr::toStr("UPDATE PRODUCTS SET ARTICUL = '".$ARTICUL."' WHERE P_NO = ".$P_NO.";"));
        if ($MAKER != "" && $MAKER != $res["MAKER"])
            dbwrapper::exec(stdstr::toStr("UPDATE PRODUCTS SET MAKER = '".$MAKER."' WHERE P_NO = ".$P_NO.";"));
        if ($COUNT != $res["COUNT"])
            dbwrapper::exec(stdstr::toStr("UPDATE PRODUCTS SET COUNT = ".$COUNT." WHERE P_NO = ".$P_NO.";"));
        if (!is_null($HIDDEN) && $HIDDEN != $res["HIDDEN"])
            dbwrapper::exec(stdstr::toStr("UPDATE PRODUCTS SET HIDDEN = ".$HIDDEN." WHERE P_NO = ".$P_NO.";"));
        if (!is_null($IS_HIT) && $IS_HIT != $res["IS_HIT"])
            dbwrapper::exec(stdstr::toStr("UPDATE PRODUCTS SET IS_HIT = ".$IS_HIT." WHERE P_NO = ".$P_NO.";"));
        if (!is_null($IS_NEW) && $IS_NEW != $res["IS_NEW"])
            dbwrapper::exec(stdstr::toStr("UPDATE PRODUCTS SET IS_NEW = ".$IS_NEW." WHERE P_NO = ".$P_NO.";"));
        if (!is_null($IS_REC) && $IS_REC != $res["IS_REC"])
            dbwrapper::exec(stdstr::toStr("UPDATE PRODUCTS SET IS_REC = ".$IS_REC." WHERE P_NO = ".$P_NO.";"));
        if (is_array($IMG) && isset($IMG["name"]) && isset($IMG["tmp_name"]) && $IMG["tmp_name"] != "" && $IMG["name"] != "")
        {
            if (is_file($res["IMG"]))
                unlink($res["IMG"]);
                
            if (is_file($this->m_pageDir.$IMG["name"]))
                unlink($this->m_pageDir.$IMG["name"]);
            
            if (move_uploaded_file($IMG["tmp_name"], $this->m_pageDir.$IMG["name"]))
                dbwrapper::exec(stdstr::toStr("UPDATE PRODUCTS SET IMG = '".$this->m_pageDir.$IMG["name"]."' WHERE P_NO = ".$P_NO.";"));
        }
        
        if (!is_null($PROPERTY_XML) && $PROPERTY_XML != $res["PROPERTY_XML"])
            dbwrapper::exec(stdstr::toStr("UPDATE PRODUCTS SET PROPERTY_XML = ".$PROPERTY_XML." WHERE P_NO = ".$P_NO.";"));
        if (!is_null($T_NO) && $T_NO != $res["T_NO"])
            dbwrapper::exec(stdstr::toStr("UPDATE PRODUCTS SET T_NO = ".$T_NO." WHERE P_NO = ".$P_NO.";"));
        if (!is_null($PG_NO) && $PG_NO != $res["PG_NO"])
            dbwrapper::exec(stdstr::toStr("UPDATE PRODUCTS SET PG_NO = ".$PG_NO." WHERE P_NO = ".$P_NO.";"));
        if ($CS_NO != $res["CS_NO"])
            dbwrapper::exec(stdstr::toStr("UPDATE PRODUCTS SET CS_NO = ".$CS_NO." WHERE P_NO = ".$P_NO.";"));
        
        return true;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _createProduct($NAME, $PRICE, $ARTICUL, $MAKER,
        $COUNT, $HIDDEN, $IS_HIT, $IS_NEW, $IS_REC, $PROPERTY_XML, $T_NO, $PG_NO, $CS_NO)
    {
        $NAME = (string) $NAME;
        $PRICE = (int)$PRICE;
        $ARTICUL = (string) $ARTICUL;
        $MAKER = (string) $MAKER;
        $COUNT = (int) $COUNT;
        
        $HIDDEN = !is_null($HIDDEN) && (int)$HIDDEN >= 1 ? 1 : 0;
        $IS_HIT = !is_null($IS_HIT) && (int)$IS_HIT >= 1 ? 1 : 0;
        $IS_NEW = !is_null($IS_NEW) && (int)$IS_NEW >= 1 ? 1 : 0;
        $IS_REC = !is_null($IS_REC) && (int)$IS_REC >= 1 ? 1 : 0;
        $domXml = stddomxml::create();
        @$bIsXML = $domXml->loadXML((string)$PROPERTY_XML);
        if (!$bIsXML)
            @$bIsXML = $domXml->loadXML("<root>".(string)$PROPERTY_XML."</root>");
        
        if ($bIsXML)
            $PROPERTY_XML = "'".$domXml->saveXML()."'";
        else
            $PROPERTY_XML = "NULL";
        
        $T_NO = (int) $T_NO;
        $PG_NO = (int) $PG_NO;
            
        $CS_NO = !is_null($CS_NO) && (int)$CS_NO >= 1 ? (int)$CS_NO : "NULL";
        
        if (is_array($IMG) && isset($IMG["name"]) && isset($IMG["tmp_name"]) && $IMG["tmp_name"] != "" && $IMG["name"] != "")
        {
            
            if (is_file($this->m_pageDir.$IMG["name"]))
                unlink($this->m_pageDir.$IMG["name"]);
            
            if (move_uploaded_file($IMG["tmp_name"], $this->m_pageDir.$IMG["name"]))
                $IMG = "'".$this->m_pageDir.$IMG["name"]."'";
            else
                $IMG = "NULL";
        }
        else
        {
            $IMG = "NULL";
        }
        
        
        dbwrapper::query(stdstr::toStr("INSERT INTO PRODUCTS VALUES (0, '".$NAME."', ".$PRICE.", '".$ARTICUL."', '".$MAKER."', ".$COUNT.", ".$HIDDEN.", ".$IS_HIT.", ".$IS_NEW.", ".$IS_REC.", ".$IMG.", ".$PROPERTY_XML.", ".$T_NO.", ".$PG_NO.", ".$CS_NO.");"));
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _removeProduct($P_NO)
    {
        if ($this->_isRealProduct($P_NO))
        {
            $res = dbwrapper::query(stdstr::toStr("SELECT IMG FROM PRODUCTS WHERE P_NO = ".(int)$P_NO.";"));
            if ($res && $res->rowCount() != 0)
            {
                $res = $res->fetch(PDO::FETCH_ASSOC);
                if (is_file($res["IMG"]))
                    unlink($res["IMG"]);
            }
            
            $res = dbwrapper::query(stdstr::toStr("DELETE FROM PRODUCTS WHERE P_NO = ".(int)$P_NO." LIMIT 1;"));
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _readExcel($page, $file, $PG_NO )
    {
        $data = new Spreadsheet_Excel_Reader();
        
        // Set output Encoding.
        $data->setOutputEncoding(self::DEFAULT_CHARSET);
        
        $data->read($file);
        
        $products = array();
        $headers = array();
        $names = array();
        $bTableFind = false;
        $row = 1;
        
        $size1 = null;
        $size2 = null;
        
        $headerLen = 0;
        
        $sheet = $data->sheets[(int)$page];
        
        for ($i = 1; $i <= $sheet['numRows']; $i++)
        {
            $cols = $sheet['cells'][$i];
        
        	for ($j = 1; $j <= $sheet['numCols']; $j++)
            {
                if ($bTableFind)
                {   
                    if ($row == 1)
                    {
                        if ($j > 2)
                        {
                            if ($cols[$j])
                            {
                                $headers[$j] = array("NAME" => $cols[$j]);
                            }
                            else
                            {
                                $headers[$j - 1];
                            }
                        }
                    }
                    else if ($row == 2)
                    {
                        if ($cols[$j])
                            if ($j > 2 && $j <= sizeof($headers) + 2)
                                $names[$j] = $headers[$j]["NAME"]." ".$cols[$j];
                    }
                    else
                    {
                        if ($cols[$j])
                        {            
                            if ($j == 1)
                            {
                                // todo if ($cols[$j] is not nuber) $bTableFind = false;
                                $size1 = $cols[$j];   
                            }
                            else if ($j == 2)
                            {
                                $size2 = $cols[$j];
                            }
                            else
                            {
                                $products[$names[$j]][] = array("SIZE1" => $size1, "SIZE2" => $size2, "PRICE" => $cols[$j]);
                            }
                        }
                    }
                }
        		else if (!(iconv_strpos("Вид матраца", $cols[$j], 0, self::DEFAULT_CHARSET) === false))
                {
                    $bTableFind = true;
                }
        	}
            
            if ($bTableFind)
                $row++;
        }
        
        foreach ($products as $key => $value)
        {
            $price = false;
            $xml = "<root>";
            foreach ($value as $params)
            {
                if ($price === false || $price > $params["PRICE"])
                    $price = $params["PRICE"];
                
                $xml .= "<price>".$params["PRICE"]."<size>".$params["SIZE1"]."x".$params["SIZE2"]."</size></price>";
            }
            $xml .= "</root>";
            
            if (!$this->_updateXMLPropertyByArticul($key, $xml, $price))
                $this->_createProduct($key, $price, $key, null, null, 0, 0, 0, 0, $xml, 2, $PG_NO, null);
        }

    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _updateXMLPropertyByArticul(/*string*/ $articul, /*string*/ $priceXML, /*int*/ $price)
    {
        $articul = (string) $articul;
        $priceXML = (string) $priceXML;
        $price = (int) $price;
        
        $res = dbwrapper::query(stdstr::toStr("SELECT * FROM PRODUCTS WHERE ARTICUL = '".$articul."';"));
    
        if (!(bool)$res || $res->rowCount() <= 0)
            return false;
        
        $res = $res->fetch(PDO::FETCH_ASSOC);
        
        $dom = stddomxml::create();
        $dom->loadXML($res["PROPERTY_XML"]);
        
        $rep = stddomxml::create();
        $rep->loadXML($priceXML);
        
        $newXML = stddomxml::unitedDOM($dom, $rep);
        $newXML = $newXML->saveXML();
        
        return $this->_updateProduct($res["P_NO"], $res["NAME"], $price, $res["ARTICUL"], $res["MAKER"], $res["COUNT"], $res["HIDDEN"], $res["IS_HIT"], $res["IS_NEW"], $res["IS_REC"], $newXML, $res["T_NO"], $res["PG_NO"], $res["CS_NO"]);
    }
    
    /////////////////////////////////////////////////////////////////////////
    private function _selectFromProduct(/*bool*/ $isAdmin, /*int|bool*/ $number)
    {
        $isAdmin = (bool) $isAdmin;
        $number = (int) $number;
        
        $sql = "SELECT  p.HIDDEN, p.NAME, p.PRICE, p.P_NO, p.ARTICUL, p.MAKER, p.COUNT, p.PROPERTY_XML, p.IS_HIT, p.IS_NEW, p.IS_REC, p.IMG, t.T_NO, t.TYPE_XSL, t.NAME AS T_NAME, cs.CS_NO, cs.NAME AS CS_NAME, cb.C_NO, cb.C_NAME, cb.B_NO, cb.B_NAME, g.G_NO, g.NAME AS G_NAME, pg.PG_NO, pg.NAME AS PG_NAME FROM ";
        
        $cs = "(PRODUCTS p LEFT OUTER JOIN COUNTSTATUS cs ON p.CS_NO = cs.CS_NO)";
        $pg = "(".$cs." LEFT OUTER JOIN PGROUPS pg ON p.PG_NO = pg.PG_NO)";
        $g = "(".$pg." LEFT OUTER JOIN GROUPS g ON pg.G_NO = g.G_NO)";
        
        // note: group may containt only brend or category and never brend AND category
        $cb = "(".$g." LEFT OUTER JOIN (SELECT C_NO, B_NO, c.NAME AS C_NAME, b.NAME AS B_NAME FROM CATEGORY c, BRANDS b) cb ON (g.C_NO = cb.C_NO AND g.B_NO IS NULL) OR (g.B_NO = cb.B_NO AND g.C_NO IS NULL))";
        
        $sql .= $cb.", TYPES t";
        
        $sql .= " WHERE ".($this->isAdmin ? "" : " p.HIDDEN = 0 AND ");
        
        $sql .= "t.T_NO = p.T_NO AND p.P_NO = ".$number." LIMIT 1;";
        
        return $sql;
    }
	
    /////////////////////////////////////////////////////////////////////////
    private $content = false;
    private $isAdmin = false;
    private $listType = self::DEFAULT_LIST_TYPE;
    private $productView = false;
    private $m_pageDir = self::DEFAULT_UPLOAD_DIR;
    
    static private $currCBNO = false;  // current category or brand number in BD
    static private $currGNO = false;   // current group number in BD
    static private $currPGNO = false;  // current subgroup number in BD
}
?>