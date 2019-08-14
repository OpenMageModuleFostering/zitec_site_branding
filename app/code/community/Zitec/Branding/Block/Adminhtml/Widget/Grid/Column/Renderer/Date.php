<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alexandru.enciu
 * Date: 27.05.2013
 * Time: 15:40
 * To change this template use File | Settings | File Templates.
 */

class Zitec_Branding_Block_Adminhtml_Widget_Grid_Column_Renderer_Date
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Date
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            if ($this->getColumn()->getUsePlain())
            {
                return $data;
            }

            $format = $this->_getFormat();
            try {
                if($this->getColumn()->getGmtoffset()) {
                    $data = Mage::app()->getLocale()
                        ->date($data, Varien_Date::DATETIME_INTERNAL_FORMAT)->toString($format);
                } else {
                    $data = Mage::getSingleton('core/locale')
                        ->date($data, Zend_Date::ISO_8601, null, false)->toString($format);
                }
            }
            catch (Exception $e)
            {
                if($this->getColumn()->getTimezone()) {
                    $data = Mage::app()->getLocale()
                        ->date($data, Varien_Date::DATETIME_INTERNAL_FORMAT)->toString($format);
                } else {
                    $data = Mage::getSingleton('core/locale')->date($data, null, null, false)->toString($format);
                }
            }
            return $data;
        }

        return $this->getColumn()->getDefault();
    }
}