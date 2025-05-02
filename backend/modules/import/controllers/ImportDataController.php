<?php
namespace backend\modules\import\controllers;

use backend\modules\import\models\ExcelUploadForm;
use common\models\Location;
use common\models\Company;
use common\models\User;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportDataController extends Controller
{
    public function actionIndex()
    {
        $model = new ExcelUploadForm();

        Yii::info('Accediendo a la página de importación', 'import');

        if (Yii::$app->request->isPost) {
            $model->excelFile = UploadedFile::getInstance($model, 'excelFile');

            if ($model->upload()) {
                Yii::info('Archivo subido correctamente: ' . $model->excelFile->name, 'import');
                return $this->redirect(['process', 'file' => $model->excelFile->name]);
            } else {
                Yii::error('Error al subir el archivo: ' . json_encode($model->errors), 'import');
            }
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionProcess($file)
    {

        // Obtener el ID del grupo minero asociado al usuario actual
        $userId = Yii::$app->user->id;
        $userMiningGroup = $this->getUserMiningGroup($userId);

        if (!$userMiningGroup) {
            Yii::$app->session->setFlash('error', 'El usuario actual no está asociado a ningún grupo minero');
            return $this->redirect(['index']);
        }

        Yii::info('Usuario ID ' . $userId . ' asociado al grupo minero ID ' . $userMiningGroup->id, 'import');

        // Ruta del archivo subido
        $filePath = Yii::getAlias('@webroot/uploads/') . $file;
        Yii::info('Iniciando procesamiento del archivo: ' . $file, 'import');

        try {
            // Cargar archivo Excel
            Yii::info('Cargando archivo Excel', 'import');
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Obtener el rango de datos
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            Yii::info("Datos encontrados: $highestRow filas, hasta la columna $highestColumn", 'import');

            // Array para almacenar estadísticas
            $stats = [
                'companies_created' => 0,
                'companies_updated' => 0,
                'locations_created' => 0,
                'errors' => [],
            ];

            // Comenzamos desde la fila 2 para saltar encabezados
            Yii::info('Comenzando procesamiento fila por fila', 'import');
            for ($row = 2; $row <= $highestRow; $row++) {
                // Leer datos de la fila
                $companyName = $sheet->getCell('A' . $row)->getValue();
                $locationData = $sheet->getCell('B' . $row)->getValue();

                Yii::info("Procesando fila $row: Compañía='$companyName', Ubicación='$locationData'", 'import');

                // Validar datos mínimos requeridos
                if (empty($companyName) || empty($locationData)) {
                    $errorMsg = "Fila $row: faltan datos obligatorios";
                    Yii::warning($errorMsg, 'import');
                    $stats['errors'][] = $errorMsg;
                    continue;
                }

                // Extraer coordenadas de ubicación
                $coordinates = explode(',', $locationData);
                if (count($coordinates) != 2) {
                    $errorMsg = "Fila $row: formato de ubicación inválido. Debe ser 'latitud,longitud'";
                    Yii::warning($errorMsg, 'import');
                    $stats['errors'][] = $errorMsg;
                    continue;
                }

                $latitude = (float)trim($coordinates[0]);
                $longitude = (float)trim($coordinates[1]);

                // Iniciar transacción para asegurar integridad
                $transaction = Yii::$app->db->beginTransaction();
                Yii::info("Iniciando transacción para fila $row", 'import');

                try {
                    // 1. Procesar la compañía
                    // Buscar por nombre de compañía y grupo_id para evitar duplicados
                    $company = Company::findOne([
                        'name' => $companyName,
                        'mining_group_id' => $userMiningGroup->id
                    ]);

                    $isNew = false;

                    if (!$company) {
                        Yii::info("Creando nueva compañía: $companyName", 'import');
                        $company = new Company();
                        $company->name = $companyName;
                        $company->mining_group_id = $userMiningGroup->id;
                        $isNew = true;
                    } else {
                        Yii::info("Actualizando compañía existente: $companyName (ID: {$company->id})", 'import');
                    }

                    // 2. Crear y asociar la ubicación a la compañía
                    $location = new Location();
                    $location->latitude = $latitude;
                    $location->longitude = $longitude;

                    if (!$location->save()) {
                        $errorMsg = "Error al guardar ubicación: " . json_encode($location->errors);
                        Yii::error($errorMsg, 'import');
                        throw new \Exception($errorMsg);
                    }

                    $stats['locations_created']++;

                    // Asociar la ubicación a la compañía
                    $company->location_id = $location->id;

                    if (!$company->save()) {
                        $errorMsg = "Error al guardar Compañía: " . json_encode($company->errors);
                        Yii::error($errorMsg, 'import');
                        throw new \Exception($errorMsg);
                    }

                    if ($isNew) {
                        $stats['companies_created']++;
                        Yii::info("Compañía '$companyName' creada exitosamente", 'import');
                    } else {
                        $stats['companies_updated']++;
                        Yii::info("Compañía '$companyName' actualizada exitosamente", 'import');
                    }

                    // Confirmar la transacción
                    $transaction->commit();
                    Yii::info("Transacción completada para fila $row", 'import');

                } catch (\Exception $e) {
                    // Revertir cambios si algo falló
                    $transaction->rollBack();
                    Yii::error("Error en fila $row: " . $e->getMessage(), 'import');
                    $stats['errors'][] = "Fila $row: " . $e->getMessage();
                }
            }

            // Mostrar resultados
            Yii::info('Proceso de importación completado. Estadísticas: ' . json_encode($stats), 'import');
            return $this->render('result', [
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            $errorMsg = 'Error al procesar archivo: ' . $e->getMessage();
            Yii::error($errorMsg, 'import');
            Yii::$app->session->setFlash('error', $errorMsg);
            return $this->redirect(['index']);
        }
    }

    /**
     * Obtiene el grupo minero asociado al usuario actual
     *
     * @param int $userId ID del usuario
     * @return \common\models\MiningGroup|null Grupo minero o null si no existe
     */
    private function getUserMiningGroup($userId)
    {

        $user = User::findOne($userId);
        $miningGroup = $user->getMiningGroup();
        if (!$user) {
            return null;
        }
        return $user->getMiningGroup();
    }
}