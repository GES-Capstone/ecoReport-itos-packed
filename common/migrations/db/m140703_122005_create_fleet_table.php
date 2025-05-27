<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%fleet}}`.
 */
class m140703_122005_create_fleet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%fleet}}', [
            'id' => $this->primaryKey(),
            'mining_group_id' => $this->integer(),
            'area_id' => $this->integer(),
            'location_id' => $this->integer(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text(),
'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Añade índices para claves foráneas
        $this->createIndex(
            'idx-fleet-mining_group_id',
            '{{%fleet}}',
            'mining_group_id'
        );

        $this->createIndex(
            'idx-fleet-area_id',
            '{{%fleet}}',
            'area_id'
        );

        $this->createIndex(
            'idx-fleet-location_id',
            '{{%fleet}}',
            'location_id'
        );

        $this->addForeignKey(
            'fk-fleet-mining_group_id',
            '{{%fleet}}',
            'mining_group_id',
            '{{%mining_group}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-fleet-area_id',
            '{{%fleet}}',
            'area_id',
            '{{%area}}',
            'id',
            'CASCADE',
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
        $this->dropForeignKey('fk-fleet-area_id', '{{%fleet}}');
        $this->dropForeignKey('fk-fleet-location_id', '{{%fleet}}');
        
        // Eliminar los índices
        $this->dropIndex('idx-fleet-mining_group_id', '{{%fleet}}');
        $this->dropIndex('idx-fleet-area_id', '{{%fleet}}');
        $this->dropIndex('idx-fleet-location_id', '{{%fleet}}');
        
        // Finalmente eliminar la tabla
        $this->dropTable('{{%fleet}}');
    }
}