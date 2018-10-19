<?php
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

header("HTTP/1.x 200 OK");
header("Content-Type: text/html;charset=utf-8");
session_start();

require_once("./stat.php");

$xml = simplexml_load_file("quest.xml");

if (!$xml)
    $xml->name = "Пусто";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Система онлайн тестирования (на основе HTML, AJAX, PHP, XML и MySQL)</title>
    <script type="text/javascript" src="md5.js"></script>
    <script>
        function isChecked(cbName)
        {
            var cb = document.forms["test"][cbName];
            if (cb.checked)
                return "yes";
            return "no";
        }

        function getRadioGroupValue(rgName)
        {
          var rg = document.forms["test"][rgName];
          for (var i=0; i < rg.length; i++)
            if (rg[i].checked) return rg[i].value;

          return null;
        }

        function sendAnswer()
        {
            var xmlHttp = new XMLHttpRequest() || new ActiveXObject('Microsoft.XMLHTTP');
            xmlHttp.open("POST", "answer.php", true);
            xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8");
            var a = "";
            <?php
            if (sizeof($xml->item) != 0)
            {
                foreach ($xml->item as $item)
                {
                    switch($item["type"])
                    {
                        case "check":
                            foreach ($item->variant as $var)
                            {
                                echo "a += \"".$item["id"].$var["name"]."=\"+encodeURI(isChecked(\"cb".$item["id"].$var["name"]."\"))+\"&\";\n";
                            }
                            break;
                        case "radio":
                            echo "a += \"".$item["id"]."=\"+encodeURI(getRadioGroupValue(\"rb".$item["id"]."\"))+\"&\";\n";
                            break;
                        case "select":
                            echo "a += \"".$item["id"]."=\"+encodeURI(document.forms[\"test\"].s".$item["id"].".options[document.forms[\"test\"].s".$item["id"].".selectedIndex].value)+\"&\";\n";
                            break;
                        case "text":
                        default:
                            echo "a += \"".$item["id"]."=\"+encodeURI(document.forms[\"test\"].t".$item["id"].".value)+\"&\";\n";
                    }
                }
                echo "a += \"capcha=\"+encodeURIComponent(document.forms[\"test\"].capcha.value);\n";
            }
?>

            xmlHttp.send(a);
            xmlHttp.onreadystatechange = function()
            {
                if (xmlHttp.readyState != 4) return;
                document.getElementById("cont").innerHTML = xmlHttp.responseText;
            }
        }
        
        function update()
        {
            document.getElementById("capcha").src="./capcha.php?now="+escape(new Date());
            document.getElementById("validate").innerHTML = "";
            return false;
        }
        
        var tryCount = 0;
        function validate()
        {
            document.getElementById("validate").innerHTML = "Ждите, идёт проверка кода ...";
            var xmlHttp = new XMLHttpRequest() || new ActiveXObject('Microsoft.XMLHTTP');
            xmlHttp.open("POST", "capcha.php", true);
            xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8");
            xmlHttp.send("act=validate&capcha="+md5(encodeURIComponent(document.forms["test"].capcha.value)));
            xmlHttp.onreadystatechange = function()
            {
                if (xmlHttp.readyState != 4) return;
                var res = xmlHttp.responseText;
                if (res == "true")
                {
                    document.getElementById("validate").innerHTML = "Обработка ответов теста ...";
                    sendAnswer();
                }
                else
                {
                    tryCount += 1;
                    document.getElementById("validate").innerHTML = "Неверный код подтверждения, попробуйте ещё раз !!! Попыток: "+tryCount;
                }
            }
        }
    </script>
</head>
<body>
    <?php
        echo "<h3>".$xml->name."</h3>";
    ?>
    <div id="cont">
    <?php
    if (sizeof($xml->item) != 0)
    {
        echo "<form name=\"test\">\n<ol>\n";
        foreach ($xml->item as $item)
        {
            echo "<li>\n";
            echo $item["text"]." <br /><br />\n";
            switch($item["content"])
            {
                case "video":
                    echo "<object width=\"425\" height=\"344\">\n
    <param name=\"movie\" value=\"".$item["srcVideo"]."\" />\n
    <param name=\"allowscriptaccess\" value=\"always\" />\n
    <embed src=\"".$item["srcVideo"]."\" type=\"application/x-shockwave-flash\" width=\"425\" height=\"355\"></embed>\n
    </object>\n<br />\n";
                    break;
                case "flash":
                    echo "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"
    codebase=\"http://download.macromedia.com/pub/shockwave/cabs/
    flash/swflash.cab#3,0,0,0\" width=\"425\" height=\"344\">\n
    <param name=\"SRC\" value=\"".$item["srcFlash"]."\">\n
    <embed src=\"".$item["srcFlash"]."\" pluginspage=\"http://www.macromedia.com/
    shockwave/download/\" type=\"application/x-shockwave-flash\"
    width=\"425\" height=\"344\">\n
    </embed>\n
    </object>\n<br />\n";
                    break;
                case "img":
                    echo "<img src=\"".$item["srcImg"]."\" alt=\"image\" />\n<br />";
                    break;
                case "textonly":
                default:
            }
            echo "Ваш ответ: <br /><br />\n";
            switch($item["type"])
            {
                case "check":
                    foreach ($item->variant as $var)
                    {
                        echo "<pre><input type=\"checkbox\" name=\"cb".$item["id"].$var["name"]."\" value=\"yes\" /> ".$var."</pre>\n";
                    }
                    break;
                case "radio":
                    foreach ($item->variant as $var)
                    {
                        echo "<pre><input type=\"radio\" name=\"rb".$item["id"]."\" value=\"".$var["name"]."\" /> ".$var."</pre>\n";
                    }
                    break;
                case "select":
                    echo "<select name=\"s".$item["id"]."\">\n";
                    foreach ($item->variant as $var)
                    {
                        echo "<option value=\"".$var["name"]."\">".$var."</option>\n";
                    }
                    echo "</select>\n<br />\n";
                    break;
                case "text":
                default:
                    echo "<input type=\"text\" name=\"t".$item["id"]."\" size=\"100\" maxlength=\"1000\" /><br />\n";
            }
            echo "<br />\n</li>\n";
        }
        echo "</ol>\n";
        echo "<p>Введите код подтверждения: <br />\n";
        echo "<img src=\"./capcha.php\" alt=\"capcha\" id=\"capcha\"/><br />\n";
        echo "<input type=\"text\" name=\"capcha\" size=\"20\" maxlength=\"5\" /><br />\n";
        echo "Если символы не читабельны, то попробуйте <input type=\"button\" value=\"Обновить\" onClick=\"update();\"/><br />\n";
        echo "<div id=\"validate\" style=\"font-weight:bold;\"></div>";
        echo "</p>\n";
        echo "<input type=\"button\" value=\"Ответить\" onClick=\"validate();\"/>\n";
        echo "</form>\n";
    }
    else
    {
        echo "Нет вопросов\n<br />\n";
    }
    ?>
    </div>
	<br />
    <hr />
    <p><strong>&copy;2009 <em>Артемий Николенко</em></strong></p>
</body>
</html>