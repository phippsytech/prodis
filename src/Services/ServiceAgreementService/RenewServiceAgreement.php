<?php

namespace NDISmate\Services\ServiceAgreementService;

use Exception;
use NDISmate\Models\Participant\ServiceAgreement;
use NDISmate\Models\Participant\ServiceBooking;
use RedBeanPHP\R as R;

class RenewServiceAgreement
{
    public function __invoke($data)
    {
        try {
            $serviceAgreementId = $data['service_agreement_id'];

            // Load the original service agreement
            $originalBean = R::load('serviceagreements', $serviceAgreementId);
            if (!$originalBean || !$originalBean->id) {
                throw new Exception("Original service agreement not found.", 404);
            }

            // Check if a draft already exists
            $existingDraft = R::findOne('serviceagreements', 'client_id=:client_id AND status = :status', [":client_id" => $originalBean->client_id, ":status"=>"draft"]);
            if ($existingDraft) {
                throw new Exception("A draft service agreement is already in progress. Please review the draft and decide whether to cancel it before trying to renew the service agreement.", 400);
            }

            // Create a new service agreement as a draft
            $newBean = (new ServiceAgreement)->create([
                'client_id' => $originalBean->client_id,
                'service_agreement_signed_date' => date('Y-m-d', strtotime($originalBean->service_agreement_end_date . ' +1 day')),
                'service_agreement_end_date' => null,
                'is_active' => null,
                'signatory_name' => $originalBean->signatory_name,
                'signatory_email' => $originalBean->signatory_email,
                'signatory_phone' => $originalBean->signatory_phone,
                'is_draft' => true,
                'parent_id' => $serviceAgreementId,
                'status' => 'draft',
            ]);

            $newBeanId = $newBean['id'];
                        
            // Duplicate related bookings
            $parentServiceBookings = R::getAll("SELECT * FROM servicebookings WHERE plan_id = :planId", [':planId' => $originalBean->id]);

            foreach ($parentServiceBookings as $booking) {

                $newBooking = $booking;//->export();
                unset($newBooking['id']);  // Remove ID to create a new entry

                $newBooking['plan_id'] = $newBeanId;
                $newBooking['is_draft'] = true;
                $newBooking['is_active'] = null;

                // Get the service rate
                $service = (new \NDISmate\Models\Service\GetService)(['id' => $booking['service_id']]);

                $serviceRate = $service->rate;
                $newBooking['rate'] = $serviceRate;


                // it is possible that the rate has changed since the previous service agreement
                // so we may need to recalculate the budget so that the same number of hours can be delivered in
                // the new service agreement.

                // I'm not putting this in here because it's not clear what the best approach for this is yet.


                // Generate a new booking
                $result = (new ServiceBooking)->create($newBooking);
                
            }

            return $newBean;
        } catch (Exception $e) {
            throw new Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
