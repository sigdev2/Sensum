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
class CWidgetInfo extends CModulInfo
{
    /////////////////////////////////////////////////////////////////////////
    public function __construct(stdtext $id, stdname $class, SplFileInfo $src,
        /*int*/ $access, /*int*/ $admin, stdname $bar, stdparam $options = null)
    {
        parent::__construct($id, $class, $src);
        $this->m_bar = $bar;
        $this->m_options = $options;
        $m_access = (int)$access;
        $m_admin = (int)$admin;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function isValid()
    {
        return parent::isValid() && !is_null($this->m_bar) &&
            !$this->m_bar->isEmpty() && (is_null($this->m_options) || $this->m_options->isValid());
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function access()
    {
        return $this->m_access;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function admin()
    {
        return $this->m_admin;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function bar()
    {
        return $this->m_bar;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function options()
    {
        return $this->m_options;
    }
    
    /////////////////////////////////////////////////////////////////////////
    private $m_bar = null;
    private $m_options = null;
    private $m_access = CUserInfo::DEFAULT_ACCESS;
    private $m_admin = CUserInfo::DEFAULT_ACCESS;
}

/////////////////////////////////////////////////////////////////////////////
?>