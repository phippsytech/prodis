<?php
namespace NDISmate\CORE;

use \NDISmate\CORE\TransactionalDatabase;
use \NDISmate\CORE\Validation;
use \RedBeanPHP\R as R;

class CRUD
{
    var $table;
    var $fields;

    function __construct($singular, $fields)
    {
        $plural = $singular . 's';
        $this->table = $plural;
        $this->fields = $fields;
    }

    function create($data)
    {
        $checkDB = $this->checkConnection();
        if ($checkDB !== true)
            return $checkDB;

        // remove the id to prevent create from updating existing records.
        unset($data['id']);

        $bean = R::dispense($this->table);
        $db_result = TransactionalDatabase::storeFields($bean, $this->fields, $data);

        // the id will be 0 if the store failed.
        if ($db_result['id'] == 0)
            return ['http_code' => 400, 'error_message' => 'Bad Request'];
        return ['http_code' => 201, 'result' => $bean->export()];
    }

    function update($data)
    {
        /*
         * If data is not updating it is likely that the type for the data that was created
         * is different to the new data being supplied.  Manually update the field type to fix the error
         *
         * eg:
         * ALTER TABLE meetingnotes
         * CHANGE COLUMN start_time start_time VARCHAR(191);
         */

        $checkDB = $this->checkConnection();
        if ($checkDB !== true)
            return $checkDB;

        $bean = R::load($this->table, $data['id']);
        if (!$bean->id)
            return ['http_code' => 404, 'error_message' => 'Not found.'];
        $thang = TransactionalDatabase::storeFields($bean, $this->fields, $data);
        return ['http_code' => 200, 'result' => $bean];
    }

    function delete($id)
    {
        $checkDB = $this->checkConnection();
        if ($checkDB !== true)
            return $checkDB;

        $bean = R::load($this->table, $id);
        if (!$bean->id)
            return ['http_code' => 404, 'error_message' => 'Not found'];
        TransactionalDatabase::trash($bean);
        return ['http_code' => 200];
    }

    function getOne($id)
    {
        $checkDB = $this->checkConnection();
        if ($checkDB !== true)
            return $checkDB;

        $bean = R::load($this->table, $id);

        if (!$bean->id)
            return ['http_code' => 404, 'error_message' => 'Not found.'];

        return ['http_code' => 200, 'result' => $bean->export()];
    }

    function getAll()
    {
        $checkDB = $this->checkConnection();
        if ($checkDB !== true)
            return $checkDB;

        $beans = R::getAll('SELECT * FROM ' . $this->table);

        if (!count($beans)) {
            return ['http_code' => 404, 'error_message' => 'Not Found'];
        } else {
            return ['http_code' => 200, 'result' => $beans];  // R::exportAll($beans, TRUE)
        }
    }

    /*
     * Because I have a persistent connection to the database, I need to handle timeouts
     * https://github.com/gabordemooij/redbean/issues/667
     */
    private function checkConnection()
    {
        $dbHandler = R::getDatabaseAdapter()->getDatabase();

        try {
            @$dbHandler->getPDO()->query('SELECT 1');
            return true;
        } catch (\PDOException $e) {
            try {
                $dbHandler->close();
                $dbHandler->connect();
            } catch (\PDOException $e) {
                return ['http_code' => 503, 'error_message' => 'Database Unavailable'];
            }
        }
    }
}
