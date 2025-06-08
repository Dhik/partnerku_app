<?php

namespace App\Domain\OtherSpent\DAL\OtherSpent;

use App\DomainUtils\BaseDAL\BaseDAL;
use App\Domain\OtherSpent\Models\OtherSpent;

/**
 * @property OtherSpent model
 */
class OtherSpentDAL extends BaseDAL implements OtherSpentDALInterface
{
    public function __construct(OtherSpent $otherSpent)
    {
        $this->model = $otherSpent;
    }
}
