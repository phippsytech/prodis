<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Participant\ServiceAgreement\GetServiceAgreement;
use NDISmate\Models\Participant\ServiceAgreement\ListServiceAgreementsByParticipantId;
use NDISmate\Models\Participant\ServiceAgreement;
use NDISmate\Services\ServiceAgreementService\ListServiceAgreementsByStaffId;
use NDISmate\Services\ServiceAgreementService\ListServiceAgreementsToAmend;
use NDISmate\Services\ServiceAgreementService\PDFTest;
use NDISmate\Services\ServiceAgreementService\DeleteDraftServiceAgreement;

final class ParticipantServiceAgreementController extends BaseController  // Final prevents inheritance
{
    protected function defineController()
    {
        $this->controller = [
            'addServiceAgreement' => [new ServiceAgreement, 'create', true, ['admin']],
            'updateServiceAgreement' => [new ServiceAgreement, 'update', true, ['admin']],
            'deleteServiceAgreement' => [new DeleteDraftServiceAgreement, null, true, ['admin']],
            'getServiceAgreement' => [new GetServiceAgreement, null, true, []],
            'listServiceAgreementsByParticipantId' => [new ListServiceAgreementsByParticipantId, null, true, []],
            'listServiceAgreementsByStaffId' => [new ListServiceAgreementsByStaffId, null, true, []],
            'listServiceAgreementsToAmend' => [new ListServiceAgreementsToAmend, null, true, []],
            'viewServiceAgreementPDF' => [new PDFTest, null, true, []]
        ];
    }
}
