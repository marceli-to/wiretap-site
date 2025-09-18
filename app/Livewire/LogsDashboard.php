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
		'sortBy'        => ['except' => 'timestamp'],
		'sortDirection' => ['except' => 'desc'],
		'page'          => ['except' => 1],
	];

	public function updatingSearch()      { $this->resetPage(); }
	public function updatingLevelFilter() { $this->resetPage(); }

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
		$this->reset(['search', 'levelFilter']);
		$this->sortBy = 'timestamp';
		$this->sortDirection = 'desc';
	}

	public function showContext(int $id): void
	{
		$this->activeLogId = $id;
	}

	/** Livewire computed property: $this->activeContext */
	public function getActiveContextProperty(): mixed
	{
		$log = $this->activeLogId ? Log::query()->select('id', 'context')->find($this->activeLogId) : null;
		return $log?->context ?? null;
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
			->orderBy($this->sortBy, $this->sortDirection)
			->paginate(15);

		$levels = Log::query()
			->select('level')
			->distinct()
			->orderBy('level')
			->pluck('level');

		return view('livewire.logs-dashboard', compact('logs', 'levels'))->layout('layouts.app');
	}
}
