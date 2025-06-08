<?php

namespace App\Domain\OtherSpent\BLL\OtherSpent;

use App\DomainUtils\BaseBLL\BaseBLL;
use App\DomainUtils\BaseBLL\BaseBLLFileUtils;
use App\Domain\OtherSpent\DAL\OtherSpent\OtherSpentDALInterface;

/**
 * @property OtherSpentDALInterface DAL
 */
class OtherSpentBLL extends BaseBLL implements OtherSpentBLLInterface
{
    use BaseBLLFileUtils;

    public function __construct(OtherSpentDALInterface $otherSpentDAL)
    {
        $this->DAL = $otherSpentDAL;
    }
}
