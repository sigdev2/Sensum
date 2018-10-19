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

/////////////////////////////////////////////////////////////////////////////
require_once dirname(__FILE__)."/../../src/stdlib/hidesource.php";

/////////////////////////////////////////////////////////////////////////////
class CGuestbook implements IModul
{
    // todo: email to login with ajax and sendmail
    // todo: data base table columns names => class constants
    // todo: date format => stdhelpers::datatime
    /////////////////////////////////////////////////////////////////////////
    const MESSAGE_TAG = "message";
    const MESSAGE_ARG_DATE = "datetime";
    const MESSAGE_ARG_ID = "id";
    const MESSAGE_ARG_NIKNAME = "nikname";
    const MESSAGE_ARG_NUMBER = "number";
    
    const ACT_DEL_MSG = "delMessage";
    const ACT_EDIT_MSG = "editMessage";
    const ACT_ADD_MSG = "addMessage";
    
    const PARAM_MSG_ID = "messageId";
    const PARAM_TEXT = "message";
    
    const XSLT_FILE_NAME = "tpl.xsl";
    const ADMIN_XSLT_FILE_NAME = "admintpl.xsl";
    const GUEST_XSLT_FILE_NAME = "guesttpl.xsl";
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct()
    {
        dbwrapper::connectSQLite(stdstr::toStr("guestbook"));
        $res = dbwrapper::querySQLite(stdstr::toStr("SELECT count(name) as 'num' FROM sqlite_master where name='GB_MESSAGES'"));
        $res = $res->fetchAll();
        
        if ($res[0]["num"] <= 0)
        {
            dbwrapper::execSQLite(stdstr::toStr("CREATE TABLE GB_MESSAGES (M_NO INTEGER PRIMARY KEY, MSG_TEXT, U_NO, M_TIME);"));
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function init(CCurrentPage $page)
    {
        $user = CUsersManager::getCurrUser();
        $this->m_isAdmin = $page->isAdmin();
        $this->m_isGuest = $user->isGuest();
        
        $this->m_content = stddomxml::create();
        $this->m_content->loadXML("<".self::CONTENT_TAG_NAME."/>");
        
        if (!$this->m_isGuest)
        {
            $message = stdtext::toText($_POST[self::PARAM_TEXT]);
  
            if ($this->m_isAdmin)
            {
                $id = (int) $_POST[self::PARAM_MSG_ID];
                
                $res = dbwrapper::querySQLite(stdstr::toStr("SELECT M_NO FROM GB_MESSAGES WHERE M_NO = ".$id.";"));

                if ((bool)$res)
                {
                    if ((bool)$_POST[self::ACT_DEL_MSG])
                    {
                        dbwrapper::querySQLite(stdstr::toStr("DELETE FROM GB_MESSAGES WHERE M_NO = ".$id.";"));
                    }
                    else if ((bool)$_POST[self::ACT_EDIT_MSG])
                    {
                        if (!$message->isEmpty())
                            dbwrapper::querySQLite(stdstr::toStr("UPDATE GB_MESSAGES SET MSG_TEXT = '".$message."' WHERE M_NO = ".$id.";"));
                    }
                }
            }
            
            if ((bool)$_POST[self::ACT_ADD_MSG])
            {
                dbwrapper::querySQLite(stdstr::toStr("INSERT INTO GB_MESSAGES VALUES(0, '".$message."', ".(int) $user->id().", ".time().");"));
            }
        }
        
        $res = dbwrapper::querySQLite(stdstr::toStr("SELECT U_NO, M_NO, MSG_TEXT, M_TIME FROM GB_MESSAGES"));

        if (!(bool)$res)
            return;
        
        $resMySQL = dbwrapper::query(stdstr::toStr("SELECT U_NO, NIKNAME FROM USERS"));
        
        if (!(bool)$resMySQL || $resMySQL->rowCount() <= 0)
            return;
        
        $users = new ArrayObject($resMySQL->fetchAll());
        $messages = new ArrayObject($res->fetchAll());
        $messgesCount = sizeof($messages);
        for ($i = $messgesCount - 1; $i >= 0; $i--)
        {
            $msgTag = $this->m_content->createElement(self::MESSAGE_TAG, $messages[$i]["MSG_TEXT"]);
            $msgTag->setAttribute(self::MESSAGE_ARG_DATE, date("l dS of F Y h:i:s A", $messages[$i]["M_TIME"]));
            $msgTag->setAttribute(self::MESSAGE_ARG_NUMBER, $i);
            $msgTag->setAttribute(self::MESSAGE_ARG_ID, $messages[$i]["M_NO"]);
            
            foreach ($users as $user)
            {
                if ($user["U_NO"] == $messages[$i]["U_NO"])
                {
                    $msgTag->setAttribute(self::MESSAGE_ARG_NIKNAME , $user["NIKNAME"]);
                    break;
                }
            }
            
            $this->m_content->documentElement->appendChild($msgTag);
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getAsDOMXML()
    {
        return $this->m_content;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getXSLT()
    {
        if ($this->m_isAdmin)
            return stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/".self::ADMIN_XSLT_FILE_NAME));
        else if ($this->m_isGuest)
            return stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/".self::GUEST_XSLT_FILE_NAME));
        else
            return stddomxml::fromFile(stdfile::info(dirname(__FILE__)."/".self::XSLT_FILE_NAME));
    }

    /////////////////////////////////////////////////////////////////////////
    private $m_content = null;
    private $m_isAdmin = false;
    private $m_isGuest = true;
}

/////////////////////////////////////////////////////////////////////////////
?>