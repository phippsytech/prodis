<?php
namespace NDISmate\Services\ServiceAgreementService;

use Exception;
use RedBeanPHP\R as R;

class RenewServiceAgreement
{
    public function __invoke($data)
    {
        try {
            $service_agreement_id = $data['service_agreement_id'];

            // check for draft

            $existingDraft = R::findOne('serviceagreements', 'parent_id = ? AND status = ?', [$service_agreement_id, 'draft']);

            if ($existingDraft) throw new Exception("A draft service agreement is already in progress. Please review the draft and decide whether to cancel it before trying to renew the service agreement.", 400);

            $originalBean = R::load('serviceagreements', $service_agreement_id);

            // Unbox the original bean to get its properties
            $properties = $originalBean->export();

            // Create a new bean with the same properties
            $newBean = R::dispense('serviceagreements');
            $newBean->import($properties);

            unset($newBean->id);  // Remove the ID to avoid conflicts
            unset($newBean->updated);  // Remove the updated timestamp
            unset($newBean->archived);  // Remove the archived flag

            $newBean->is_draft = true;
            $newBean->status = 'draft';
            $newBean->is_active = null;
            $newBean->is_amendment = true;
            $newBean->parent_id = $service_agreement_id;
            $newBean->service_agreement_signed_date = date('Y-m-d', strtotime($originalBean->service_agreement_end_date . ' +1 day'));

            $newBean->created = date('Y-m-d H:i:s');

            // Store the new bean
            $newBeanId = R::store($newBean);

            return $newBean;
        } catch (Exception $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
