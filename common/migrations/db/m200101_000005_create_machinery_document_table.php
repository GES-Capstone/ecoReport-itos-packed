<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%machinery_document}}`.
 */
class m200101_000004_create_machinery_document_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%machinery_document}}', [
            'id' => $this->primaryKey(),
            'machinery_id' => $this->integer()->notNull(),
            // Tipo de documento usando ENUM
            'type' => "ENUM('OPERATIONS_PLAN', 'MAINTENANCE_PLAN', 'INSPECTION_PLAN', 'TDS', 
                      'INSPECTION_DOCS', 'MAINTENANCE_MANUAL', 'CERTIFICATE', 'WARRANTY')",
            // Información del documento
            'name' => $this->string()->notNull(),
            'base_url' => $this->string(),
            'file_path' => $this->string(),
            'mime_type' => $this->string(), // tipo de archivo
            'description' => $this->text(),
            'upload_date' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Agregar clave foránea a la tabla de maquinaria
        $this->addForeignKey(
            'fk-machinery_document-machinery_id',
            '{{%machinery_document}}',
            'machinery_id',
            '{{%machinery}}',
            'id',
            'CASCADE'
        );
        
        // Opcionalmente, índice para búsquedas por tipo
        $this->createIndex(
            'idx-machinery_document-type',
            '{{%machinery_document}}',
            'type'
        );
    }

    public function safeDown()
    {
        // Eliminar clave foránea
        $this->dropForeignKey('fk-machinery_document-machinery_id', '{{%machinery_document}}');
        
        // Eliminar índice
        $this->dropIndex('idx-machinery_document-type', '{{%machinery_document}}');
        
        // Eliminar tabla
        $this->dropTable('{{%machinery_document}}');
    }
}