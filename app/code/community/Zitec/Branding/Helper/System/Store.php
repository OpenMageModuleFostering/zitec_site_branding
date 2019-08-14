<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alexandru.enciu
 * Date: 26.07.2013
 * Time: 14:39
 * To change this template use File | Settings | File Templates.
 */

class Zitec_Branding_Helper_System_Store {
    /**
     * Generates the stores structure that can be used in select
     * form field.
     *
     * @param int $websiteId
     *   The website for which to retrieve the store
     *
     * @return array
     *   The stores and store views organized under groups
     */
    public function getStoresOptions($websiteId)
    {
        if (!$websiteId)
        {
            return array(
                array(
                    'label' => 'Admin',
                    'value' => '0',
                )
            );
        }
        $structure = Mage::getSingleton('adminhtml/system_store')
            ->getStoresStructure(FALSE, array(), array(), array($websiteId));

        return $this->storesToOptions($structure);
    }

    /**
     * Generates the stores structure that can be used in select
     * form field.
     *
     * @param array $structure
     *   The website structure, as returned by
     *   Mage_Adminhtml_Model_System_Store::getStoresStructure
     *
     * @return array
     *   The stores and store views organized under groups
     */
    public function storesToOptions($structure)
    {
        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        $options = array();

        foreach ($structure as $websiteId => $website) {
            $options[] = array(
                'label' => $website['label'],
                'value' => array(),
            );

            foreach ($website['children'] as $groupId => $group) {
                $values = array();
                foreach ($group['children'] as $storeId => $store) {
                    $values[] = array(
                        'label' => str_repeat($nonEscapableNbspChar, 4) . $store['label'],
                        'value' => $store['value']
                    );
                }

                $options[] = array(
                    'label' => str_repeat($nonEscapableNbspChar, 4) . $group['label'],
                    'value' => $values
                );
            }
        }

        return $options;
    }
}