<?php

namespace App\Domain\Income\DAL\Income;

use App\DomainUtils\BaseDAL\BaseDAL;
use App\Domain\Income\Models\Income;

/**
 * @property Income model
 */
class IncomeDAL extends BaseDAL implements IncomeDALInterface
{
    public function __construct(Income $income)
    {
        $this->model = $income;
    }
}
