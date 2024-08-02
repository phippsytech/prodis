<?php
namespace NDISmate\Models\Client;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;

class Plan extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('clientplan', [
            'id' => [v::numericVal()],
            'client_id' => [v::numericVal()],
            'service_agreement_signed_date' => [v::stringType()],  // this should be renamed service_agreement_start_date
            'service_agreement_end_date' => [v::stringType()],
            'is_active' => [v::boolVal()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['addClientPlan', 'create', true, ['participant.modify', 'admin']],
            ['getClientPlan', 'getOne', true, []],
            ['listClientPlans', 'getAll', true, []],
            ['updateClientPlan', 'update', true, ['participant.modify', 'admin']],
            ['archiveClientPlan', 'archive', true, ['participant.modify', 'admin']],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:

    function getOne($data)
    {
        // check if $data['client_id'] is set and is numeric
        if (!isset($data['client_id']) || !is_numeric($data['client_id'])) {
            return ['http_code' => 400, 'error_message' => 'Invalid client_id'];
        }

        $bean = R::findOne('clientplans', ' client_id=:client_id ', [':client_id' => $data['client_id']]);
        if (!$bean->id)
            return ['http_code' => 404, 'error_message' => 'Not found.'];

        return ['http_code' => 200, 'result' => $bean->export()];
    }
}
