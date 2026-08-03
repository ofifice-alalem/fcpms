<?php

namespace App\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface TaskComponentRepositoryInterface
 */
interface TaskComponentRepositoryInterface extends RepositoryInterface
{
    public function createComponentWithOptions(int $taskDefinitionId, array $componentData, array $options = []);
}
