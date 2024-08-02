<?php
namespace NDISmate\Services\TimeTrackingService;

use \RedBeanPHP\R as R;

class DeleteTimeTracking
{
    public function __invoke($data)
    {
        $timetracking_id = $data['id'];

        // Delete Time Tracking
        $bean = R::load('timetrackings', $timetracking_id);
        if (!$bean->id)
            return ['http_code' => 404, 'error_message' => 'Not found'];
        R::trash($bean);

        // Delete related case note?
        $notes = R::findAll('clientcasenotes', 'timetracking_id=:timetracking_id', [':timetracking_id' => $timetracking_id]);
        foreach ($notes as $note) {
            R::trash($note);
        }

        return;
    }
}
