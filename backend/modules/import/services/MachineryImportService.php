<?php

namespace backend\modules\import\services;

use backend\modules\import\services\ImportServiceInterface;
use common\models\Company;
use common\models\User;
use common\models\MachineryType;
use common\models\Fleet;
use common\models\Area;
use common\models\Machinery;
use common\models\MiningGroup;
use common\models\MiningProcess;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use common\models\Location;
use Yii;

class MachineryImportService implements ImportServiceInterface
{
    /**
     * @throws \Exception
     */
    public function processFile($filePath, $userId)
    {
        $miningGroup = $this->getMiningGroup($userId);
        if (!$miningGroup) {
            throw new \Exception('User not associated to mining group');
        }
        $stats = [
            'machinery_created' => 0,
            'mining_process_created' => 0,
            'areas_created' => 0,
            'fleets_created' => 0,
            'companies_created' => 0,
            'machinery_types_created' => 0,
            'errors' => [],
        ];

        try {
            $spreadSheet = IOFactory::load($filePath);
            $sheet = $spreadSheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            for ($row = 2; $row <= $highestRow; $row++) {
                $company = trim($sheet->getCell('A' . $row)->getValue() ?? '');
                $miningProcess = trim($sheet->getCell('B' . $row)->getValue() ?? '');
                $machineryFamily = trim($sheet->getCell('C' . $row)->getValue() ?? '');
                $areaData = trim($sheet->getCell('D' . $row)->getValue() ?? '');
                $fleetData = trim($sheet->getCell('E' . $row)->getValue() ?? '');
                $machineryType = trim($sheet->getCell('F' . $row)->getValue() ?? '');
                $tag = trim($sheet->getCell('G' . $row)->getValue() ?? '');
                $brand = trim($sheet->getCell('H' . $row)->getValue() ?? '');
                $model = trim($sheet->getCell('I' . $row)->getValue() ?? '');
                $startedOperations = trim($sheet->getCell('J' . $row)->getValue() ?? '');
                $usefulLife = trim($sheet->getCell('K' . $row)->getValue() ?? '');
                $supplier = trim($sheet->getCell('L' . $row)->getValue() ?? '');
                $machineryCost = trim($sheet->getCell('M' . $row)->getValue() ?? '');
                $location = trim($sheet->getCell('N' . $row)->getValue() ?? '');

                $result = $this->processRow(
                    $row,
                    $company,
                    $machineryType,
                    $fleetData,
                    $brand,
                    $model,
                    $machineryFamily,
                    $areaData,
                    $startedOperations,
                    $usefulLife,
                    $supplier,
                    $machineryCost,
                    $miningGroup->id,
                    $location,
                    $miningProcess,
                    $tag,
                );

                // Actualizar estadísticas según el resultado
                if ($result['success']) {
                    if ($result['machinery']['isNew']) {
                        $stats['machinery_created']++;
                    }

                    if ($result['area']['isNew']) {
                        $stats['areas_created']++;
                    }

                    if ($result['fleet']['isNew']) {
                        $stats['fleets_created']++;
                    }

                    if ($result['company']['isNew']) {
                        $stats['companies_created']++;
                    }
                    if ($result['miningProcess']['isNew']) {
                        $stats['mining_process_created']++;
                    }

                    if ($result['machineryType']['isNew']) {
                        $stats['machinery_types_created']++;
                    }
                } else {
                    $stats['errors'][] = "Fila $row: " . $result['error'];
                }
            }

            return $stats;
        } catch (\Exception $e) {
            throw new \Exception('Error procesando archivo: ' . $e->getMessage());
        }
    }

