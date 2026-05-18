<ul class="flex flex-col gap-1.5 mt-1">
    @php
        $state = $getState() ?? '';
        $text = strip_tags(str_replace(['<br>', '</div>', '</li>', '</p>', '</h1>', '</h2>', '</h3>'], "\n", $state));
    @endphp
    @foreach(explode("\n", $text) as $line)
        @if(!empty(trim($line)))
            <li class="flex items-start gap-3">
                <x-filament::icon icon="heroicon-m-chevron-right" class="h-4 w-4 text-primary-500 shrink-0 mt-0.5" />
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300 leading-relaxed">{{ trim($line) }}</span>
            </li>
        @endif
    @endforeach
</ul>
