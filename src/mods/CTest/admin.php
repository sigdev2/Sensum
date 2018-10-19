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

if (!$xml)
    $xml->name = "Пусто";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Система онлайн тестирования. Админцентр</title>
    <link rel="stylesheet" type="text/css" href="jsxgraph.css" />
    <script type="text/javascript" src="prototype.js"></script>
    <script type="text/javascript" src="jsxgraphcore.js"></script>

    <?php
        require_once("./dbconnect.php");
    ?>

    <script type="text/javascript">
        function doVisitsGraph()
        {
            var x = [];
            var y = [];
            
            <?php
                $day_time = 24 * 60 * 60;
                $next_time = 0;
                $res = $dbh->query("SELECT DATA FROM VISITS ORDER BY DATA ASC;");
                $count = array();
                $bFirst = true;
                $days = 0;
                if ($res)
                {
                    while ($row = $res->fetch(PDO::FETCH_ASSOC))
                    {   
                        if ($bFirst == true)
                        {
                            $next_time = $row["DATA"] + $day_time;
                            $bFirst = false;
                        }
                        
                        if ($row["DATA"] >= $next_time)
                        {
                            $days++;
                            $count[$days] = 0;
                            $next_time += $day_time;
                        }
                        
                        $count[$days]++;
                    }
                
                    $start = 0;
                    if ($days > 29)
                    {
                        $start = $days - 29;
                        $days = 29;
                    }
                    
                    $max = 0;
                    for ($i = $start; $i <= $days; $i++)
                    {
                        if ($max < $count[$i])
                            $max = $count[$i];
                        echo "y.push(".$count[$i].");";
                        echo "x.push(".$i.");";
                    }                
                }
                
                echo "\n\nvar countX = ".($days + 1).";\n";
                echo "var countY = ".$max.";\n";
            ?>
            
            var visits_box = JXG.JSXGraph.initBoard('visits_box', {boundingbox:[0, countY + 1, countX - 1, 0], showCopyright:false, axis: true});
            
            visits_box.suspendUpdate();
            
            var p;
            var points = [];
            
            points.push(visits_box.createElement('point', [0, 0], {visible:false, name:'', fixed:true})); 
            
            for (i = 0; i < countX; i++)
            {
                // Plot it
                p = visits_box.createElement('point', [x[i], y[i]], 
                                             {strokeWidth:2, strokeColor:'#ffffff', 
                                              highlightStrokeColor:'#0077cc', fillColor:'#0077cc',  
                                              highlightFillColor:'#0077cc', style:6, name:'', fixed:true});
                points.push(p);
            }
            
            points.push(visits_box.createElement('point', [countX - 1, 0], {visible:false, name:'', fixed:true}));
            
            visits_box.createElement('polygon', points, {withLines:false, fillColor:'#e6f2fa'});
         
            visits_box.createElement('curve', [x, y], 
                                     {strokeWidth:3, strokeColor:'#0077cc', 
                                      highlightStrokeColor:'#0077cc'});

            visits_box.unsuspendUpdate();
        }
        
        function testStat()
        {
            var dataArr = [];
            var labels = [];
            var useColors = [];
            var useHColors = [];
            var colors = ['#0F408D','#6F1B75','#CA147A','#DA2228','#E8801B','#FCF302','#8DC922','#15993C','#87CCEE','#0092CE'];
            var highlightColors = ['#E46F6A','#F9DF82','#F7FA7B','#B0D990','#69BF8E','#BDDDE4','#92C2DF','#637CB0','#AB91BC','#EB8EBF'];
            
            <?php
                $res = $dbh->query("SELECT PRCENT, count(*) AS 'COUNT' FROM ANSWERS  GROUP BY PRCENT;");
                if ($res)
                {
                    $i = 0;
                    while ($row = $res->fetch(PDO::FETCH_ASSOC))
                    {
                        echo "dataArr.push(".$row["COUNT"].");";
                        echo "labels.push('".$row["PRCENT"]."% (".$row["COUNT"].")');";
                        if ($i >= 10)
                            $i = 0;
                        echo "useColors.push(colors[".$i."]);";
                        echo "useHColors.push(highlightColors[".$i."]);";
                        $i++;
                    }
                }
                
                echo "\n";
            ?>
            
            var board = JXG.JSXGraph.initBoard('stat_box', {boundingbox:[-2, 10, 12, -5], showNavigation:false, showCopyright:false});
            board.containerObj.style.backgroundColor = 'black';
            board.suspendUpdate();

            var a = board.createElement('chart', dataArr, {chartStyle:'pie',  
                                                           colorArray:useColors,
                                                           fillOpacity:0.9, center:[5, 2], strokeColor:'black', highlightStrokeColor:'black', strokeWidth:4,
                                                           labelArray:labels,
                                                           highlightColorArray:useHColors,
                                                           highlightOnSector:true,
                                                           gradient:'linear'});

            for(var i = 0; i < a[0].arcs.length; i++)
            {
                a[0].arcs[i].highlight = function()
                    {
                        this.board.renderer.highlight(this);
                        if(this.label.content != null)
                        {
                            this.label.content.rendNode.style.fontSize = (2 * this.board.fontSize) + 'px';
                        }
                    
                        var dx = - this.midpoint.coords.usrCoords[1] + this.point2.coords.usrCoords[1];
                        var dy = - this.midpoint.coords.usrCoords[2] + this.point2.coords.usrCoords[2];
                    
                        var ddx = 20 / (this.board.stretchX);
                        var ddy = 20 / (this.board.stretchY);
                        var z = Math.sqrt(dx * dx + dy * dy);
                    
                        this.point2.coords = new JXG.Coords(JXG.COORDS_BY_USER, 
                                                            [this.midpoint.coords.usrCoords[1] + dx * (z + ddx) / z,
                                                             this.midpoint.coords.usrCoords[2] + dy * (z+ddy) / z],
                                                            this.board);
                        this.board.renderer.updateArc(this); 
                    
                        this.board.renderer.updateLine(this.additionalLines[0]);
                    
                        dx = - this.midpoint.coords.usrCoords[1] + this.additionalLines[1].point2.coords.usrCoords[1];
                        dy = - this.midpoint.coords.usrCoords[2] + this.additionalLines[1].point2.coords.usrCoords[2];
                    
                        ddx = 20 / (this.board.stretchX);
                        ddy = 20 / (this.board.stretchY);
                        z = Math.sqrt(dx * dx + dy * dy);            
                        this.additionalLines[1].point2.coords = new JXG.Coords(JXG.COORDS_BY_USER, 
                                                                               [this.midpoint.coords.usrCoords[1] + dx * (z + ddx) / z,
                                                                                this.midpoint.coords.usrCoords[2] + dy * (z + ddy) / z],
                                                                               this.board);
                        this.board.renderer.highlight(this.additionalLines[1]);
                        this.board.renderer.updateLine(this.additionalLines[1]); 
                    };
                  
                a[0].arcs[i].noHighlight = function()
                    {
                        this.board.renderer.noHighlight(this);
                        if(this.label.content != null)
                        {
                            this.label.content.rendNode.style.fontSize = (this.board.fontSize) + 'px';
                        }
                    
                        var dx = -this.midpoint.coords.usrCoords[1] + this.point2.coords.usrCoords[1];
                        var dy = -this.midpoint.coords.usrCoords[2] + this.point2.coords.usrCoords[2];
                    
                        var ddx = 20 / (this.board.stretchX);
                        var ddy = 20 / (this.board.stretchY);
                        var z = Math.sqrt(dx * dx + dy * dy);
                    
                        this.point2.coords = new JXG.Coords(JXG.COORDS_BY_USER, 
                                                            [this.midpoint.coords.usrCoords[1] + dx * (z - ddx) / z,
                                                             this.midpoint.coords.usrCoords[2] + dy * (z - ddy) / z],
                                                            this.board);
                        this.board.renderer.updateArc(this);
                    
                        this.board.renderer.updateLine(this.additionalLines[0]);                                              


                        this.board.renderer.noHighlight(this);
                    
                        dx = -this.midpoint.coords.usrCoords[1] + this.additionalLines[1].point2.coords.usrCoords[1];
                        dy = -this.midpoint.coords.usrCoords[2] + this.additionalLines[1].point2.coords.usrCoords[2];
                    
                        ddx = 20 / (this.board.stretchX);
                        ddy = 20 / (this.board.stretchY);
                        z = Math.sqrt(dx * dx + dy * dy);
                    
                        this.additionalLines[1].point2.coords = new JXG.Coords(JXG.COORDS_BY_USER, 
                                                                               [this.midpoint.coords.usrCoords[1]+dx*(z-ddx)/z,
                                                                                this.midpoint.coords.usrCoords[2]+dy*(z-ddy)/z],
                                                                               this.board);                                               
                        this.board.renderer.updateLine(this.additionalLines[1]); 
                    }; 
            }
            
            board.unsuspendUpdate();
        }

        function delItem(intemId)
        {
            var xmlHttp = new XMLHttpRequest() || new ActiveXObject('Microsoft.XMLHTTP');
            xmlHttp.open("POST", "del.php", true);
            xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8");
            xmlHttp.send("key="+intemId);
            xmlHttp.onreadystatechange = function()
            {
                if (xmlHttp.readyState != 4) return;
                var res = xmlHttp.responseText;
                if (res)
                    document.getElementById(intemId).innerHTML = res;
            }
        }
    </script>
