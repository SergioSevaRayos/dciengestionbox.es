<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Clases de hoy</x-slot>

        @php $sessions = $this->getSessions(); @endphp

        @if(empty($sessions))
            <p style="font-size:13px;font-style:italic;text-align:center;padding:1rem 0;color:var(--color-text-tertiary)">
                No hay clases programadas para hoy.
            </p>
        @else
        <div style="display:flex;flex-direction:column;gap:8px">
            @foreach($sessions as $session)
            @php
                $full  = $session['booked'] >= $session['capacity'];
                $empty = $session['booked'] === 0;
                $past  = $session['past'];
            @endphp
            <div x-data="{ open: false }"
                 style="border-radius:10px;border:0.5px solid var(--color-border-tertiary);overflow:hidden;background:{{ $past ? 'var(--color-background-secondary)' : 'var(--color-background-primary)' }}">

                <div @click="open=!open"
                     @mouseenter="$el.style.background='var(--color-background-secondary)'"
                     @mouseleave="$el.style.background='transparent'"
                     style="display:flex;align-items:center;gap:10px;padding:11px 14px;cursor:pointer;user-select:none">

                    <span style="font-size:12px;font-family:monospace;min-width:36px;flex-shrink:0;color:{{ $past ? 'var(--color-text-tertiary)' : 'var(--color-text-secondary)' }}">{{ $session['time'] }}</span>

                    <div style="width:6px;height:6px;border-radius:50%;flex-shrink:0;background:{{ $past ? 'var(--color-border-primary)' : '#22c55e' }}"></div>

                    <span style="font-size:13px;font-weight:500;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:{{ $past ? 'var(--color-text-tertiary)' : 'var(--color-text-primary)' }}">{{ $session['name'] }}</span>

                    <span style="font-size:11px;font-weight:500;padding:2px 8px;border-radius:20px;flex-shrink:0;
                        @if($past) background:var(--color-background-tertiary);color:var(--color-text-tertiary);border:0.5px solid var(--color-border-tertiary)
                        @elseif($full) background:#fee2e2;color:#991b1b;border:0.5px solid #fecaca
                        @elseif($empty) background:var(--color-background-secondary);color:var(--color-text-tertiary);border:0.5px solid var(--color-border-tertiary)
                        @else background:#dcfce7;color:#166534;border:0.5px solid #bbf7d0
                        @endif">
                        {{ $session['booked'] }}/{{ $session['capacity'] }}
                    </span>

                    <span x-text="open ? '▲' : '▼'" style="font-size:9px;flex-shrink:0;color:var(--color-text-tertiary)"></span>
                </div>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     style="padding:0 14px 12px;border-top:0.5px solid var(--color-border-tertiary)">

                    @if(empty($session['users']))
                        <p style="font-size:12px;font-style:italic;color:var(--color-text-tertiary);padding-top:10px">Sin reservas aún</p>
                    @else
                        <div style="display:flex;flex-wrap:wrap;gap:5px;padding-top:10px">
                            @foreach(array_slice($session['users'], 0, 8) as $user)
                            @php $initials = collect(explode(' ', $user['name']))->map(fn($w)=>strtoupper($w[0]??''))->take(2)->implode(''); @endphp
                            <div style="display:flex;align-items:center;gap:5px;background:var(--color-background-secondary);border:0.5px solid var(--color-border-tertiary);border-radius:20px;padding:2px 8px 2px 2px">
                                <div style="width:20px;height:20px;border-radius:50%;background:var(--color-background-info);overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:500;color:var(--color-text-info);flex-shrink:0">@if($user['avatar'])<img src="{{ $user['avatar'] }}" style="width:100%;height:100%;object-fit:cover" alt="">@else{{ $initials }}@endif</div>
                                <span style="font-size:11px;color:var(--color-text-secondary)">{{ explode(' ', $user['name'])[0] }}</span>
                            </div>
                            @endforeach
                            @if(count($session['users']) > 8)
                            <div style="display:flex;align-items:center;background:var(--color-background-secondary);border:0.5px solid var(--color-border-tertiary);border-radius:20px;padding:2px 8px;font-size:11px;color:var(--color-text-tertiary)">
                                +{{ count($session['users']) - 8 }} más
                            </div>
                            @endif
                        </div>
                    @endif

                    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:8px;margin-top:8px;border-top:0.5px solid var(--color-border-tertiary)">
                        <a href="{{ $session['edit_url'] }}" style="font-size:12px;font-weight:500;color:var(--color-text-info);text-decoration:none" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                            ✎ Gestionar
                        </a>
                        @if($past)
                            <span style="font-size:11px;color:var(--color-text-tertiary)">⏱ Finalizada</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
