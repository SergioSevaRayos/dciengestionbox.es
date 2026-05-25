@php
    $users = is_array($getState()) ? $getState() : [];
@endphp

@if(empty($users))
    <span class="text-xs text-gray-400 italic">Sin reservas aún</span>
@else
    <div class="flex flex-wrap gap-2">
        @foreach($users as $user)
        <div class="flex items-center gap-1.5 bg-gray-100 dark:bg-gray-800 rounded-full pl-0.5 pr-3 py-0.5">
            {{-- Avatar --}}
            <div class="w-6 h-6 rounded-full overflow-hidden shrink-0 bg-primary-500 flex items-center justify-center">
                @php
                    $avatarPath = $user['avatar'] ?? null;
                    $avatarFile = $avatarPath ? base_path('../public_html/storage/' . $avatarPath) : null;
                    $hasAvatar = $avatarFile && file_exists($avatarFile);
                @endphp
                @if($hasAvatar)
                    @php
                        $mime = mime_content_type($avatarFile);
                        $b64 = base64_encode(file_get_contents($avatarFile));
                    @endphp
                    <img src="data:{{ $mime }};base64,{{ $b64 }}"
                         class="w-full h-full object-cover"
                         alt="{{ $user['name'] ?? '' }}">
                @else
                    <span class="text-white text-xs font-bold">
                        {{ strtoupper(substr($user['name'] ?? '?', 0, 1)) }}
                    </span>
                @endif
            </div>
            {{-- Nombre --}}
            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                {{ $user['name'] ?? 'Atleta' }}
            </span>
        </div>
        @endforeach
    </div>
@endif
