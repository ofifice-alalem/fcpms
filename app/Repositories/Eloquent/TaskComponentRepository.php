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

        // Ensure field aliases
        if (isset($componentData['component_type']) && !isset($componentData['type'])) {
            $componentData['type'] = $componentData['component_type'];
        }

        // Format visibility rules for conditional logic
        if (!empty($componentData['has_condition'])) {
            $componentData['visibility_rules'] = [
                'has_condition' => true,
                'condition_parent_idx' => $componentData['condition_parent_idx'] ?? null,
                'condition_value' => $componentData['condition_value'] ?? '',
            ];
        } else {
            $componentData['visibility_rules'] = null;
        }

        // Format validation rules / extra settings
        if (!empty($componentData['placeholder']) || !empty($componentData['settings'])) {
            $componentData['validation_rules'] = [
                'placeholder' => $componentData['placeholder'] ?? '',
                'settings' => $componentData['settings'] ?? [],
            ];
        }

        $component = $this->create($componentData);

        if (!empty($options)) {
            foreach ($options as $index => $option) {
                // Support array structure ['option_label' => '...'], ['label' => '...'], or string '...'
                if (is_array($option)) {
                    $label = $option['option_label'] ?? ($option['label'] ?? ($option['option_value'] ?? ($option['value'] ?? '')));
                    $value = $option['option_value'] ?? ($option['value'] ?? $label);
                    $order = $option['order_index'] ?? $index;
                } else {
                    $label = (string) $option;
                    $value = (string) $option;
                    $order = $index;
                }

                if (trim($label) !== '') {
                    $component->options()->create([
                        'option_label' => $label,
                        'option_value' => $value,
                        'order_index' => $order,
                    ]);
                }
            }
        }

        return $component->load('options');
    }
}
