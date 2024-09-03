<?php
namespace NDISmate\Services\DocumentTypeService;

use Respect\Validation\Validator as v;
use \NDISmate\CORE\CRUD;
use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;

class DeleteDocumentType
{
    function __invoke($data, $fields, $guard)
    {

    
        $user_roles = $guard->roles;
        // check if the user_role is a super user.
        $is_super = false;
        if (count(array_intersect($user_roles, ['super'])) !== 0) {
            $is_super = true;
        }

        // Check if document is in use by a participant member
        $result = R::getAll('SELECT * FROM participantdocuments WHERE document_id=:document_id',
            [':document_id' => $data['id']]);

        if (count($result) !== 0) {
            if (!$is_super) {
                return ['http_code' => 400, 'error_message' => 'Document is in use by a participant member'];
            } else {
                // Only super user has premission to delete documents that are in use by participant
                // delete the participant documents
                $result = R::exec('DELETE FROM participantdocuments WHERE document_id=:document_id',
                    [':document_id' => $data['id']]);
            }
        }

        // Delete the document
        $result = $this->delete($data);
        $result = $result['result'];
        return ['http_code' => 200];



    }
}
