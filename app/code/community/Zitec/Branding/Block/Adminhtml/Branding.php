<?php

class Zitec_Branding_Block_Adminhtml_Branding extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_branding';
        $this->_blockGroup = 'zitec_branding';
        $this->_headerText = Mage::helper('zitec_branding')->__('Manage Branding');
        $this->_addButtonLabel = Mage::helper('zitec_branding')->__('Add New Branding');

        parent::__construct();
    }

}