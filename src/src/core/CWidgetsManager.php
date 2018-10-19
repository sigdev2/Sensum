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
class CWidgetsManager
{
    /////////////////////////////////////////////////////////////////////////
    const DEFAULT_WIDGETS_TAG = "widgets";
    
    /////////////////////////////////////////////////////////////////////////
    public function __construct(CCurrentPage $page, /*IteratorAggregate*/CWidgetInfoIter $widgets, IUserAccess $currUser)
    {
        stderr::assert(!is_null($page));
        stderr::assert(!is_null($currUser));
        
        if ($widgets->valid())
            return;
        
        $this->m_widgets = new ArrayObject();
        
        foreach ($widgets as $widgetInfo)
        {
            if (!$this->m_widgets->offsetExists($widgetInfo->bar()->str()))
                    $this->m_widgets[$widgetInfo->bar()->str()] = new ArrayObject();
            
            if (!$currUser->isHasAccessTo($widgetInfo->access()))
                continue;
            
            include_once($widgetInfo->src()->getRealPath());
            $class = $widgetInfo->class_name()->str();
            $widget = new $class;
            
            if ($widget instanceof IWidget)
            {
                $widget->init($page, $currUser->isAdminFor($widgetInfo->admin()), $widgetInfo->options());

                $this->m_widgets[$widgetInfo->bar()->str()]->append($widget);
            }
            else
            {
                stderr::warning(5000);
            }
        }
    }

    /////////////////////////////////////////////////////////////////////////
    public function getAsDOMXML()
    {
        $dom = stddomxml::create();
        $dom->loadXML("<".self::DEFAULT_WIDGETS_TAG."/>");
        
        foreach ($this->m_widgets as $bar => $widgets)
        {
            $barElem = $dom->createElement($bar);

            foreach ($widgets as $widget)
            {
                $content = $widget->getAsDOMXML();
                if (is_null($content) || !$content)
                    continue;
                
                if ($content instanceof DOMDocument)
                    $barElem->appendChild($dom->importNode($content->documentElement, true));
                else if ($content instanceof DOMNode)
                    $barElem->appendChild($dom->importNode($content, true));
                else
                    continue;
            }

            $dom->documentElement->appendChild($barElem);
        }
        
        return $dom;
    }
    
    /////////////////////////////////////////////////////////////////////////
    public function getXSLTArray()
    {
        $arr = new ArrayObject();

        $tplSH = new stdshdoc();
        foreach ($this->m_widgets as $bar => $widgets)
        {
            $barTpl = $tplSH->newTpl(stdshdoc::ATT_NAME, $bar);
            $tplSH->appendChild($barTpl);
            
            foreach ($widgets as $widget)
            {
                $xslt = $widget->getXSLT();
                if (is_null($xslt) || !$xslt || !($xslt instanceof DOMDocument))
                {
                    stderr::warning(5002);
                    continue;
                }
                
                // todo: template validation by dtd w3c 5002
                
                // todo: optimizate
                foreach($xslt->documentElement->childNodes as $child)
                     stdarr::appendUnic($arr, $child);

                $generateXSLT = $widget->generateXSLT();
                if (!is_null($generateXSLT))
                {
                    if (!($generateXSLT instanceof DOMDocument))
                    {
                        stderr::warning(5002);
                        continue;
                    }
                    
                    // todo: optimizate
                    foreach($generateXSLT->documentElement->childNodes as $child)
                         stdarr::appendUnic($arr, $child);
                         
                    $calls = $tplSH->allCalls($generateXSLT);
                
                    if (!is_null($calls))
                    {
                        foreach($calls as $tpl)
                            $barTpl->appendChild($tpl);
                    }
                    
                    continue;
                }
                
                $calls = $tplSH->allCalls($xslt);
                if (!is_null($calls))
                {
                    foreach($calls as $tpl)
                        $barTpl->appendChild($tpl);
                }
            }
            
            $arr->append($barTpl);
        }

        return $arr;
    }
   
    /////////////////////////////////////////////////////////////////////////
    private $m_widgets = null;
}

/////////////////////////////////////////////////////////////////////////////
?>