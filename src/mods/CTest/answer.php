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

require_once("./dbconnect.php");
header("HTTP/1.x 200 OK");
header("Content-Type: text/html;charset=utf-8");
session_start();
 
$bIsNotBot = false;
if (isset($_POST["capcha"]) && 
    isset($_SESSION["capcha"]) &&
    $_POST["capcha"] != "" && $_POST["capcha"] != null &&
    $_SESSION["capcha"] != "" && $_SESSION["capcha"] != null)
    if (md5($_POST["capcha"]) == $_SESSION["capcha"])
    {
        $bIsNotBot = true;
    }

$xml = simplexml_load_file("quest.xml");

if ($bIsNotBot)
{        
    $questCount = 0;
    $rightCount = 0;
    foreach ($xml->item as $item)
    {
        $questCount++;
        switch($item["type"])
        {
            case "check":
            {
                $str = "";
                foreach ($item->variant as $var)
                {
                    if ($str != "")
                        $str .= " ";
                    
                    if (isset($_POST[(string)($item["id"].$var["name"])]))
                    {
                        $str .= htmlspecialchars($_POST[(string)($item["id"].$var["name"])]);
                    }
                }
                
                if ((string)$item["answer"] == $str)
                {
                    $rightCount++;
                }
                
                break;
            }
            case "radio":
            case "select":
            {
                $str = "";
                if (isset($_POST[(string)$item["id"]]))
                {
                    $str = htmlspecialchars($_POST[(string)$item["id"]]);
                    foreach ($item->variant as $var)
                    {
                        if ((string)$var["name"] == $str)
                        {
                            $str = (string)$var;
                            break;
                        }
                    }
                }
                
                if ((string)$item["answer"] == $str)
                {
                    $rightCount++;
                }
                
                break;
            }
            
            case "text":
            default:
            {
                $str = "";
                if (isset($_POST[(string)$item["id"]]))
                {
                    $str = htmlspecialchars($_POST[(string)$item["id"]]);
                }
                if ((string)$item["answer"] == $str)
                {
                    $rightCount++;
                }
            }
        }
    }
    
    $procent = round(($rightCount / $questCount) * 100);
    echo "<p>\n";
    echo "Ваша статистика:<br />\n";
    echo "Всего вопросов - ".$questCount."<br />\n";
    echo "Правильных ответов - ".$rightCount."<br />\n";
    echo "Процент правильных ответов - ".$procent."%<br />\n";
    echo "<br />\n";
    $value = "неуд.";
    
    if ($procent > 65)
    {
       $value = "уд.";
    }
    
    if ($procent > 80)
    {
       $value = "хор.";
    }
    
    if ($procent > 98)
    {
       $value = "отл.";
    }
    
    echo "Оценка: <b>".$value."</b>.<br />\n<a href=\"javascript:window.location.reload(true)\">Назад</a>\n";
    
    echo "</p>\n";
    
    if (!$dbh->exec("INSERT INTO ANSWERS VALUES(".time().", ".$procent.");"))
        echo "Data base error";
}
else
{
    echo "<p>Неверный код подтверждения.<br />\n<a href=\"javascript:window.location.reload(true)\">Назад</a></p>";
}
unset($_SESSION["capcha"]);
?>
