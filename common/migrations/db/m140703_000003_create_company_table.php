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

        // Añadir índice para mining_group_id
        $this->createIndex(
            'idx-company-mining_group_id',
            '{{%company}}',
            'mining_group_id'
        );

        // Añadir índice para location_id
        $this->createIndex(
            'idx-company-location-id',
            '{{%company}}',
            'location_id'
        );

        // Añadir clave foránea para mining_group_id (FK)
        $this->addForeignKey(
            'fk-company-mining_group_id',
            '{{%company}}',
            'mining_group_id',
            '{{%mining_group}}',
            'id',
            'SET NULL', 
        );

        // Añadir clave foránea para location_id (FK)
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
        // Eliminar primero las claves foráneas
        $this->dropForeignKey('fk-company-mining_group_id', '{{%company}}');
        $this->dropForeignKey('fk-company-location-id', '{{%company}}');
        
        // Eliminar los índices
        $this->dropIndex('idx-company-mining_group_id', '{{%company}}');
        $this->dropIndex('idx-company-location-id', '{{%company}}');
        
        // Finalmente eliminar la tabla
        $this->dropTable('{{%company}}');
    }
}