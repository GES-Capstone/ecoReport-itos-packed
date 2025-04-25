<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%fleet}}`.
 */
class m250424_163928_create_fleet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%fleet}}', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer(),
            'location_id' => $this->integer(),
            'name' => $this-> string(100),
            'description' => $this->text(),


        ]);
        $this->addForeignKey(
            'fk-fleet-company_id',
            '{{%fleet}}',
            'company_id',
            '{{%company}}',
            'id',
            'SET NULL',
        );
        $this->addForeignKey(
            'fk-fleet-location',
            '{{%fleet}}',
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
        $this->dropTable('{{%fleet}}');
    }
}
