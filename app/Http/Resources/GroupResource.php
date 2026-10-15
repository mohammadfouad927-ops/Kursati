<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            'id' => $this->id,
            'name' => $this->name,
            'activated' => $this->is_active,
            'capacity' => $this->capacity,
            'branch' => $this->branch->name?? 'online',
            'training_course' => $this->whenloaded('trainingCourse', function(){ return $this->trainingCourse->only(['id', 'name']); }),
            'group_schedules' => GroupScheduleResource::collection($this->whenLoaded('groupSchudles')),
        ];
    }
}
