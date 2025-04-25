<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%company}}`.
 */
class m250423_234532_create_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%company}}', [
            'id' => $this->primaryKey(),
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
                
                // Añadir clave foránea para location_id (FK)
                $this->addForeignKey(
                    'fk-company-location-id',
                    '{{%company}}',
                    'location_id',
                    '{{%location}}',
                    'id',
                    'SET NULL'
                );


                // Añadir índices para las claves foráneas
                $this->createIndex(
                'idx-company-location-id',
                '{{%company}}',
                'location_id'
                );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%company}}');
    }
}
