<?php
namespace backend\modules\import\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ExcelUploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $excelFile;

    /**
     * @var string Tipo de importación (company, fleet, machinery)
     */
    public $type = 'company';

    /**
     * @var string Clave única para identificar la carga en la sesión
     */
    private $_uploadKey;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['excelFile'], 'required'],
            [['excelFile'], 'file', 'skipOnEmpty' => false,
                'extensions' => 'xlsx, xls',
                'maxSize' => 1024 * 1024 * 5], // 5MB máximo
            [['type'], 'string'],
            [['type'], 'in', 'range' => ['company', 'fleet', 'machinery', 'component']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'excelFile' => 'Excel File',
            'type' => 'Tipo de Importación',
        ];
    }

    /**
     * Sube el archivo y guarda la información en sesión
     * @return bool
     */
    public function upload()
    {
        if (!$this->validate()) {
            return false;
        }

        // Generar nombre seguro para el archivo
        $uploadKey = Yii::$app->security->generateRandomString(10);
        $fileName = $uploadKey . '.' . $this->excelFile->extension;

        // Crear directorio si no existe
        $uploadDir = Yii::getAlias('@webroot/uploads/excel');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filePath = $uploadDir . '/' . $fileName;

        // Guardar archivo
        if ($this->excelFile->saveAs($filePath)) {
            // Guardar información en sesión para validar luego
            $this->_uploadKey = $uploadKey;
            Yii::$app->session->set('uploadedFile_' . $uploadKey, [
                'name' => $fileName,
                'original_name' => $this->excelFile->name,
                'path' => $filePath,
                'timestamp' => time(),
                'user_id' => Yii::$app->user->id,
                'type' => $this->type, // Guardar el tipo de importación
            ]);
            return true;
        }

        return false;
    }

    /**
     * Devuelve la clave de carga
     * @return string
     */
    public function getUploadKey()
    {
        return $this->_uploadKey;
    }
}