</head>
<body onload="doVisitsGraph(); testStat();">
	<?php
        echo "<h3>".$xml->name.". Администрирование.</h3>";
    ?>
    <div id="cont">
    <?php
        if (sizeof($xml->item) != 0)
        {
            echo "<ol>\n";
            foreach ($xml->item as $item)
            {
                echo "<li id=\"".$item["id"]."\">\n";
                echo "<form name=\"del".$item["id"]."\">\n
                      <input type=\"button\" value=\"Удалить.\" onClick=\"delItem('".$item["id"]."');\"/>\n
                      </form>\n";
                
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
                echo "<br />\n</li>\n";
            }
            echo "</ol>\n<br />\n";
        }
        else
        {
            echo "Нет вопросов\n<br />\n";
        }
    ?>
    </div>
    <p>
    <br />
    <h3>Добавить вопрос</h3>
    <form name="add" action="add.php" method="post" enctype="multipart/form-data">
    Текст вопроса <input type="text" name="questText" size="100" maxlength="1000" /><br />
    Ответ <input type="text" name="answer" size="100" maxlength="1000" /><br /><br />
    Варианты ответа:<br />
    <ul>
    <li>Вариант 1:<br /><textarea rows="10" cols="25" name="var1"></textarea><br /></li>
    <li>Вариант 2:<br /><textarea rows="10" cols="25" name="var2"></textarea><br /></li>
    <li>Вариант 3:<br /><textarea rows="10" cols="25" name="var3"></textarea><br /></li>
    <li>Вариант 4:<br /><textarea rows="10" cols="25" name="var4"></textarea><br /></li>
    <li>Вариант 5:<br /><textarea rows="10" cols="25" name="var5"></textarea><br /></li>
    </ul>
    Тип ответа на вопрос:<br />
    <select name="answerType">
        <option value="check">Несколько ответов</option>
        <option value="radio">Выбор одного ответа переключателем</option>
        <option value="select">Выбор одного ответа выделением</option>
        <option value="text">Текстовое поле</option>
    </select><br /><br />
    Содержание вопроса:<br />
    <select name="questType">
        <option value="textonly">Только текст</option>
        <option value="video">Видео ролик с YouTube</option>
        <option value="flash">Загруженный флеш ролик</option>
        <option value="img">Загруженная картинка</option>
    </select><br />
    Ссылка на видео с YouTube <input type="text" name="video" size="100" maxlength="1000" /><br />
    Адрес загружаемого Флеш ролика <input type="file" name="uploadflashfile" /><br />
    Адрес загружаемой картинки <input type="file" name="uploadimgfile" /><br /><br />
    <input type="submit" value="Добавить"/>
    </form>
    </p>
    <br />
    <p>
        <h3>График посещаймости за последние 30 дней</h3>
    </p>
    <p>
        Показывает зависимость уникальных посетителей от дня.
    </p>
    <p>
        <div id="visits_box" style="width:500px; height:500px;"></div>
    </p>
        <br /><br />
    <p>
        <h3>Статистика ответов</h3>
    </p>
    <p>
        Показывает количество прохождений теста на процент правильных ответов.<br />
    </p>
    <p>
        <div id="stat_box" style="width:500px; height:500px;"></div>
    </p>
	<br />
    <hr />
    <p><strong>&copy;2009 <em>Артемий Николенко</em></strong></p>
</body>
</html>
