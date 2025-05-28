<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%area}}`.
 * Has foreign keys to the tables:
 * - `{{%mining_process}}`
 */
class m140703_122004_create_area_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%area}}', [
            'id' => $this->primaryKey(),
            'mining_process_id' => $this->integer()->notNull(),
            'location_id' => $this->integer()->Null(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->string(255),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-area-mining_process_id', '{{%area}}', 'mining_process_id');
        $this->createIndex('idx-area-location_id', '{{%area}}', 'location_id');


        $this->addForeignKey(
            'fk-area-mining_process_id',
            '{{%area}}',
            'mining_process_id',
            '{{%mining_process}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-area-location_id',
            '{{%area}}',
            'location_id',
            '{{%location}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-area-location_id', '{{%area}}');
        $this->dropForeignKey('fk-area-mining_process_id', '{{%area}}');

        $this->dropIndex('idx-area-location_id', '{{%area}}');
        $this->dropIndex('idx-area-mining_process_id', '{{%area}}');

        $this->dropTable('{{%area}}');
    }
}