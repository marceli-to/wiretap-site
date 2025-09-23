<div class="space-y-8 !bg-red-500" wire:poll.5s>
  <!-- Header -->
  <div class="sm:flex sm:items-center sm:justify-between">
    <div>
      <p class="mt-2 text-lg dark:text-gray-300 text-pretty">
        Taps into your systems and monitors and analyzes your application logs in (almost) realtime.
      </p>
    </div>
    <div class="mt-4 sm:mt-0 flex gap-2">
      @if($search || $levelFilter || $envFilter || $appFilter || $fixedFilter)
        <flux:button
          variant="danger"
          icon="trash"
          iconVariant="outline"
          wire:click="deleteFilteredLogs"
          wire:confirm="Are you sure you want to delete all logs matching the current filters? This action cannot be undone.">
          Delete
        </flux:button>
      @endif
      <flux:modal.trigger name="filters">
        <flux:button variant="primary" color="zinc" icon="funnel" iconVariant="outline">Filters</flux:button>
      </flux:modal.trigger>
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

      <flux:table.column>Environment</flux:table.column>
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

          <flux:table.cell class="max-w-xs">
            <div class="truncate" title="{{ $log->message }}">
              {{ $log->message }}
            </div>
          </flux:table.cell>

          <flux:table.cell class="max-w-xs">
            <div class="truncate font-medium">{{ $log->app_name ?? 'Unknown' }}</div>
          </flux:table.cell>

          <flux:table.cell>
            @if($log->app_env)
              <flux:badge :color="$this->envColor($log->app_env)" size="sm">
                {{ ucfirst($log->app_env) }}
              </flux:badge>
            @else
              <div class="text-gray-500">N/A</div>
            @endif
          </flux:table.cell>

          <flux:table.cell align="end">
            <div class="flex justify-end">
              <flux:button
                icon="check-circle"
                iconVariant="outline"
                size="sm"
                variant="subtle"
                class="{{ $log->fixed_at ? '!text-green-500' : 'text-gray-400' }}"
                wire:click="toggleFixed({{ $log->id }})"
                title="{{ $log->fixed_at ? 'Mark as not fixed' : 'Mark as fixed' }}" />
              @if($log->context)
                <flux:modal.trigger name="context">
                  <flux:button icon="arrow-top-right-on-square" iconVariant="outline" size="sm" variant="subtle" wire:click="showContext({{ $log->id }})" />
                </flux:modal.trigger>
              @endif
              <flux:button icon="trash" iconVariant="outline" size="sm" variant="subtle" wire:click="deleteLog({{ $log->id }})" wire:confirm="Are you sure you want to delete this log entry?" />
            </div>
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

  <!-- Filters Modal -->
  <flux:modal name="filters" class="w-full md:w-96 lg:w-128" variant="flyout">
    <div class="space-y-6">
      <div>
        <flux:heading size="lg">Filter Logs</flux:heading>
        <flux:subheading>Refine your log view</flux:subheading>
      </div>

      <div class="space-y-4">
        <div>
          <flux:field>
            <flux:label>Search</flux:label>
            <flux:input
              wire:model.live.debounce.300ms="search"
              placeholder="Search logs…"
              type="search"
            />
          </flux:field>
        </div>

        <div>
          <flux:field>
            <flux:label>Level</flux:label>
            <flux:select wire:model.live="levelFilter" placeholder="Filter by level">
              <flux:select.option value="">All Levels</flux:select.option>
              @foreach($levels as $level)
                <flux:select.option value="{{ $level }}">{{ ucfirst($level) }}</flux:select.option>
              @endforeach
            </flux:select>
          </flux:field>
        </div>

        <div>
          <flux:field>
            <flux:label>Environment</flux:label>
            <flux:select wire:model.live="envFilter" placeholder="Filter by environment">
              <flux:select.option value="">All Environments</flux:select.option>
              @foreach($environments as $env)
                <flux:select.option value="{{ $env }}">{{ ucfirst($env) }}</flux:select.option>
              @endforeach
            </flux:select>
          </flux:field>
        </div>

        <div>
          <flux:field>
            <flux:label>Application</flux:label>
            <flux:select wire:model.live="appFilter" placeholder="Filter by application">
              <flux:select.option value="">All Applications</flux:select.option>
              @foreach($applications as $app)
                <flux:select.option value="{{ $app }}">{{ $app }}</flux:select.option>
              @endforeach
            </flux:select>
          </flux:field>
        </div>

        <div>
          <flux:field>
            <flux:label>Fixed Status</flux:label>
            <flux:select wire:model.live="fixedFilter" placeholder="Filter by status">
              <flux:select.option value="">All Records</flux:select.option>
              <flux:select.option value="fixed">Fixed</flux:select.option>
              <flux:select.option value="not_fixed">Not Fixed</flux:select.option>
            </flux:select>
          </flux:field>
        </div>
      </div>

      <div class="flex">
        <flux:button variant="subtle" wire:click="clearFilters">Clear filters</flux:button>
        <flux:spacer />
        <flux:modal.close>
          <flux:button variant="primary" color="zinc">Apply</flux:button>
        </flux:modal.close>
      </div>
    </div>
  </flux:modal>

  <!-- Single Context Modal -->
  <flux:modal name="context" class="w-[90vw] max-w-2xl">
    <div class="space-y-6">
      <div>
        <flux:heading size="lg">Log Details</flux:heading>
        <flux:subheading>Complete information for this entry</flux:subheading>
      </div>

      @if($this->activeLog)
        <div class="space-y-4">
          <!-- Server Information -->
          <h3 class="font-medium text-sm text-gray-900 dark:text-white mb-3">Server Information</h3>
          <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
            <div class="flex flex-col md:grid md:grid-cols-2 gap-4 text-sm">
              <div>
                <span class="font-medium text-gray-900 dark:text-gray-400">Hostname:</span>
                <div class="mt-1">{{ $this->activeLog->server_hostname ?? 'Unknown' }}</div>
              </div>
              <div>
                <span class="font-medium text-gray-900 dark:text-gray-400">IP Address:</span>
                <div class="mt-1">{{ $this->activeLog->server_ip ?? 'N/A' }}</div>
              </div>
              <div>
                <span class="font-medium text-gray-900 dark:text-gray-400">Application:</span>
                <div class="mt-1">{{ $this->activeLog->app_name ?? 'Unknown' }}</div>
              </div>
              <div>
                <span class="font-medium text-gray-900 dark:text-gray-400">Environment:</span>
                <div class="mt-1">
                  @if($this->activeLog->app_env)
                    <flux:badge :color="$this->envColor($this->activeLog->app_env)" size="sm">
                      {{ ucfirst($this->activeLog->app_env) }}
                    </flux:badge>
                  @else
                    N/A
                  @endif
                </div>
              </div>
            </div>
          </div>

          <!-- Context Data -->
          @if($this->activeContext)
            <div class="flex justify-between items-end" x-data="{
              copied: false,
              copyToClipboard() {
                navigator.clipboard.writeText(@js(json_encode($this->activeContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)));
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
              }
            }">
              <h3 class="font-medium text-sm text-gray-900 dark:text-white">
                Context Data
              </h3>
              <flux:button
                size="sm"
                variant="subtle"
                square
                x-on:click="copyToClipboard()"
                x-bind:tooltip="copied ? 'Copied!' : 'Copy to clipboard'">
                <span x-show="!copied" x-cloak>
                  <flux:icon.clipboard-document-list variant="mini" />
                </span>
                <span x-show="copied" x-cloak>
                  <flux:icon.check-badge variant="mini" class="text-green-400" />
                </span>
              </flux:button>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
              <pre class="text-sm overflow-x-auto">@json($this->activeContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)</pre>
            </div>
          @endif
        </div>
      @else
        <p class="text-sm text-gray-500">No log details available.</p>
      @endif
    </div>
  </flux:modal>
</div>
