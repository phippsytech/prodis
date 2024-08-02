<?php

namespace NDISmate\Models\Tickets\Ticket\Activity;

use NDISmate\Models\Tickets\Ticket\Activity\ActivityInterface;
use RedBeanPHP\R;

class AssigneeActivity implements ActivityInterface
{
    public $user_id;
    public $data;

    public function __construct($data, $guard)
    {
        $this->user_id = $guard->user_id;
        $this->data = $data;
    }

    public function perform()
    {
        // Assignee activity logic
    }

    public function get($activity_id)
    {
        return 'Assigned to: ' . $this->assignee;
    }

    public function save()
    {
        $activityBean = R::dispense('ticketactivity');
        $activityBean->ticket_id = $this->data['ticket_id'];
        $activityBean->activity_type = get_class($this);
        $activityId = R::store($activityBean);

        $assigneeActivityBean = R::dispense('ticketassignees');
        $assigneeActivityBean->activity_id = $activityId;
        $assigneeActivityBean->assigned_to = $this->data['assigned_to'];
        $assigneeActivityBean->user_id = $this->user_id;
        $assigneeActivityBean->status = $this->data['status'];  // unassigned
        R::store($assigneeActivityBean);
    }

    public static function getAssigneesForTicket($ticketId)
    {
        $assignees = [];
        $activityBeans = R::find('activity', ' ticket_id = ? AND activity_type = ? ', [$ticketId, __CLASS__]);

        foreach ($activityBeans as $activityBean) {
            $assigneeBeans = R::find('ticketassignees', ' activity_id = ? ', [$activityBean->id]);
            foreach ($assigneeBeans as $assigneeBean) {
                $assignees[] = $assigneeBean->assignee;
            }
        }

        return $assignees;
    }
}
