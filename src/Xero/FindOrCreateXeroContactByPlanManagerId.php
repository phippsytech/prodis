<?php
namespace NDISmate\Xero;

use \NDISmate\CORE\TransactionalDatabase;
use \RedBeanPHP\R as R;

class FindOrCreateXeroContactByPlanManagerId
{
    function __invoke($obj, $data)
    {
        // First, try to find the xero contact id by email address
        $planmanager = R::findOne('planmanagers',
            ' id=:id ',
            [':id' => $data['plan_manager_id']]);

        if (!empty($planmanager['xero_contact_ref']))
            return $planmanager['xero_contact_ref'];

        // if we didn't find it fall over to xero to fetch and update the record
        try {
            $where = 'EmailAddress=="' . $data['email'] . '"';
            $apiResponse = $obj->accountingApi->getContacts($obj->tenant_id, null, $where, null, null, null, null, true, null);
            $contacts = $apiResponse->getContacts();

            // if contact is not found, create contact
            if (empty($contacts)) {
                $new_contact = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
                $new_contact->setName($data['name']);
                $new_contact->setEmailAddress($data['email']);
                $arr_contacts = [];
                array_push($arr_contacts, $new_contact);
                $contacts_obj = new \XeroAPI\XeroPHP\Models\Accounting\Contacts;
                $contacts_obj->setContacts($arr_contacts);
                $apiResponse = $obj->accountingApi->createContacts($obj->tenant_id, $contacts_obj);
                $contacts = $apiResponse->getContacts();
            }

            $xero_contact_ref = $contacts[0]->getContactId();

            // update planmanager with xero contact id
            $bean = R::load('planmanagers', $planmanager['id']);
            $bean->xero_contact_ref = $xero_contact_ref;
            $thang = TransactionalDatabase::store($bean);

            // return $xero_contact_ref; // TODO: Decide if this is better
            return $contacts[0]->getContactId();
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ['http_code' => 400, 'error' => $error];
        }
    }
}
