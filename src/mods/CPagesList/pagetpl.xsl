<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="content">
        <xsl:copy-of select="page/*" />
        <xsl:element name="a">
             <xsl:attribute name="href">
                 <xsl:value-of select="$baseUrl" />
             </xsl:attribute>
             <br />
             <xsl:text><![CDATA[<<<]]> вернуться к списку</xsl:text>.
         </xsl:element>
    </xsl:template>
</xsl:stylesheet>