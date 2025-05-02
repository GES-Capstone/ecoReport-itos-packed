<?php
namespace backend\modules\import\models;

use yii\base\Model;
use yii\web\UploadedFile;
use Yii;

class ExcelUploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public UploadedFile $excelFile;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['excelFile'], 'required', 'message' => 'Por favor seleccione un archivo Excel'],
            [['excelFile'], 'file', 'skipOnEmpty' => false,
                'extensions' => 'xlsx, xls, csv',
                'mimeTypes' => [
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'text/csv',
                    'application/csv',
                    'text/plain'
                ],
                'maxSize' => 1024 * 1024 * 10, // 10 MB
                'message' => 'Por favor suba un archivo Excel válido (.xlsx, .xls o .csv)',
                'tooBig' => 'El archivo es demasiado grande. Tamaño máximo 10MB',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'excelFile' => 'Archivo Excel',
        ];
    }

    /**
     * Upload file to server
     * @return boolean
     */
    public function upload()
    {
        if ($this->validate()) {
            // Crear directorio uploads si no existe
            $path = Yii::getAlias('@webroot/uploads');

            if (!is_dir($path)) {
                mkdir($path, 0775, true);
            }

            // Guardar el archivo con nombre único para evitar colisiones
            $timestamp = time();
            $fileName = "{$timestamp}_{$this->excelFile->baseName}.{$this->excelFile->extension}";
            $filePath = $path . '/' . $fileName;

            if ($this->excelFile->saveAs($filePath)) {
                // Puedes guardar el nombre original y el nombre de archivo en una propiedad
                $this->excelFile->name = $fileName;
                return true;
            }
        }

        return false;
    }
}