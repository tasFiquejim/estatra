<?php

namespace App\Traits;

use Livewire\Attributes\Url;
use Livewire\WithPagination;

trait HasSortingAndSearch
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: 'created_at')]
    public string $sortBy = 'created_at';

    #[Url(except: 'desc')]
    public string $sortDirection = 'desc';


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortByField(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }
    /**
     * Apply search to query for common fields
     */
    protected function applySearch($query, array $searchFields)
    {
        if (empty($this->search)) {
            return $query;
        }

        return $query->where(function ($q) use ($searchFields) {
            foreach ($searchFields as $field) {
                $q->orWhere($field, 'like', '%' . $this->search . '%');
            }
        });
    }
    protected function applyAdvancedSearch($query, array $searchConfig)
    {
        if (empty($this->search)) {
            return $query;
        }

       return $query->where(function ($q) use ($searchConfig) {
        foreach ($searchConfig as $config) {
            if (isset($config['relation'])) {
                // Relationship search
                $q->orWhereHas($config['relation'], function ($relationQuery) use ($config) {
                    $relationQuery->where(function($subQuery) use ($config) {
                        foreach ($config['fields'] as $field) {
                            $subQuery->orWhere($field, 'like', '%' . $this->search . '%');
                        }
                    });
                });
            } else {
                // Direct field search
                $q->orWhere($config['field'], 'like', '%' . $this->search . '%');
            }
        }
    });
    }
    /**
     * Apply sorting to query
     */
    protected function applySorting($query)
    {
        return $query->orderBy($this->sortBy, $this->sortDirection);
    }
}
