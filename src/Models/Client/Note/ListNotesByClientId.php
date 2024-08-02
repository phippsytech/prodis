<?php
namespace NDISmate\Models\Client\Note;

use \RedBeanPHP\R as R;
use \RedBeanPHP\RedException;

class ListNotesByClientId
{
    public function __invoke($data)
    {
        try {
            $sql =
                "SELECT 
                    clientnotes.client_id,
                    clientnotes.note,
                    clientnotes.user_id,
                    clientnotes.archived,
                    DATE_FORMAT(clientnotes.created, '%Y-%m-%d %H:%i:%s') as created,
                    users.name AS user_name 
                FROM clientnotes
                JOIN clients 
                    ON (clients.id = clientnotes.client_id)
                JOIN users 
                    ON (users.id = clientnotes.user_id)
                WHERE clients.id = :client_id
                ORDER BY clientnotes.id DESC";

            $params = [':client_id' => $data['client_id']];

            $beans = R::getAll($sql, $params);

            return $beans;
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
