<?php
namespace NDISmate\Services\ReportService;

use \RedBeanPHP\R as R;

class FilterRecordsByTherapistId
{
  public function __invoke($records, $therapistId)
  {
    $filteredRecords = array();
    foreach ($records as $record) {
      $therapists = $record['therapists'];
      foreach ($therapists as $therapist) {
        if ($therapist['therapist_id'] == $therapistId) {
          $filteredRecords[] = $record;
          break;  // No need to check other therapists in the same record
        }
      }
    }
    return $filteredRecords;
  }
}
