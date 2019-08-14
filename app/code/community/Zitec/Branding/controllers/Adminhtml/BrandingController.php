<?php

class Zitec_Branding_Adminhtml_BrandingController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Initializes the current branding model
     *
     * @param string $idFieldName
     *
     * @return Zitec_Branding_Model_Branding
     */
    protected function _initBranding($idFieldName = 'entity_id')
    {
        $brandingId = (int) $this->getRequest()->getParam($idFieldName);
        $branding = Mage::getModel('zitec_branding/branding');

        if ($brandingId) {
            $branding->load($brandingId);
        }

        Mage::register('current_branding', $branding);

        return $branding;
    }

    /**
     * Customers list action
     */
    public function indexAction()
    {
        $this->_title($this->__('Branding'))->_title($this->__('Manage Branding'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('dacris/branding');

//        /**
//         * Append customers block to content
//         */
//        $this->_addContent(
//            $this->getLayout()->createBlock('adminhtml/customer', 'customer')
//        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(Mage::helper('zitec_branding')->__('Customers'), Mage::helper('zitec_branding')->__('Branding'));
        $this->_addBreadcrumb(Mage::helper('zitec_branding')->__('Manage Customers'), Mage::helper('zitec_branding')->__('Manage Branding'));

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customer edit action
     */
    public function editAction()
    {
        $branding = $this->_initBranding();
        $this->loadLayout();

        $this->_title($branding->getId() ? $branding->getTitle() : $this->__('New Branding'));

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('dacris/branding/edit');

        $this->renderLayout();
    }

    /**
     * Create new customer action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Delete customer action
     */
    public function deleteAction()
    {
        $branding = $this->_initBranding();

        if ($branding->getId()) {
            try {
                $branding->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('zitec_adminhtml')->__('The branding has been deleted.'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/branding');
    }

    /**
     * Save customer action
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $branding = $this->_initBranding();
            $data = $this->_filterDates($data, array('start_date', 'end_date'));
            if (isset($data['image_url']) && isset($data['image_url']['value']))
            {
                $data['image_url'] = $data['image_url']['value'];
                $data['image'] = Mage::helper('zitec_branding')->getImage($data['image_url']);
                $data['image_path'] = Mage::helper('zitec_branding')->getImagePath($data['image']);
            }

            $branding->setData($data);

            if (isset($_FILES['image_url']['tmp_name']) && $_FILES['image_url']['tmp_name'])
            {
                $branding->setFilesKey('image_url');
                $branding->setImage(NULL);
            }

            try {
                $errors = $branding->validate();

                if ($errors)
                {
                    foreach ($errors as $error)
                    {
                        $this->_getSession()->addError($error);
                    }

                    $this->_getSession()->setBrandingData($data);
                    $this->getResponse()->setRedirect($this->getUrl('*/branding/edit', array('entity_id' => $branding->getId())));
                    return;
                }

                $branding->save();
                $this->_getSession()->addSuccess($this->__('The branding was saved successfully'));
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setBrandingData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/branding/edit', array('entity_id' => $branding->getId())));
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addException($e,
                    Mage::helper('adminhtml')->__('An error occurred while saving the branding.'));
                $this->_getSession()->setBrandingData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/branding/edit', array('entity_id'=>$branding->getId())));
                return;
            }
        }

        $this->getResponse()->setRedirect($this->getUrl('*/branding'));
    }
}
