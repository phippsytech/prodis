<?php
namespace NDISmate\Models\Ticket\Activity;

use RedBeanPHP\R;

class ListActivityByTicketId{

    function __invoke($data){
        $ticket_id = $data['ticket_id'];
        $activities = R::findAll('ticketactivity', 'ticket_id = ?', [$ticket_id]);
        return $activities;

    }





    
}