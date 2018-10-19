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
require_once dirname(__FILE__)."/stdstr.php";
require_once dirname(__FILE__)."/stdurl.php";

/////////////////////////////////////////////////////////////////////////////
class dbwrapper
{
    /////////////////////////////////////////////////////////////////////////
    const DEFAULT_CHARSET = "UTF-8";
    const DB_CHARSET = "utf8";
    
    const PDO_DB = "PDO";
    const SQLITE_DB = "SQLiteDatabase";
    
    const CURRENT_DB = self::PDO_DB;
    
    /////////////////////////////////////////////////////////////////////////
    public static function connectSQLite(stdstr $dbname, /*int*/ $access = 0666)
    {
        if (!(self::$dbsi = new SQLiteDatabase($dbname->str(), $access, $sqliteerror)))
        {
            throw new Exception($sqliteerror);
            return false;
        }
        
        return true;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function querySQLite(stdstr $query)
    {
        if (!is_null(self::$dbsi) && !is_null($query))
            return self::$dbsi->query($query->str(self::DEFAULT_CHARSET));
        
        return false;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function execSQLite(stdstr $query)
    {
        if (!is_null(self::$dbsi) && !is_null($query))
            return self::$dbsi->queryExec($query->str(self::DEFAULT_CHARSET));

        return false;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function disconnectSQLite()
    {
        self::$dbsi = null;
        return true;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function connect(stdhost $host, stdstr $login, stdstr $pass)
    {
        if (is_null($host) || is_null($login) || is_null($pass))
            return false;

        switch (self::CURRENT_DB)
        {
            case self::SQLITE_DB:
            {
                if (!(self::$db = sqlite_open('mysqlitedb', 0666, $sqliteerror)))
                {
                    throw new Exception($sqliteerror);
                    return false;
                }
                
                break;
            }
            
            case self::PDO_DB:
            {
                try
                {
                    self::$db = new PDO($host->str(self::DEFAULT_CHARSET),
                        $login->str(self::DEFAULT_CHARSET), $pass->str(self::DEFAULT_CHARSET));
                    self::$db->exec("SET NAMES ".self::DB_CHARSET);
                }
                catch (Exception $e)
                {
                    throw $e;
                    return false;
                }
                
                break;
            }
        }

        return true;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function query(stdstr $query)
    {
        if (!is_null(self::$db) && !is_null($query))
            return self::$db->query($query->str(self::DEFAULT_CHARSET));
        
        return false;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function exec(stdstr $query)
    {
        switch (self::CURRENT_DB)
        {
            case self::SQLITE_DB:
            {
                if (!is_null(self::$db) && !is_null($query))
                    return self::$db->queryExec($query->str(self::DEFAULT_CHARSET));
                
                break;
            }
            
            case self::PDO_DB:
            {
                if (!is_null(self::$db) && !is_null($query))
                    return self::$db->exec($query->str(self::DEFAULT_CHARSET));
                
                break;
            }
        }
        
        return 0;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function disconnect()
    {
        self::$db = null;
        return true;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private static $db = null;
    private static $dbsi = null;
}

/////////////////////////////////////////////////////////////////////////////
?>