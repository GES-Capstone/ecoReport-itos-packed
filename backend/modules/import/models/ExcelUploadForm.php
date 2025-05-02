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
    public $excelFile;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['excelFile'], 'required', 'message' => 'Please select an Excel file'],
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
                'message' => 'Please upload a valid Excel file (.xlsx, .xls or .csv)',
                'tooBig' => 'The file is too large. Maximum size 10MB',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'excelFile' => 'Excel File',
        ];
    }

    /**
     * Upload file to server
     * @return boolean
     */
    public function upload()
    {
        if ($this->validate()) {
            // creo que esto ya esta implemntado en el sistema ver si se puede reutilizar
            $path = Yii::getAlias('@webroot/uploads');

            if (!is_dir($path)) {
                mkdir($path, 0775, true);
            }

            // Save the file with a unique name to avoid collisions
            $timestamp = time();
            $fileName = "{$timestamp}_{$this->excelFile->baseName}.{$this->excelFile->extension}";
            $filePath = $path . '/' . $fileName;

            if ($this->excelFile->saveAs($filePath)) {
                // You can save the original name and file name in a property
                $this->excelFile->name = $fileName;
                return true;
            }
        }

        return false;
    }
}