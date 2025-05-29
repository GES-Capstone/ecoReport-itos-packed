<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%fleet}}`.
 */
class m250526_190458_create_fleet_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%fleet}}', [
            'id' => $this->primaryKey(),
            'mining_group_id' => $this->integer()->notNull(),
            'company_id' => $this->integer()->notNull(),
            'area_id' => $this->integer()->notNull(),
            'location_id' => $this->integer()->null(),
            'name' => $this->string()->notNull(),
            'description' => $this->string()->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-fleet-mining_group_id', '{{%fleet}}', 'mining_group_id');
        $this->createIndex('idx-fleet-company_id', '{{%fleet}}', 'company_id');
        $this->createIndex('idx-fleet-area_id', '{{%fleet}}', 'area_id');
        $this->createIndex('idx-fleet-location_id', '{{%fleet}}', 'location_id');

        $this->addForeignKey('fk_fleet_group', '{{%fleet}}', 'mining_group_id', '{{%mining_group}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_fleet_company', '{{%fleet}}', 'company_id', '{{%company}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_fleet_area', '{{%fleet}}', 'area_id', '{{%area}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_fleet_location', '{{%fleet}}', 'location_id', '{{%location}}', 'id', 'SET NULL');
    }

    public function safeDown()
    {

        $this->dropForeignKey('fk_fleet_location', '{{%fleet}}');
        $this->dropForeignKey('fk_fleet_area', '{{%fleet}}');
        $this->dropForeignKey('fk_fleet_company', '{{%fleet}}');
        $this->dropForeignKey('fk_fleet_group', '{{%fleet}}');

        $this->dropIndex('idx-fleet-location_id', '{{%fleet}}');
        $this->dropIndex('idx-fleet-area_id', '{{%fleet}}');
        $this->dropIndex('idx-fleet-company_id', '{{%fleet}}');
        $this->dropIndex('idx-fleet-mining_group_id', '{{%fleet}}');

        $this->dropTable('{{%fleet}}');
    }
}
