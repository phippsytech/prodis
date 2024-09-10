<?php
namespace NDISmate\Services\ParticipantService;

use RedBeanPHP\R as R;
use RedBeanPHP\RedException;
use NDISmate\Utilities\ConvertFieldsToBoolean;

class ListParticipantServicesByParticipantId
{
    public function __invoke($data)
    {
        try {
            $bean = R::getAll(
                'SELECT 
                    clientplanservices.id as id,
                    services.id as service_id,
                    services.code as service_code,
                    services.name as services_name,
                    clientplanservices.rate as service_rate,
                    clientplanservices.budget as budget,
                    clientplanservices.budget_start_date,
                    clientplanservices.kilometer_budget as kilometer_budget,
                    clientplanservices.is_active AS is_active,
                    clientplanservices.adjust_weekly_time AS adjust_weekly_time
                FROM clientplanservices 
                JOIN services on (services.id = clientplanservices.service_id) 
                JOIN clientplans on (clientplans.id = clientplanservices.plan_id) 
                WHERE clientplans.is_active=1 AND clientplanservices.participant_id=:participant_id',
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
