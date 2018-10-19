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
class stddefiner
{    
    /////////////////////////////////////////////////////////////////////////
    public static function is_set($key)
    {
        return defined($key);
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function get($key)
    {
        if (self::is_set($key))
            return constant($key);
        
        return null;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public static function keys()
    {
        return self::$m_defined;
    }
    
    /////////////////////////////////////////////////////////////////////////
    protected static function set($key, $data)
    {
        if (defined($key))
            return false;

        if (define($key, $data, true))
        {
            if (self::$m_defined == false)
                self::$m_defined = new ArrayObject();
            
            self::$m_defined->append($key);
            return true;
        }

        return false;
    }
    
    /////////////////////////////////////////////////////////////////////////
    protected static $m_defined = false;
}

/////////////////////////////////////////////////////////////////////////////
?>