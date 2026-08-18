<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Stock;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Component;

new #[Layout('layouts.admin')] class extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $dateFrom    = '';
    public string $dateTo      = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingDateFrom()
    {
        $this->resetPage();
    }
    public function updatingDateTo()
    {
        $this->resetPage();
    }

    #[Computed]
    public function histories()
    {
        return Stock::with(['resource.resourceType', 'user'])
            ->when(
                $this->search,
                fn($q) =>
                $q->whereHas(
                    'resource',
                    fn($r) =>
                    $r->where('resource_name', 'like', '%' . $this->search . '%')
                )
                    ->orWhere('supplier', 'like', '%' . $this->search . '%')
                    ->orWhereHas(
                        'user',
                        fn($u) =>
                        $u->where('name', 'like', '%' . $this->search . '%')
                    )
            )
            ->when(
                $this->dateFrom,
                fn($q) =>
                $q->whereDate('arrival_date', '>=', $this->dateFrom)
            )
            ->when(
                $this->dateTo,
                fn($q) =>
                $q->whereDate('arrival_date', '<=', $this->dateTo)
            )
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function totalRestocks()
    {
        return Stock::count();
    }

    #[Computed]
    public function totalQuantityAdded()
    {
        return Stock::sum('quantity_added');
    }

    #[Computed]
    public function totalValue()
    {
        return Stock::selectRaw('SUM(quantity_added * unit_price) as total')
            ->value('total') ?? 0;
    }

    #[Computed]
    public function todayRestocks()
    {
        return Stock::whereDate('arrival_date', today())->count();
    }
};
