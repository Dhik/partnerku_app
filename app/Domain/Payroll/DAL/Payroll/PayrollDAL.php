<?php

namespace App\Domain\Payroll\DAL\Payroll;

use App\DomainUtils\BaseDAL\BaseDAL;
use App\Domain\Payroll\Models\Payroll;

/**
 * @property Payroll model
 */
class PayrollDAL extends BaseDAL implements PayrollDALInterface
{
    public function __construct(Payroll $payroll)
    {
        $this->model = $payroll;
    }
}
