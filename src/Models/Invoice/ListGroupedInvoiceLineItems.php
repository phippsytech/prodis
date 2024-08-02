<?php
namespace NDISmate\Models\Invoice;

use \RedBeanPHP\R as R;

class ListGroupedInvoiceLineItems
{
    public function __invoke($data)
    {
        $items = (new \NDISmate\Models\Invoice\ListUnbilledLineItems)($data)['result'];

        $keys = ['NDISNumber', 'PlanManagerId', 'SupportNumber', 'ClaimType', 'SupportsDeliveredFrom'];
        $groupedItems = $this->groupBy($items, $keys);

        $aggregatedGroup = [];

        foreach ($groupedItems as $group) {
            $aggregatedGroup[] = $this->aggregateGroup($group);
        }

        return ['http_code' => 200, 'result' => $aggregatedGroup];
    }

    function groupBy($items, $keys)
    {
        $result = [];

        foreach ($items as $item) {
            $keyParts = [];
            foreach ($keys as $key) {
                $keyParts[] = $item[$key];
            }
            $key = join('-', $keyParts);

            if (!array_key_exists($key, $result)) {
                $result[$key] = [];
            }
            $result[$key][] = $item;
        }

        return array_values($result);
    }

    function aggregateGroup($group)
    {
        $aggregate = [
            'Quantity' => 0,
            'SessionDuration' => 0,
            'UnitPrice' => 0,  // Assuming you want to start with 0 and adjust based on your logic
            'SessionIds' => [],
        ];

        foreach ($group as $item) {
            // Copying all the individual fields
            $fieldsToCopy = [
                'CancellationReason', 'ClaimReference', 'ClaimType', 'ClientBillingEmail',
                'ClientBillingId', 'ClientBillingName', 'ClientId', 'ClientName',
                'InvoiceBatch', 'NDISNumber', 'PlanManagerId', 'RegistrationNumber',
                'ServiceBillingUnit', 'ServiceCode', 'ServiceName', 'SessionId',
                'SupportNumber', 'SupportsDeliveredFrom', 'SupportsDeliveredTo',
                'TherapistName', 'UnitPrice'
            ];

            foreach ($fieldsToCopy as $field) {
                $aggregate[$field] = $item[$field];
            }

            // Replaces $aggregate['Quantity'] += floatval($item['Quantity']);
            if (!empty($item['Quantity']) && is_numeric($item['Quantity'])) {
                $aggregate['Quantity'] += floatval($item['Quantity']);
            }

            // Replaces $aggregate['SessionDuration'] += floatval($item['SessionDuration']);
            if (!empty($item['SessionDuration']) && is_numeric($item['SessionDuration'])) {
                $aggregate['SessionDuration'] += floatval($item['SessionDuration']);
            }

            $aggregate['SessionIds'][] = $item['SessionId'];
        }

        return $aggregate;
    }
}
