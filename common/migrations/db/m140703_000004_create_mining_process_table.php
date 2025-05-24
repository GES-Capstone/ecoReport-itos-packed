<?php

use yii\db\Migration;

/**
 * Class m140701_000004_create_mining_process_table
 */
class m140703_000004_create_mining_process_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mining_process}}', [
            'id' => $this->primaryKey(),
            'mining_group_id' => $this->integer()->notNull(),
            'company_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

       
        $this->addForeignKey(
            'fk-mining_process-mining_group_id',
            '{{%mining_process}}',
            'mining_group_id',
            '{{%mining_group}}',
            'id',
            'CASCADE',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk-mining_process-company_id',
            '{{%mining_process}}',
            'company_id',
            '{{%company}}',
            'id',
            'CASCADE',
            'RESTRICT'
        );
      
        $this->createIndex(
            'idx-mining_process-mining_group_id',
            '{{%mining_process}}',
            'mining_group_id'
        );
        $this->createIndex(
            'idx-mining_process-company_id',
            '{{%mining_process}}',
            'company_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-mining_process-mining_group_id',
            '{{%mining_process}}'
        );
        $this->dropForeignKey(
            'fk-mining_process-company_id',
            '{{%mining_process}}'
        );
        $this->dropIndex(
            'idx-mining_process-company_id',
            '{{%mining_process}}'
        );

        $this->dropIndex(
            'idx-mining_process-mining_group_id',
            '{{%mining_process}}'
        );

        $this->dropTable('{{%mining_process}}');
    }
}