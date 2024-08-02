<?php
namespace NDISmate\Models\Payroll\Payrun;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;

class SalaryPackagingDeduction extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('salarypackagingdeduction', [
            'id' => [v::numericVal()],
            'ncb' => [v::stringType()],
            'full_name' => [v::stringType()],
            'total_amount' => [v::stringType()],
            'adjusted_amount' => [v::stringType()],
            'account_type' => [v::stringType()],
            'status' => [v::stringType()],
            'pid' => [v::stringType()],  // this should contain the staff_id
            'deduction_csv_name' => [v::stringType()],
            'payrun_ref' => [v::stringType()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['addSalaryPackagingDeduction', 'create', true, ['payroll', 'admin']],
            ['getSalaryPackagingDeduction', 'getOne', true, ['payroll', 'admin']],
            ['listSalaryPackagingDeductions', 'listSalaryPackagingDeductions', true, ['payroll', 'admin']],
            ['updateSalaryPackagingDeduction', 'update', true, ['payroll', 'admin']],
            ['deleteSalaryPackagingDeduction', 'delete', true, ['payroll', 'admin']],
            ['uploadCSV', 'uploadCSV', true, ['payroll', 'admin']],
            ['downloadCSV', 'downloadCSV', true, ['payroll', 'admin']],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    function listSalaryPackagingDeductions($data)
    {
        return (new \NDISmate\Models\Payroll\Payrun\SalaryPackagingDeduction\ListSalaryPackagingDeductions)($data);
    }

    // function getSalaryPackagingDeduction($data){
    //     return (new \NDISmate\Models\Payroll\Payrun\SalaryPackagingDeduction\GetSalaryPackagingDeduction)($data);
    // }

    function uploadCSV($data)
    {
        return (new \NDISmate\Models\Payroll\Payrun\SalaryPackagingDeduction\UploadCSV)($this, $data);
    }

    function downloadCSV($data)
    {
        return (new \NDISmate\Models\Payroll\Payrun\SalaryPackagingDeduction\DownloadCSV)($data);
    }
}
