<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    
    <xsl:template name="catalogList">
        <xsl:apply-templates select="/root//catalogList"/>
    </xsl:template>
    
    <xsl:template match="catalogList">
        <form id="frmCatalogListTypeSelect" action="{$baseUrl}" method="post" style="padding: 5px 5px 5px 10px;">
            <div id="catalog_view_menu_selcter_control">
                <xsl:text>Показывать: </xsl:text>
                <select name="catalogType" onChange='document.getElementById("frmCatalogListTypeSelect").submit()' class="ui-widget-content ui-corner-all">
                    <xsl:call-template name="option">
                        <xsl:with-param name="currValue" select="listType"/>
                        <xsl:with-param name="value">category</xsl:with-param>
                        <xsl:with-param name="name">Категории</xsl:with-param>
                    </xsl:call-template>
                    <xsl:call-template name="option">
                        <xsl:with-param name="currValue" select="listType"/>
                        <xsl:with-param name="value">brend</xsl:with-param>
                        <xsl:with-param name="name">Бренды</xsl:with-param>
                    </xsl:call-template>
                </select>
            </div>
        </form>
        <ul>
            <xsl:variable name="catalogURL" select="catalogURL" />
            <xsl:for-each select="category|brend">
                <li>
                    <xsl:variable name="num" select="@num" />
                    <a href="{$catalogURL}{$URL_SEP}{$num}_0">
                        <xsl:value-of select="@name"/>
                    </a>
                    <xsl:if test="group">
                        <ul>
                            <xsl:for-each select="group">
                                <li>
                                    <xsl:variable name="gNum" select="@num" />
                                    <a href="{$catalogURL}{$URL_SEP}{$num}_{$gNum}_0">
                                        <xsl:value-of select="@name"/>
                                    </a>
                                    <xsl:if test="subgroup">
                                        <ul>
                                            <xsl:for-each select="subgroup">
                                                <li>
                                                    <xsl:variable name="subgNum" select="@num" />
                                                    <a href="{$catalogURL}{$URL_SEP}{$num}_{$gNum}_{$subgNum}_0">
                                                        <xsl:value-of select="@name"/>
                                                    </a>
                                                </li>
                                            </xsl:for-each>
                                        </ul>
                                    </xsl:if>
                                </li>
                            </xsl:for-each>
                        </ul>
                    </xsl:if>
                </li>
            </xsl:for-each>
        </ul>
        <br />
    </xsl:template>
</xsl:stylesheet>