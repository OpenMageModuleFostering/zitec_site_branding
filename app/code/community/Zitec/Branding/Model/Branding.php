<?php
class Zitec_Branding_Model_Branding
    extends Mage_Core_Model_Abstract
{
    /**
     * Pseudo constructor.
     */
    public function _construct()
    {
        $this->_init('zitec_branding/branding');
    }

    /**
     * Validates data for branding.
     *
     * @return array
     */
    public function validate()
    {
        $errors = array();
        $checkDates = TRUE;
        if (!Zend_Validate::is( trim($this->getTitle()) , 'NotEmpty')) {
            $errors[] = Mage::helper('zitec_branding')->__('The title cannot be empty.');
        }
        if (!Zend_Validate::is( trim($this->getImage()) , 'NotEmpty') && !$this->getFilesKey()) {
            $errors[] = Mage::helper('zitec_branding')->__('The image cannot be empty.');
        } elseif ($this->getFilesKey()) {
            try {
                $uploader = new Varien_File_Uploader($this->getFilesKey());
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif')); //Allowed extension for file
                $uploader->setAllowCreateFolders(true); //for creating the directory if not exists
                $uploader->setAllowRenameFiles(false); //if true, uploaded file's name will be changed, if file with the same name already exists directory.
                $uploader->setFilesDispersion(false);

                if (!$uploader->checkAllowedExtension($uploader->getFileExtension()))
                {
                    $errors[] = Mage::helper('zitec_branding')->__('The allowed file extensions are: jpg, jpeg, png, gif.');
                }
                $this->setUploader($uploader);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!Zend_Validate::is( trim($this->getStartDate()) , 'NotEmpty')) {
            $errors[] = Mage::helper('zitec_branding')->__('The start date cannot be empty.');
            $checkDates = FALSE;
        }
        if (!Zend_Validate::is( trim($this->getEndDate()) , 'NotEmpty')) {
            $errors[] = Mage::helper('zitec_branding')->__('The end date cannot be empty.');
            $checkDates = FALSE;
        }
        if (!$this->getStoreIds()) {
            $errors[] = Mage::helper('zitec_branding')->__('The store cannot be empty.');
        } else {
            if (!is_array($this->getStoreIds()))
            {
                $this->setStoreIds(explode(',', $this->getStoreIds()));
            }
            $availableStores = array_keys(Mage::app()->getStores());

            if (array_diff($this->getStoreIds(), $availableStores))
            {
                $errors[] = Mage::helper('zitec_branding')->__('Some stores are invalid');
            }
            if ($checkDates)
            {
                if ($this->getStartDate() > $this->getEndDate())
                {
                    $errors[] = Mage::helper('zitec_branding')->__('The start date must be smaller than end date.');
                }

                if ($this->getStatus())
                {
                    foreach ($this->getStoreIds() as $storeId)
                    {
                        $existingBrandingId = $this->_getResource()
                            ->checkBrandingInPeriod($this->getStartDate(),
                                                    $this->getEndDate(),
                                                    $storeId,
                                                    $this->getId()
                            );

                        if ($existingBrandingId)
                        {
                            $errors[] = Mage::helper('zitec_branding')->__('A branding already exists in the selected period.');
                            break;
                        }
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Checks if the branding image exists on disk.
     *
     * @return boolean
     */
    public function fileExists()
    {
        return file_exists($this->getImagePath());
    }

    /**
     *
     */
    protected function _beforeSave()
    {
        if (!$this->getImage() || !$this->fileExists())
        {
            $this->uploadImage();
        }

        return parent::_beforeSave();
    }

    /**
     * Uploads the image file for branding on disk.
     *
     * @return Zitec_Branding_Model_Branding
     */
    public function uploadImage()
    {
        $uploader = $this->getUploader();
        if (!$uploader instanceof Varien_File_Uploader)
        {
            $uploader = new Varien_File_Uploader($this->getFilesKey());
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif')); //Allowed extension for file
            $uploader->setAllowCreateFolders(true); //for creating the directory if not exists
            $uploader->setAllowRenameFiles(false); //if true, uploaded file's name will be changed, if file with the same name already exists directory.
            $uploader->setFilesDispersion(false);
        }

        $path = Mage::helper('zitec_branding')->getBaseDir();

        $result = $uploader->save($path);
        if (!$result)
        {
            throw new Mage_Exception(Mage::helper('zitec_branding')->__("The file wasn't uploaded"));
        }

        $this->setImage($result['file']);
    }
}