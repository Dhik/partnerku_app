<?php

namespace App\Domain\Niche\BLL\Niche;

use App\DomainUtils\BaseBLL\BaseBLL;
use App\DomainUtils\BaseBLL\BaseBLLFileUtils;
use App\Domain\Niche\DAL\Niche\NicheDALInterface;

/**
 * @property NicheDALInterface DAL
 */
class NicheBLL extends BaseBLL implements NicheBLLInterface
{
    use BaseBLLFileUtils;

    public function __construct(NicheDALInterface $nicheDAL)
    {
        $this->DAL = $nicheDAL;
    }
}
