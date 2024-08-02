<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Participant\Service\GetService;
use NDISmate\Models\Participant\Service;
use NDISmate\Services\ParticipantService\AddParticipantService;
use NDISmate\Services\ParticipantService\GetAvailableSessionDuration;
use NDISmate\Services\ParticipantService\GetParticipantService;
use NDISmate\Services\ParticipantService\ListParticipantServicesByParticipantId;
use NDISmate\Services\ParticipantService\ListProviderTravelByClientId;
use NDISmate\Services\ParticipantService\UpdateParticipantService;
use NDISmate\Services\ServiceAgreementService\ListServicesByServiceAgreementId;

final class ParticipantServiceController extends BaseController  // Final prevents inheritance
{
    protected function defineController()
    {
        $this->controller = [
            'addService' => [new AddParticipantService, null, true, ['admin']],
            'updateService' => [new UpdateParticipantService, null, true, ['admin']],
            'deleteService' => [new Service, 'delete', true, ['admin']],
            'getService' => [new GetService, null, true, []],
            'getParticipantService' => [new GetParticipantService, null, true, []],
            'getAvailableSessionDuration' => [new GetAvailableSessionDuration, null, true, []],
            'listParticipantServicesByParticipantId' => [new ListParticipantServicesByParticipantId, null, true, []],
            'listProviderTravelByClientId' => [new ListProviderTravelByClientId, null, true, []],
            'listServicesByServiceAgreementId' => [new ListServicesByServiceAgreementId, null, true, []],
        ];
    }
}
