<?php
class Zitec_Branding_Model_Resource_Branding extends Mage_Core_Model_Resource_Db_Abstract
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
    public function _construct()
    {
        $this->_init('zitec_branding/branding', 'entity_id');
    }
    /**
     * Add store ids to rule data after load
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Zitec_Branding_Model_Resource_Branding
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->setData('store_ids', (array)$this->getStoreIds($object->getId()));
        $object->setData('image_path', Mage::helper('zitec_branding')->getImagePath($object->getImage()));
        $object->setData('image_url', Mage::helper('zitec_branding')->getImageUrl($object->getImage()));

        return parent::_afterLoad($object);
    }

    /**
     * Bind branding to store(s).
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Zitec_Branding_Model_Resource_Branding
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->hasStoreIds()) {
            $storeIds = $object->getStoreIds();
            if (!is_array($storeIds)) {
                $storeIds = explode(',', (string)$storeIds);
            }
            $this->bindRuleToEntity($object->getId(), $storeIds, 'store');
        }

        parent::_afterSave($object);

        return $this;
    }

    /**
     * Retrieve store ids of specified branding
     *
     * @param int $ruleId
     * @return array
     */
    public function getStoreIds($brandingId)
    {
        return $this->getAssociatedEntityIds($brandingId, 'store');
    }

    /**
     * Returns the branding for the selected period.
     *
     * @param string $startDate
     *   The start date, in MySQL format: YYYY-MM-DD
     *
     * @param string $endDate
     *   The end date, in MySQL format: YYYY-MM-DD
     *
     * @return NULL | Zitec_Branding_Model_Branding
     */
    public function getBrandingInPeriod($startDate, $endDate, $storeId)
    {
        $readAdapter = $this->_getReadAdapter();
        $select = $readAdapter->select()
            ->from($this->getTable('zitec_branding/branding'))
            ->where('start_date <= :start_date AND end_date >= :start_date OR
                     start_date <= :end_date AND end_date >= :end_date')
            ->joinInner($this->getTable('zitec_branding/store'), 'branding_id=entity_id', NULL)
            ->where('store_id=:store_id')
            ->where('status=1')
            ->limit(1);
        $bind = array(
            ':start_date'  => $startDate,
            ':end_date'    => $endDate,
            ':store_id'    => $storeId,
        );

        $fields = $readAdapter->fetchRow($select, $bind);

        if ($fields)
        {
            $object = Mage::getModel('zitec_branding/branding');
            $object->setData($fields);
            $this->_afterLoad($object);

            return $object;
        }

        return NULL;
    }
    /**
     * Returns the branding for the selected period.
     *
     * @param string $startDate
     *   The start date, in MySQL format: YYYY-MM-DD
     *
     * @param string $endDate
     *   The end date, in MySQL format: YYYY-MM-DD
     *
     * @return NULL | Zitec_Branding_Model_Branding
     */
    public function checkBrandingInPeriod($startDate, $endDate, $storeId, $entityId)
    {
        $readAdapter = $this->_getReadAdapter();
        $select = $readAdapter->select()
            ->from($this->getTable('zitec_branding/branding'), 'entity_id')
            ->where('start_date <= :start_date AND end_date >= :start_date OR
                     start_date <= :end_date AND end_date >= :end_date OR
                     start_date >= :start_date AND end_date <= :end_date')
            ->joinInner($this->getTable('zitec_branding/store'), 'branding_id=entity_id', NULL)
            ->where('store_id=:store_id')
            ->where('status=1')
            ->where('entity_id !=:entity_id')
            ->limit(1);
        $bind = array(
            ':start_date'  => $startDate,
            ':end_date'    => $endDate,
            ':store_id'    => $storeId,
            ':entity_id'   => $entityId,
        );

        $entityId = $readAdapter->fetchOne($select, $bind);

        return $entityId;
    }

	/**
     * Bind specified rules to entities
     *
     * @param array|int|string $ids
     * @param array|int|string $entityIds
     * @param string $entityType
     *
     * @return Mage_Rule_Model_Resource_Abstract
     */
    public function bindRuleToEntity($ids, $entityIds, $entityType)
    {
        if (empty($ids) || empty($entityIds)) {
            return $this;
        }
        $adapter    = $this->_getWriteAdapter();
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        if (!is_array($ids)) {
            $ids = array((int) $ids);
        }
        if (!is_array($entityIds)) {
            $entityIds = array((int) $entityIds);
        }

        $data  = array();
        $count = 0;

        $adapter->beginTransaction();

        try {
            foreach ($ids as $id) {
                foreach ($entityIds as $entityId) {
                    $data[] = array(
                        $entityInfo['entity_id_field'] => $entityId,
                        $entityInfo['id_field'] => $id
                    );
                    $count++;
                    if (($count % 1000) == 0) {
                        $adapter->insertOnDuplicate(
                            $this->getTable($entityInfo['associations_table']),
                            $data,
                            array($entityInfo['id_field'])
                        );
                        $data = array();
                    }
                }
            }
            if (!empty($data)) {
                $adapter->insertOnDuplicate(
                    $this->getTable($entityInfo['associations_table']),
                    $data,
                    array($entityInfo['id_field'])
                );
            }

            $adapter->delete($this->getTable($entityInfo['associations_table']),
                $adapter->quoteInto($entityInfo['id_field']   . ' IN (?) AND ', $ids) .
                $adapter->quoteInto($entityInfo['entity_id_field'] . ' NOT IN (?)',  $entityIds)
            );
        } catch (Exception $e) {
            $adapter->rollback();
            throw $e;

        }

        $adapter->commit();

        return $this;
    }

    /**
     * Retrieve branding's associated entity Ids by entity type
     *
     * @param int $id
     * @param string $entityType
     *
     * @return array
     */
    public function getAssociatedEntityIds($id, $entityType)
    {
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable($entityInfo['associations_table']), array($entityInfo['entity_id_field']))
            ->where($entityInfo['id_field'] . ' = ?', $id);

        return $this->_getReadAdapter()->fetchCol($select);
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
            Mage::helper('core')->__(
                'There is no information about associated entity type "%s".', $entityType
            )
        );
        throw $e;
    }
}