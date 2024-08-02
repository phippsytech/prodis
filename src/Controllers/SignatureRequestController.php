<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\SignatureRequest\ListSignatureRequestEvents;
use NDISmate\Models\SignatureRequest\ListSignatureRequests;
use NDISmate\Services\SignatureRequestService\ArchiveSignatureRequest;
use NDISmate\Services\SignatureRequestService\EmailSignatureRequest;
use NDISmate\Services\SignatureRequestService\GetSignatureRequest;

final class SignatureRequestController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            // 'deleteSignatureRequest' => [new SignatureRequest, 'delete', true, ['admin']],
            'getSignatureRequest' => [new GetSignatureRequest, null, false, ['admin']],
            'listSignatureRequests' => [new ListSignatureRequests, null, false, ['admin']],
            'listSignatureRequestEvents' => [new ListSignatureRequestEvents, null, false, ['admin']],
            'emailSignatureRequest' => [new EmailSignatureRequest, null, false, ['admin']],
            'archiveSignatureRequest' => [new ArchiveSignatureRequest, null, false, ['admin']],
        ];
    }
}
