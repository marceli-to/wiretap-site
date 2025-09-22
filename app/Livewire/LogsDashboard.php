<?php
namespace App\Livewire;
use App\Models\Log;
use Livewire\Component;
use Livewire\WithPagination;

class LogsDashboard extends Component
{
	use WithPagination;

	/** Filters & table state */
	public string $search = '';
	public string $levelFilter = '';
	public string $envFilter = '';
	public string $appFilter = '';
	public string $sortBy = 'timestamp';
	public string $sortDirection = 'desc';

	/** Active row for context modal */
	public ?int $activeLogId = null;

	/** Only allow these columns to be sortable */
	protected array $sortable = ['timestamp', 'level', 'app_name'];

	/** Persist state to the URL */
	protected $queryString = [
		'search'        => ['except' => ''],
		'levelFilter'   => ['except' => ''],
		'envFilter'     => ['except' => ''],
		'appFilter'     => ['except' => ''],
		'sortBy'        => ['except' => 'timestamp'],
		'sortDirection' => ['except' => 'desc'],
	];

	public function updatingSearch()      { $this->resetPage(); }
	public function updatingLevelFilter() { $this->resetPage(); }
	public function updatingEnvFilter()   { $this->resetPage(); }
	public function updatingAppFilter()   { $this->resetPage(); }

	public function setSort(string $field): void
	{
		if (! in_array($field, $this->sortable, true)) {
			return;
		}

		if ($this->sortBy === $field) {
			$this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
		} else {
			$this->sortBy = $field;
			$this->sortDirection = 'asc';
		}

		$this->resetPage();
	}

	public function clearFilters(): void
	{
		$this->reset(['search', 'levelFilter', 'envFilter', 'appFilter']);
		$this->sortBy = 'timestamp';
		$this->sortDirection = 'desc';
	}

	public function showContext(int $id): void
	{
		$this->activeLogId = $id;
	}

	public function deleteLog(int $id): void
	{
		Log::query()->find($id)?->delete();
		$this->resetPage();
	}

	public function deleteFilteredLogs(): void
	{
		$query = Log::query()
			->when($this->search, function ($q, $term) {
				$q->where(function ($q) use ($term) {
					$like = "%{$term}%";
					$q->where('message', 'like', $like)
					  ->orWhere('app_name', 'like', $like)
					  ->orWhere('server_hostname', 'like', $like);
				});
			})
			->when($this->levelFilter, fn ($q, $lvl) => $q->where('level', $lvl))
			->when($this->envFilter, fn ($q) => $q->where('app_env', $this->envFilter))
			->when($this->appFilter, fn ($q) => $q->where('app_name', $this->appFilter));

		$deletedCount = $query->count();
		$query->delete();

		$this->clearFilters();
		$this->dispatch('notify', 'success', "Deleted {$deletedCount} log records");
	}

	/** Livewire computed property: $this->activeContext */
	public function getActiveContextProperty(): mixed
	{
		$log = $this->activeLogId ? Log::query()->select('id', 'context')->find($this->activeLogId) : null;
		return $log?->context ?? null;
	}

	/** Livewire computed property: $this->activeLog */
	public function getActiveLogProperty(): ?Log
	{
		return $this->activeLogId ? Log::query()->find($this->activeLogId) : null;
	}

	public function levelColor(string $level): string
	{
		return [
			'error'   => 'red',
			'warning' => 'yellow',
			'info'    => 'blue',
			'debug'   => 'gray',
		][$level] ?? 'green';
	}

	public function envColor(string $env): string
	{
		return [
			'production' => 'green',
			'staging'    => 'blue',
			'testing'    => 'blue',
			'development'=> 'blue',
			'local'      => 'gray',
		][strtolower($env)] ?? 'purple';
	}

	public function render()
	{
		$logs = Log::query()
			->when($this->search, function ($q, $term) {
				$q->where(function ($q) use ($term) {
					$like = "%{$term}%";
					$q->where('message', 'like', $like)
					  ->orWhere('app_name', 'like', $like)
					  ->orWhere('server_hostname', 'like', $like);
				});
			})
			->when($this->levelFilter, fn ($q, $lvl) => $q->where('level', $lvl))
			->when($this->envFilter, fn ($q) => $q->where('app_env', $this->envFilter))
			->when($this->appFilter, fn ($q) => $q->where('app_name', $this->appFilter))
			->orderBy($this->sortBy, $this->sortDirection)
			->paginate(15);

		$levels = Log::query()
			->select('level')
			->distinct()
			->orderBy('level')
			->pluck('level');

		$environments = Log::query()
			->select('app_env')
			->distinct()
			->whereNotNull('app_env')
			->orderBy('app_env')
			->pluck('app_env');

		$applications = Log::query()
			->select('app_name')
			->distinct()
			->whereNotNull('app_name')
			->orderBy('app_name')
			->pluck('app_name');

		return view('livewire.logs-dashboard', compact('logs', 'levels', 'environments', 'applications'))->layout('layouts.app');
	}
}
