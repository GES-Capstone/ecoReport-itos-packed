<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%fleet}}`.
 */
class m140703_000005_create_fleet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%fleet}}', [
            'id' => $this->primaryKey(),
            'mining_group_id' => $this->integer(),
            'company_id' => $this->integer(),
            'location_id' => $this->integer(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        // Añade índices para claves foráneas
        $this->createIndex(
            'idx-fleet-mining_group_id',
            '{{%fleet}}',
            'mining_group_id'
        );

        $this->createIndex(
            'idx-fleet-company_id',
            '{{%fleet}}',
            'company_id'
        );

        $this->createIndex(
            'idx-fleet-location_id',
            '{{%fleet}}',
            'location_id'
        );

        // Añade claves foráneas
        $this->addForeignKey(
            'fk-fleet-mining_group_id',
            '{{%fleet}}',
            'mining_group_id',
            '{{%mining_group}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-fleet-company_id',
            '{{%fleet}}',
            'company_id',
            '{{%company}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-fleet-location_id',
            '{{%fleet}}',
            'location_id',
            '{{%location}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Eliminar primero las claves foráneas
        $this->dropForeignKey('fk-fleet-mining_group_id', '{{%fleet}}');
        $this->dropForeignKey('fk-fleet-company_id', '{{%fleet}}');
        $this->dropForeignKey('fk-fleet-location_id', '{{%fleet}}');
        
        // Eliminar los índices
        $this->dropIndex('idx-fleet-mining_group_id', '{{%fleet}}');
        $this->dropIndex('idx-fleet-company_id', '{{%fleet}}');
        $this->dropIndex('idx-fleet-location_id', '{{%fleet}}');
        
        // Finalmente eliminar la tabla
        $this->dropTable('{{%fleet}}');
    }
}