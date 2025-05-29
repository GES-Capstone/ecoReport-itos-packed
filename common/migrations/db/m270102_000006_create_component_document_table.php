<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%component_document}}`.
 */
class m270102_000006_create_component_document_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%component_document}}', [
            'id' => $this->primaryKey(),
            'component_id' => $this->integer()->notNull(),
            // Tipo de documento usando ENUM
            'type' => "ENUM('MAINTENANCE_PLAN', 'INSPECTION_PLAN', 'TDS', 'OPERATIONS_MANUAL', 
                      'MAINTENANCE_MANUAL', 'CERTIFICATES', 'WARRANTIES', 'SAP_TECHNICAL_LOCATION', 'OBSERVATIONS')",
            // Información del documento
            'name' => $this->string()->notNull(),
            'base_url' => $this->string(),
            'file_path' => $this->string(),
            'mime_type' => $this->string(), // tipo de archivo
            'description' => $this->text(),
            'upload_date' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Agregar clave foránea a la tabla de componentes
        $this->addForeignKey(
            'fk-component_document-component_id',
            '{{%component_document}}',
            'component_id',
            '{{%component}}',
            'id',
            'CASCADE'
        );

        // Índice para búsquedas por tipo
        $this->createIndex(
            'idx-component_document-type',
            '{{%component_document}}',
            'type'
        );

        // Índice para búsquedas por component_id y type (común en consultas)
        $this->createIndex(
            'idx-component_document-component_type',
            '{{%component_document}}',
            ['component_id', 'type']
        );
    }

    public function safeDown()
    {
        // Eliminar índices
        $this->dropIndex('idx-component_document-component_type', '{{%component_document}}');
        $this->dropIndex('idx-component_document-type', '{{%component_document}}');

        // Eliminar clave foránea
        $this->dropForeignKey('fk-component_document-component_id', '{{%component_document}}');

        // Eliminar tabla
        $this->dropTable('{{%component_document}}');
    }
}