    private function processRow(
        int $row,
        $companyName,
        $machineryType,
        $fleetData,
        $brand,
        $model,
        $machineryFamily,
        $areaData,
        $startedOperations,
        $usefulLife,
        $supplier,
        $machineryCost,
        $miningGroupId,
        $location,
        $miningProcess,
        $tag
    ) {
        $result = [
            'success' => false,
            'error' => null,
            'company' => ['isNew' => false],
            'miningProcess' => ['isNew' => false],
            'machinery' => ['isNew' => false],
            'area' => ['isNew' => false],
            'fleet' => ['isNew' => false],
            'machineryType' => ['isNew' => false]
        ];

        // Validación de campos obligatorios
        if (empty($companyName)) {
            $result['error'] = "Empty company name";
            return $result;
        }
        if (empty($miningProcess)) {
            $result['error'] = "Empty mining process";
            return $result;
        }
        if (empty($tag)) {
            $result['error'] = "Empty tag";
            return $result;
        }
        if (empty($machineryType)) {
            $result['error'] = "Empty machinery type";
            return $result;
        }
        if (empty($fleetData)) {
            $result['error'] = "Empty Fleet";
            return $result;
        }
        if (empty($machineryFamily)) {
            $result['error'] = "Empty Machinery family";
            return $result;
        }
        if (empty($areaData)) {
            $result['error'] = "Empty Area";
            return $result;
        }

        $validFamilies = ['SEMI', 'MOVIL', 'MÓVIL', 'FIJO', 'FIXED', 'MOBILE'];
        $normalizedFamily = strtoupper(trim($machineryFamily));

        if (!in_array($normalizedFamily, $validFamilies)) {
            $result['error'] = "Valor de familia no válido: $machineryFamily. Valores permitidos: SEMI, MÓVIL, FIJO";
            return $result;
        }

        // Mapeo a los valores ENUM en inglés
        if ($normalizedFamily == 'MÓVIL' || $normalizedFamily == 'MOVIL') {
            $normalizedFamily = 'MOBILE';
        } elseif ($normalizedFamily == 'FIJO') {
            $normalizedFamily = 'FIXED';
        }
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Encontrar o crear compañía
            $companyResult = $this->findCompany($companyName, $miningGroupId);
            if (!$companyResult['success']) {
                throw new \Exception($companyResult['error']);
            }
            $company = $companyResult['company'];
            $result['company']['isNew'] = $companyResult['isNew'];

            $miningProcessResult = $this->findMiningProcess($miningProcess, $company->id, $miningGroupId);
            if (!$miningProcessResult['success']) {
                throw new \Exception($miningProcessResult['error']);
            }
            $result['miningProcess']['isNew'] = $miningProcessResult['isNew'];
            $miningProcess = $miningProcessResult['miningProcess'];

            $areaResult = $this->findArea($areaData, $miningProcess->id, $miningGroupId);
            if (!$areaResult['success']) {
                throw new \Exception($areaResult['error']);
            }
            $area = $areaResult['area'];
            $result['area']['isNew'] = $areaResult['isNew'];

            // Encontrar o crear tipo de maquinaria
            $machineryTypeResult = $this->findMachineryType($machineryType, $miningGroupId);
            if (!$machineryTypeResult['success']) {
                throw new \Exception($machineryTypeResult['error']);
            }
            $machineryType = $machineryTypeResult['machineryType'];
            $result['machineryType']['isNew'] = $machineryTypeResult['isNew'];

            // Encontrar o crear flota
            $fleetResult = $this->findFleet($fleetData, $area->id, $miningGroupId);
            if (!$fleetResult['success']) {
                throw new \Exception($fleetResult['error']);
            }
            $fleet = $fleetResult['fleet'];
            $result['fleet']['isNew'] = $fleetResult['isNew'];

            // Procesar fecha de inicio de operaciones
            $formattedStartedOperations = $this->formatDate($startedOperations);

            // Crear nueva maquinaria (cada fila es una unidad física diferente)
            $processLocation = $this->generateLocation($location);
            $machineryUniqueTag = $this->generateUniqueTag($tag, $area->name, $fleet->name, $miningProcess->name, $company->name);
            $machineryResult = $this->processMachinery(
                $miningGroupId,
                $fleet->id,
                $machineryType,
                $normalizedFamily,
                $brand,
                $model,
                $formattedStartedOperations,
                $usefulLife,
                $supplier,
                $machineryCost,
                $processLocation,
                $tag,
                $machineryUniqueTag
            );

            if (!$machineryResult['success']) {
                throw new \Exception($machineryResult['error']);
            }

            $result['machinery']['isNew'] = $machineryResult['isNew'];
            $transaction->commit();
            $result['success'] = true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Crea una nueva maquinaria
     */
    private function processMachinery(
        $miningGroupId,
        $fleetId,
        $machineryType,
        $machineryFamily,
        $brand,
        $model,
        $startedOperations,
        $usefulLife,
        $supplier,
        $machineryCost,
        $processLocation,
        $tag,
        $machineryUniqueTag
    ) {
        $result = [
            'success' => false,
            'error' => null,
            'machinery' => null,
            'isNew' => true,
        ];


        try {
            // Siempre crear una nueva maquinaria
            $machinery = new Machinery();
            $machinery->mining_group_id = $miningGroupId;
            $machinery->fleet_id = $fleetId;
            $machinery->machinery_type_id = $machineryType->id;
            $machinery->family = $machineryFamily;
            $machinery->location_id = $processLocation->id;
            $machinery->tag = $tag;
            $machinery->unique_tag = $machineryUniqueTag;

            // Asignar campos opcionales solo si tienen valor
            if (!empty($brand)) {
                $machinery->brand = $brand;
            }

            if (!empty($model)) {
                $machinery->model = $model;
            }

            if (!empty($startedOperations)) {
                $machinery->start_operation = $startedOperations;
            }

            if (!empty($usefulLife)) {
                $machinery->lifespan_years = $usefulLife;
            }

            if (!empty($supplier)) {
                $machinery->supplier = $supplier;
            }

            if (!empty($machineryCost)) {
                $machinery->cost = $machineryCost;
            }

            if (!$machinery->save()) {
                throw new \Exception("Error guardando maquinaria: " . implode(", ", $machinery->getErrorSummary(true)));
            }

            $result['success'] = true;
            $result['machinery'] = $machinery;
        } catch (\Exception $e) {
            $result['error'] = "Error procesando maquinaria: " . $e->getMessage();
        }
        return $result;
    }

    /**
     * Formatea una fecha al formato YYYY-MM-DD para la base de datos
     */
    private function formatDate($date)
    {
        if (empty($date)) {
            return null;
        }

        $date = trim($date);

        // Intentar formato DD-MM-YYYY
        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $date, $matches)) {
            $day = (int) $matches[1];
            $month = (int) $matches[2];
            $year = (int) $matches[3];

            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        // Si es numérico, tratar como número de serie de Excel
        if (is_numeric($date)) {
            // La fecha base de Excel es 30/12/1899 para sistemas Windows
            $unixTimestamp = ($date - 25569) * 86400;
            $phpDate = date('Y-m-d', $unixTimestamp);

            // Verificar que la fecha producida es válida
            $dateParts = explode('-', $phpDate);
            if (count($dateParts) == 3) {
                $year = (int) $dateParts[0];
                $month = (int) $dateParts[1];
                $day = (int) $dateParts[2];

                if (checkdate($month, $day, $year)) {
                    return $phpDate;
                }
            }
        }

        return null;
    }


