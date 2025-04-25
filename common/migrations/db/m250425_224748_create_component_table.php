<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%component}}`.
 */
class m250425_224748_create_component_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%component}}', [
            'id' => $this->primaryKey(),
            'mining_group_id' => $this->integer(),
            'company_id' => $this->integer(),
            'fleet_id' => $this->integer(),
            'area_id' => $this->integer(),
            'machinery_id' => $this->integer(),
            'component_id' => $this->integer(),
            'status_id' => $this->integer(),
            'location_id' => $this->integer(),
            'name' => $this->string()->notNull(),
            'machinery_tag' => $this->string(),
            'component_tag' => $this->string(),
            'brand' => $this->string(),
            'model' => $this->string(),
            'start_operation' => $this->date(),
            'lifespan_years' => $this->integer(),
            'lifespan_hours' => $this->integer(),
            'supplier' => $this->string(),
            'cost' => $this->decimal(19, 4),
            'sap_code' => $this->string(),
            'description' => $this->text(),
            'photo_base_url' => $this->string(),
            'photo_path' => $this->string(),
        ]);

        // Añadir índices para mejorar el rendimiento en las claves foráneas
        $this->createIndex('idx-component-mining_group_id', '{{%component}}', 'mining_group_id');
        $this->createIndex('idx-component-company_id', '{{%component}}', 'company_id');
        $this->createIndex('idx-component-fleet_id', '{{%component}}', 'fleet_id');
        $this->createIndex('idx-component-area_id', '{{%component}}', 'area_id');
        $this->createIndex('idx-component-machinery_id', '{{%component}}', 'machinery_id');
        $this->createIndex('idx-component-component_id', '{{%component}}', 'component_id');
        $this->createIndex('idx-component-status_id', '{{%component}}', 'status_id');
        $this->createIndex('idx-component-location_id', '{{%component}}', 'location_id');

        // Añadir claves foráneas
        $this->addForeignKey(
            'fk-component-mining_group_id',
            '{{%component}}',
            'mining_group_id',
            '{{%mining_group}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-component-company_id',
            '{{%component}}',
            'company_id',
            '{{%company}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-component-fleet_id',
            '{{%component}}',
            'fleet_id',
            '{{%fleet}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-component-area_id',
            '{{%component}}',
            'area_id',
            '{{%area}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-component-machinery_id',
            '{{%component}}',
            'machinery_id',
            '{{%machinery}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-component-component_id',
            '{{%component}}',
            'component_id',
            '{{%component}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-component-status_id',
            '{{%component}}',
            'status_id',
            '{{%status}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-component-location_id',
            '{{%component}}',
            'location_id',
            '{{%location}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // Eliminar claves foráneas
        $this->dropForeignKey('fk-component-mining_group_id', '{{%component}}');
        $this->dropForeignKey('fk-component-company_id', '{{%component}}');
        $this->dropForeignKey('fk-component-fleet_id', '{{%component}}');
        $this->dropForeignKey('fk-component-area_id', '{{%component}}');
        $this->dropForeignKey('fk-component-machinery_id', '{{%component}}');
        $this->dropForeignKey('fk-component-component_id', '{{%component}}');
        $this->dropForeignKey('fk-component-status_id', '{{%component}}');
        $this->dropForeignKey('fk-component-location_id', '{{%component}}');

        // Eliminar índices
        $this->dropIndex('idx-component-mining_group_id', '{{%component}}');
        $this->dropIndex('idx-component-company_id', '{{%component}}');
        $this->dropIndex('idx-component-fleet_id', '{{%component}}');
        $this->dropIndex('idx-component-area_id', '{{%component}}');
        $this->dropIndex('idx-component-machinery_id', '{{%component}}');
        $this->dropIndex('idx-component-component_id', '{{%component}}');
        $this->dropIndex('idx-component-status_id', '{{%component}}');
        $this->dropIndex('idx-component-location_id', '{{%component}}');

        // Eliminar tabla
        $this->dropTable('{{%component}}');
    }
}