<?php

namespace NDISmate\Services\DocumentSigningService\Webhooks;

class TemplateUpdated
{
    public function __invoke($data)
    {
        return ['http_code' => 200];
    }
}
