<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="content">
        <xsl:for-each select="product">
            <form enctype="multipart/form-data" action="{$baseUrl}{$URL_SEP}{P_NO}" method="post">
            <p style="margin-left: 250px; margin-bottom: 10px; margin-top: 10px;">
                <input type="hidden" name="act" value="upd"/>
                <input type="hidden" name="P_NO" value="{P_NO}"/>
                <label for="catalog_add_product_name_edit">Наименование</label><br />
                <input id="catalog_add_product_name_edit" size="101" maxlength="150" name="NAME" value="{NAME}" class="text ui-widget-content ui-corner-all"/><br />
                <label for="catalog_add_product_price_edit">Цена(число)</label><br />
                <input id="catalog_add_product_price_edit" size="101" maxlength="150" name="PRICE" value="{PRICE}" class="text ui-widget-content ui-corner-all"/><br />
                <label for="catalog_add_product_articul_edit">Артикул</label><br />
                <input id="catalog_add_product_articul_edit" size="101" maxlength="150" name="ARTICUL" value="{ARTICUL}" class="text ui-widget-content ui-corner-all"/><br />
                <label for="catalog_add_product_maker_edit">Производитель</label><br />
                <input id="catalog_add_product_maker_edit" size="101" maxlength="150" name="MAKER" value="{MAKER}" class="text ui-widget-content ui-corner-all"/><br />
                <label for="catalog_add_product_count_edit">В наличии(число)</label><br />
                <input id="catalog_add_product_count_edit" size="101" maxlength="150" name="COUNT" value="{COUNT}" class="text ui-widget-content ui-corner-all"/><br />
                <label>Скрыть:</label>
                <xsl:call-template name="boolChoos">
                    <xsl:with-param name="falseVar">Нет</xsl:with-param>
                    <xsl:with-param name="trueVar">Да</xsl:with-param>
                    <xsl:with-param name="name">HIDDEN</xsl:with-param>
                    <xsl:with-param name="currValue" select="HIDDEN"/>
                </xsl:call-template>
                <br /><br />
                <label>Это хит:</label>
                <xsl:call-template name="boolChoos">
                    <xsl:with-param name="falseVar">Нет</xsl:with-param>
                    <xsl:with-param name="trueVar">Да</xsl:with-param>
                    <xsl:with-param name="name">IS_HIT</xsl:with-param>
                    <xsl:with-param name="currValue" select="IS_HIT"/>
                </xsl:call-template>
                <br /><br />
                <label>Новинка:</label>
                <xsl:call-template name="boolChoos">
                    <xsl:with-param name="falseVar">Нет</xsl:with-param>
                    <xsl:with-param name="trueVar">Да</xsl:with-param>
                    <xsl:with-param name="name">IS_NEW</xsl:with-param>
                    <xsl:with-param name="currValue" select="IS_NEW"/>
                </xsl:call-template>
                <br /><br />
                <label>Рекомендован:</label>
                <xsl:call-template name="boolChoos">
                    <xsl:with-param name="falseVar">Нет</xsl:with-param>
                    <xsl:with-param name="trueVar">Да</xsl:with-param>
                    <xsl:with-param name="name">IS_REC</xsl:with-param>
                    <xsl:with-param name="currValue" select="IS_REC"/>
                </xsl:call-template>
                <br /><br />
                <label>Текущая картинка:</label><br />
                <xsl:element name="img">
                <xsl:attribute name="alt"><xsl:value-of select="NAME" /></xsl:attribute>
                <xsl:attribute name="src">
                     <xsl:choose>
                        <xsl:when test="IMG and (not(IMG=''))">
                           <xsl:value-of select="IMG" />
                        </xsl:when>
                        <xsl:otherwise>
                           <xsl:value-of select="PROPERTY_XML/root/img" />
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:attribute>
                </xsl:element>
                <br />
                <label>Загрузить другую:</label>
                <input name="IMG" type="file" />
                <br /><br />
                <label for="catalog_add_product_xml_data_edit">Дополнительные XML данные: </label>
                <br />
                <textarea id="catalog_add_product_xml_data_edit" name="PROPERTY_XML" rows="2" cols="50" class="text ui-widget-content ui-corner-all">
                    <xsl:copy-of select="PROPERTY_XML/*" />
                </textarea>
                
                <label for="catalog_add_product_subgroups_select" >Подгруппа</label>
                <select id="catalog_add_product_subgroups_select" name="PG_NO" class="ui-widget-content ui-corner-all">
                <xsl:variable name="pgNo" select="PG_NO"/>
                    <xsl:for-each select="../subGroups/subGroup">
                        <xsl:call-template name="option">
                            <xsl:with-param name="currValue" select="$pgNo" />
                            <xsl:with-param name="value" select="@PG_NO" />
                            <xsl:with-param name="name"  select="text()" />
                        </xsl:call-template>
                    </xsl:for-each>
                </select>
                <br />
                
                <label for="catalog_add_product_types_select" >Тип шаблона:</label>
                <select id="catalog_add_product_types_select" name="T_NO" class="ui-widget-content ui-corner-all">
                <xsl:variable name="tNo" select="T_NO"/>
                    <xsl:for-each select="../types/type">
                        <xsl:call-template name="option">
                            <xsl:with-param name="currValue" select="$tNo" />
                            <xsl:with-param name="value" select="@T_NO" />
                            <xsl:with-param name="name"  select="text()" />
                        </xsl:call-template>
                    </xsl:for-each>
                </select>
                <br />
                
                <label for="catalog_add_product_count_status_select" >Статус наличия</label>
                <select id="catalog_add_product_count_status_select" name="CS_NO" class="ui-widget-content ui-corner-all">
                <xsl:variable name="csNo" select="CS_NO"/>
                    <xsl:for-each select="../countStatuss/countStatus">
                        <xsl:call-template name="option">
                            <xsl:with-param name="currValue" select="$csNo" />
                            <xsl:with-param name="value" select="@CS_NO" />
                            <xsl:with-param name="name"  select="text()" />
                        </xsl:call-template>
                    </xsl:for-each>
                </select>
            </p>
            <input type="submit" name="upd" value="Изменить" class="button"/>
        </form>
        <form action="{$baseUrl}" method="post" class="item_delelet">
            <input type="hidden" name="act" value="del"/>
            <input type="hidden" name="P_NO" value="{P_NO}"/>
            <input type="submit" name="del" value="Удалить" class="button"/>
        </form>
        </xsl:for-each>
        <!-- <xsl:if test = "pageCount">
            <xsl:call-template name="pageList">
                <xsl:with-param name="pageCount" select="pageCount"/>
            </xsl:call-template>
        </xsl:if> -->
    </xsl:template>
    
    <xsl:template name="pageList">
        <xsl:param name="pageCount">1</xsl:param>
        <xsl:param name="i">0</xsl:param>
        <xsl:if test = "not($pageCount = 0)" >
            <xsl:if test="not(($pageCount - $i) = 0)">
                <xsl:element name="a">
                    <xsl:attribute name="href">
                        <xsl:value-of select="$baseUrl" /><xsl:value-of select="$URL_SEP" />
                        <xsl:if test="sbNum"><xsl:value-of select="sbNum" /></xsl:if>
                        <xsl:if test="gNum">_<xsl:value-of select="gNum" /></xsl:if>
                        <xsl:if test="pgNum">_<xsl:value-of select="pgNum" /></xsl:if>
                        <xsl:text>_</xsl:text><xsl:value-of select="$i" />
                    </xsl:attribute>
                    <xsl:value-of select="$i" />
                </xsl:element>
                <xsl:call-template name="pageList">
                    <xsl:with-param name="pageCount" select="$pageCount"/>
                    <xsl:with-param name="i" select="$i + 1"/>
                </xsl:call-template>
            </xsl:if>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>