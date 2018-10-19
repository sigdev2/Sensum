/* UTF-8
   Copyright 2010-2018 SigDev

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License. */
             
function htmlspecialchars_decode(string, quote_style) {  
	// http://kevin.vanzonneveld.net  
	//     original by: Mirek Slugen  
	//     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)  
	//     bugfixed by: loonquawl  
	// *     example 1: htmlspecialchars_decode("<p>this -> "</p>", 'ENT_NOQUOTES');  
	// *     returns 1: '<p>this -> "</p>'  
		
	string = string.toString();  
		
	// Always encode  
	string = string.replace(/&amp;/, '&');  
	string = string.replace(/&lt;/g, '<');  
	string = string.replace(/&gt;/g, '>');  
	
	// Encode depending on quote_style  
	if (quote_style == 'ENT_QUOTES') {  
		string = string.replace(/"/g, '"');  
		string = string.replace(/\'/g, '\'');  
	} else if (quote_style != 'ENT_NOQUOTES') {  
		// All other cases (ENT_COMPAT, default, but not ENT_NOQUOTES)  
		string = string.replace(/"/g, '"');  
	}  
		
	return string;  
}

function setMap () 
{
	var map = new YMaps.Map(document.getElementById("YMapsID"));
	//map.enableRuler();
	map.enableScrollZoom();

	map.addControl(new YMaps.Zoom());
	//map.addControl(new YMaps.ScaleLine());

	map.setCenter(new YMaps.GeoPoint(centerPoint["x"], centerPoint["y"]), 10);
	
	// Создает стиль
	var s = new YMaps.Style();
	s.iconStyle = new YMaps.IconStyle();
	s.iconStyle.href = "http://"+gDomain+"/mods/CYandexMap/star.png";
	s.iconStyle.size = new YMaps.Point(19, 24);
	s.iconStyle.offset = new YMaps.Point(-9, -24);
	
    if (pointCount > 1)
    {
    	for (var i = 0; i < pointCount; i++)
    	{
    		var placemark = new YMaps.Placemark(new YMaps.GeoPoint(parseFloat(placemarks[i]["x"]),parseFloat(placemarks[i]["y"])));

    		placemark.name = placemarks[i]["name"];
    		placemark.description = "Я меткА";
    		placemark.metaDataProperty = i;
    		placemark.setStyle(s);
    		
    		YMaps.Events.observe(placemark, placemark.Events.Click, function (obj, ev)
    		{
    			if(document.getElementById('mainText') != null)
    			{
    				document.getElementById('mainText').style.display = 'none';
    			}
    			
    			var textDiv = document.getElementById('labelText');
    			textDiv.style.display = 'inline-block';
    			textDiv.innerHTML = "<h1>"+ htmlspecialchars_decode(placemarks[obj.metaDataProperty]["name"], 'ENT_QUOTES') +"</h1>" +htmlspecialchars_decode(placemarks[obj.metaDataProperty]["text"], 'ENT_QUOTES');
    		});

    		map.addOverlay(placemark);

    	}
    }
    else if (pointCount > 0)
    {
        var placemark = new YMaps.Placemark(new YMaps.GeoPoint(parseFloat(placemarks[i]["x"]),parseFloat(placemarks[i]["y"])), {hasBalloon:false});

        placemark.name = placemarks.name;
        placemark.description = "Я меткА";
        placemark.metaDataProperty = 1;
        placemark.setStyle(s);
        
        YMaps.Events.observe(placemark, placemark.Events.Click, function (obj, ev)
        {
            if(document.getElementById('mainText') != null)
            {
                document.getElementById('mainText').style.display = 'none';
            }
            
            var textDiv = document.getElementById('labelText');
            textDiv.style.display = 'inline-block';
            textDiv.innerHTML = "<h1>"+ htmlspecialchars_decode(placemarks["name"], 'ENT_QUOTES') +"</h1>" +htmlspecialchars_decode(placemarks["text"], 'ENT_QUOTES');
        });

        map.addOverlay(placemark);
    }
}

// Browser detecting
var ua = navigator.userAgent.toLowerCase(); 
var isOpera = (ua.indexOf('opera')  > -1); 
var isIE = (!isOpera && ua.indexOf('msie') > -1); 

function getDocumentHeight() { 
	return Math.max(document.compatMode != 'CSS1Compat' ? document.body.scrollHeight : document.documentElement.scrollHeight, getViewportHeight()); 
}

function getViewportHeight() { 
	return ((document.compatMode || isIE) && !isOpera) ? (document.compatMode == 'CSS1Compat') ? document.documentElement.clientHeight : document.body.clientHeight : (document.parentWindow || document.defaultView).innerHeight; 
}

function showImage(imagePath)
{
	actualWidth = document.body.clientWidth;
	actualHeight = document.body.clientHeight - 100;

	newImage = document.getElementById('fullImage');
	
	newImage.style.width = "auto";
	newImage.style.height = "auto";
	
	newImage.src = imagePath;
	
	newHeight = newImage.height;
	newWidth = newImage.width;
    
    if (newHeight > actualHeight) {
		newHeight = actualHeight;
		newImage.style.height = newHeight + "px";
		newWidth = newWidth * (newHeight / newImage.height);
	} else if (newWidth > actualWidth) {
		newWidth = actualWidth;
		newImage.style.width = newWidth + "px";
		newHeight = newHeight * (newWidth / newImage.width);
	}
	
	imageBox = document.getElementById('fullImgBox');
	
	imageBox.style.top = ((actualHeight - newHeight + 60)/2) + "px";
	imageBox.style.left = ((actualWidth - newWidth)/2) + "px";
	
	docHeight = getDocumentHeight();
	
	document.getElementById('imageDarker').style.width = '100%';
	document.getElementById('imageDarker').style.height = docHeight + "px";
	
	document.getElementById('imageDarker').style.display = 'block';
	document.getElementById('fullImgBox').style.display = 'block';
}

function hideImage() {
	document.getElementById('imageDarker').style.display = 'none';
	document.getElementById('fullImgBox').style.display = 'none';
}