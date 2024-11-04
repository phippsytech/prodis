<?php

namespace NDISmate\Services\ParticipantServiceBooking;

use RedBeanPHP\R as R;
use RedBeanPHP\RedException;

class CheckUniqueServiceBooking
{
    function __invoke($data)
    {
        try {

            $bean_count = 0;

            // Determine whether we are checking for drafts or active bookings
            $is_draft = isset($data['is_draft']) && $data['is_draft'] == 1;
            $status_column = $is_draft ? 'is_draft' : 'is_active';

            if (empty($data['id'])) {
                // Creating a new booking, need to check for conflicts
                $bean_count = R::getCell(
                    "SELECT COUNT(*) as servicebookings_count
                     FROM servicebookings 
                     LEFT JOIN serviceagreements sa on servicebookings.id = sa.plan_id
                     WHERE participant_id = :participant_id
                     AND service_id = :service_id
                     AND servicebookings.$status_column = 1
                     AND sa.status IN ('pending', 'draft')",

                    [
                        ':participant_id' => $data['participant_id'],
                        ':service_id' => $data['service_id']
                    ]
                );
                
            } else {
                // Updating an existing booking
                // First, fetch the current service_id for this record
                $current_service_booking = R::getRow(
                    'SELECT participant_id, service_id FROM servicebookings WHERE id = :current_id',
                    [':current_id' => $data['id']]
                );
                
                $current_service_id = $current_service_booking['service_id'];
                $participant_id = $current_service_booking['participant_id'];

                // Check if the service_id has changed
                if ($data['service_id'] != $current_service_id) {
                    // The service_id has changed, so we need to check for conflicts
                    $bean_count = R::getCell(
                        "SELECT COUNT(*) as servicebookings_count
                         FROM servicebookings
                         WHERE participant_id = :participant_id
                         AND service_id = :service_id
                         AND {$status_column} = 1
                         AND id != :current_id",  // Exclude the current booking record
                        [
                            ':participant_id' => $participant_id,
                            ':service_id' => $data['service_id'],
                            ':current_id' => $data['id']
                            ]
                        );
                }
            }

            // If no conflicting servicebookings found, return true
            if ($bean_count == 0) return true;

            // If conflicting servicebookings found, return false
            return false;
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
