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

define("__MAIN__", "");
/////////////////////////////////////////////////////////////////////////////
require_once dirname(__FILE__)."/../src/core/CConfigManager.php";

$cfg = new CConfigManager(new SplFileInfo(dirname(__FILE__)."/../cfg/config.xml"));

$keys = CConfigManager::keys();
for($i = 0; $i < count($keys); $i++)
    echo $keys[$i]." = ".CConfigManager::get($keys[$i])."<br />\n";

echo stddochead::getAsDOMXML()->saveXML()."<br /><br />\n\n";

echo "Moduls: <br />\n";
$iterator = $cfg->getModuls();
if ($iterator)
{
    foreach ($iterator as $modul)
    {
        echo "name - ".$modul->id()."<br />\n";
        echo "class - ".$modul->class_name()."<br /> \n";
        echo "src - ".$modul->src()->getRealPath()."<br />\n";
        echo "xslt - ".$modul->xslt()->getRealPath()."<br /><br />\n\n";
    }
}

echo "Widgets: <br />\n";
$iterator = $cfg->getWidgets();
if ($iterator)
{
    foreach ($iterator as $widget)
    {
        echo "bar name - ".$widget->bar()."<br />\n";
        echo "name - ".$widget->id()."<br />\n";
        echo "class - ".$widget->class_name()."<br /> \n";
        echo "src - ".$widget->src()->getRealPath()."<br />\n";
        echo "xslt - ".$widget->xslt()->getRealPath()."<br /><br />\n\n";
    }
}

echo "Pages file: ".$cfg->getPagesFile()->getRealPath()."<br />\n";
echo "XSLT file: ".$cfg->getXSLTFile()->getRealPath()."<br />\n";
echo "Admin XSLT file: ".$cfg->getXSLTAdminFile()->getRealPath()."<br />\n";

stderr::getFormatedWarnings();
?>