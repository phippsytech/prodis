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

            // Check if a draft already exists
            $existingDraft = R::findOne('serviceagreements', 'parent_id = ? AND status = ?', [$service_agreement_id, 'draft']);
            if ($existingDraft) {
                throw new Exception("A draft service agreement is already in progress. Please review the draft and decide whether to cancel it before trying to renew the service agreement.", 400);
            }

            // Load the original service agreement
            $originalBean = R::load('serviceagreements', $service_agreement_id);
            if (!$originalBean || !$originalBean->id) {
                throw new Exception("Original service agreement not found.", 404);
            }


            // Export the original bean's properties
            $properties = $originalBean->export();
            $properties = (array) $properties;
          
            // Create a new bean and import properties
            $newBean = R::dispense('serviceagreements');
          
            // Remove fields that shouldn't be duplicated
            // unset( $newBean->id, $newBean->updated, $newBean->archived);
            unset( $properties['id'], $properties['updated'], $properties['archived']);
            $newBean->import($properties);
            // Set specific fields for the draft
            $newBean->is_draft = true;
            $newBean->status = 'draft';
            $newBean->is_active = null;
            $newBean->is_amendment = true;
            $newBean->parent_id = $service_agreement_id;
            $newBean->service_agreement_signed_date = date('Y-m-d', strtotime($originalBean->service_agreement_end_date . ' +1 day'));
            $newBean->created = date('Y-m-d H:i:s');
            $newBean->client_id = (int) $newBean->client_id;
            // Debugging - inspect bean before storing
        
          
            // Store the new bean
            $newBeanId = R::store($newBean);
            
            // Duplicate related bookings
            $parentsServiceBookings = R::getAll("SELECT * FROM servicebookings WHERE service_id = :serviceId", [':serviceId' => $originalBean->id]);
            
            foreach ($parentsServiceBookings as $booking) {
                $newBooking = R::dispense('servicebookings');
                
                unset($booking['id']);  // Ensure ID is removed for the new entry

                // Import fields, set relationships, and draft status
                $newBooking->import($booking);
                $newBooking->service_id = $newBeanId;
                $newBooking->is_draft = true;
                $newBooking->is_active = null;

                $newId = R::store($newBooking);  // Store each duplicated booking
                
            }

            return $newBean;
        } catch (Exception $e) {
            // Handle RedBeanPHP specific exceptions
            throw new Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
