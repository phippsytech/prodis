<?php

namespace NDISmate\Services\DocumentSigningService\Webhooks;

class TemplateCreated
{
    public function __invoke($data)
    {
        return ['http_code' => 200];
    }
}
