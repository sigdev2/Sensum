<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="xml" version="1.0" encoding="UTF-8"
        doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN"
        doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"/>
    
    <xsl:variable name="URL_SEP">
        <xsl:value-of select="/root/siteInfo/url_sep" />
    </xsl:variable>
    
    <xsl:variable name="domain">
        <xsl:value-of select="/root/siteInfo/host" />
    </xsl:variable>
    
    <xsl:variable name="baseUrl">
        <xsl:value-of select="/root/url/mainURL" />
    </xsl:variable>
    
    <xsl:variable name="currPageName">
        <xsl:value-of select="/root/url/id"/>
    </xsl:variable>
    
    <xsl:template name="option">
        <xsl:param name="currDis">1</xsl:param>
        <xsl:param name="currValue">none</xsl:param>
        <xsl:param name="value">none</xsl:param>
        <xsl:param name="name" />
        <xsl:element name="option">
            <xsl:if test="$currValue = $value">
                <xsl:if test="$currDis = 1">
                    <xsl:attribute name="disabled">true</xsl:attribute>
                </xsl:if>
                <xsl:attribute name="selected">true</xsl:attribute>
            </xsl:if>
            <xsl:attribute name="value">
                <xsl:value-of select="$value"/>
            </xsl:attribute>
            <xsl:value-of select="$name"/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template name="boolChoos">
        <xsl:param name="falseVar"/>
        <xsl:param name="trueVar"/>
        <xsl:param name="name"/>
        <xsl:param name="currValue">0</xsl:param>
        <xsl:element name="input">
            <xsl:attribute name="type">radio</xsl:attribute>
            <xsl:attribute name="name"><xsl:value-of select="$name" /></xsl:attribute>
            <xsl:attribute name="value">1</xsl:attribute>
            <xsl:attribute name="class">ui-widget-content ui-corner-all</xsl:attribute>
            <xsl:attribute name="style">float: left;</xsl:attribute>
            <xsl:if test="$currValue = 1">
                <xsl:attribute name="checked"/>
            </xsl:if>
        </xsl:element> <xsl:value-of select="$trueVar" />
        <br />
        <xsl:element name="input">
            <xsl:attribute name="type">radio</xsl:attribute>
            <xsl:attribute name="name"><xsl:value-of select="$name" /></xsl:attribute>
            <xsl:attribute name="value">0</xsl:attribute>
            <xsl:attribute name="class">ui-widget-content ui-corner-all</xsl:attribute>
            <xsl:attribute name="style">float: left;</xsl:attribute>
            <xsl:if test="$currValue = 0">
                <xsl:attribute name="checked"/>
            </xsl:if>
        </xsl:element> <xsl:value-of select="$falseVar" />
    </xsl:template>
    
    <xsl:template name="argLink">
        <xsl:param name="href">#</xsl:param>
        <xsl:param name="rel">none</xsl:param>
        <xsl:param name="name">
            <xsl:number/>
        </xsl:param>
        <xsl:param name="current">0</xsl:param>
        <xsl:param name="target">none</xsl:param>
        <xsl:element name="a">
            <xsl:attribute name="href">
                <xsl:if test="not(starts-with($href, 'http://'))">
                    <xsl:text>http://</xsl:text>
                    <xsl:value-of select="$domain"/>
                </xsl:if>
                <xsl:value-of select="$href"/>
            </xsl:attribute>
            <xsl:if test="not($rel = 'none')">
                <xsl:attribute name="rel">
                    <xsl:value-of select="$rel"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:if test="not($current = 0)">
                <xsl:attribute name="class">
                    <xsl:text>currentLink</xsl:text>
                </xsl:attribute>
            </xsl:if>
            <xsl:if test="not($target = 'none')">
                <xsl:attribute name="target">
                    <xsl:value-of select="$target"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:value-of select="$name"/>
        </xsl:element>
    </xsl:template>

    <xsl:template name="link">
        <xsl:call-template name="argLink">
            <xsl:with-param name="href">
                <xsl:choose>
                    <xsl:when test="url[position() = 1]/@id">
                        <xsl:value-of select="url[position() = 1]/@id"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text>#</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:with-param>
            <xsl:with-param name="name">
                <xsl:value-of select="@id"/>
            </xsl:with-param>
            <xsl:with-param name="current">
                <xsl:choose>
                    <xsl:when test="url[position() = 0] = $baseUrl">
                        <xsl:text>1</xsl:text>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text>0</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:with-param>
            <xsl:with-param name="target">
                <xsl:choose>
                    <xsl:when test="@target">
                        <xsl:value-of select="@target"/>
                    </xsl:when>
                    <xsl:otherwise>none</xsl:otherwise>
                </xsl:choose>
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>
    
    <!-- Template -->
    <xsl:template match="/">
        <xsl:apply-templates/>
    </xsl:template>
    
    <xsl:template match="root">
        <html>
            <head>
                <title><xsl:value-of select="head/title" /></title>
                <link type="text/css" href="./js/themes/base/jquery.ui.all.css" rel="stylesheet" />
                <script type="text/javascript" src="./js/jquery-1.4.2.js"></script>
                <script type="text/javascript" src="./js/external/jquery.bgiframe-2.1.1.js"></script>
                <script type="text/javascript" src="./js/ui/jquery.ui.core.js"></script>

                <script type="text/javascript" src="./js/ui/jquery.ui.widget.js"></script>
                <script type="text/javascript" src="./js/ui/jquery.ui.mouse.js"></script>
                <script type="text/javascript" src="./js/ui/jquery.ui.button.js"></script>
                <script type="text/javascript" src="./js/ui/jquery.ui.draggable.js"></script>
                <script type="text/javascript" src="./js/ui/jquery.ui.position.js"></script>
                <script type="text/javascript" src="./js/ui/jquery.ui.resizable.js"></script>
                <script type="text/javascript" src="./js/ui/jquery.ui.tabs.js"></script>

                <script type="text/javascript" src="./js/ui/jquery.ui.dialog.js"></script>
                <script type="text/javascript" src="./js/ui/jquery.effects.core.js"></script>
                <link type="text/css" href="./js/themes/jquery-theme-patch.css" rel="stylesheet" />
                <script>
                    var gDomain = '<xsl:value-of select="$domain" />';
                    $(function(){
                        $(".button").button();
                    });
                </script>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />  
                <meta name="Description" content="{head/description}"/>  
                <meta name="Keywords" content="{head/keywords}"/>
                <link type="text/css" href="./tpl/example/example.css" rel="stylesheet" />
                <link type="text/css" href="./tpl/example/catalog.css" rel="stylesheet" />
            </head>
            <body>
                <div id="centre">
                    <table cellpadding="0" cellspacing="0" class="mainTable">
                        <tr>
                            <td id="topPan" colspan="2">
                                <div id="logo"> 
                                </div>
                                <div id="logo_text"> 
                                </div>
                                <br />
                                <br />
                                <br />
                                <div id="topMenu">
                                    <xsl:call-template name="topBar"/>
                                </div>
                                <div id="line">
                                </div>
                            </td>
                        </tr>                     
                        <tr>
                            <td id="leftPan">
                                <h3>Каталог</h3>
                                <xsl:call-template name="leftBar"/>
                            </td>
                            <td id="rightPan">
                                <div class="contentLine">
                                    <h3>
                                        <xsl:variable name="catalogURL" select="/root/widgets/leftBar/catalogList/catalogURL" />
                                        <xsl:choose>
                                            <xsl:when test="boolean(/root/widgets/leftBar/catalogList/category) and (number(/root/widgets/leftBar/catalogList/category/@current) = 1)">
                                                <xsl:variable name="cnum" select="/root/widgets/leftBar/catalogList/category[number(@current) = 1]/@num" />
                                                <xsl:choose>
                                                    <xsl:when test="boolean(/root/widgets/leftBar/catalogList/category/group) and (number(/root/widgets/leftBar/catalogList/category/group/@current) = 1)">
                                                      <xsl:variable name="gnum" select="/root/widgets/leftBar/catalogList/category[number(@current) = 1]/group[number(@current) = 1]/@num" />
                                                      <xsl:choose>
                                                        <xsl:when test="boolean(/root/widgets/leftBar/catalogList/category/group/subgroup) and (number(/root/widgets/leftBar/catalogList/category/group/subgroup/@current) = 1)">
                                                          <xsl:variable name="sgnum" select="/root/widgets/leftBar/catalogList/category[number(@current) = 1]/group[number(@current) = 1]/subgroup[number(@current) = 1]/@num" />
                                                          <a href="{$catalogURL}{$URL_SEP}{$cnum}_{$gnum}_{$sgnum}_0"><xsl:value-of select="/root/widgets/leftBar/catalogList/category[number(@current) = 1]/group[number(@current) = 1]/subgroup[number(@current) = 1]/@name" /></a>
                                                        </xsl:when>
                                                        <xsl:otherwise>
                                                          <a href="{$catalogURL}{$URL_SEP}{$cnum}_{$gnum}_0"><xsl:value-of select="/root/widgets/leftBar/catalogList/category[number(@current) = 1]/group[number(@current) = 1]/@name" /></a>
                                                        </xsl:otherwise>
                                                      </xsl:choose>
                                                    </xsl:when>
                                                    <xsl:otherwise>
                                                      <a href="{$catalogURL}{$URL_SEP}{$cnum}_0"><xsl:value-of select="/root/widgets/leftBar/catalogList/category[number(@current) = 1]/@name" /></a>
                                                    </xsl:otherwise>
                                                </xsl:choose>
                                            </xsl:when>
                                            <xsl:otherwise>
                                              <a href="{$catalogURL}"><xsl:value-of select="$currPageName" /></a>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </h3>
                                </div>
                                <xsl:choose>
                                    <xsl:when test="content">
                                      <xsl:apply-templates select="content"/>
                                    </xsl:when>
                                    <xsl:otherwise>
                                      <xsl:text>Здесь ничего нет</xsl:text>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <br />
                            </td>
                         </tr> 
                         <tr>
                             <td>
                             </td>
                             <td>
                                <div id="line1">
                                </div>
                                <br />
                             </td>
                        </tr>
                        <tr>
                            <td colspan="2" id="contact">
                                Контакты<br /> т.телефон e-mail: почта
                                <div id="foter">
                                    <xsl:call-template name="buttomBar"/>
                                    <xsl:call-template name="loginBar"/>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>  
            </body>
        </html>
    </xsl:template>
    <!-- /Template -->

</xsl:stylesheet>
