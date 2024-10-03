<?php

namespace NDISmate\Models\Compliment;

use \RedBeanPHP\R as R;

class GetCompliment
{
    public function __invoke($id)
    {
        // Fetch the compliment by ID
        $compliment = R::load('compliment', $id);
        
        // Check if compliment exists
        if ($compliment->id === 0) {
            throw new \Exception('Compliment not found');
        }
        
        return $compliment;
    }
}
