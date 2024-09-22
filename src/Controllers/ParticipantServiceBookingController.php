<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Participant\ServiceBooking;
use NDISmate\Models\Participant\ServiceBooking\GetServiceBooking;
use NDISmate\Services\ParticipantServiceBooking\AddParticipantServiceBooking;
use NDISmate\Services\ParticipantServiceBooking\GetAvailableSessionDuration;
use NDISmate\Services\ParticipantServiceBooking\GetParticipantServiceBooking;
use NDISmate\Services\ParticipantServiceBooking\ListProviderTravelByClientId;
use NDISmate\Services\ParticipantServiceBooking\UpdateParticipantServiceBooking;
use NDISmate\Models\Participant\ServiceBooking\ListServiceBookings;

final class ParticipantServiceBookingController extends BaseController  // Final prevents inheritance
{
    protected function defineController()
    {
        $this->controller = [
            'addServiceBooking' => [new AddParticipantServiceBooking, null, true, ['admin']],
            'deleteServiceBooking' => [new ServiceBooking, 'delete', true, ['admin']],
            'getServiceBooking' => [new GetServiceBooking, null, true, []],
            'getParticipantServiceBooking' => [new GetParticipantServiceBooking, null, true, []],
            'updateServiceBooking' => [new UpdateParticipantServiceBooking, null, true, ['admin']],
            'getAvailableSessionDuration' => [new GetAvailableSessionDuration, null, true, []],
            'listProviderTravelByClientId' => [new ListProviderTravelByClientId, null, true, []],
            'listServiceBookings' => [new ListServiceBookings, null, true, []],
        ];
    }
}
