<div class="space-y-6">
  <!-- Header -->
  <div class="sm:flex sm:items-center sm:justify-between">
    <div>
      <h1 class="text-2xl font-mono uppercase text-gray-900 dark:text-white">Wiretap</h1>
      <p class="mt-2 text-sm dark:text-gray-300">
        Taps into your systems and monitors and analyzes your application logs in real-time.
      </p>
    </div>
    <div class="mt-4 sm:mt-0">
      <flux:button variant="subtle" size="sm" wire:click="clearFilters">Clear filters</flux:button>
    </div>
  </div>

  <!-- Filters -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="Search logs…"
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
  </div>

  <!-- Logs Table -->
  <flux:table :paginate="$logs">
    <flux:table.columns>
      <flux:table.column
        sortable
        :sorted="$sortBy === 'timestamp'"
        :direction="$sortDirection"
        wire:click="setSort('timestamp')"
      >
        Date
      </flux:table.column>

      <flux:table.column
        sortable
        :sorted="$sortBy === 'level'"
        :direction="$sortDirection"
        wire:click="setSort('level')"
      >
        Level
      </flux:table.column>

      <flux:table.column>Message</flux:table.column>

      <flux:table.column
        sortable
        :sorted="$sortBy === 'app_name'"
        :direction="$sortDirection"
        wire:click="setSort('app_name')"
      >
        Application
      </flux:table.column>

      <flux:table.column>Server</flux:table.column>
      <flux:table.column>&nbsp;</flux:table.column>
    </flux:table.columns>

    <flux:table.rows>
      @forelse($logs as $log)
        <flux:table.row>
          <flux:table.cell>
            {{ optional($log->timestamp)->format('d.m.Y H:i:s') }}
          </flux:table.cell>

          <flux:table.cell>
            <flux:badge :color="$this->levelColor($log->level)" size="sm">
              {{ ucfirst($log->level) }}
            </flux:badge>
          </flux:table.cell>

          <flux:table.cell class="max-w-md">
            <div class="truncate text-sm" title="{{ $log->message }}">
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
              <flux:modal.trigger name="context">
                <flux:button icon="arrow-up-right" class="text-red-500" size="sm" variant="subtle" wire:click="showContext({{ $log->id }})" />
              </flux:modal.trigger>
            @endif
          </flux:table.cell>
        </flux:table.row>
      @empty
        <flux:table.row>
          <flux:table.cell colspan="6" class="text-center py-12">
            <div class="text-gray-500">
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No logs found</h3>
              <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria.</p>
            </div>
          </flux:table.cell>
        </flux:table.row>
      @endforelse
    </flux:table.rows>
  </flux:table>

  <!-- Single Context Modal -->
  <flux:modal name="context" class="md:w-96 lg:w-1/3">
    <div class="space-y-6">
      <div>
        <flux:heading size="lg">Context</flux:heading>
        <flux:subheading>Additional data for this entry</flux:subheading>
      </div>

      @if($this->activeContext)
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
          <pre class="text-sm overflow-x-auto">@json($this->activeContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)</pre>
        </div>
      @else
        <p class="text-sm text-gray-500">No context available.</p>
      @endif

      <div class="flex">
        <flux:spacer />
        <flux:modal.close>
          <flux:button variant="primary">Close</flux:button>
        </flux:modal.close>
      </div>
    </div>
  </flux:modal>
</div>
