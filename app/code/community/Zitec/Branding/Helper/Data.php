<?php
class Zitec_Branding_Helper_Data extends
    Mage_Core_Helper_Data
{
    const BRANDING_DIR = 'branding';

    /**
     * Get image path for branding.
     *
     * @param string $filename
     *
     * @return string
     */
    public function getImagePath($filename)
    {
         return $this->getBaseDir() . DS . $filename;
    }

    /**
     * Get image URL for branding.
     *
     * @param string $filename
     *
     * @return string
     */
    public function getImageUrl($filename)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . self::BRANDING_DIR . '/' . $filename;
    }

    /**
     *
     */
    public function getImage($imageUrl)
    {
        return str_replace(
            Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . self::BRANDING_DIR . '/',
            '',
            $imageUrl);
    }

    /**
     * Returns the base directory for branding image.
     *
     * @return string
     */
    public function getBaseDir()
    {
        return Mage::getBaseDir('media') . DS . self::BRANDING_DIR;
    }

    /**
     * @return NULL | Zitec_Branding_Model_Branding
     */
    public function getActiveBranding()
    {
        $resourceModel = Mage::getResourceModel('zitec_branding/branding');
        $date = date('Y-m-d', Mage::getSingleton('core/date')->timestamp());
        $storeId = Mage::app()->getStore()->getId();
        $branding = $resourceModel->getBrandingInPeriod($date, $date, $storeId);

        return $branding;
    }
}