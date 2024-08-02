<?php
namespace NDISmate\Models;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;

class TimeLog extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('timelog', [
            'id' => [v::numericVal()],
            'client_id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],
            'start_date' => [v::stringType()],
            'start_time' => [v::stringType()],
            'end_date' => [v::stringType()],
            'end_time' => [v::stringType()],
            'description' => [v::stringType()],
            'do_not_bill' => [v::boolVal()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['addTime', 'create', false, ['therapist.lead', 'sil.admin', 'admin']],
            ['getTime', 'getOne', true, ['therapist.lead', 'sil.admin', 'admin']],
            ['listTimes', 'listTimes', true, ['admin']],
            ['updateTime', 'update', true, ['therapist.lead', 'sil.admin', 'admin']],
            ['deleteTime', 'delete', true, ['therapist.lead', 'sil.admin', 'admin']],
            // ["deleteTimes", "deleteTimes", true, ["admin"] ],
            ['getTimeLog', 'getTimeLog', false, []],
            ['getTimeLogs', 'getTimeLogs', false, []],
            ['getTimes', 'getTimes', false, []],
            ['getTimesByStaffId', 'getTimesByStaffId', false, []],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:

    function create($data)
    {
        if (!isset($data['end_date'])) {
            $data['end_date'] = $data['start_date'];
            if ($data['end_time'] < $data['start_time']) {
                $data['end_date'] = date('Y-m-d', strtotime($data['end_date'] . ' +1 day'));
            }
        }

        if ($data['client_id']) {
            $client = R::findOne('clients', ' client_id=:client_id ', [':client_id' => $data['client_id']]);
            if ($client) {
                $data['client_id'] = $client->id;
            }
        }

        $result = $this->CRUD->create([
            'staff_id' => $data['staff_id'],
            'client_id' => $data['client_id'],
            'start_date' => $data['start_date'],
            'start_time' => $data['start_time'],
            'end_date' => $data['end_date'],
            'end_time' => $data['end_time'],
            // this is a bit silly.  I need to make this consistent.
            'do_not_bill' => $data['do_not_bill'],  // boolean 1 = do not bill.  0 or null = bill.
        ]);

        return ['http_code' => 201, 'result' => $result];
    }

    function update($data)
    {
        // TODO: if an id doesn't exist, what are we updating?  should no id return an error?
        if (isset($data['id']))
            $time['id'] = $data['id'];

        if (array_key_exists('staff_id', $data))
            $time['staff_id'] = $data['staff_id'];
        if (array_key_exists('client_id', $data))
            $time['client_id'] = $data['client_id'];
        if (isset($data['do_not_bill']))
            $time['do_not_bill'] = $data['do_not_bill'];
        if (isset($data['client_id']))
            $time['client_id'] = $data['client_id'];
        if (isset($data['start_date']))
            $time['start_date'] = $data['start_date'];
        if (isset($data['start_time']))
            $time['start_time'] = $data['start_time'];
        if (isset($data['end_date']))
            $time['end_date'] = $data['end_date'];
        if (isset($data['end_time']))
            $time['end_time'] = $data['end_time'];
        if (isset($data['description']))
            $time['description'] = $data['description'];

        $result = $this->CRUD->update($time);

        return ['http_code' => 200, 'result' => $result];
    }

    function getOne($data)
    {
        $result = $this->CRUD->getOne($data['id']);
        $result = $result['result'];
        return ['http_code' => 201, 'result' => $result];
    }

    function listTimes($data)
    {
        $times = (new \NDISmate\Models\TimeLog\ListTimes)($data);
        return ['http_code' => 200, 'result' => $times];
    }

    function getTimeLog($data)
    {
        return (new \NDISmate\Models\TimeLog\GetTimeLog)($data);
    }

    function getTimeLogs($data)
    {
        return (new \NDISmate\Models\TimeLog\GetTimeLogs)($data);
    }

    function getTimes($data)
    {
        $times = (new \NDISmate\Models\TimeLog\GetTimes)($data);
        return ['http_code' => 200, 'result' => $times];
    }

    function getTimesByStaffId($data)
    {
        $times = (new \NDISmate\Models\TimeLog\GetTimesByStaffId)($data);
        return ['http_code' => 200, 'result' => $times];
    }
}
