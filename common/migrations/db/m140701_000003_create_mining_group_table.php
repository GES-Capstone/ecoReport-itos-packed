<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mining_group}}` .
 */
class m140701_000003_create_mining_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mining_group}}', [
            'id' => $this->primaryKey(),
            'location_id' => $this->integer(),
            'name' => $this->string(255)->notNull()->unique(),
            'ges_name' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'commercial_address' => $this->text(),
            'operational_address' => $this->text(),
            'phone' => $this->string(50),
            'email' => $this->string(100),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
            'logo_path' => $this->string(255),
            'logo_base_url' => $this->string(255),
        ]);
        $this->addForeignKey(
            'fk-mining-group-location-id',
            '{{%mining_group}}',
            'location_id',
            '{{%location}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-mining_group-location_id',
            '{{%mining_group}}'
        );
        $this->dropTable('{{%mining_group}}');
    }
}
