<?php
namespace backend\modules\import\services;
use Yii;
use backend\modules\import\services\ImportServiceInterface;
use common\models\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class ComponentImportService implements ImportServiceInterface
{
    public function processFile($filePath, $userId){
        $miningGroup = $this->getMiningGroup($userId);
        if (!$miningGroup){
            throw new \Exception('User not associated to mining group');
        }
        $stats = [
            "components_created" => 0,
            'errors' => [],
        ];
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            for ($row = 2; $row <= $highestRow; $row++){
                $machineryTag = trim($sheet->getCell('A' . $row)->getValue() ?? '');
                $componentName = trim($sheet->getCell('B' . $row)->getValue() ?? '');
                $componentTag = trim($sheet->getCell('C' . $row)->getValue() ?? '');
                $model = trim($sheet->getCell('D' . $row)->getValue() ?? '');
                $startedOperations = trim($sheet->getCell('E' . $row)->getValue() ?? '');
                $usefulLifeYear = trim($sheet->getCell('F'. $row)->getValue() ?? '');
                $usefulLifeHours = trim($sheet->getCell('G'. $row)->getValue() ?? '');
                $supplier = trim($sheet->getCell('H' . $row)->getValue() ?? '');
                $componentCost = trim($sheet->getCell('I' . $row)->getValue() ?? '');
                $maintenancePlan = trim($sheet->getCell('J' . $row)->getValue() ?? '');
                $inspectionPlan = trim($sheet->getCell('K' . $row)->getValue() ?? '');
                $location = trim($sheet->getCell('L' . $row)->getValue() ?? '');

                $result = $this->processComponentRow(
                    $machineryTag,
                    $componentName,
                    $componentTag,
                    $model,
                    $startedOperations,
                    $usefulLifeYear,
                    $usefulLifeHours,
                    $supplier,
                    $componentCost,
                    $maintenancePlan,
                    $inspectionPlan,
                    $location
                );


                // Procesar la fila y obtener resultado
        } catch (\Throwable $th) {
            throw $th;
        }
        

    }
public function processComponentRow(
    $machineryTag,
    $componentName,
    $componentTag,
    $model,
    $startedOperations,
    $usefulLifeYear,
    $usefulLifeHours,
    $supplier,
    $componentCost,
    $maintenancePlan,
    $inspectionPlan,
    $location
){
    $result = [
        'success' => false,
        'isNew' => true,
        'error' => null,
    ];
    
    // Validar campos uno por uno
    if (empty($machineryTag)) {
        $result['error'] = 'El campo Tag Equipo es requerido';
        return $result;
    }
    
    if (empty($componentName)) {
        $result['error'] = 'El campo Nombre Componente es requerido';
        return $result;
    }
    
    if (empty($componentTag)) {
        $result['error'] = 'El campo Tag Componente es requerido';
        return $result;
    }
    
    if (empty($model)) {
        $result['error'] = 'El campo Modelo es requerido';
        return $result;
    }
    
    if (empty($startedOperations)) {
        $result['error'] = 'El campo Inicio de Operaciones es requerido';
        return $result;
    }
    
    if (empty($usefulLifeYear)) {
        $result['error'] = 'El campo Vida Útil (Años) es requerido';
        return $result;
    }
    
    if (empty($usefulLifeHours)) {
        $result['error'] = 'El campo Vida Útil (Horas) es requerido';
        return $result;
    }
    
    if (empty($supplier)) {
        $result['error'] = 'El campo Proveedor es requerido';
        return $result;
    }
    
    if (empty($componentCost)) {
        $result['error'] = 'El campo Costo Componente es requerido';
        return $result;
    }
    
    if (empty($maintenancePlan)) {
        $result['error'] = 'El campo Plan de Mantenimiento es requerido';
        return $result;
    }
    
    if (empty($inspectionPlan)) {
        $result['error'] = 'El campo Plan de Inspección es requerido';
        return $result;
    }
    
    if (empty($location)) {
        $result['error'] = 'El campo Ubicación es requerido';
        return $result;
    }
    
    $machinery = $this->getMachinery($machineryTag,$miningGroup->id);
    if(!machinery){
        $result['error'] = 'El equipo no existe';
        return $result;
    }



   
    
    
    return $result;
}
//por ahora asi, hay que cambiar el tag del equipo por el tag personalizado
private function getMachinery($machineryTag){
    $machinery = Machinery::findOne(['tag' => $machineryTag], ['mining_group_id' => $miningGroup->id]);
    if ($machinery) {
        return $machinery;
    }
    return null;
}
    public function generateTemplate($path){

    }
    private function getMiningGroup($userId){
        $user = User::findOne($userId);
        if ($user && $user->miningGroup) {
            return $user->miningGroup()->one();
        }
        return null;
    }
}