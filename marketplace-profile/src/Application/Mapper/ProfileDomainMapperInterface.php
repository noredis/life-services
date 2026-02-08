<?php

declare(strict_types=1);

namespace App\Application\Mapper;

use App\Application\Response\ProfileResponse;
use App\Domain\Entity\Profile;

interface ProfileDomainMapperInterface
{
    public function mapFromProfileEntity(Profile $profile): ProfileResponse;
}
