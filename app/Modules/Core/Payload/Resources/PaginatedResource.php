<?php

namespace App\Modules\Core\Payload\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginatedResource extends JsonResource
{
    protected $resourceClass;

    public function __construct($resource, $resourceClass = null)
    {
        parent::__construct($resource);
        $this->resourceClass = $resourceClass;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $data = $this['data'] ?? $this->resource->items();
        
        // Eğer resource class belirtilmişse, collection olarak dönüştür
        if ($this->resourceClass && class_exists($this->resourceClass)) {
            $data = $this->resourceClass::collection($data);
        }
        
        return [
            'data' => $data,
            'pagination' => [
                'currentPage' => $this->resource->currentPage(),
                'perPage' => $this->resource->perPage(),
                'total' => $this->resource->total(),
                'lastPage' => $this->resource->lastPage(),
                'from' => $this->resource->firstItem(),
                'to' => $this->resource->lastItem()
            ]
        ];
    }
}
