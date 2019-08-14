<?php
class Zitec_Branding_Block_Branding extends Mage_Core_Block_Template
{
    /**
     * Adds the css for branding, if any.
     */
    protected function _prepareLayout()
    {
		$branding = Mage::helper('zitec_branding')->getActiveBranding();
		if ($branding)
		{
			$this->setTemplate('z_branding/branding.phtml');
			$head = $this->getLayout()->getBlock('head');
			$head->addItem('skin_css', 'css/z_branding/z_branding.css');
			$root = $this->getLayout()->getBlock('root');
			$root->setBodyStyle("background-image: url('" . $branding->getImageUrl() . "')");
            $this->setBranding($branding);
		}

        return parent::_prepareLayout();
    }
}