<?php
namespace NDISmate\Models;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 


class MeetingNote extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("meetingnote", [
            'id' => [v::numericVal()],
            'purpose' => [v::stringType()],
            'supervisor' => [v::stringType()],
            'staff' => [v::stringType()],
            'meeting_date' => [v::stringType()],
            'start_time' => [v::stringType()],
            'end_time' => [v::stringType()],
            'location' => [v::stringType()],
            'notes' => [v::stringType()],  // long text
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addMeetingNote" , "create", true, [] ],
            ["getMeetingNote", "getOne", true, [] ],
            ["listMeetingNotes", "getAll", true, [] ],
            ["updateMeetingNote", "update", true, [] ],
            ["deleteMeetingNote", "delete", true, [] ],
        ];

        return $this->invoke($request, $response, $args, $this);
    }


    // Additional Methods and overrides here:

    
}