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

$res = $dbh->query("SELECT IP, SESSION_ID FROM VISITS WHERE IP = '".$_SERVER["REMOTE_ADDR"]."' AND SESSION_ID = '".session_id()."' ORDER BY DATA DESC LIMIT 1;");

if ($res && $res->rowCount() > 0)
{
    if (!$dbh->exec("UPDATE VISITS SET DATA = '".time()."' WHERE IP = '".$_SERVER["REMOTE_ADDR"]."' AND SESSION_ID = '".session_id()."' ORDER BY DATA DESC LIMIT 1;"))
        echo "Data base error";
}
else
{
    if (!$dbh->exec("INSERT INTO VISITS VALUES('".session_id()."', '".$_SERVER["REMOTE_ADDR"]."', ".time().");"))
        echo "Data base error";
}
?>