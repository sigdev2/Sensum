<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="content">
        <xsl:if test="message"><b><xsl:value-of select="message"/></b><br /></xsl:if>
        <form action="{$baseUrl}" method="post">
            <label for="string_list_description_edit">Дескрипшен (150): </label>
            <br />
            <input id="string_list_description_edit" size="101" maxlength="150" name="description" value="{root/description}" class="text ui-widget-content ui-corner-all"/><br />
            <label for="string_list_keywords_edit">Ключевые слова (200): </label>
            <br />
            <input id="string_list_keywords_edit" size="95" maxlength="150" name="keywords" value="{root/keywords}" class="text ui-widget-content ui-corner-all"/><br />
            <p>
                <label for="string_list_text_area">Текст: </label>
                <br />
                <textarea id="string_list_text_area" name="content" rows="50" cols="80" class="text ui-widget-content ui-corner-all">
                    <xsl:copy-of select="root/content/*" />
                </textarea>
            </p>
            <p><input name="edit" class="button" value="Изменить" type="submit" /></p>
        </form>
    </xsl:template>
    
</xsl:stylesheet>