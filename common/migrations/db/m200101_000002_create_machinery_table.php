<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%machinery}}`.
 */
class m200101_000002_create_machinery_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%machinery}}', [
            'id' => $this->primaryKey(),
            'fleet_id' => $this->integer(),
            'mining_group_id' => $this->integer(),
            'functional_status_id' => $this->integer(),
            'machinery_type_id' => $this->integer(),
            'location_id' => $this->integer(),
            'tag' => $this->string()->notNull(),
            'unique_tag' => $this->string()->notNull(),
            'brand' => $this->string(),
            'model' => $this->string(),
            'start_operation' => $this->date(),
            'lifespan_years' => $this->integer(),
            'supplier' => $this->string(),
            'cost' => $this->decimal(19, 4),
            'sap_code' => $this->string(),
            'description' => $this->text(),
            'photo_base_url' => $this->string(),
            'photo_path' => $this->string(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
            'inspection_type' => "ENUM('PER COMPONENT', 'COMPLETE')",
            'family' => "ENUM('SEMI','MOBILE','FIXED') DEFAULT 'FIXED'"
        ]);

        // Agregar índices
        $this->createIndex('idx-machinery-tag', '{{%machinery}}', 'tag');
        $this->createIndex('idx-machinery-unique_tag', '{{%machinery}}', 'unique_tag', true); // índice único
        $this->createIndex('idx-machinery-sap_code', '{{%machinery}}', 'sap_code');
        $this->createIndex('idx-machinery-brand', '{{%machinery}}', 'brand');
        $this->createIndex('idx-machinery-model', '{{%machinery}}', 'model');

        // Agregar Foreign Keys
        $this->addForeignKey(
            'fk-machinery-fleet_id',
            '{{%machinery}}',
            'fleet_id',
            '{{%fleet}}',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-machinery-mining_group_id',
            '{{%machinery}}',
            'mining_group_id',
            '{{%mining_group}}',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-machinery-functional_status_id',
            '{{%machinery}}',
            'functional_status_id',
            '{{%functional_status}}',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-machinery-location_id',
            '{{%machinery}}',
            'location_id',
            '{{%location}}',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-machinery-machinery_type_id',
            '{{%machinery}}',
            'machinery_type_id',
            '{{%machinery_type}}',
            'id',
            'SET NULL'
        );
    }

    public function safeDown()
    {
        // Eliminar Foreign Keys
        $this->dropForeignKey('fk-machinery-fleet_id', '{{%machinery}}');
        $this->dropForeignKey('fk-machinery-mining_group_id', '{{%machinery}}');
        $this->dropForeignKey('fk-machinery-functional_status_id', '{{%machinery}}');
        $this->dropForeignKey('fk-machinery-location_id', '{{%machinery}}');
        $this->dropForeignKey('fk-machinery-machinery_type_id', '{{%machinery}}');

        // Eliminar índices
        $this->dropIndex('idx-machinery-tag', '{{%machinery}}');
        $this->dropIndex('idx-machinery-unique_tag', '{{%machinery}}');
        $this->dropIndex('idx-machinery-sap_code', '{{%machinery}}');
        $this->dropIndex('idx-machinery-brand', '{{%machinery}}');
        $this->dropIndex('idx-machinery-model', '{{%machinery}}');

        // Eliminar tabla
        $this->dropTable('{{%machinery}}');
    }
}