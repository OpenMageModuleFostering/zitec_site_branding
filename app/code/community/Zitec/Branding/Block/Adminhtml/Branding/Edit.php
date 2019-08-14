<?php
class Zitec_Branding_Block_Adminhtml_Branding_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Object constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_branding';
        $this->_blockGroup = 'zitec_branding';
        $this->_headerText = Mage::helper('zitec_branding')->__('Edit Branding');

        return parent::__construct();
    }
}