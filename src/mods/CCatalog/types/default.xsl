<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="content">
        <xsl:for-each select="product">
            <xsl:choose>
                <xsl:when test="IMG">
                    <img vspace="7" src="{IMG}" alt="{NAME}" />
                </xsl:when>
                <xsl:otherwise>
                    <img vspace="7" src="./template/pic/defaultProductImg" alt="{NAME}" />
                </xsl:otherwise>
            </xsl:choose>
            <p class="simpleCart_shelfItem">
                <!--<span class="article"><xsl:text>Артикул: </xsl:text>
                <a href="{$baseUrl}{$URL_SEP}{P_NO}">
                    <xsl:value-of select="ARTICUL" />
                </a></span>
                <br />-->
                <b>Наименование: </b>
                <span class="item_name">
                    <a href="{$baseUrl}{$URL_SEP}{P_NO}">
                        <xsl:value-of select="NAME" />
                    </a>
                </span>
                <br />
                <!--<xsl:text>Изготовитель: </xsl:text>
                <xsl:value-of select="MAKER" />
                <br />-->
                <b>Цена: </b>
                <span class="item_price"><xsl:value-of select="PRICE" /> руб.</span>
                <br />
                <input type="text" class="item_quantity ui-widget-content ui-corner-all" style="text-align: center; vertical-align: middle; width: 33px; height: 33px; float: left;" value="1" />
                <input type="button" class="item_add button" value="в корзину" style="float: left;"/>

                <!--<br />
                <xsl:text>Наличие на складе: </xsl:text>
                <xsl:value-of select="COUNT" />
                <xsl:text> шт.</xsl:text>-->
            </p>
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