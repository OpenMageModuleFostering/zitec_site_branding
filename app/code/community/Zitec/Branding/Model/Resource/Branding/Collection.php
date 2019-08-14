<?php
class Zitec_Branding_Model_Resource_Branding_Collection extends
    Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = array(
        'store' => array(
            'associations_table' => 'zitec_branding/store',
            'id_field'      => 'branding_id',
            'entity_id_field'    => 'store_id'
        ),
    );
    /**
     * Pseudo constructor.
     */
    protected function _construct()
    {
        $this->_init('zitec_branding/branding');
    }

    /**
     * Limit rules collection by specific websites
     *
     * @param int|array|Mage_Core_Model_Website $websiteId
     *
     * @return Zitec_Branding_Model_Resource_Branding
     */
    public function addStoreFilter($storeId)
    {
        $entityInfo = $this->_getAssociatedEntityInfo('store');
        if (!$this->getFlag('is_store_table_joined')) {
            $this->setFlag('is_store_table_joined', true);
            if ($storeId instanceof Mage_Core_Model_Store) {
                $storeId = $storeId->getId();
            }

            $subSelect = $this->getConnection()->select()
                ->from(array('store' => $this->getTable($entityInfo['associations_table'])), '')
                ->where('store.' . $entityInfo['entity_id_field'] . ' IN (?)', $storeId);
            $this->getSelect()->exists(
                $subSelect,
                'main_table.entity_id = store.' . $entityInfo['id_field']
            );
        }
        return $this;
    }

    /**
     * Provide support for website id filter
     *
     * @param string $field
     * @param mixed $condition
     *
     * @return Zitec_Branding_Model_Resource_Branding
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'store_ids') {
            return $this->addStoreFilter($condition);
        }

        parent::addFieldToFilter($field, $condition);
        return $this;
    }

    /**
     * Init flag for adding rule website ids to collection result
     *
     * @param bool|null $flag
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
     */
    public function addStoresToResult($flag = null)
    {
        $flag = ($flag === null) ? true : $flag;
        $this->setFlag('add_stores_to_result', $flag);
        return $this;
    }

    /**
     * Add website ids to rules data
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->getFlag('add_stores_to_result') && $this->_items) {
            /** @var Mage_Rule_Model_Abstract $item */
            foreach ($this->_items as $item) {
                $item->afterLoad();
            }
        }

        return $this;
    }

    /**
     * Retrieve correspondent entity information (associations table name, columns names)
     * of rule's associated entity by specified entity type
     *
     * @param string $entityType
     *
     * @return array
     */
    protected function _getAssociatedEntityInfo($entityType)
    {
        if (isset($this->_associatedEntitiesMap[$entityType])) {
            return $this->_associatedEntitiesMap[$entityType];
        }

        $e = Mage::exception(
            'Mage_Core',
            Mage::helper('rule')->__(
                'There is no information about associated entity type "%s".', $entityType
            )
        );
        throw $e;
    }
}