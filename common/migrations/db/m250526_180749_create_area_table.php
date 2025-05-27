<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%area}}`.
 */
class m250526_180749_create_area_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%area}}', [
            'id' => $this->primaryKey(),
            'mining_group_id' => $this->integer()->notNull(),
            'company_id' => $this->integer()->notNull(),
            'mining_process_id' => $this->integer()->notNull(),
            'location_id' => $this->integer()->Null(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->string(255),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-area-mining_group_id', '{{%area}}', 'mining_group_id');
        $this->createIndex('idx-area-company_id', '{{%area}}', 'company_id');
        $this->createIndex('idx-area-mining_process_id', '{{%area}}', 'mining_process_id');
        $this->createIndex('idx-area-location_id', '{{%area}}', 'location_id');

        $this->addForeignKey(
            'fk-area-mining_group_id',
            '{{%area}}',
            'mining_group_id',
            '{{%mining_group}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-area-company_id',
            '{{%area}}',
            'company_id',
            '{{%company}}',
            'id',
            'CASCADE'
        );

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
        $this->dropForeignKey('fk-area-company_id', '{{%area}}');
        $this->dropForeignKey('fk-area-mining_group_id', '{{%area}}');

        $this->dropIndex('idx-area-location_id', '{{%area}}');
        $this->dropIndex('idx-area-mining_process_id', '{{%area}}');
        $this->dropIndex('idx-area-company_id', '{{%area}}');
        $this->dropIndex('idx-area-mining_group_id', '{{%area}}');

        $this->dropTable('{{%area}}');
    }
}
