<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%company}}`.
 */
class m140703_000003_create_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%company}}', [
            'id' => $this->primaryKey(),
            'mining_group_id' => $this->integer(),
            'location_id' => $this->integer(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'commercial_address' => $this->string(255),
            'operational_address' => $this->string(255),
            'phone' => $this->string(50),
            'email' => $this->string(100),
            'logo_path' => $this->string(255)->comment('Path to logo image'),
            'logo_base_url' => $this->string(255)->comment('Base URL for logo image'),
        ]);

        $this->createIndex(
            'idx-company-mining_group_id',
            '{{%company}}',
            'mining_group_id'
        );

        $this->createIndex(
            'idx-company-location-id',
            '{{%company}}',
            'location_id'
        );

        $this->addForeignKey(
            'fk-company-mining_group_id',
            '{{%company}}',
            'mining_group_id',
            '{{%mining_group}}',
            'id',
            'SET NULL', 
        );

        $this->addForeignKey(
            'fk-company-location-id',
            '{{%company}}',
            'location_id',
            '{{%location}}',
            'id',
            'SET NULL',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-company-mining_group_id', '{{%company}}');
        $this->dropForeignKey('fk-company-location-id', '{{%company}}');
        
        $this->dropIndex('idx-company-mining_group_id', '{{%company}}');
        $this->dropIndex('idx-company-location-id', '{{%company}}');
        
        $this->dropTable('{{%company}}');
    }
}