<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="content">
        <script src="/mods/CYandexMap/core.js" type="text/javascript"></script>
        <script src="http://api-maps.yandex.ru/1.1/index.xml?key=ANMol0wBAAAAdii_HgQA1gHTtwMjZZYrkn3SbQjVz7l4-P4AAAAAAAAAAAC5h-gZ2Bb5JMipfKR2k5y7S69RSw==" type="text/javascript"></script>
        <xsl:copy-of select="./jsonMapData/*" />
        <script type="text/javascript">
			$(function() {
				$(".galleryList").jCarouselLite({
					btnNext: ".next",
					btnPrev: ".prev"
				});
			});
		</script>
        <script type="text/javascript">
		window.onload = function () {
		setMap();
		}
		</script>
        <div class='darker' id='imageDarker' style='display:none;'>
		</div>
		<div class='fullImgBox' id='fullImgBox' style='display:none;'>
			<input type='button' onclick='hideImage()' />
			<img src='' id='fullImage' onclick='hideImage()' />
		</div>
		<div class='wrapper'>
    		<div class='header'>
        		<a href='/'>
                    <xsl:copy-of select="./title/*" />
        		</a>
            </div>
            <div class='middle'>
				<div class='topPanel'>
				    <xsl:copy-of select="./info/*" />
                    <xsl:if test="./gallery">
                        <div class='galleryBox'>
                            <button class="prev"></button>
                            <div class="galleryList">
                                <ul>
                                    <xsl:for-each select="./gallery/img">
                                        <li><img src="{.}" onclick="showImage(this.src)" alt="{@alt}" width='125' height='100'/></li>
                                    </xsl:for-each>
                                </ul>
                            </div>
                            <button class="next"></button>
                        </div>
                    </xsl:if>
				</div>
				<div class='areaContainer' style='z-index:1;'>
					<a href='' id='areaDescLink'>Описание района</a>
					<div class='leftCol'>
                        <div class='areaMapBox'>
                            <div id="YMapsID"></div>
                        </div>
                        <xsl:copy-of select="./media/*" />
                    </div>
					<div id='mainText'>
						<xsl:copy-of select="./text/*" />
					</div>
					<div id='labelText'>
					</div>
				</div>
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>