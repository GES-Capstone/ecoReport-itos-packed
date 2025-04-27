<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%machinery_type}}`.
 */
class m140703_122005_create_machinery_type_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%machinery_type}}', [
            'id' => $this->primaryKey(),
            'mining_group_id' => $this->integer(),
            'company_id' => $this->integer(),
            'fleet_id' => $this->integer(),
            'area_id' => $this->integer(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'photo_base_url' => $this->string(),
            'photo_path' => $this->string(),
        ]);

        // Añade claves foráneas (sin índices)
        $this->addForeignKey(
            'fk-machinery_type-mining_group_id',
            '{{%machinery_type}}',
            'mining_group_id',
            '{{%mining_group}}',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-machinery_type-company_id',
            '{{%machinery_type}}',
            'company_id',
            '{{%company}}',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-machinery_type-fleet_id',
            '{{%machinery_type}}',
            'fleet_id',
            '{{%fleet}}',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-machinery_type-area_id',
            '{{%machinery_type}}',
            'area_id',
            '{{%area}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Elimina claves foráneas
        $this->dropForeignKey(
            'fk-machinery_type-mining_group_id',
            '{{%machinery_type}}'
        );

        $this->dropForeignKey(
            'fk-machinery_type-company_id',
            '{{%machinery_type}}'
        );

        $this->dropForeignKey(
            'fk-machinery_type-fleet_id',
            '{{%machinery_type}}'
        );

        $this->dropForeignKey(
            'fk-machinery_type-area_id',
            '{{%machinery_type}}'
        );

        $this->dropTable('{{%machinery_type}}');
    }
}