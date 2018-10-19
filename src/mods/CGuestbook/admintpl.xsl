<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="content">
        <xsl:for-each select="message">
            <div class="box">
                <form action="{$baseUrl}" method="post">
                    <input type="hidden" name="messageId" value="{@id}"/>
                    <div class="boxHeader">
                        <xsl:value-of select="@number"/>. <xsl:value-of select="@nikname"/> - <xsl:value-of select="@datetime"/>
                        <div class="boxClose">
                           <input name="delMessage" value="X" type="submit" />
                        </div>
                    </div>
                    <div class="boxContent">
                        <textarea name="message" rows="10" cols="60" class="text ui-widget-content ui-corner-all">
                            <xsl:value-of select="."/>
                        </textarea>
                        <br />
                        <input name="editMessage" value="Изменить" type="submit" />
                    </div>
                </form>
            </div>
        </xsl:for-each>
        <br />
        <hr />
        <form action="{$baseUrl}" method="post">
            <textarea name="message" rows="10" cols="60" class="text ui-widget-content ui-corner-all">
            </textarea>
            <input name="addMessage" value="Добавить" type="submit" />
        </form>
    </xsl:template>
</xsl:stylesheet>