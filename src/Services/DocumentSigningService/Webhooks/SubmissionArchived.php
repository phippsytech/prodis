<?php

namespace NDISmate\Services\DocumentSigningService\Webhooks;

class SubmissionArchived
{
    public function __invoke($data)
    {
        return ['http_code' => 200];
    }
}