    private function getMiningGroup($userId)
    {
        $user = User::findOne($userId);
        if (!$user) {
            return null;
        }
        return $user->getMiningGroup()->one();
    }

    private function findCompany($companyName, $miningGroupId)
    {
        $result = [
            'success' => false,
            'error' => null,
            'company' => null,
            'isNew' => false
        ];

        if (empty($companyName)) {
            $result['error'] = "Nombre de compañía vacío";
            return $result;
        }

        try {
            $company = Company::findOne(['name' => $companyName, 'mining_group_id' => $miningGroupId]);
            if (!$company) {
                $company = new Company();
                $company->name = $companyName;
                $company->mining_group_id = $miningGroupId;

                if (!$company->save()) {
                    throw new \Exception("Error guardando compañía: " . implode(", ", $company->getErrorSummary(true)));
                }
                $result['isNew'] = true;
            }

            $result['success'] = true;
            $result['company'] = $company;
        } catch (\Exception $e) {
            $result['error'] = "Error procesando compañía: " . $e->getMessage();
        }

        return $result;
    }

    private function findArea($areaName, $miningProcessId, $miningGroupId)
    {
        $result = [
            'success' => false,
            'error' => null,
            'area' => null,
            'isNew' => false
        ];

        if (empty($areaName)) {
            $result['error'] = "Nombre de área vacío";
            return $result;
        }

        try {
            $area = Area::findOne(['name' => $areaName, 'mining_process_id' => $miningProcessId, 'mining_group_id' => $miningGroupId]);
            if (!$area) {
                $area = new Area();
                $area->name = $areaName;
                $area->mining_process_id = $miningProcessId;
                $area->mining_group_id = $miningGroupId;

                if (!$area->save()) {
                    throw new \Exception("Error guardando área: " . implode(", ", $area->getErrorSummary(true)));
                }
                $result['isNew'] = true;
            }
            $result['success'] = true;
            $result['area'] = $area;
        } catch (\Exception $e) {
            $result['error'] = "Error procesando área: " . $e->getMessage();
        }
        return $result;
    }

