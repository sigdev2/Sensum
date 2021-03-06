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
class CWidgetInfoIter extends FilterIterator
{   
    /////////////////////////////////////////////////////////////////////////
	public function __construct(ArrayObject $widgets, stdtext $bar = null)
    {
		parent::__construct(new ArrayIterator($widgets));
        $this->m_bar = $bar;
	}
    
    /////////////////////////////////////////////////////////////////////////
	public function accept()
    {
        if (is_null($this->current()) || !($this->current() instanceof CWidgetInfo))
            return false;

        if (!is_null($this->m_bar) && !$this->m_bar->isEmpty())
            return ($this->m_bar->str($this->current()->bar()->charset()) == $this->current()->bar()->str());
        
        return true;
	}
    
    /////////////////////////////////////////////////////////////////////////
    protected $m_bar = null;
}

/////////////////////////////////////////////////////////////////////////////
?>