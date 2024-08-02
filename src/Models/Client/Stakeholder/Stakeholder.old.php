<?php
namespace NDISmate\Models\Client;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;

class Stakeholder extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('stakeholder', [
            'id' => [v::numericVal()],
            'client_id' => [v::numericVal()],
            'user_id' => [v::numericVal()],
            'name' => [v::stringType()],
            'relationship' => [v::stringType()],
            'email' => [v::stringType()],
            'phone' => [v::stringType()],
            'notes' => [v::stringType()],
            'representative' => [v::boolVal()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['addStakeholder', 'create', true, ['therapist', 'participant.modify', 'admin']],
            ['listStakeholders', 'getAll', true, []],
            ['listStakeholdersByClientId', 'listStakeholdersByClientId', false, []],
            ['getStakeholder', 'getOne', true, []],
            ['updateStakeholder', 'update', true, ['therapist', 'participant.modify', 'admin']],
            ['deleteStakeholder', 'delete', true, ['therapist', 'participant.modify', 'admin']],
            ['makeRepresentative', 'makeRepresentative', true, ['therapist', 'participant.modify', 'admin']],
            ['removeRepresentative', 'removeRepresentative', true, ['therapist', 'participant.modify', 'admin']],
            ['archiveStakeholder', 'archive', true, ['therapist', 'participant.modify', 'admin']],
            ['restoreStakeholder', 'restore', true, ['therapist', 'participant.modify', 'admin']],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:

    function listStakeHoldersByClientId($data)
    {
        $beans = R::getAll('SELECT * from stakeholders  WHERE client_id = :client_id ', [':client_id' => $data['client_id']]);
        return ['http_code' => 200, 'result' => $beans];
    }

    function makeRepresentative($data)
    {
        // checking for existing record
        $beanCount = R::count('stakeholders', 'client_id = ? AND id = ?', [$data['client_id'], $data['stakeholder_id']]);
        if ($beanCount === 0) {
            return ['http_code' => 404, 'error_message' => 'Stakeholder not found'];
        }

        // first make the Stakeholder representative
        // This will create the representative column if it does not exist.
        $bean = R::findOne('stakeholders', 'client_id = :client_id AND id = :stakeholder_id', [':client_id' => $data['client_id'], ':stakeholder_id' => $data['stakeholder_id']]);
        $bean = R::load('stakeholders', $bean['id']);
        $bean->representative = true;
        R::store($bean);

        // then update all of the client's other stakeholders to not be representative
        $update_query = 'UPDATE stakeholders SET representative = false WHERE id != :stakeholder_id AND client_id = :client_id';
        R::exec($update_query, [':stakeholder_id' => $data['stakeholder_id'], ':client_id' => $data['client_id']]);

        return ['http_code' => 200, 'result' => $bean];
    }
}