    private function findMachineryType($machineryTypeName, $miningGroupId)
    {
        $result = [
            'success' => false,
            'error' => null,
            'machineryType' => null,
            'isNew' => false
        ];

        if (empty($machineryTypeName)) {
            $result['error'] = "Nombre de tipo de maquinaria vacío";
            return $result;
        }

        try {
            $machineryType = MachineryType::findOne(['name' => $machineryTypeName, 'mining_group_id' => $miningGroupId]);
            if (!$machineryType) {
                $machineryType = new MachineryType();
                $machineryType->name = $machineryTypeName;
                $machineryType->mining_group_id = $miningGroupId;


                if (!$machineryType->save()) {
                    throw new \Exception("Error guardando tipo de maquinaria: " . implode(", ", $machineryType->getErrorSummary(true)));
                }
                $result['isNew'] = true;
            }
            $result['success'] = true;
            $result['machineryType'] = $machineryType;
        } catch (\Exception $e) {
            $result['error'] = "Error procesando tipo de maquinaria: " . $e->getMessage();
        }
        return $result;
    }

    private function findFleet($fleetName, $areaId, $miningGroupId)
    {
        $result = [
            'success' => false,
            'error' => null,
            'fleet' => null,
            'isNew' => false
        ];

        if (empty($fleetName)) {
            $result['error'] = "Nombre de flota vacío";
            return $result;
        }

        try {
            $area = Area::findOne($areaId);
            $fleet = Fleet::findOne(['name' => $fleetName, 'area_id' => $areaId, 'mining_group_id' => $miningGroupId]);
            if (!$fleet) {
                $fleet = new Fleet();
                $fleet->name = $fleetName;
                $fleet->area_id = $areaId;
                $fleet->mining_group_id = $miningGroupId;

                if (!$fleet->save()) {
                    throw new \Exception("Error guardando flota: " . implode(", ", $fleet->getErrorSummary(true)));
                }
                $result['isNew'] = true;
            }
            $result['success'] = true;
            $result['fleet'] = $fleet;
        } catch (\Exception $e) {
            $result['error'] = "Error procesando flota: " . $e->getMessage();
        }
        return $result;
    }
    private function generateLocation($locationData)
    {
        $location = new Location();
        $location->location_url = $locationData;

        if (!$location->save()) {
            throw new \Exception('Error saving location');
        }
        return $location;
    }

