<div class="space-y-6">
	<!-- Header -->
	<div class="sm:flex sm:items-center sm:justify-between">
		<div>
			<h1 class="text-2xl font-bold text-gray-900 dark:text-white">Wiretap</h1>
			<p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
				Taps into your systems and monitors and analyzes your application logs in real-time.
			</p>
		</div>
	</div>

	<!-- Filters -->
  {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="Search logs..."
        type="search"
      />
    </div>
    <div>
      <flux:select wire:model.live="levelFilter" placeholder="Filter by level">
        <flux:select.option value="">All Levels</flux:select.option>
        @foreach($levels as $level)
          <flux:select.option value="{{ $level }}">{{ ucfirst($level) }}</flux:select.option>
        @endforeach
      </flux:select>
    </div>
  </div> --}}

	<!-- Logs Table -->
  <flux:table :paginate="$logs">
    <flux:table.columns>
      <flux:table.column sortable :sorted="$sortBy === 'timestamp'" :direction="$sortDirection" wire:click="sortBy('timestamp')">
        Date
      </flux:table.column>
      <flux:table.column sortable :sorted="$sortBy === 'level'" :direction="$sortDirection" wire:click="sortBy('level')">
        Level
      </flux:table.column>
      <flux:table.column>Message</flux:table.column>
      <flux:table.column sortable :sorted="$sortBy === 'app_name'" :direction="$sortDirection" wire:click="sortBy('app_name')">
        Application
      </flux:table.column>
      <flux:table.column>Server</flux:table.column>
      <flux:table.column>Context</flux:table.column>
    </flux:table.columns>

    <flux:table.rows>
      @forelse($logs as $log)
        <flux:table.row>
          <flux:table.cell>
            {{ $log->timestamp->format('d.m.Y H:i:s') }}
          </flux:table.cell>
          <flux:table.cell>
            <flux:badge
              :color="match($log->level) {
                'error' => 'red',
                'warning' => 'yellow',
                'info' => 'blue',
                'debug' => 'gray',
                default => 'green'
              }"
              size="sm"
            >
              {{ ucfirst($log->level) }}
            </flux:badge>
          </flux:table.cell>
          <flux:table.cell class="max-w-md">
            <div class="truncate" title="{{ $log->message }}">
              {{ $log->message }}
            </div>
          </flux:table.cell>
          <flux:table.cell>
            <div class="text-sm">
              <div class="font-medium">{{ $log->app_name ?? 'Unknown' }}</div>
              <div class="text-gray-500">{{ $log->app_env ?? 'N/A' }}</div>
            </div>
          </flux:table.cell>
          <flux:table.cell>
            <div class="text-sm">
              <div class="font-medium">{{ $log->server_hostname ?? 'Unknown' }}</div>
              <div class="text-gray-500">{{ $log->server_ip ?? 'N/A' }}</div>
            </div>
          </flux:table.cell>
          <flux:table.cell>
            @if($log->context)
              <flux:modal.trigger name="context-{{ $log->id }}">
                <flux:button variant="subtle" size="sm">View</flux:button>
              </flux:modal.trigger>
            @else
              <span class="text-gray-400 text-sm">No context</span>
            @endif
          </flux:table.cell>
        </flux:table.row>
      @empty
        <flux:table.row>
          <flux:table.cell colspan="6" class="text-center py-12">
            <div class="text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No logs found</h3>
              <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria.</p>
            </div>
          </flux:table.cell>
        </flux:table.row>
      @endforelse
    </flux:table.rows>
  </flux:table>


	<!-- Context Modals -->
	@foreach($logs as $log)
		@if($log->context)
			<flux:modal name="context-{{ $log->id }}" class="md:w-96 lg:w-1/2">
				<div class="space-y-6">
					<div>
						<flux:heading size="lg">Log Context</flux:heading>
						<flux:subheading>Additional data for this log entry</flux:subheading>
					</div>
					<div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
						<pre class="text-sm overflow-x-auto">{{ json_encode($log->context, JSON_PRETTY_PRINT) }}</pre>
					</div>
					<div class="flex">
						<flux:spacer />
						<flux:modal.close>
							<flux:button variant="primary">Close</flux:button>
						</flux:modal.close>
					</div>
				</div>
			</flux:modal>
		@endif
	@endforeach
</div>
