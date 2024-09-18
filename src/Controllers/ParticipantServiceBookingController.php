<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Participant\ServiceBooking\GetServiceBooking;
use NDISmate\Models\Participant\ServiceBooking;
use NDISmate\Services\ParticipantServiceBooking\AddParticipantServiceBooking;
use NDISmate\Services\ParticipantServiceBooking\GetAvailableSessionDuration;
use NDISmate\Services\ParticipantServiceBooking\GetParticipantServiceBooking;
use NDISmate\Services\ParticipantServiceBooking\ListParticipantServiceBookingsByParticipantId;
use NDISmate\Services\ParticipantServiceBooking\ListProviderTravelByClientId;
use NDISmate\Services\ServiceAgreementService\ListServiceBookingsByServiceAgreementId;

final class ParticipantServiceBookingController extends BaseController  // Final prevents inheritance
{
    protected function defineController()
    {
        $this->controller = [
            'addServiceBooking' => [new AddParticipantServiceBooking, null, true, ['admin']],
            'updateServiceBooking' => [new ServiceBooking, 'update', true, ['admin']],
            'deleteServiceBooking' => [new ServiceBooking, 'delete', true, ['admin']],
            'getServiceBooking' => [new GetServiceBooking, null, true, []],
            'getParticipantServiceBooking' => [new GetParticipantServiceBooking, null, true, []],
            'getAvailableSessionDuration' => [new GetAvailableSessionDuration, null, true, []],
            'listParticipantServiceBookingsByParticipantId' => [new ListParticipantServiceBookingsByParticipantId, null, true, []],
            'listProviderTravelByClientId' => [new ListProviderTravelByClientId, null, true, []],
            'listServiceBookingsByServiceAgreementId' => [new ListServiceBookingsByServiceAgreementId, null, true, []],
        ];
    }
}
