<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%area}}`.
 * Has foreign keys to the tables:
 * - `{{%mining_group}}`
 * - `{{%company}}`
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
            'company_id' => $this->integer(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
        ]);

        $this->createIndex(
            'idx-area-mining_group_id',
            '{{%area}}',
            'mining_group_id'
        );

        $this->createIndex(
            'idx-area-company_id',
            '{{%area}}',
            'company_id'
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
            'fk-area-company_id',
            '{{%area}}',
            'company_id',
            '{{%company}}',
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
            'fk-area-company_id',
            '{{%area}}'
        );


 

        $this->dropIndex(
            'idx-area-mining_group_id',
            '{{%area}}'
        );

        $this->dropIndex(
            'idx-area-company_id',
            '{{%area}}'
        );

        $this->dropTable('{{%area}}');
    }
}
