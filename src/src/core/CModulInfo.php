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
require_once dirname(__FILE__)."/../stdlib/hidesource.php";

/////////////////////////////////////////////////////////////////////////////
class CModulInfo
{
    /////////////////////////////////////////////////////////////////////////
    public function __construct(stdtext $id, stdname $class, SplFileInfo $src)
    {
        $this->m_id = $id;
        $this->m_class = $class;
        $this->m_src = $src;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function id()
    {
        return $this->m_id;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function class_name()
    {
        return $this->m_class;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function src()
    {
        return $this->m_src;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function isValid()
    {
          
         return !is_null($this->m_id) &&
                !is_null($this->m_class) &&
                !is_null($this->m_src) &&
                !$this->m_id->isEmpty() &&
                !$this->m_class->isEmpty() &&
                $this->m_src->isFile();
    }
    
    /////////////////////////////////////////////////////////////////////////
    protected $m_id = null;
    protected $m_class = null;
    protected $m_src = null;
}

/////////////////////////////////////////////////////////////////////////////
?>