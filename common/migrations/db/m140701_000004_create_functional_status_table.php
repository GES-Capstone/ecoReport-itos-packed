<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%functional_status}}`.
 */
class m140701_000004_create_functional_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%functional_status}}', [
            'id' => $this->primaryKey(),
            'mining_group_id'=> $this->integer()->notNull(),
            'status' => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
        $this->createIndex(
            '{{%idx-functional_status-mining_group_id}}',
            '{{%functional_status}}',
            'mining_group_id'
        );
        $this->addForeignKey(
            '{{%fk-functional_status-mining_group_id}}',
            '{{%functional_status}}',
            'mining_group_id',
            '{{%mining_group}}',
            'id',
            'CASCADE',
            'CASCADE',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(){
        $this->dropForeignKey(
            '{{%fk-functional_status-mining_group_id}}',
            '{{%functional_status}}'
        );

        $this->dropIndex(
            '{{%idx-functional_status-mining_group_id}}',
            '{{%functional_status}}'
        );

        $this->dropTable('{{%functional_status}}');
    }
}
