<?php

use yii\db\Migration;

/**
 * Class m140701_000004_create_mining_process_table
 */
class m140703_000004_create_mining_process_table extends Migration
{
     public function safeUp()
    {
        $this->createTable('{{%mining_process}}', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer()->notNull(),
            'location_id' => $this->integer()->Null(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->string(255),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-mining_process-company_id', '{{%mining_process}}', 'company_id');
        $this->createIndex('idx-mining_process-location_id', '{{%mining_process}}', 'location_id');

        $this->addForeignKey(
            'fk-mining_process-company_id',
            '{{%mining_process}}',
            'company_id',
            '{{%company}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-mining_process-location_id',
            '{{%mining_process}}',
            'location_id',
            '{{%location}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-mining_process-location_id', '{{%mining_process}}');
        $this->dropForeignKey('fk-mining_process-company_id', '{{%mining_process}}');

        $this->dropIndex('idx-mining_process-location_id', '{{%mining_process}}');
        $this->dropIndex('idx-mining_process-company_id', '{{%mining_process}}');

        $this->dropTable('{{%mining_process}}');
    }
}