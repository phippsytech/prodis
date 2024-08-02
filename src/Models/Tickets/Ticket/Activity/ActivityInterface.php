<?php

namespace NDISmate\Models\Tickets\Ticket\Activity;

interface ActivityInterface
{
    public function perform();
    public function get($activity_id);
    public function save();
}
