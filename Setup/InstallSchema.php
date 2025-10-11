<?php
namespace NativeMind\Translation\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'nativemind_translation_history'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('nativemind_translation_history')
        )->addColumn(
            'history_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'History ID'
        )->addColumn(
            'entity_type',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Entity Type (product, category, cms_page, etc.)'
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Entity ID'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store ID'
        )->addColumn(
            'attribute_code',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Attribute Code (name, description, etc.)'
        )->addColumn(
            'original_text',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Original Text'
        )->addColumn(
            'translated_text',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Translated Text'
        )->addColumn(
            'source_language',
            Table::TYPE_TEXT,
            10,
            ['nullable' => true],
            'Source Language Code'
        )->addColumn(
            'target_language',
            Table::TYPE_TEXT,
            10,
            ['nullable' => false],
            'Target Language Code'
        )->addColumn(
            'translation_service',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Translation Service (google, openai)'
        )->addColumn(
            'confidence_score',
            Table::TYPE_DECIMAL,
            '5,4',
            ['nullable' => true],
            'Confidence Score (0.0000 - 1.0000)'
        )->addColumn(
            'status',
            Table::TYPE_TEXT,
            20,
            ['nullable' => false, 'default' => 'pending'],
            'Status (pending, completed, failed)'
        )->addColumn(
            'error_message',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Error Message'
        )->addColumn(
            'processing_time',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Processing Time in Milliseconds'
        )->addColumn(
            'user_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'User ID who initiated translation'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->addIndex(
            $installer->getIdxName('nativemind_translation_history', ['entity_type']),
            ['entity_type']
        )->addIndex(
            $installer->getIdxName('nativemind_translation_history', ['entity_id']),
            ['entity_id']
        )->addIndex(
            $installer->getIdxName('nativemind_translation_history', ['store_id']),
            ['store_id']
        )->addIndex(
            $installer->getIdxName('nativemind_translation_history', ['status']),
            ['status']
        )->addIndex(
            $installer->getIdxName('nativemind_translation_history', ['created_at']),
            ['created_at']
        )->addIndex(
            $installer->getIdxName(
                'nativemind_translation_history',
                ['entity_type', 'entity_id', 'store_id', 'attribute_code']
            ),
            ['entity_type', 'entity_id', 'store_id', 'attribute_code']
        )->setComment(
            'NativeMind Translation History Table'
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'nativemind_translation_logs'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('nativemind_translation_logs')
        )->addColumn(
            'log_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Log ID'
        )->addColumn(
            'level',
            Table::TYPE_TEXT,
            20,
            ['nullable' => false, 'default' => 'info'],
            'Log Level (debug, info, warning, error, critical)'
        )->addColumn(
            'message',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => false],
            'Log Message'
        )->addColumn(
            'context',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Context Data (JSON)'
        )->addColumn(
            'entity_type',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true],
            'Related Entity Type'
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Related Entity ID'
        )->addColumn(
            'operation',
            Table::TYPE_TEXT,
            100,
            ['nullable' => true],
            'Operation Type'
        )->addColumn(
            'user_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'User ID'
        )->addColumn(
            'ip_address',
            Table::TYPE_TEXT,
            45,
            ['nullable' => true],
            'IP Address'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addIndex(
            $installer->getIdxName('nativemind_translation_logs', ['level']),
            ['level']
        )->addIndex(
            $installer->getIdxName('nativemind_translation_logs', ['entity_type']),
            ['entity_type']
        )->addIndex(
            $installer->getIdxName('nativemind_translation_logs', ['entity_id']),
            ['entity_id']
        )->addIndex(
            $installer->getIdxName('nativemind_translation_logs', ['operation']),
            ['operation']
        )->addIndex(
            $installer->getIdxName('nativemind_translation_logs', ['created_at']),
            ['created_at']
        )->setComment(
            'NativeMind Translation Logs Table'
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'nativemind_translation_cache'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('nativemind_translation_cache')
        )->addColumn(
            'cache_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Cache ID'
        )->addColumn(
            'cache_key',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Cache Key (hash of original_text + target_language + service)'
        )->addColumn(
            'original_text',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Original Text'
        )->addColumn(
            'translated_text',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Translated Text'
        )->addColumn(
            'source_language',
            Table::TYPE_TEXT,
            10,
            ['nullable' => true],
            'Source Language Code'
        )->addColumn(
            'target_language',
            Table::TYPE_TEXT,
            10,
            ['nullable' => false],
            'Target Language Code'
        )->addColumn(
            'translation_service',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Translation Service'
        )->addColumn(
            'hit_count',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Cache Hit Count'
        )->addColumn(
            'last_hit_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Last Cache Hit'
        )->addColumn(
            'expires_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Cache Expiration Time'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->addIndex(
            $installer->getIdxName('nativemind_translation_cache', ['cache_key'], \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
            ['cache_key'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('nativemind_translation_cache', ['target_language']),
            ['target_language']
        )->addIndex(
            $installer->getIdxName('nativemind_translation_cache', ['translation_service']),
            ['translation_service']
        )->addIndex(
            $installer->getIdxName('nativemind_translation_cache', ['expires_at']),
            ['expires_at']
        )->addIndex(
            $installer->getIdxName('nativemind_translation_cache', ['hit_count']),
            ['hit_count']
        )->setComment(
            'NativeMind Translation Cache Table'
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'nativemind_translation_queue'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('nativemind_translation_queue')
        )->addColumn(
            'queue_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Queue ID'
        )->addColumn(
            'batch_id',
            Table::TYPE_TEXT,
            100,
            ['nullable' => true],
            'Batch ID for grouped operations'
        )->addColumn(
            'entity_type',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Entity Type'
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Entity ID'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store ID'
        )->addColumn(
            'attribute_code',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Attribute Code'
        )->addColumn(
            'priority',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 5],
            'Priority (1-10, higher = more important)'
        )->addColumn(
            'status',
            Table::TYPE_TEXT,
            20,
            ['nullable' => false, 'default' => 'pending'],
            'Status (pending, processing, completed, failed, retry)'
        )->addColumn(
            'retry_count',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Retry Count'
        )->addColumn(
            'max_retries',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 3],
            'Max Retries'
        )->addColumn(
            'error_message',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Last Error Message'
        )->addColumn(
            'scheduled_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Scheduled Execution Time'
        )->addColumn(
            'started_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Processing Started At'
        )->addColumn(
            'completed_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Processing Completed At'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->addIndex(
            $installer->getIdxName('nativemind_translation_queue', ['batch_id']),
            ['batch_id']
        )->addIndex(
            $installer->getIdxName('nativemind_translation_queue', ['status']),
            ['status']
        )->addIndex(
            $installer->getIdxName('nativemind_translation_queue', ['priority']),
            ['priority']
        )->addIndex(
            $installer->getIdxName('nativemind_translation_queue', ['scheduled_at']),
            ['scheduled_at']
        )->addIndex(
            $installer->getIdxName('nativemind_translation_queue', ['entity_type', 'entity_id']),
            ['entity_type', 'entity_id']
        )->setComment(
            'NativeMind Translation Queue Table'
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'nativemind_api_usage_tracking'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('nativemind_api_usage_tracking')
        )->addColumn(
            'tracking_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Tracking ID'
        )->addColumn(
            'service',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'API Service (google, openai)'
        )->addColumn(
            'operation_type',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Operation Type (translate, detect_language, etc.)'
        )->addColumn(
            'request_size',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Request Size in Bytes'
        )->addColumn(
            'response_size',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Response Size in Bytes'
        )->addColumn(
            'character_count',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Character Count Translated'
        )->addColumn(
            'response_time',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Response Time in Milliseconds'
        )->addColumn(
            'status_code',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'HTTP Status Code'
        )->addColumn(
            'success',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => false],
            'Success Flag'
        )->addColumn(
            'error_message',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Error Message'
        )->addColumn(
            'cost_estimate',
            Table::TYPE_DECIMAL,
            '10,6',
            ['nullable' => true],
            'Estimated Cost in USD'
        )->addColumn(
            'metadata',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Additional Metadata (JSON)'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addIndex(
            $installer->getIdxName('nativemind_api_usage_tracking', ['service']),
            ['service']
        )->addIndex(
            $installer->getIdxName('nativemind_api_usage_tracking', ['operation_type']),
            ['operation_type']
        )->addIndex(
            $installer->getIdxName('nativemind_api_usage_tracking', ['success']),
            ['success']
        )->addIndex(
            $installer->getIdxName('nativemind_api_usage_tracking', ['created_at']),
            ['created_at']
        )->setComment(
            'NativeMind API Usage Tracking Table'
        );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}

