<?php
namespace NDISmate\Models\Client\Plan\Service;

use \RedBeanPHP\R as R;

class GetClientServiceSummary
{
    public function __invoke($data)
    {
        /*
         * I want this to be stand alone so I can use
         * * client_id
         * * service_id
         * to display the widget.
         *
         *
         *
         * service_code
         *
         * budget_total_minutes
         * budget_minutes_per_week
         * budget_expected_state // how many minutes should have been used to stay on budget
         *
         * elapsed_weeks
         *
         * actual_minutes // the actual minutes that have been recorded
         *
         * expected_diff_actual_minutes //
         *
         *
         * last_seen // how many days ago the client was last seen for this service
         *
         *
         * // future: last_seen_by_staff_id
         */

        // validate that the client_id and service_id are set
        if (!isset($data['client_id']) || !is_numeric($data['client_id']) || !isset($data['service_id']) || !is_numeric($data['service_id'])) {
            return ['http_code' => 400, 'error_message' => 'client_id and service_id are required'];
        }

        $service = R::getRow('SELECT
        clientplans.service_agreement_signed_date,
        clientplans.service_agreement_end_date,
        services.budget_display as budget_display,
        clientplanservices.rate
        services.code AS service_code,
        services.billing_code,

        supportitems.support_item_name,
        supportitems.registration_group_name,
        supportitems.support_category_name,

        clientplanservices.xero_account_code AS xero_account_code,
        clientplanservices.budget AS budget,
        clientplanservices.kilometer_budget as kilometer_budget,
        clientplanservices.plan_manager_id as plan_manager_id,
        planmanagers.name as plan_manager_name
    FROM clientplanservices 
    JOIN clientplans ON (clientplans.id = clientplanservices.plan_id) 
    JOIN services ON (services.id = clientplanservices.service_id) 
    JOIN supportitems ON (supportitems.support_item_number = services.billing_code)
    left JOIN planmanagers on (planmanagers.id = clientplanservices.plan_manager_id)
    WHERE 
        clientplans.client_id=:client_id 
        AND clientplanservices.service_id=:service_id',
            [
                ':service_id' => $data['service_id'],
                ':client_id' => $data['client_id']
            ]);

        if (!isset($service['service_agreement_signed_date'])) {
            return ['http_code' => 404, 'error_message' => 'Service Agreement Signed Date Not Found'];
        }

        // get the timetracking data
        $beans = R::getRow(
            'SELECT 

        SUM( 
            session_duration +
            IFNULL((CASE WHEN actual_travel_time > 30 THEN 30 ELSE actual_travel_time END),0) 
        ) as total_session_minutes
        
    FROM timetrackings 
    WHERE client_id=:client_id 
        AND service_id=:service_id
        AND session_date >= :service_agreement_signed_date
',
            [
                ':client_id' => $data['client_id'],
                ':service_id' => $data['service_id'],
                ':service_agreement_signed_date' => $service['service_agreement_signed_date']
            ]
        );
        if (!count($beans)) {
            return ['http_code' => 404, 'error_message' => 'Session duration Not Found'];
        }

        $timetracking = $beans;

        // get the timetracking data
        $beans = R::getRow(
            'SELECT 
        session_date,
        client_id,
        service_id
     FROM timetrackings 
    WHERE client_id=:client_id 
        AND service_id=:service_id
        AND session_date >= :service_agreement_signed_date
    ORDER BY session_date desc 
    LIMIT 1
', [
                ':client_id' => $data['client_id'],
                ':service_id' => $data['service_id'],
                ':service_agreement_signed_date' => $service['service_agreement_signed_date']
            ]
        );
        $last_seen = $beans;

        if (!count($beans)) {
            $last_seen = null;
            // return ["http_code"=>404, "error_message"=>"Last seen date Not Found"];
        }

        return ['http_code' => 200, 'service' => $service, 'timetracking' => $timetracking, 'last_seen' => $last_seen];
    }
}
