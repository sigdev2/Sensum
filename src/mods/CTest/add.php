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

$xml = simplexml_load_file("quest.xml");
$dom_sxe = dom_import_simplexml($xml);
if (!$dom_sxe)
{
    echo "<p>Ошибка конвертирования XML</p>\n";
}
else
{
    $dom = new DOMDocument("1.0", "UTF-8");
    $dom_sxe = $dom->importNode($dom_sxe, true);
    $dom_sxe = $dom->appendChild($dom_sxe);
    
    $item = $dom->createElement("item");
    $n = 0;
    
    while (true)
    {
        $bAlredyExist = false;
        foreach ($xml->item as $xmlItem)
        {
            if ((int)($xmlItem["id"]) == $n)
            {
                $bAlredyExist = true;
                break;
            }
        }
        
        if (!$bAlredyExist)
            break;
            
        $n += 1;
    }
    
    $item->setAttribute("id", $n);
    
    if (isset($_POST["questText"]))
        if ($_POST["questText"] != null)
        {
            $item->setAttribute("text", htmlspecialchars($_POST["questText"]));
        }
        
    if (isset($_POST["answer"]))
        if ($_POST["answer"] != null)
        {
            $item->setAttribute("answer", htmlspecialchars($_POST["answer"]));
        }
    
    for($i = 1; $i <= 5; $i++)
    {
        if (isset($_POST["var".$i]))
            if ($_POST["var".$i] != null)
            {
                $var = $dom->createElement("variant");
                $var->setAttribute("name", "var".$i);
                $var->appendChild($dom->createTextNode(htmlspecialchars($_POST["var".$i])));
                $item->appendChild($var);
            }
    }
        
    if (isset($_POST["questType"]))
        if ($_POST["questType"] != null)
        {
            $item->setAttribute("content", htmlspecialchars($_POST["questType"]));
        }
        
    if (isset($_POST["answerType"]))
        if ($_POST["answerType"] != null)
        {
            $item->setAttribute("type", htmlspecialchars($_POST["answerType"]));
        }
    
    switch ($_POST["questType"])
    {
        case "video":
        {
            if (isset($_POST["video"]))
                if ($_POST["video"] != null)
                {
                    $item->setAttribute("srcVideo", htmlspecialchars($_POST["video"]));
                }
            
            break;
        }
        
        case "img":
        {
            @mkdir("upload", 0777);
            
            $fileName = "./upload/".$_FILES["uploadimgfile"]["name"];
    
            if (is_file($fileName))
            {
                $n = 0;
                while (is_file("./upload/".$n.$_FILES["uploadimgfile"]["name"]))
                    $n += 1;
                
                $fileName = "./upload/".$n.$_FILES["uploadimgfile"]["name"];
            }
            
            if (move_uploaded_file($_FILES["uploadimgfile"]["tmp_name"], $fileName))
            {
                $item->setAttribute("srcImg", $fileName);
                chmod($fileName, 0755);
            }
            
            break;
        }
        
        case "flash":
        {
            @mkdir("upload", 0777);
    
            $fileName = $_SERVER["DOCUMENT_ROOT"]."/upload/".$_FILES["uploadflashfile"]["name"];
            
            if (is_file($fileName))
            {
                $n = 0;
                while (is_file($fileName.$n))
                    $n += 1;
                
                $fileName .= $n;
            }
                
            if (move_uploaded_file($_FILES["uploadflashfile"]["tmp_name"], $fileName))
            {
                $item->setAttribute("srcFlash", $fileName);
                chmod($fileName, 0755);
            }
            
            break;
        }
        
        case "textonly":
        default:
            break;
    }

    $dom_sxe->appendChild($item);
    $dom->formatOutput = true;
    $dom->save("quest.xml");
    
    $item = simplexml_import_dom($item);

    echo "<p>\n Элемент добавлен <hr /><br />\n";
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
            echo "<img src=\"".$item["srcImg"]."\" alt=\"img\" />\n<br />";
            break;
        case "textonly":
        default:
    }
    
    switch($item["type"])
    {
        case "check":
            echo "Варианты (несколько): <br />\n";
            foreach ($item->variant as $var)
            {
                echo "<ul>\n";
                echo "<li><pre>".$var."</pre></li>\n";
                echo "</ul>\n";
            }
            break;
        case "select":
        case "radio":
            echo "Варианты (один из): <br />\n";
            foreach ($item->variant as $var)
            {
                echo "<ul>\n";
                echo "<li><pre>".$var."</pre></li>\n";
                echo "</ul>\n";
            }
            break;
        case "text":
        default:
            echo "Нужно ввести текст. <br />\n";
    }
    echo "Ответ: ".$item["answer"]."<br />\n";
    echo "<br />\n</p>\n";
}
echo "<a href=\"./admin.php\">Назад</a>\n";
?>