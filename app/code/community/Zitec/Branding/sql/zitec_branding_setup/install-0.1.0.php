<?php
/**
 * @var Zitec_Branding_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->startSetup();
$table = $installer->getConnection()
    ->newTable($installer->getTable('zitec_branding/branding'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 'medium', array(
        'primary'  => TRUE,
        'nullable' => FALSE,
        'identity' => TRUE,
        'unsigned' => TRUE,
    ), 'The primary key')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, '50', array(
        'nullable' => FALSE,
    ), 'The branding title')
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_TEXT, '50', array(
        'nullable' => FALSE,
    ), 'The branding image')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => FALSE,
        'unsigned' => TRUE,
    ), 'The branding status: 0 is inactive, 1 is active')
    ->addColumn('start_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable' => FALSE,
    ), 'The branding start date')
    ->addColumn('end_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable' => FALSE,
    ), 'The branding end date')
    ->addColumn('url', Varien_Db_Ddl_Table::TYPE_TEXT, '100', array(
        'nullable' => TRUE,
    ), 'The branding URL');

$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('zitec_branding/store'))
    ->addColumn('branding_store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'primary'  => TRUE,
        'nullable' => FALSE,
        'identity' => TRUE,
        'unsigned' => TRUE,
    ), 'The primary key')
    ->addColumn('branding_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => FALSE,
        'unsigned' => TRUE,
    ), 'The branding id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => TRUE,
        'nullable'  => FALSE,
    ), 'The primary key')
    ->addForeignKey($installer->getFkName('zitec_branding/store', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey($installer->getFkName('zitec_branding/store', 'branding_id', 'zitec_branding/branding', 'entity_id'),
        'branding_id', $installer->getTable('zitec_branding/branding'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addIndex($installer->getIdxName('zitec_branding/store', array('branding_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('branding_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE));

$installer->getConnection()->createTable($table);
$installer->endSetup();