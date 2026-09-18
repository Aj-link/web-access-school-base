<?php

use App\Models\Request as ResourceRequest;
use App\Models\Resource;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.admin')] class extends Component
{
    public ResourceRequest $reservation;

    public function mount($id)
    {
        $this->reservation = ResourceRequest::with(['user', 'department', 'requestType', 'items.resource'])
            ->findOrFail($id);
    }

    public function getItemsWithStockProperty()
    {
        return $this->reservation->items->map(function ($item) {
            $resource = $item->resource_id
                ? $item->resource
                : Resource::whereRaw('LOWER(resource_name) = ?', [strtolower($item->item_name)])->first();

            return [
                'item'     => $item,
                'resource' => $resource,
                'hasStock' => $resource && $resource->quantity_available >= $item->quantity,
            ];
        });
    }
};
