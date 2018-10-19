<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="xml" version="1.0" encoding="UTF-8"
        doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN"
        doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"/>
    
    <!-- Template -->
    <xsl:template match="/">
        <xsl:apply-templates/>
    </xsl:template>
    
    <xsl:template match="root">
        <xsl:choose>
            <xsl:when test="content">
              <xsl:apply-templates select="content"/>
            </xsl:when>
            <xsl:otherwise>
              <xsl:text>Здесь ничего нет</xsl:text>
            </xsl:otherwise>
          </xsl:choose>
          
          <xsl:call-template name="leftBar"/>
    </xsl:template>
    <!-- /Template -->

</xsl:stylesheet>