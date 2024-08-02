<?php

namespace NDISmate\Models\Tickets;

use NDISmate\Models\Tickets\Ticket\Activity\ActivityInterface;
use NDISmate\Models\Tickets\Ticket\Activity\AssigneeActivity;
use NDISmate\Models\Tickets\Ticket\Activity\CommentActivity;
use RedBeanPHP\R;

class Ticket
{
    private $ticket_id;
    private $bean;

    public function __construct($data, $guard)
    {
        if ($data['ticket_id'] === null) {
            $this->bean = R::dispense('tickets');
        } else {
            $this->ticket_id = (int) $data['ticket_id'];
            $this->bean = R::load('tickets', $this->ticket_id);
        }
    }

    public function create()
    {
        $this->bean->title = $data['title'];
        $this->bean->content = $data['content'];
        $this->bean->user_id = $guard->user_id;
        $this->bean->created_at = date('Y-m-d H:i:s');
        $this->bean->due = $data['due'];
        $this->bean->status = $data['status'];
        R::store($this->bean);
    }

    public function get()
    {
        $ticket = R::getRow('SELECT tickets.*, users.name AS user_name 
            FROM tickets 
            LEFT JOIN users ON tickets.user_id = users.id 
            WHERE tickets.id=:ticket_id',
            [':ticket_id' => $this->ticket_id]);
        $ticket['activities'] = $this->getActivities();
        $ticket['assignees'] = $this->getAssignees();

        return $ticket;
    }

    public function addActivity(ActivityInterface $activity)
    {
        $activity->save($this->bean->id);
    }

    public function getActivities()
    {
        $activities = [];
        $activityBeans = R::find('ticketactivity', ' ticket_id=:ticket_id ', [':ticket_id' => $this->ticket_id]);

        foreach ($activityBeans as $activityBean) {
            $activityType = $activityBean->activity_type;
            // $activityType = $activityBean->activity_type;
            $activity = new $activityType($this->data, $this->$guard);
            // If additional data needs to be loaded, add logic here
            $activities[] = $activity->get($activityBean->id);
        }

        return $activities;
    }

    // public static function getAssigneesForTicket($ticketId)
    // {
    //     $assignees = [];
    //     $activityBeans = R::find('activity', ' ticket_id = ? AND activity_type = ? ', [$ticketId, __CLASS__]);

    //     foreach ($activityBeans as $activityBean) {
    //         $assigneeBeans = R::find('Assigneeactivity', ' activity_id = ? ', [$activityBean->id]);
    //         foreach ($assigneeBeans as $assigneeBean) {
    //             $assignees[] = $assigneeBean->assignee;
    //         }
    //     }

    //     return $assignees;
    // }

    public function getAssignees()
    {
        // Getting all assignments for a ticket

        $assignees = AssigneeActivity::getAssigneesForTicket($this->ticket_id);

        return $assignees;
    }
}
