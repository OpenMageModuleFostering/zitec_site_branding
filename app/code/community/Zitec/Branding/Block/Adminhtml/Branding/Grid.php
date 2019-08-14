<?php
class Zitec_Branding_Block_Adminhtml_Branding_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('brandingGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('zitec_branding/branding')->getCollection();
        $collection->addStoresToResult();
        $this->setCollection($collection);

        return parent::_prepareCollection($collection);
    }

    /**
     * Prepare grid column
     *
     * @return $this
     */
    public function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('zitec_branding')->__('ID'),
            'width'  => '50px',
            'index'  => 'entity_id',
            'type'   => 'number',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('zitec_branding')->__('Title'),
            'index'  => 'title',
            'type'   => 'text',
        ));

        $this->addColumn('url', array(
            'header' => Mage::helper('zitec_branding')->__('URL'),
            'index'  => 'url',
            'type'   => 'text',
        ));

        $this->addColumn('start_date', array(
            'header'   => Mage::helper('zitec_branding')->__('Start date'),
            'index'    => 'start_date',
            'type'     => 'date',
            'renderer'  => 'zitec_branding/adminhtml_widget_grid_column_renderer_date',
            'use_plain' => TRUE
        ));

        $this->addColumn('end_date', array(
            'header' => Mage::helper('zitec_branding')->__('End date'),
            'index'  => 'end_date',
            'type'   => 'date',
            'renderer'  => 'zitec_branding/adminhtml_widget_grid_column_renderer_date',
            'use_plain' => TRUE
        ));
        $this->addColumn('status', array(
            'header'  => Mage::helper('zitec_branding')->__('Status'),
            'index'   => 'status',
            'type'    => 'options',
            'options' => array(
                0 => $this->__('Inactive'),
                1 => $this->__('Active'),
            )
        ));


        $this->addColumn('store_id', array(
            'header'    => Mage::helper('zitec_branding')->__('Store'),
            'align'     => 'center',
            'width'     => '80px',
            'type'      => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_store')->getStoreOptionHash(FALSE),
            'index'     => 'store_ids',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Return row url for js event handlers
     *
     * @param Varien_Object
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('entity_id' => $row->getId()));
    }
}