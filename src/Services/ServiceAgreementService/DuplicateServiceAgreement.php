<?php
namespace NDISmate\Services\ServiceAgreementService;

use RedBeanPHP\R as R;

class DuplicateServiceAgreement
{
    public function __invoke($data)
    {
        try {
            $service_agreement_id = $data['service_agreement_id'];

            $originalBean = R::load('serviceagreements', $service_agreement_id);

            // Unbox the original bean to get its properties
            $properties = $originalBean->export();

            // Create a new bean with the same properties
            $newBean = R::dispense('serviceagreements');
            $newBean->import($properties);

            unset($newBean->id);  // Remove the ID to avoid conflicts
            unset($newBean->updated);  // Remove the updated timestamp
            unset($newBean->archived);  // Remove the archived flag

            $newBean->is_active = true;
            $newBean->is_amendment = true;
            $newBean->parent_id = $service_agreement_id;
            $newBean->service_agreement_signed_date = $data['amendment_start_date'];  // I'm going to use this as start date.
            $newBean->created = date('Y-m-d H:i:s');

            // Store the new bean
            $newBeanId = R::store($newBean);

            return $newBean;
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
