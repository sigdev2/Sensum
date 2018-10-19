<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    
    <xsl:template match="menu">
        <xsl:variable name="mode" select="mode"/>
        <xsl:choose>
            <xsl:when test="$mode = span">
                <xsl:element name="span">
                    <xsl:attribute name="class">
                        <xsl:text>spanMenu</xsl:text>
                    </xsl:attribute>
                    <xsl:for-each select="page">
                        <xsl:call-template name="link" />
                    </xsl:for-each>
                </xsl:element>
            </xsl:when>
            <xsl:otherwise><!-- ul -->
                <xsl:element name="ul">
                    <xsl:attribute name="class">
                        <xsl:text>ulMenu</xsl:text>
                    </xsl:attribute>
                    <xsl:for-each select="page">
                        <li>
                            <xsl:call-template name="link" />
                        </li>
                    </xsl:for-each>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

</xsl:stylesheet>