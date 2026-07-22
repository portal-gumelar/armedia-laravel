<x-filament-widgets::widget>
    @if($this->announcements->isNotEmpty())
        <x-filament::section style="background: linear-gradient(135deg, rgba(59,130,246,0.05) 0%, rgba(37,99,235,0.05) 100%); border-left: 4px solid #3b82f6; border-radius: 0.75rem; overflow: hidden; transition: all 0.3s ease;" class="hover:shadow-md mt-4">
            <x-slot name="heading">
                <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400">
                    <x-filament::icon icon="heroicon-o-megaphone" class="w-6 h-6" />
                    <span class="font-bold">Pengumuman Terbaru</span>
                </div>
            </x-slot>

            <div class="space-y-4">
                @foreach($this->announcements as $announcement)
                    <div class="p-4 bg-white dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-800 shadow-sm hover:border-blue-200 dark:hover:border-blue-800 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-bold text-gray-900 dark:text-white">{{ $announcement->title }}</h3>
                            <x-filament::badge 
                                :color="match($announcement->type) {
                                    'info' => 'info',
                                    'warning' => 'warning',
                                    'promo' => 'success',
                                    default => 'gray',
                                }"
                            >
                                {{ ucfirst($announcement->type) }}
                            </x-filament::badge>
                        </div>
                        <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-300">
                            {!! function_exists('clean') ? clean($announcement->content) : strip_tags($announcement->content, '<b><strong><i><em><u><p><br><ul><ol><li><h1><h2><h3>') !!}
                        </div>
                        <div class="mt-3 text-xs text-gray-400">
                            Dipublikasikan pada: {{ $announcement->created_at->format('d M Y H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif
</x-filament-widgets::widget>
