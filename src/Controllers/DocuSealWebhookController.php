<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\JsonResponse;
use NDISmate\Services\DocumentSigningService\Webhooks\FormCompleted;
use NDISmate\Services\DocumentSigningService\Webhooks\FormStarted;
use NDISmate\Services\DocumentSigningService\Webhooks\FormViewed;
use NDISmate\Services\DocumentSigningService\Webhooks\SubmissionArchived;
use NDISmate\Services\DocumentSigningService\Webhooks\SubmissionCreated;
use NDISmate\Services\DocumentSigningService\Webhooks\TemplateCreated;
use NDISmate\Services\DocumentSigningService\Webhooks\TemplateUpdated;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DocuSealWebhookController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = json_decode($request->getBody(), true);

        // Send the data to the Pulse channel
        $json = json_encode([
            'action' => 'sendToChannel',
            'channel' => 'pulse',
            'data' => $body
        ]);

        $socket = stream_socket_client('unix:///tmp/ndismate.sock');

        if ($socket !== false) {
            fwrite($socket, $json);
            $response = fread($socket, 8192);
        }

        if (!isset($body['event_type']) && !isset($body['data']))
            return JsonResponse::notFound();

        $event_type = $body['event_type'];
        $data = $body['data'];

        switch ($event_type) {
            case 'form.viewed':
                (new FormViewed)($data);
                break;
            case 'form.started':
                (new FormStarted)($data);
                break;
            case 'form.completed':
                (new FormCompleted)($data);
                break;
            case 'template.created':
                (new TemplateCreated)($data);
                break;
            case 'template.updated':
                (new TemplateUpdated)($data);
                break;
            case 'submissions.created':
                (new SubmissionCreated)($data);
                break;
            case 'submissions.archived':
                (new SubmissionArchived)($data);
                break;
        }

        return JsonResponse::noContent();
    }
}
