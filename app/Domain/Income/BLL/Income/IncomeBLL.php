<?php

namespace App\Domain\Income\BLL\Income;

use App\DomainUtils\BaseBLL\BaseBLL;
use App\DomainUtils\BaseBLL\BaseBLLFileUtils;
use App\Domain\Income\DAL\Income\IncomeDALInterface;

/**
 * @property IncomeDALInterface DAL
 */
class IncomeBLL extends BaseBLL implements IncomeBLLInterface
{
    use BaseBLLFileUtils;

    public function __construct(IncomeDALInterface $incomeDAL)
    {
        $this->DAL = $incomeDAL;
    }
}
