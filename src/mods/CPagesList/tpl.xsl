<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="content">
        <xsl:value-of select="header"/>
        <br /><hr />
        <script language="JavaScript" type="text/javascript" src="./mods/CPagesList/js.js"/>
        <xsl:choose>
            <xsl:when test="nocategory">
                <ul>
                    <xsl:apply-templates select=".//item" />
                </ul>
            </xsl:when>
            <xsl:otherwise>
                <xsl:for-each select="list/category">
                    <h4 style="cursor:hand" onClick="expandit(this)">
                        <xsl:value-of select="@name"/>
                    </h4>
                    <ul>
                        <xsl:apply-templates select=".//item" />
                    </ul>
                    <br />
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
 
    <xsl:template match="item">
        <xsl:if test="@name">
            <li>
                <xsl:element name="a">
                    <xsl:attribute name="href">
                        <xsl:value-of select="$baseUrl" /><xsl:value-of select="$URL_SEP" /><xsl:apply-templates select="parent::category"/>_<xsl:number/>
                    </xsl:attribute>
                    <xsl:if test="@autor"><xsl:value-of select="@autor"/>. </xsl:if><xsl:value-of select="@name"/>.
                </xsl:element>
            </li>
        </xsl:if>
    </xsl:template>

    <xsl:template match="category">
        <xsl:number/>
    </xsl:template>
    
</xsl:stylesheet>