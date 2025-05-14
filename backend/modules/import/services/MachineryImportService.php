<?php
namespace backend\modules\import\services;
use backend\modules\import\services\ImportServiceInterface;
use common\models\Company;
use common\models\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
class MachineryImportService implements ImportServiceInterface{

    /**
     * @throws \Exception
     */
    public function processFile($filePath, $userId)
    {
        $miningGroup = $this->getMiningGroup($userId);
        if(!$miningGroup) {
            throw new \Exception('User not associated to mining group');
        }
        $stats = [
            'machinery_created' => 0,
            'machinery_updated' => 0,
            'errors' => [],
        ];
        $spreadSheet = IOFactory::load($filePath);
        $sheet = $spreadSheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++){
            $company = $sheet->getCell('A' . $row)->getValue();
            $machineryType = $sheet->getCell('B' . $row)->getValue();
            $fleetData = $sheet->getCell('C' . $row)->getValue();
            $brand = $sheet->getCell('D' . $row)->getValue();
            $model = $sheet->getCell('E' . $row)->getValue();
            $machineryFamily = $sheet->getCell('F' . $row)->getValue();
            $areaData = $sheet->getCell('G' . $row)->getValue();
            $startedOperations = $sheet->getCell('H' . $row)->getValue();
            $usefulLife = $sheet->getCell('I' . $row)->getValue();
            $supplier = $sheet->getCell('J' . $row)->getValue();
            $machineryCost = $sheet->getCell('K' . $row)->getValue();

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
                $miningGroup->id
            );



        }
    }

    private function processRow(int $row, $company, $machineryType, $fleetData, $brand, $model, $machineryFamily, $areaData, $startedOperations, $usefulLife, $supplier, $machineryCost, $miningroupId)
    {
        $result = [
            'success' => false,
            'error' => null,
            'machinery' => ['isNew' => false],
            'area' => ['isNew' => false],
            'fleet' => ['isNew' => false],
            'machineryType' => ['isNew' => false],
            'machineryFamily'=>['isNew'=>false]
        ];
        if (empty($company)){
            $result['error'] = "Empty company name";
            return $result;
        }
        if (empty($machineryType)){
            $result['error'] = "Empty machinery type";
            return $result;
        }
        if(empty($fleetData)){
            $result['error'] = "Empty Fleet";
            return $result;
        }
        if(empty($machineryFamily)){
            $result['error'] = "Empty Machinery family";
            return $result;
        }
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $companyResult = $this->findCompany($company,$miningroupId);
        }
    }

    public function generateTemplate($path)
    {
        // TODO: Implement generateTemplate() method.
    }

    private function getMiningGroup($userId){
        $user = User::findOne($userId);
        if (!$user){
            return null;
        }
        return $user->getMiningGroup()->one();

    }

    private function findCompany($company,$miningroupId)
    {
        $companyResult = Company::findOne(['name' => $company, 'mining_group_id' => $miningroupId]);
        $isNew = false;
        if(!$companyResult){
            $newCompany = new Company();
            $newCompany->name = $company;
            $newCompany->
        }
    }


}
