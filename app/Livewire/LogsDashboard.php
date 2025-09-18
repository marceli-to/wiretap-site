<?php

namespace App\Livewire;

use App\Models\Log;
use Livewire\Component;
use Livewire\WithPagination;

class LogsDashboard extends Component
{
	use WithPagination;

	public $search = '';
	public $levelFilter = '';
	public $sortBy = 'timestamp';
	public $sortDirection = 'desc';

	public function updatingSearch()
	{
		$this->resetPage();
	}

	public function updatingLevelFilter()
	{
		$this->resetPage();
	}

	public function sortBy($field)
	{
		if ($this->sortBy === $field) {
			$this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
		} else {
			$this->sortBy = $field;
			$this->sortDirection = 'asc';
		}
	}

	public function render()
	{
		$query = Log::query();

		if ($this->search) {
			$query->where(function ($q) {
				$q->where('message', 'like', '%' . $this->search . '%')
					->orWhere('app_name', 'like', '%' . $this->search . '%')
					->orWhere('server_hostname', 'like', '%' . $this->search . '%');
			});
		}

		if ($this->levelFilter) {
			$query->where('level', $this->levelFilter);
		}

		$logs = $query->orderBy($this->sortBy, $this->sortDirection)
			->paginate(15);

		return view('livewire.logs-dashboard', [
			'logs' => $logs,
			'levels' => Log::distinct('level')->pluck('level')
		])->layout('layouts.app');
	}
}
