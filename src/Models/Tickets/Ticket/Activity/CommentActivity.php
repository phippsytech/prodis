<?php

namespace NDISmate\Models\Tickets\Ticket\Activity;

use NDISmate\Models\Tickets\Ticket\Activity\ActivityInterface;
use RedBeanPHP\R;

class CommentActivity implements ActivityInterface
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
        // Comment activity logic
    }

    public function get($activity_id)
    {
        $comment = R::getRow('SELECT ticketcomment.*, users.name AS user_name 
        FROM ticketcomment 
        LEFT JOIN users ON ticketcomment.user_id = users.id 
        WHERE ticketcomment.id=:comment_id',
            [':comment_id' => $activity_id]);

        return $comment;
    }

    public function save()
    {
        $activityBean = R::dispense('ticketactivity');
        $activityBean->ticket_id = $this->data['ticket_id'];
        $activityBean->activity_type = get_class($this);
        $activityId = R::store($activityBean);

        $commentActivityBean = R::dispense('ticketcomment');
        $commentActivityBean->ticketactivity_id = $activityId;
        $commentActivityBean->user_id = $this->user_id;
        $commentActivityBean->comment = $this->data['comment'];
        R::store($commentActivityBean);
    }
}
