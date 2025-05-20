<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%location}}`.
 */
class m140701_000001_create_location_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%location}}', [
            'id' => $this->primaryKey(),
            'latitude' => $this->decimal(18, 15)->notNull()->comment('Latitude coordinate'),
            'longitude' => $this->decimal(18, 15)->notNull()->comment('Longitude coordinate')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%location}}');
    }
}
