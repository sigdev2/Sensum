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
class stdItByKeyArr extends FilterIterator
{   
    /////////////////////////////////////////////////////////////////////////
	public function __construct(ArrayObject $arr, ArrayObject $arrKeys = null)
    {
		parent::__construct(new ArrayIterator($arr));
        $this->m_arrKeys = $arrKeys;
	}
    
    /////////////////////////////////////////////////////////////////////////
	public function accept()
    {
        if (!is_null($this->m_arrKeys))
        {
            foreach($this->m_arrKeys as $key)
                if ($key == $this->key())
                    return true;

            return false;
        }
        
        return true;
	}
    
    /////////////////////////////////////////////////////////////////////////
    private $m_arrKeys = null;
}
/////////////////////////////////////////////////////////////////////////////
?>