<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="content">
        <xsl:for-each select="message">
            <div class="box">
                <div class="boxHeader"><xsl:value-of select="@number"/>. <xsl:value-of select="@nikname"/> - <xsl:value-of select="@datetime"/></div>
                <div class="boxContent"><xsl:value-of select="."/></div>
            </div>
        </xsl:for-each>
    </xsl:template>
</xsl:stylesheet>