<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
              'id' => $this->id,
            'project_id' => $this->project_id,
            'start_time' => $this->start_time->format('d M Y H:i') ,
            'end_time' => $this->end_time->format('d M Y H:i') ,
            'description' => $this->description,
            'hours' => (float) $this->hours,
            'tag' => $this->tag,
            // 'created_at' => $this->created_at->toDateTimeString(),
            // 'updated_at' => $this->updated_at->toDateTimeString(),
             'project' => $this->relationLoaded('project') ? new ProjectResource($this->project) : null,

        ];
    }
}
