<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%area}}`.
 * Has foreign keys to the tables:
 * - `{{%mining_group}}`
 * - `{{%mining_process}}`
 */
class m140703_122004_create_area_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%area}}', [
            'id' => $this->primaryKey(),
            'mining_group_id' => $this->integer(),
            'mining_process_id' => $this->integer(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-area-mining_group_id',
            '{{%area}}',
            'mining_group_id'
        );

        $this->createIndex(
            'idx-area-mining_process_id',
            '{{%area}}',
            'mining_process_id'
        );

        $this->addForeignKey(
            'fk-area-mining_group_id',
            '{{%area}}',
            'mining_group_id',
            '{{%mining_group}}',
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

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-area-mining_group_id',
            '{{%area}}'
        );

        $this->dropForeignKey(
            'fk-area-mining_process_id',
            '{{%area}}'
        );


 

        $this->dropIndex(
            'idx-area-mining_group_id',
            '{{%area}}'
        );

        $this->dropIndex(
            'idx-area-mining_process_id',
            '{{%area}}'
        );

        $this->dropTable('{{%area}}');
    }
}