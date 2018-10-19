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
$xml = simplexml_load_file("quest.xml");

if ($_POST["key"] != "" && $_POST["key"] != null && sizeof($xml->item) != 0)
{
    $bEdited = false;
    for ($i = 0; $i < sizeof($xml->item); $i++)
    {
        $item = $xml->item[$i];
        if ((string)$item["id"] == htmlspecialchars($_POST["key"]))
        {
            if (is_file($xml->item[$i]["srcFlash"]))
                unlink($xml->item[$i]["srcFlash"]);
                
            if (is_file($xml->item[$i]["srcImg"]))
                unlink($xml->item[$i]["srcImg"]);
            
            unset($xml->item[$i]);
            $i -= 1;
            $bEdited = true;
            
            if (sizeof($xml->item) == 0)
                break;
        }
    }
    
    if ($bEdited)
    {
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
            $dom->formatOutput = true;
            $dom->save("quest.xml");
        }
        
        echo "<p>Вопрос успешно удалён</p>\n";
    }
}
?>
