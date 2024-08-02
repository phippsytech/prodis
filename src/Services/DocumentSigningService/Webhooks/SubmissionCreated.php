<?php

namespace NDISmate\Services\DocumentSigningService\Webhooks;

class SubmissionCreated
{
    public function __invoke($data)
    {
        return ['http_code' => 200];
    }
}
