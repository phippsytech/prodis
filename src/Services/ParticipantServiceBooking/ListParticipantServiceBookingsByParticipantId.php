<?php
namespace NDISmate\Services\ParticipantServiceBooking;

use RedBeanPHP\R as R;
use RedBeanPHP\RedException;
use NDISmate\Utilities\ConvertFieldsToBoolean;

class ListParticipantServiceBookingsByParticipantId
{
    public function __invoke($data)
    {
        try {
            $bean = R::getAll(
                'SELECT 
                    servicebookings.id as id,
                    services.id as service_id,
                    services.code as service_code,
                    services.name as services_name,
                    servicebookings.rate as service_rate,
                    servicebookings.budget as budget,
                    servicebookings.budget_start_date,
                    servicebookings.kilometer_budget as kilometer_budget,
                    servicebookings.is_active AS is_active,
                    servicebookings.adjust_weekly_time AS adjust_weekly_time
                FROM servicebookings 
                JOIN services on (services.id = servicebookings.service_id) 
                JOIN serviceagreements on (serviceagreements.id = servicebookings.plan_id) 
                WHERE serviceagreements.is_active=1 AND servicebookings.participant_id=:participant_id',
                [':participant_id' => $data['participant_id']]
            );

            $converter = new ConvertFieldsToBoolean();
            $bean = $converter($bean, ['adjust_weekly_time']);

            return $bean;
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