    private function findMiningProcess($miningProcessName, $companyId, $miningGroupId)
    {
        $result = [
            'success' => false,
            'error' => null,
            'miningProcess' => null,
            'isNew' => false
        ];

        if (empty($miningProcessName)) {
            $result['error'] = "Nombre de proceso minero vacío";
            return $result;
        }

        try {
            $miningProcess = MiningProcess::findOne(['name' => $miningProcessName, 'company_id' => $companyId, 'mining_group_id' => $miningGroupId]);
            if (!$miningProcess) {
                $miningProcess = new MiningProcess();
                $miningProcess->name = $miningProcessName;
                $miningProcess->company_id = $companyId;
                $miningProcess->mining_group_id = $miningGroupId;

                if (!$miningProcess->save()) {
                    throw new \Exception("Error guardando proceso minero: " . implode(", ", $miningProcess->getErrorSummary(true)));
                }
                $result['isNew'] = true;
            }
            $result['success'] = true;
            $result['miningProcess'] = $miningProcess;
        } catch (\Exception $e) {
            $result['error'] = "Error procesando proceso minero: " . $e->getMessage();
        }
        return $result;
    }
    private function generateUniqueTag($tag, $areaName, $fleetName, $miningProcessName, $companyName)
    {
        $areaName = str_replace(' ', '_', $areaName);
        $fleetName = str_replace(' ', '_', $fleetName);
        $miningProcessName = str_replace(' ', '_', $miningProcessName);
        $companyName = str_replace(' ', '_', $companyName);

        $uniqueTag = $tag . "-" . $areaName . "-" . $fleetName . "-" . $miningProcessName . "-" . $companyName;
        return $uniqueTag;
    }

    public function generateTemplate($path)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Compañía*');
        $sheet->setCellValue('B1', 'Proceso Minero*');
        $sheet->setCellValue('C1', 'Familia Equipos* (SEMI/MOVIL/FIJO)');
        $sheet->setCellValue('D1', 'Área*');
        $sheet->setCellValue('E1', 'Planta/Flota*');
        $sheet->setCellValue('F1', 'Tipo de Equipo*');
        $sheet->setCellValue('G1', 'Tag/Código*');
        $sheet->setCellValue('H1', 'Marca');
        $sheet->setCellValue('I1', 'Modelo');
        $sheet->setCellValue('J1', 'Inicio Operaciones (DD-MM-YYYY)');
        $sheet->setCellValue('K1', 'Vida Útil Equipo (años)');
        $sheet->setCellValue('L1', 'Proveedor');
        $sheet->setCellValue('M1', 'Costo Maquinaria MUSD');
        $sheet->setCellValue('N1', 'Ubicación (lat,lng)');

        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '99CCFF'],
            ],
        ]);

        // Auto-ajustar anchos de columna
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Ejemplo de datos
        $sheet->setCellValue('A2', 'EL TOFO');
        $sheet->setCellValue('B2', 'EXTRACCIÓN');
        $sheet->setCellValue('C2', 'MOBILE');
        $sheet->setCellValue('D2', 'TRANSPORTE MINA');
        $sheet->setCellValue('E2', 'CAEX-KMTSU');
        $sheet->setCellValue('F2', 'CAEX');
        $sheet->setCellValue('G2', 'CAEX-001');
        $sheet->setCellValue('H2', 'Komatsu');
        $sheet->setCellValue('I2', '830E-AC');
        $sheet->setCellValue('J2', '01-02-2014');
        $sheet->setCellValue('K2', '10');
        $sheet->setCellValue('L2', 'Komatsu');
        $sheet->setCellValue('M2', '800000');
        $sheet->setCellValue('N2', '-29.9574,-71.3089');

        // Dar formato a los datos de ejemplo
        $sheet->getStyle('A2:N2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EEEEEE'], // Gris claro para la fila de ejemplo
            ],
        ]);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($path);

        return true;
    }
}
