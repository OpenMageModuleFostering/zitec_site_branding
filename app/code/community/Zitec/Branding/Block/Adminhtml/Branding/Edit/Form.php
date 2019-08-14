<?php
class Zitec_Branding_Block_Adminhtml_Branding_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Constructs the form for branding edit.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_branding');

        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));
        $form->setUseContainer(true);
        $fieldset = $form->addFieldset('main', array(
            'legend'=>Mage::helper('zitec_branding')->__('Branding')
        ));
        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', array(
                'name'  => 'entity_id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => Mage::helper('zitec_branding')->__('Branding title'),
            'required'  => true,
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('zitec_branding')->__('Status'),
            'title'     => Mage::helper('zitec_branding')->__('Status'),
            'name'      => 'status',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('zitec_branding')->__('Active'),
                '0' => Mage::helper('zitec_branding')->__('Inactive'),
            ),
        ));

        if (Mage::app()->isSingleStoreMode()) {
            $storeId = Mage::app()->getStore(true)->getId();
            $fieldset->addField('store_ids', 'hidden', array(
                'name'     => 'store_ids[]',
                'value'    => $storeId
            ));
        } else {
            $fieldset->addField('store_ids', 'multiselect', array(
                'name'     => 'store_ids[]',
                'label'    => Mage::helper('zitec_branding')->__('Stores'),
                'title'    => Mage::helper('zitec_branding')->__('Stores'),
                'required' => true,
                'values'   => Mage::helper('zitec_branding/system_store')->storesToOptions(
                        Mage::getSingleton('adminhtml/system_store')->getStoresStructure())
            ));
        }

        $fieldset->addField('image_url', 'image', array(
            'name'      => 'image_url',
            'label'     => Mage::helper('zitec_branding')->__('Branding image'),
            'required'  => true,
        ));

        $fieldset->addField('url', 'text', array(
            'name'      => 'url',
            'label'     => Mage::helper('zitec_branding')->__('Branding URL'),
        ));

        $fieldset->addField('start_date', 'date', array(
            'name'      => 'start_date',
            'label'     => Mage::helper('zitec_branding')->__('Branding start date'),
            'required'  => true,
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'image'  => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . '/adminhtml/default/default/images/grid-cal.gif',
        ));

        $fieldset->addField('end_date', 'date', array(
            'name'      => 'end_date',
            'label'     => Mage::helper('zitec_branding')->__('Branding end date'),
            'required'  => true,
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'image'  => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . '/adminhtml/default/default/images/grid-cal.gif',
        ));

        $data = Mage::getSingleton('adminhtml/session')->getBrandingData();

		if (!$data)
		{
			if ($model->getId())
            {
				$data = $model->getData();
			}
		}

        if ($data)
        {
			if (Mage::app()->isSingleStoreMode() && isset($data['store_ids']) && is_array($data['store_ids']))
			{
				$data['store_ids'] = reset($data['store_ids']);
			}
            $form->setValues($data);
            Mage::getSingleton('adminhtml/session')->unsetData('branding_data');
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}