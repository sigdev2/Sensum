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

session_start();

if ($_POST["act"] == "validate")
{
    header("HTTP/1.x 200 OK");
    header("Content-Type: text/html;charset=utf-8");

    $bIsNotBot = "false";
    if (isset($_POST["capcha"]) && 
        isset($_SESSION["capcha"]) &&
        $_POST["capcha"] != "" && $_POST["capcha"] != null &&
        $_SESSION["capcha"] != "" && $_SESSION["capcha"] != null)
        if ($_POST["capcha"] == $_SESSION["capcha"])
        {
            $bIsNotBot = "true";
        }

    echo $bIsNotBot;
    exit;
}

$symbols = "QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890";

$nums = 5;
$linecount = 3;
$key = "";

for ($i = 0; $i < $nums; $i++)
{
    $key .= $symbols[rand(0,strlen($symbols) - 1)];
}

$_SESSION["capcha"] = md5($key);

$imgw = 40 + 20 * $nums;
$imgh = 60;
$image = imagecreate($imgw, $imgh);
imagecolorallocate($image, 255, 255, 255);

$x = 0; 
for ($i = 0; $i < $nums; $i++)
{
    $color = imagecolorallocate($image, rand(0, 254), rand(0, 254), rand(0,254));

    $x += 20;
    $y = rand($imgh/2 - 5, $imgh/2 + 15);
    $angle = rand(-40, 40);
    $fontsize = rand(16, 22);

    imagettftext($image, $fontsize, $angle, $x, $y, $color, "./Abagail Regular.ttf", $key[$i]);
}

for ($i = 0; $i < $linecount; $i++)
{
    $color = imagecolorallocate($image, rand(0, 254), rand(0, 254), rand(0,254));
    imageline($image, rand(0, $imgw), rand(0, $imgh), rand(0, $imgw), rand(0, $imgh), $color);
}

header("Content-type: image/jpeg");
imagejpeg($image, '', 100);
?>