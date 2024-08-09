<?php

namespace NDISmate\Services\ObjectStorageService;

use Exception;
use RedBeanPHP\R as R;
use NDISmate\Services\MessageQueueService;

class MigrateFromGoogleDrive
{
    public function __invoke($data)
    {
        $results = [];
        try {
            $results = $this->migrateCredentials('staffcredentials');
            return $results;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function migrateCredentials(string $table): array
    {
        $googleDriveFileRefs = R::getCol("SELECT google_drive_file_ref FROM {$table} WHERE google_drive_file_ref IS NOT NULL LIMIT 10");

        $results = [];

        if (count($googleDriveFileRefs) > 0) {
            foreach ($googleDriveFileRefs as $value) {
                $message = [
                    'google_drive_file_ref' => $value,
                    'table' => $table
                ];
                MessageQueueService::sendToQueue('google_drive_migration', $message);
                $results[] = $message;
            }
        }

        return $results;
    }
}
