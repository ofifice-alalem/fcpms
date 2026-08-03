<?php

namespace App\Repositories\Eloquent;

use App\Models\TaskComponent;
use App\Repositories\Contracts\TaskComponentRepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class TaskComponentRepository
 * Task Builder Module Components
 */
class TaskComponentRepository extends BaseRepository implements TaskComponentRepositoryInterface
{
    public function model(): string
    {
        return TaskComponent::class;
    }

    public function createComponentWithOptions(int $taskDefinitionId, array $componentData, array $options = [])
    {
        $componentData['task_definition_id'] = $taskDefinitionId;
        $component = $this->create($componentData);

        if (!empty($options)) {
            foreach ($options as $index => $option) {
                $component->options()->create([
                    'option_label' => $option['label'],
                    'option_value' => $option['value'],
                    'order_index' => $option['order_index'] ?? $index,
                ]);
            }
        }

        return $component->load('options');
    }
}
