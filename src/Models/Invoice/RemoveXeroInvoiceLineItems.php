<?php
namespace NDISmate\Models\Invoice;

use XeroAPI\XeroPHP\Models\Accounting\Invoice;
use XeroAPI\XeroPHP\ApiException;

/**
 * Remove Xero Invoice Line Items Class
 *
 * @category Class
 * @package  NDISmate
 * @author   Phippsy Tech
 * @link     https://phippsy.tech
 */
class RemoveXeroInvoiceLineItems
{
    private array $removedBillableIds = [];
    private Invoice $invoice;
    private array $lineItemsToRemove = [];

    public function __invoke(Invoice $invoice, array $lineItemsToRemove): array
    {
        $this->invoice = $invoice;
        $this->lineItemsToRemove = $lineItemsToRemove;

        if (empty($lineItemsToRemove)) {
            return [$this->invoice, $this->removedBillableIds];
        }

        $this->processLineItems();

        return [$this->invoice, $this->removedBillableIds];
    }

    private function processLineItems(): void
    {
        $invoiceLineItems = $this->invoice->getLineItems();

        foreach ($this->lineItemsToRemove as $lineItemToRemove) {
            foreach ($invoiceLineItems as $index => $lineItem) {
                if ($this->lineItemMatches($lineItem, $lineItemToRemove)) {
                    unset($invoiceLineItems[$index]);
                    $this->removedBillableIds = array_unique(array_merge($this->removedBillableIds, $lineItemToRemove['SessionIds']));
                    break;
                }
            }
        }

        $this->invoice->setLineItems(array_values($invoiceLineItems));
    }

    private function lineItemMatches($lineItem, $lineItemToRemove): bool
    {
        $description = $lineItem->getDescription();
        $quantity = $lineItem->getQuantity();
        $unitAmount = $lineItem->getUnitAmount();
        $dateMatch = strpos($description, (new \DateTime($lineItemToRemove['SupportsDeliveredFrom']))->format('d/m/Y')) !== false;
        $claimTypeMatch = empty($lineItemToRemove['ClaimType'])
            ? true
            : strpos($description, 'Claim Type: ' . $lineItemToRemove['ClaimType']) !== false;

        return strpos($description, $lineItemToRemove['SupportNumber']) !== false &&
            $quantity == $lineItemToRemove['Quantity'] &&
            $unitAmount == $lineItemToRemove['UnitPrice'] &&
            $dateMatch &&
            $claimTypeMatch;
    }
}
