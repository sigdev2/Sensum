<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="content">
        <script type="text/javascript" src="./mods/CCatalog/js.js"></script>
        
        <div id="catalog_panes">
            <ul id="catalog_tabs_menu"> 
                <li><a href="#catalog_tab">Каталог</a></li> 
                <li><a href="#add_product_tab">Добавить продукт</a></li> 
                <li><a href="#excel_tab">Загрузить из Excel</a></li> 
                <li><a href="#categ_edit_tab">Редактировать разделы</a></li> 
            </ul>
            <div id="catalog_tab" style="padding: 0 0 0 0;">
                <script type="text/javascript">
                    $(function()
                    {
                        $(".matras_size_select").change(function()
                        {
                            var sel = $(this);
                            sel.next("span.item_price").text(sel.val() + " руб.").next(".item_size").attr("value", sel.children("option:selected").text());
                        }).change();
                    });
                </script>
                
                <xsl:variable name="catalogURL" select="/root/widgets/leftBar/catalogList/catalogURL" />
                <xsl:if test="boolean(/root/widgets/leftBar/catalogList/category) and (number(/root/widgets/leftBar/catalogList/category/@current) = 1)">
                    <xsl:choose>
                        <xsl:when test="boolean(/root/widgets/leftBar/catalogList/category/group) and (number(/root/widgets/leftBar/catalogList/category/group/@current) = 1)">
                          <xsl:choose>
                              <xsl:when test="/root/widgets/leftBar/catalogList/category[number(@current) = 1]/group[number(@current) = 1]/subgroup">
                                 <xsl:call-template name="subgroupMenu"/>
                              </xsl:when>
                              <xsl:otherwise>
                                  <xsl:call-template name="groupMenu"/>
                              </xsl:otherwise>
                           </xsl:choose>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:call-template name="groupMenu"/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:if>
                
                <table cellPadding="0" cellSpacing="0" border="0" width="100%" class="block">
                    <tr>
                        <td colspan="2">
                            <div id="catalog_sort_control">
                                <form id="frmCatalogListSortBySelect" action="{$baseUrl}" method="post">
                                    <xsl:text>Сортировать: </xsl:text>
                                    <select name="catalogSortBy" onChange='document.getElementById("frmCatalogListSortBySelect").submit()' class="ui-widget-content ui-corner-all">
                                        <xsl:call-template name="option">
                                            <xsl:with-param name="currValue" select="sortBy"/>
                                            <xsl:with-param name="value">by name</xsl:with-param>
                                            <xsl:with-param name="name">по наименованию</xsl:with-param>
                                        </xsl:call-template>
                                        <xsl:call-template name="option">
                                            <xsl:with-param name="currValue" select="sortBy"/>
                                            <xsl:with-param name="value">by price</xsl:with-param>
                                            <xsl:with-param name="name">по цене</xsl:with-param>
                                        </xsl:call-template>
                                        <!--<xsl:call-template name="option">
                                            <xsl:with-param name="currValue" select="sortBy"/>
                                            <xsl:with-param name="value">by count</xsl:with-param>
                                            <xsl:with-param name="name">по наличию</xsl:with-param>
                                        </xsl:call-template>
                                        <xsl:call-template name="option">
                                            <xsl:with-param name="currValue" select="sortBy"/>
                                            <xsl:with-param name="value">by articul</xsl:with-param>
                                            <xsl:with-param name="name">по артиклу</xsl:with-param>
                                        </xsl:call-template>
                                        <xsl:call-template name="option">
                                            <xsl:with-param name="currValue" select="sortBy"/>
                                            <xsl:with-param name="value">by maker</xsl:with-param>
                                            <xsl:with-param name="name">по производителю</xsl:with-param>
                                        </xsl:call-template>-->
                                    </select>
                                    <xsl:text> | </xsl:text>
                                    <select name="catalogSortType" onChange='document.getElementById("frmCatalogListSortBySelect").submit()'  class="ui-widget-content ui-corner-all">
                                        <xsl:call-template name="option">
                                            <xsl:with-param name="currValue" select="sortType"/>
                                            <xsl:with-param name="value">ASC</xsl:with-param>
                                            <xsl:with-param name="name">по возрастанию</xsl:with-param>
                                        </xsl:call-template>
                                        <xsl:call-template name="option">
                                            <xsl:with-param name="currValue" select="sortType"/>
                                            <xsl:with-param name="value">DESC</xsl:with-param>
                                            <xsl:with-param name="name">по убыванию</xsl:with-param>
                                        </xsl:call-template>
                                    </select>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <xsl:for-each select="product">
                            <xsl:call-template name="tdProduct"/>
                            <xsl:if test="position() mod 2 = 0">
                                <xsl:text disable-output-escaping="yes">
                                    &lt;/tr&gt;
                                    &lt;tr&gt;
                                </xsl:text>
                            </xsl:if>
                        </xsl:for-each>
                    </tr>
                    
                    <tr>
                        <td  colspan="4">
                             <div id="catalog_page_control">
                                 <xsl:if test = "pageCount and not(pageCount = 0)">
                                    <form id="frmCatalogPageSizeSelect" action="{$baseUrl}" method="post">
                                        <p>
                                            <xsl:text>Страницы: </xsl:text>
                                            <xsl:call-template name="for">
                                                <xsl:with-param name="n" select="pageCount"/>
                                            </xsl:call-template>
                                            <xsl:text> - </xsl:text>
                                            <xsl:text> Выводить товаров на странице по </xsl:text>
                                            <select name="catalogPageSize" onChange='document.getElementById("frmCatalogPageSizeSelect").submit()' class="ui-widget-content ui-corner-all">
                                                <xsl:call-template name="option">
                                                    <xsl:with-param name="currValue" select="pageSize"/>
                                                    <xsl:with-param name="value">4</xsl:with-param>
                                                    <xsl:with-param name="name">4</xsl:with-param>
                                                </xsl:call-template>
                                                <xsl:call-template name="option">
                                                    <xsl:with-param name="currValue" select="pageSize"/>
                                                    <xsl:with-param name="value">6</xsl:with-param>
                                                    <xsl:with-param name="name">6</xsl:with-param>
                                                </xsl:call-template>
                                                <xsl:call-template name="option">
                                                    <xsl:with-param name="currValue" select="pageSize"/>
                                                    <xsl:with-param name="value">8</xsl:with-param>
                                                    <xsl:with-param name="name">8</xsl:with-param>
                                                </xsl:call-template>
                                                <xsl:call-template name="option">
                                                    <xsl:with-param name="currValue" select="pageSize"/>
                                                    <xsl:with-param name="value">10</xsl:with-param>
                                                    <xsl:with-param name="name">10</xsl:with-param>
                                                </xsl:call-template>
                                            </select>
                                            <xsl:text> шт.</xsl:text>
                                        </p>
                                    </form>
                                </xsl:if>
                            </div>
                        </td>
                    </tr>
                </table>

            </div>
            
            <div id="add_product_tab">
                <b>Добавить продукт:</b><br />
                <form action="{$baseUrl}" method="post">
                    <p style="margin-left: 250px; margin-bottom: 10px; margin-top: 10px;">
                        <input type="hidden" name="act" value="add"/>
                        <label for="catalog_add_product_name_edit">Наименование</label>
                        <input id="catalog_add_product_name_edit" size="101" maxlength="150" name="NAME" value="" class="text ui-widget-content ui-corner-all"/><br />
                        <label for="catalog_add_product_price_edit">Цена(число)</label>
                        <input id="catalog_add_product_price_edit" size="101" maxlength="150" name="PRICE" value="" class="text ui-widget-content ui-corner-all"/><br />
                        <label for="catalog_add_product_articul_edit">Артикул</label>
                        <input id="catalog_add_product_articul_edit" size="101" maxlength="150" name="ARTICUL" value="" class="text ui-widget-content ui-corner-all"/><br />
                        <label for="catalog_add_product_maker_edit">Производитель</label>
                        <input id="catalog_add_product_maker_edit" size="101" maxlength="150" name="MAKER" value="" class="text ui-widget-content ui-corner-all"/><br />
                        <label for="catalog_add_product_count_edit">В наличии(число)</label>
                        <input id="catalog_add_product_count_edit" size="101" maxlength="150" name="COUNT" value="" class="text ui-widget-content ui-corner-all"/><br />
                        <label for="catalog_add_product_hidden_edit">Скрыть</label>
                        <input id="catalog_add_product_hidden_edit" type="checkbox" name="HIDDEN" value="1" class="ui-widget-content ui-corner-all"/><br />
                        <label for="catalog_add_product_hit_edit">Хит</label>
                        <input id="catalog_add_product_hit_edit" type="checkbox" name="IS_HIT" value="1" class="ui-widget-content ui-corner-all"/><br />
                        <label for="catalog_add_product_new_edit">Новинка</label>
                        <input id="catalog_add_product_new_edit" type="checkbox" name="IS_NEW" value="1" class="ui-widget-content ui-corner-all"/><br />
                        <label for="catalog_add_product_rec_edit">Рекомендован</label>
                        <input id="catalog_add_product_rec_edit" type="checkbox" name="IS_REC" value="1" class="ui-widget-content ui-corner-all"/><br />
                        <label for="catalog_add_product_xml_data_edit">Дополнительные XML данные: </label>
                        <textarea id="catalog_add_product_xml_data_edit" name="PROPERTY_XML" rows="2" cols="50" class="text ui-widget-content ui-corner-all">
                        </textarea>
                        
                        <label for="catalog_add_product_subgroups_select" >Подгруппа</label>
                        <select id="catalog_add_product_subgroups_select" name="PG_NO" class="ui-widget-content ui-corner-all">
                            <xsl:for-each select="subGroups/subGroup">
                                <xsl:call-template name="option">
                                    <xsl:with-param name="currDis">0</xsl:with-param>
                                    <xsl:with-param name="value" select="@PG_NO" />
                                    <xsl:with-param name="name"  select="text()" />
                                </xsl:call-template>
                            </xsl:for-each>
                        </select>
                        <br />
                        
                        <label for="catalog_add_product_types_select" >Тип шаблона</label>
                        <select id="catalog_add_product_types_select" name="T_NO" class="ui-widget-content ui-corner-all">
                            <xsl:for-each select="types/type">
                                <xsl:call-template name="option">
                                    <xsl:with-param name="currDis">0</xsl:with-param>
                                    <xsl:with-param name="value" select="@T_NO" />
                                    <xsl:with-param name="name"  select="text()" />
                                </xsl:call-template>
                            </xsl:for-each>
                        </select>
                        <br />
                        
                        <label for="catalog_add_product_count_status_select" >Статус наличия</label>
                        <select id="catalog_add_product_count_status_select" name="CS_NO" class="ui-widget-content ui-corner-all">
                            <xsl:for-each select="countStatuss/countStatus">
                                <xsl:call-template name="option">
                                    <xsl:with-param name="currDis">0</xsl:with-param>
                                    <xsl:with-param name="value" select="@CS_NO" />
                                    <xsl:with-param name="name"  select="text()" />
                                </xsl:call-template>
                            </xsl:for-each>
                        </select>
                    </p>
                    <input type="submit" name="add" value="Добавить" class="button"/>
                </form>
            </div>

            <div id="excel_tab">
                <b>Загрузка из Excel</b>
                <br /><br />
                <form enctype="multipart/form-data" action="{$baseUrl}" method="POST">
                    <input type="hidden" name="act" value="excel"/>
                    <label>Выберете xls-файл:</label>
                    <input NAME="xlsfile" TYPE="file" />
                    <br /><br />
                    <label>Подгруппа, в которую будут добавлять новые товары:</label>
                    <select name="PG_NO" class="ui-widget-content ui-corner-all">
                        <xsl:for-each select="subGroups/subGroup">
                            <xsl:call-template name="option">
                                <xsl:with-param name="currDis">0</xsl:with-param>
                                <xsl:with-param name="value" select="@PG_NO" />
                                <xsl:with-param name="name"  select="text()" />
                            </xsl:call-template>
                        </xsl:for-each>
                    </select>
                    <br /><br />
                    <label>Номер страницы в xls-файле:</label>
                    <input size="101" maxlength="150" name="page" value="0" class="text ui-widget-content ui-corner-all"/>
                    <br /><br />
                    <input type="submit" value="Загрузить" class="button"/>
                </form>
            </div>

            <div id="categ_edit_tab">
                <b>Добавить категорию:</b>
                <br /><br />
                <form action="{$baseUrl}" method="post">
                    <input type="hidden" name="act" value="addCategory"/>
                    <label for="catalog_new_category_name_edit">Название</label>
                    <input id="catalog_new_category_name_edit" size="101" maxlength="150" name="C_NAME" value="" class="text ui-widget-content ui-corner-all"/>
                    <br /><br />
                    <input type="submit" name="add" value="Создать" class="button"/>
                </form>
                <br />
                
                <b>Добавить бренд:</b>
                <br /><br />
                <form action="{$baseUrl}" method="post">
                    <input type="hidden" name="act" value="addBrand"/>
                    <label for="catalog_new_brand_name_edit">Название</label>
                    <input id="catalog_new_brand_name_edit" size="101" maxlength="150" name="B_NAME" value="" class="text ui-widget-content ui-corner-all"/>
                    <br /><br />
                    <input type="submit" name="add" value="Создать" class="button"/>
                </form>
                <br />
                
                <b>Добавить группу:</b>
                <br /><br />
                <form action="{$baseUrl}" method="post">
                    <input type="hidden" name="act" value="addGroup"/>
                    <label for="catalog_new_group_name_edit">Название</label>
                    <input id="catalog_new_group_name_edit" size="101" maxlength="150" name="G_NAME" value="" class="text ui-widget-content ui-corner-all"/><br />
                    <label for="catalog_add_group_hidden_edit">Скрыть</label>
                    <input id="catalog_add_group_hidden_edit" type="checkbox" name="HIDDEN" value="1" class="ui-widget-content ui-corner-all"/>
                    <br /><br />
                    <label for="catalog_add_group_category_select" >Категория: </label>
                    <select id="catalog_add_group_category_select" name="C_NO" class="ui-widget-content ui-corner-all">
                        <xsl:call-template name="option">
                            <xsl:with-param name="currDis">0</xsl:with-param>
                            <xsl:with-param name="value">null</xsl:with-param>
                            <xsl:with-param name="name">none</xsl:with-param>
                        </xsl:call-template>
                        <xsl:for-each select="categories/category">
                            <xsl:call-template name="option">
                                <xsl:with-param name="currDis">0</xsl:with-param>
                                <xsl:with-param name="value" select="@C_NO" />
                                <xsl:with-param name="name"  select="text()" />
                            </xsl:call-template>
                        </xsl:for-each>
                    </select>
                    <br /><br />
                    <label for="catalog_add_group_brend_select" >Бренд: </label>
                    <select id="catalog_add_group_brend_select" name="B_NO" class="ui-widget-content ui-corner-all">
                        <xsl:call-template name="option">
                            <xsl:with-param name="currDis">0</xsl:with-param>
                            <xsl:with-param name="value">null</xsl:with-param>
                            <xsl:with-param name="name">none</xsl:with-param>
                        </xsl:call-template>
                        <xsl:for-each select="brands/brand">
                            <xsl:call-template name="option">
                                <xsl:with-param name="currDis">0</xsl:with-param>
                                <xsl:with-param name="value" select="@B_NO" />
                                <xsl:with-param name="name"  select="text()" />
                            </xsl:call-template>
                        </xsl:for-each>
                    </select>
                    <br /><br />
                    <input type="submit" name="add" value="Создать" class="button"/>
                </form>
                <br />
                
                <b>Добавить подгруппу:</b>
                <br /><br />
                <form action="{$baseUrl}" method="post">
                    <input type="hidden" name="act" value="addSubGroup"/>
                    <label for="catalog_new_subgroup_name_edit">Название</label>
                    <input id="catalog_new_subgroup_name_edit" size="101" maxlength="150" name="PG_NAME" value="" class="text ui-widget-content ui-corner-all"/><br /><br />
                    <label for="catalog_add_subgroup_hidden_edit">Скрыть</label>
                    <input id="catalog_add_subgroup_hidden_edit" type="checkbox" name="HIDDEN" value="1" class="ui-widget-content ui-corner-all"/><br />
                    <label for="catalog_add_subgroup_group_select" >Группа: </label>
                    <select id="catalog_add_subgroup_group_select" name="G_NO" class="ui-widget-content ui-corner-all">
                        <xsl:for-each select="groups/group">
                            <xsl:call-template name="option">
                                <xsl:with-param name="currDis">0</xsl:with-param>
                                <xsl:with-param name="value" select="@G_NO" />
                                <xsl:with-param name="name"  select="text()" />
                            </xsl:call-template>
                        </xsl:for-each>
                    </select>
                    <br /><br />
                    <input type="submit" name="add" value="Создать" class="button"/>
                </form>
                <br />
                
                <b>Удаление:</b>
                <br /><br />
                <form action="{$baseUrl}" method="post">
                    <input type="hidden" name="act" value="delCategory"/>
                    <select id="catalog_del_category_select" name="C_NO" class="ui-widget-content ui-corner-all">
                        <xsl:for-each select="categories/category">
                            <xsl:call-template name="option">
                                <xsl:with-param name="currDis">0</xsl:with-param>
                                <xsl:with-param name="value" select="@C_NO" />
                                <xsl:with-param name="name"  select="text()" />
                            </xsl:call-template>
                        </xsl:for-each>
                    </select> <input type="submit" name="add" value="Удалить категорию" class="button"/>
                </form>
                <br /><br />
                <form action="{$baseUrl}" method="post">
                    <input type="hidden" name="act" value="delBrand"/>
                    <select id="catalog_del_brand_select" name="B_NO" class="ui-widget-content ui-corner-all">
                        <xsl:for-each select="brands/brand">
                            <xsl:call-template name="option">
                                <xsl:with-param name="currDis">0</xsl:with-param>
                                <xsl:with-param name="value" select="@B_NO" />
                                <xsl:with-param name="name"  select="text()" />
                            </xsl:call-template>
                        </xsl:for-each>
                    </select> <input type="submit" name="add" value="Удалить бренд" class="button"/>
                </form>
                <br /><br />
                <form action="{$baseUrl}" method="post">
                    <input type="hidden" name="act" value="delGroup"/>
                    <select id="catalog_del_group_select" name="G_NO" class="ui-widget-content ui-corner-all">
                        <xsl:for-each select="groups/group">
                            <xsl:call-template name="option">
                                <xsl:with-param name="currDis">0</xsl:with-param>
                                <xsl:with-param name="value" select="@G_NO" />
                                <xsl:with-param name="name"  select="text()" />
                            </xsl:call-template>
                        </xsl:for-each>
                    </select> <input type="submit" name="add" value="Удалить группу" class="button"/>
                </form>
                <br /><br />
                <form action="{$baseUrl}" method="post">
                    <input type="hidden" name="act" value="delSubGroup"/>
                    <select id="catalog_del_supgroup_select" name="PG_NO" class="ui-widget-content ui-corner-all">
                        <xsl:for-each select="subGroups/subGroup">
                            <xsl:call-template name="option">
                                <xsl:with-param name="currDis">0</xsl:with-param>
                                <xsl:with-param name="value" select="@PG_NO" />
                                <xsl:with-param name="name"  select="text()" />
                            </xsl:call-template>
                        </xsl:for-each>
                    </select> <input type="submit" name="add" value="Удалить подгруппу" class="button"/>
                </form>
             </div>
        </div>
    </xsl:template>

    <xsl:template name="for">
        <xsl:param name="i" select="0"/>
        <xsl:param name="n" select="1"/>
        <xsl:if test="$i &lt; $n">

            <xsl:element name="a">
                <xsl:attribute name="href">
                    <xsl:value-of select="$baseUrl" />
                    <xsl:value-of select="$URL_SEP" />
                    <xsl:if test="cbNum">
                        <xsl:value-of select="cbNum" />
                    </xsl:if>
                    <xsl:if test="gNum">
                        <xsl:text>_</xsl:text>
                        <xsl:value-of select="gNum" />
                    </xsl:if>
                    <xsl:if test="pgNum">
                        <xsl:text>_</xsl:text>
                        <xsl:value-of select="pgNum" />
                    </xsl:if>
                    <xsl:text>_</xsl:text>
                    <xsl:value-of select="$i" />
                </xsl:attribute>
                <xsl:value-of select="$i" />
            </xsl:element>
            <xsl:text>&#32;</xsl:text>
            <xsl:call-template name="for">
                <xsl:with-param name="i" select="$i + 1"/>
                <xsl:with-param name="n" select="$n"/>
            </xsl:call-template>
        </xsl:if>
    </xsl:template>

    <xsl:template name="tdProduct">
        <td style="width: 50%; vertical-align: top;">
                <div class="catalogBlockTitle">
                    <a href="{$baseUrl}{$URL_SEP}{P_NO}">
                        <xsl:value-of select="NAME" />
                    </a>
                </div>
            
                <xsl:element name="div">
                <xsl:attribute name="class">catalogBlock</xsl:attribute>
                <xsl:attribute name="style">background: url('<xsl:choose>
                    <xsl:when test="IMG and (not(IMG=''))">
                       <xsl:value-of select="IMG" />
                    </xsl:when>
                    <xsl:otherwise>
                       <xsl:value-of select="PROPERTY_XML/root/img" />
                    </xsl:otherwise>
                </xsl:choose>') no-repeat right;vertical-align: bottom;</xsl:attribute>               
                    <xsl:if test="boolean(PROPERTY_XML/root/rigidity_top) and boolean(PROPERTY_XML/root/height) and boolean(PROPERTY_XML/root/rigidity_bottom)">
                    <table cellpadding="0" cellspacing="0" class="jost">
                        <tr>
                            <td></td>
                            <td class="josttop">
                                <xsl:choose>
                                    <xsl:when test="PROPERTY_XML/root/rigidity_top">
                                        <xsl:value-of select="PROPERTY_XML/root/rigidity_top" />
                                    </xsl:when>
                                    <xsl:otherwise>
                                      <xsl:text>неуказано</xsl:text>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </td>
                        </tr>
                        <tr>
                            <td class="jostheight">
                                <xsl:choose>
                                    <xsl:when test="PROPERTY_XML/root/height">
                                        <xsl:value-of select="PROPERTY_XML/root/height" />
                                    </xsl:when>
                                    <xsl:otherwise>
                                      <xsl:text>0 </xsl:text>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </td>
                            <td class="jostvisota"></td>
                            
                        </tr>
                        <tr>
                            <td></td>
                            <td class="jostbottom">
                                <xsl:choose>
                                    <xsl:when test="PROPERTY_XML/root/rigidity_bottom">
                                        <xsl:value-of select="PROPERTY_XML/root/rigidity_bottom" />
                                    </xsl:when>
                                    <xsl:otherwise>
                                      <xsl:text>неуказано</xsl:text>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </td>
                        </tr>
                    </table>
                    </xsl:if>
                    <xsl:if test="PROPERTY_XML/root/strain">
                    <div class="nagruzka">
                        <div class="strain">
                            <xsl:choose>
                                <xsl:when test="PROPERTY_XML/root/strain">
                                    <xsl:value-of select="PROPERTY_XML/root/strain" />
                                </xsl:when>
                                <xsl:otherwise>
                                  <xsl:text>0 кг</xsl:text>
                                </xsl:otherwise>
                            </xsl:choose>
                        </div>
                        <div class="text">нагрузка</div>
                    </div>
                    </xsl:if>
                    <xsl:if test="PROPERTY_XML/root/materials/item">
                    <div class="property">
                        <ol>
                            <xsl:for-each select="PROPERTY_XML/root/materials/item">
                                    <li><xsl:value-of select="text()" /></li>
                            </xsl:for-each>
                        </ol>
                        <br />
                    </div>
                    </xsl:if>
                    
                    <xsl:if test="PROPERTY_XML/root/advanced">
                        <div class="advanced">
                            <b>Описание:</b> <xsl:value-of select="PROPERTY_XML/root/advanced/text()" />
                            <br />
                        </div>
                    </xsl:if>
                </xsl:element>
                <div class="simpleCart_shelfItem">
                    <b>Размер-цена: </b>
                    <select class="matras_size_select ui-widget-content ui-corner-all">
                        <xsl:for-each select="PROPERTY_XML/root/price">
                            <xsl:call-template name="option">
                                <xsl:with-param name="currDis">0</xsl:with-param>
                                <xsl:with-param name="value" select="text()" />
                                <xsl:with-param name="name"  select="size" />
                            </xsl:call-template>
                        </xsl:for-each>
                    </select>
                    <span class="item_price"></span>
                    <input type="hidden" value="" class="item_size"/>
                    <span class="item_name">
                        <a href="{$baseUrl}{$URL_SEP}{P_NO}">
                            <xsl:value-of select="NAME" />
                        </a>
                    </span>
                    <hr />
                    <input type="text" class="item_quantity ui-widget-content ui-corner-all countProduct" value="1"/>
                    <input type="button" class="item_add button" value="в корзину"/>
                    <form action="{$baseUrl}" method="post" class="item_delelet">
                        <input type="hidden" name="act" value="del"/>
                        <input type="hidden" name="P_NO" value="{P_NO}"/>
                        <input type="submit" name="del" value="Удалить" class="button"/>
                    </form>
                </div>
        </td>
    </xsl:template>
    
    <xsl:template name="groupMenu">
        <xsl:variable name="catalogURL" select="/root/widgets/leftBar/catalogList/catalogURL" />
        <xsl:variable name="cnum" select="/root/widgets/leftBar/catalogList/category[number(@current) = 1]/@num" />
        <table cellPadding="0" cellSpacing="0" border="0" width="100%">
            <xsl:variable name="groupCount" select="count(/root/widgets/leftBar/catalogList/category[number(@current) = 1]/group)" />
            <xsl:variable name="firstCol">
                <xsl:choose>
                    <xsl:when test="($groupCount div 2) &lt; 1"><xsl:value-of select="$groupCount" /></xsl:when>
                    <xsl:otherwise><xsl:value-of select="(($groupCount - ($groupCount mod 2)) div 2) + ($groupCount mod 2)" /></xsl:otherwise>
                </xsl:choose>
            </xsl:variable>
            <tr>
                <td style="margin-left: 40px;">
                    <ul>
                        <xsl:for-each select="/root/widgets/leftBar/catalogList/category[number(@current) = 1]/group[position() &lt;= $firstCol]">
                             <li>
                                <xsl:variable name="gNum" select="@num" />
                                <a href="{$catalogURL}{$URL_SEP}{$cnum}_{$gNum}_0">
                                    <xsl:value-of select="@name"/>
                                </a>
                            </li>
                        </xsl:for-each>
                    </ul>
                </td>
                <td style="margin-left: 40px;">
                    <ul>
                        <xsl:for-each select="/root/widgets/leftBar/catalogList/category[number(@current) = 1]/group[position() &gt; $firstCol]">
                            <li>
                                <xsl:variable name="gNum" select="@num" />
                                <a href="{$catalogURL}{$URL_SEP}{$cnum}_{$gNum}_0">
                                    <xsl:value-of select="@name"/>
                                </a>
                            </li>
                        </xsl:for-each>
                    </ul>
                </td>
            </tr>
        </table>
    </xsl:template>
    
    <xsl:template name="subgroupMenu">
        <xsl:variable name="catalogURL" select="/root/widgets/leftBar/catalogList/catalogURL" />
        <xsl:variable name="cnum" select="/root/widgets/leftBar/catalogList/category[number(@current) = 1]/@num" />
        <xsl:variable name="gnum" select="/root/widgets/leftBar/catalogList/category[number(@current) = 1]/group[number(@current) = 1]/@num" />
        <table cellPadding="0" cellSpacing="0" border="0" width="100%">
            <xsl:variable name="subgroupCount" select="count(/root/widgets/leftBar/catalogList/category[number(@current) = 1]/group[number(@current) = 1]/subgroup)" />
            <xsl:variable name="firstCol">
                <xsl:choose>
                    <xsl:when test="($subgroupCount div 2) &lt; 1"><xsl:value-of select="$subgroupCount" /></xsl:when>
                    <xsl:otherwise><xsl:value-of select="(($subgroupCount - ($subgroupCount mod 2)) div 2) + ($subgroupCount mod 2)" /></xsl:otherwise>
                </xsl:choose>
            </xsl:variable>
            <tr>
                <td style="margin-left: 40px;">
                    <ul>
                        <xsl:for-each select="/root/widgets/leftBar/catalogList/category[number(@current) = 1]/group[number(@current) = 1]/subgroup[position() &lt;= $firstCol]">
                            <li>
                                <xsl:variable name="subgNum" select="@num" />
                                <a href="{$catalogURL}{$URL_SEP}{$cnum}_{$gnum}_{$subgNum}_0">
                                    <xsl:value-of select="@name"/>
                                </a>
                            </li>
                        </xsl:for-each>
                    </ul>
                </td>
                <td style="margin-left: 40px;">
                    <ul>
                        <xsl:for-each select="/root/widgets/leftBar/catalogList/category[number(@current) = 1]/group[number(@current) = 1]/subgroup[position() &gt; $firstCol]">
                            <li>
                                <xsl:variable name="subgNum" select="@num" />
                                <a href="{$catalogURL}{$URL_SEP}{$cnum}_{$gnum}_{$subgNum}_0">
                                    <xsl:value-of select="@name"/>
                                </a>
                            </li>
                        </xsl:for-each>
                    </ul>
                </td>
            </tr>
        </table>
    </xsl:template>
    
</xsl:stylesheet>