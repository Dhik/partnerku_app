<?php

namespace App\Domain\Payroll\BLL\Payroll;

use App\DomainUtils\BaseBLL\BaseBLL;
use App\DomainUtils\BaseBLL\BaseBLLFileUtils;
use App\Domain\Payroll\DAL\Payroll\PayrollDALInterface;

/**
 * @property PayrollDALInterface DAL
 */
class PayrollBLL extends BaseBLL implements PayrollBLLInterface
{
    use BaseBLLFileUtils;

    public function __construct(PayrollDALInterface $payrollDAL)
    {
        $this->DAL = $payrollDAL;
    }
}
