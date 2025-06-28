<?php

namespace App\Domain\Niche\DAL\Niche;

use App\DomainUtils\BaseDAL\BaseDAL;
use App\Domain\Niche\Models\Niche;

/**
 * @property Niche model
 */
class NicheDAL extends BaseDAL implements NicheDALInterface
{
    public function __construct(Niche $niche)
    {
        $this->model = $niche;
    }
}
