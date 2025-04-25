<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%area}}`.
 * Has foreign keys to the tables:
 * - `{{%mining_group}}`
 * - `{{%company}}`
 * - `{{%fleet}}`
 * - `{{%location}}`
 */
class m250425_145438_create_area_table extends Migration
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
            'fleet_id' => $this->integer(),
            'location_id' => $this->integer(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
        ]);

        // Añade índices para claves foráneas
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

        $this->createIndex(
            'idx-area-fleet_id',
            '{{%area}}',
            'fleet_id'
        );

        $this->createIndex(
            'idx-area-location_id',
            '{{%area}}',
            'location_id'
        );

        // Añade claves foráneas
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

        $this->addForeignKey(
            'fk-area-fleet_id',
            '{{%area}}',
            'fleet_id',
            '{{%fleet}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-area-location_id',
            '{{%area}}',
            'location_id',
            '{{%location}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Elimina claves foráneas
        $this->dropForeignKey(
            'fk-area-mining_group_id',
            '{{%area}}'
        );

        $this->dropForeignKey(
            'fk-area-company_id',
            '{{%area}}'
        );

        $this->dropForeignKey(
            'fk-area-fleet_id',
            '{{%area}}'
        );

        $this->dropForeignKey(
            'fk-area-location_id',
            '{{%area}}'
        );

        // Elimina índices
        $this->dropIndex(
            'idx-area-mining_group_id',
            '{{%area}}'
        );

        $this->dropIndex(
            'idx-area-company_id',
            '{{%area}}'
        );

        $this->dropIndex(
            'idx-area-fleet_id',
            '{{%area}}'
        );

        $this->dropIndex(
            'idx-area-location_id',
            '{{%area}}'
        );

        $this->dropTable('{{%area}}');
    }
}
