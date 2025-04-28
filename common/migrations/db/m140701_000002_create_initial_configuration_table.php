<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%initial_configuration}}`.
 */
class m140701_000002_create_initial_configuration_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%initial_configuration}}', [
            'id' => $this->primaryKey(),
            'step' => $this->integer()->notNull()->defaultValue(0), // Step (0 to n)
            'status' => "ENUM('not started', 'in progress', 'completed') NOT NULL DEFAULT 'not started'", // Status enum
            'mining_group_id' => $this->integer()->notNull(), // Mining Group ID
        ]);


        // Create FK to mining_group table
        if ($this->db->schema->getTableSchema('{{%mining_group}}', true) !== null) {
            $this->addForeignKey(
                'fk-initial_configuration-mining_group_id',
                '{{%initial_configuration}}',
                'mining_group_id',
                '{{%mining_group}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
        }
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-initial_configuration-mining_group_id', '{{%initial_configuration}}');
        $this->dropTable('{{%initial_configuration}}');
    }
}