<?php
namespace backend\modules\import\services;

use common\models\Company;
use common\models\Location;
use common\models\User;
use Yii;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\base\Component;
use yii\base\Exception;

/**
 * Service class for importing companies and locations from Excel files
 */
class ImportService extends Component
{
    /**
     * Processes an Excel file to import companies and locations
     * 
     * @param string $filePath Full path to the file
     * @param int $userId ID of the current user
     * @return array Import statistics
     * @throws Exception if there's an error in the process
     */
    public function processFile($filePath, $userId)
    {
        // Get the user's mining group
        $miningGroup = $this->getUserMiningGroup($userId);

        if (!$miningGroup) {
            throw new Exception('The current user is not associated with any mining group');
        }

        // Statistics
        $stats = [
            'companies_created' => 0,
            'companies_updated' => 0,
            'locations_created' => 0,
            'errors' => [],
        ];

        try {
            // Load Excel file
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Get data range
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            // Validate minimum Excel structure
            $headers = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $headers[$col] = $sheet->getCell($col . '1')->getValue();
            }

            // Process row by row
            for ($row = 2; $row <= $highestRow; $row++) {
                $result = $this->processRow($sheet, $row, $miningGroup->id);

                // Update statistics
                if ($result['success']) {
                    if ($result['company_created']) {
                        $stats['companies_created']++;
                    } else {
                        $stats['companies_updated']++;
                    }

                    if ($result['location_created']) {
                        $stats['locations_created']++;
                    }
                } else {
                    $stats['errors'][] = "Row $row: " . $result['error'];
                }
            }

            return $stats;

        } catch (\Exception $e) {
            Yii::error('Error processing file: ' . $e->getMessage(), 'import');
            throw new Exception('Error processing file: ' . $e->getMessage());
        }
    }

    /**
     * Processes a row from the Excel file
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param int $row Row number
     * @param int $miningGroupId Mining group ID
     * @return array Processing result
     */
    private function processRow($sheet, $row, $miningGroupId)
    {
        $result = [
            'success' => false,
            'company_created' => false,
            'location_created' => false,
            'error' => null
        ];

        // Read row data - Handle null values from getValue()
        $companyNameValue = $sheet->getCell('A' . $row)->getValue();
        $locationDataValue = $sheet->getCell('B' . $row)->getValue();

        $companyName = is_null($companyNameValue) ? '' : trim($companyNameValue);
        $locationData = is_null($locationDataValue) ? '' : trim($locationDataValue);

        // Validate required minimum data
        if (empty($companyName) || empty($locationData)) {
            $result['error'] = "Missing required data";
            Yii::warning($result['error'], 'import');
            return $result;
        }

        // Start transaction
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Process company
            $companyResult = $this->processCompany($companyName, $miningGroupId);
            $company = $companyResult['company'];
            $isNew = $companyResult['isNew'];
            
            // Process location
            $locationResult = $this->processLocation($locationData);
            if (!$locationResult['success']) {
                throw new \Exception($locationResult['error']);
            }
            $location = $locationResult['location'];

            // Associate location with company
            $company->location_id = $location->id;

            if (!$company->save()) {
                $errorMsg = "Error saving Company: " . json_encode($company->errors);
                Yii::error($errorMsg, 'import');
                throw new \Exception($errorMsg);
            }

            $result['company_created'] = $isNew;
            $result['location_created'] = true;
            $result['success'] = true;

            // Commit transaction
            $transaction->commit();

        } catch (\Exception $e) {
            // Rollback changes if something failed
            $transaction->rollBack();
            Yii::error("Error in row $row: " . $e->getMessage(), 'import');
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Gets the mining group associated with the user
     * 
     * @param int $userId User ID
     * @return \common\models\MiningGroup|null Mining group or null if it doesn't exist
     */
    private function getUserMiningGroup($userId)
    {
        $user = User::findOne($userId);
        if (!$user) {
            return null;
        }

        $miningGroup = $user->getMiningGroup()->one();
        return $miningGroup;
    }

    /**
     * Process company data from the Excel file
     * 
     * @param string $companyName Name of the company
     * @param int $miningGroupId Mining group ID
     * @return array Result containing the company object and whether it's new
     */
    private function processCompany($companyName, $miningGroupId)
    {
        $company = Company::findOne(["name" => $companyName, "mining_group_id" => $miningGroupId]);

        $isNew = false;

        if (!$company) {
            $company = new Company();
            $company->name = $companyName;
            $company->mining_group_id = $miningGroupId;
            $isNew = true;
        }
        
        return ['company' => $company, 'isNew' => $isNew];
    }

    /**
     * Process location data from the Excel file
     * 
     * @param string $locationData Location data in format "latitude,longitude"
     * @return array Result containing success status, location object (if successful), and error message (if failed)
     */
    private function processLocation($locationData)
    {
        // Initialize result
        $result = [
            'success' => false,
            'location' => null,
            'error' => null
        ];
        
        // Extract location coordinates
        $coordinates = explode(',', $locationData);
        if (count($coordinates) != 2) {
            $result['error'] = "Invalid location format. Must be 'latitude,longitude'";
            Yii::warning($result['error'], 'import');
            return $result;
        }

        // Handle potential null values in coordinates
        $latitude = (float) trim(is_null($coordinates[0]) ? '' : $coordinates[0]);
        $longitude = (float) trim(is_null($coordinates[1]) ? '' : $coordinates[1]);

        // Validate that coordinates are valid numbers
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            $result['error'] = "Coordinates must be valid numbers";
            Yii::warning($result['error'], 'import');
            return $result;
        }

        // Create location
        $location = new Location();
        $location->latitude = $latitude;
        $location->longitude = $longitude;

        if (!$location->save()) {
            $result['error'] = "Error saving location: " . json_encode($location->errors);
            Yii::error($result['error'], 'import');
            return $result;
        }
        
        $result['location'] = $location;
        $result['success'] = true;
        return $result;
    }
}