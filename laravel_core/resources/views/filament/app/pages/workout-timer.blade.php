<x-filament-panels::page>
    <style>
        body.timer-is-active .fi-topbar,
        body.timer-is-active .fi-sidebar {
            display: none !important;
        }
        body.timer-is-active .fi-main {
            padding: 0 !important;
            margin: 0 !important;
        }
        body.timer-is-active {
            overflow: hidden !important;
            touch-action: none;
        }
    </style>

    <div x-data="smartTimer()" class="w-full max-w-2xl mx-auto selection:bg-transparent">
        
        <div x-show="mode === 'setup'" class="space-y-6" x-transition>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <template x-for="t in ['amrap', 'fortime', 'emom', 'tabata']">
                    <button @click="type = t"
                        class="p-4 rounded-xl border-2 font-bold uppercase tracking-wider text-sm transition-all text-center"
                        :class="type === t ? 'border-primary-500 bg-primary-500/10 text-primary-600 dark:text-primary-400' : 'border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-500'">
                        <span x-text="t"></span>
                    </button>
                </template>
            </div>

            <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 shadow-sm">
                <div x-show="type === 'amrap'" class="flex flex-col items-center gap-4 text-center">
                    <p class="text-sm font-medium text-gray-500 uppercase">Tiempo Total (Minutos)</p>
                    <div class="flex items-center gap-6">
                        <button @click="if(cfgAmrap > 1) cfgAmrap--" class="p-4 rounded-full bg-gray-100 dark:bg-white/5"><x-heroicon-o-minus class="w-8 h-8"/></button>
                        <span class="text-5xl font-black" x-text="cfgAmrap"></span>
                        <button @click="cfgAmrap++" class="p-4 rounded-full bg-gray-100 dark:bg-white/5"><x-heroicon-o-plus class="w-8 h-8"/></button>
                    </div>
                </div>
                <div x-show="type === 'fortime'" class="flex flex-col items-center gap-4 text-center">
                    <p class="text-sm font-medium text-gray-500 uppercase">Tiempo Límite</p>
                    <div class="flex items-center gap-6">
                        <button @click="if(cfgForTime > 1) cfgForTime--" class="p-4 rounded-full bg-gray-100 dark:bg-white/5"><x-heroicon-o-minus class="w-8 h-8"/></button>
                        <span class="text-5xl font-black" x-text="cfgForTime"></span>
                        <button @click="cfgForTime++" class="p-4 rounded-full bg-gray-100 dark:bg-white/5"><x-heroicon-o-plus class="w-8 h-8"/></button>
                    </div>
                </div>
                <div x-show="type === 'emom'" class="flex flex-col items-center gap-4 text-center">
                    <p class="text-sm font-medium text-gray-500 uppercase">Minutos Totales</p>
                    <div class="flex items-center gap-6">
                        <button @click="if(cfgEmom > 1) cfgEmom--" class="p-4 rounded-full bg-gray-100 dark:bg-white/5"><x-heroicon-o-minus class="w-8 h-8"/></button>
                        <span class="text-5xl font-black" x-text="cfgEmom"></span>
                        <button @click="cfgEmom++" class="p-4 rounded-full bg-gray-100 dark:bg-white/5"><x-heroicon-o-plus class="w-8 h-8"/></button>
                    </div>
                </div>
                <div x-show="type === 'tabata'" class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div class="flex flex-col items-center">
                        <p class="text-xs font-bold text-gray-500 uppercase mb-4">Rondas</p>
                        <div class="flex items-center gap-4">
                            <button @click="if(cfgTabataRounds > 1) cfgTabataRounds--" class="p-3 rounded-full bg-gray-100 dark:bg-white/5"><x-heroicon-o-minus class="w-6 h-6"/></button>
                            <span class="text-3xl font-black w-12" x-text="cfgTabataRounds"></span>
                            <button @click="cfgTabataRounds++" class="p-3 rounded-full bg-gray-100 dark:bg-white/5"><x-heroicon-o-plus class="w-6 h-6"/></button>
                        </div>
                    </div>
                    <div class="flex flex-col items-center">
                        <p class="text-xs font-bold text-gray-500 uppercase mb-4">Trabajo (s)</p>
                        <div class="flex items-center gap-4">
                            <button @click="if(cfgTabataWork > 5) cfgTabataWork -= 5" class="p-3 rounded-full bg-gray-100 dark:bg-white/5"><x-heroicon-o-minus class="w-6 h-6"/></button>
                            <span class="text-3xl font-black w-12" x-text="cfgTabataWork"></span>
                            <button @click="cfgTabataWork += 5" class="p-3 rounded-full bg-gray-100 dark:bg-white/5"><x-heroicon-o-plus class="w-6 h-6"/></button>
                        </div>
                    </div>
                    <div class="flex flex-col items-center">
                        <p class="text-xs font-bold text-gray-500 uppercase mb-4">Descanso (s)</p>
                        <div class="flex items-center gap-4">
                            <button @click="if(cfgTabataRest > 0) cfgTabataRest -= 5" class="p-3 rounded-full bg-gray-100 dark:bg-white/5"><x-heroicon-o-minus class="w-6 h-6"/></button>
                            <span class="text-3xl font-black w-12" x-text="cfgTabataRest"></span>
                            <button @click="cfgTabataRest += 5" class="p-3 rounded-full bg-gray-100 dark:bg-white/5"><x-heroicon-o-plus class="w-6 h-6"/></button>
                        </div>
                    </div>
                </div>
            </div>

            <button @click="startWorkout()" class="w-full bg-primary-600 hover:bg-primary-500 text-white font-black text-2xl py-6 rounded-2xl shadow-xl transition-all uppercase tracking-widest">
                INICIAR ENTRENAMIENTO
            </button>
        </div>

        <template x-teleport="body">
            <div x-show="mode !== 'setup'" x-cloak 
                 class="fixed inset-0 z-[2147483647] flex flex-col justify-between items-center transition-colors duration-300"
                 :style="'background-color: ' + getPhaseBgColor()">
                 
                <div class="w-full flex justify-between items-start p-6 pt-16">
                    <button @click="stopWorkout()" class="p-4 bg-black/20 text-white rounded-full hover:bg-black/40 transition-all shadow-lg backdrop-blur-md">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <div class="text-right text-white drop-shadow-md" x-show="currentPhase.type !== 'end'">
                        <p class="text-3xl md:text-5xl font-black uppercase tracking-widest" x-text="currentPhase.name"></p>
                        <p class="text-xl md:text-2xl font-bold opacity-80 mt-1" x-show="phases.length > 2" x-text="'Ronda ' + currentPhaseIndex + ' / ' + (phases.length - 1)"></p>
                    </div>
                </div>

                <div class="flex-1 flex flex-col items-center justify-center w-full px-2 text-center overflow-hidden">
                    <div class="h-16 mb-2">
                        <div x-show="mode === 'paused'" x-transition class="text-3xl md:text-5xl font-black uppercase tracking-widest text-white/90 animate-pulse drop-shadow-lg">
                            PAUSADO
                        </div>
                    </div>
                    
                    <div class="text-white font-black leading-none tabular-nums whitespace-nowrap drop-shadow-[0_10px_30px_rgba(0,0,0,0.4)] w-full"
                         style="font-size: min(22vw, 25vh); text-shadow: 0px 8px 16px rgba(0,0,0,0.3);">
                        <span x-text="formatTime(displayTime)"></span>
                    </div>
                </div>

                <div class="w-full p-6 pb-12">
                    <button @click="togglePause()" 
                            class="w-full py-6 rounded-2xl font-black text-3xl md:text-4xl uppercase tracking-widest shadow-2xl transition-all border-4 backdrop-blur-sm"
                            :class="mode === 'running' ? 'bg-black/20 border-white/20 hover:bg-black/30' : 'bg-white border-white hover:bg-gray-200'"
                            :style="mode === 'running' ? 'color: #ffffff !important;' : 'color: #000000 !important;'"
                            x-show="currentPhase.type !== 'end'">
                        <span x-text="mode === 'running' ? 'PAUSAR' : 'REANUDAR'"></span>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('smartTimer', () => ({
                mode: 'setup', 
                type: 'tabata', 
                
                cfgAmrap: 12, cfgForTime: 20, cfgEmom: 10, cfgTabataRounds: 8, cfgTabataWork: 20, cfgTabataRest: 10,
                phases: [], currentPhaseIndex: 0, timeLeft: 0, displayTime: 0, interval: null, expectedEndTime: 0, audioCtx: null, wakeLock: null,

                init() {
                    document.body.classList.remove('timer-is-active');
                },

                get currentPhase() { return this.phases[this.currentPhaseIndex] || { name: 'FIN', type: 'end' }; },

                getPhaseBgColor() {
                    if (this.currentPhase.type === 'prep') return '#eab308';
                    if (this.currentPhase.name && this.currentPhase.name.includes('DESCANSO')) return '#2563eb';
                    if (this.currentPhase.type === 'end') return '#16a34a';
                    return 'rgb(var(--primary-600))'; 
                },

                buildPhases() {
                    let p = [{ name: 'PREPARACIÓN', type: 'prep', duration: 10 }];
                    if (this.type === 'amrap') {
                        p.push({ name: 'AMRAP', type: 'down', duration: this.cfgAmrap * 60 });
                    } else if (this.type === 'fortime') {
                        p.push({ name: 'FOR TIME', type: 'up', duration: this.cfgForTime * 60 }); 
                    } else if (this.type === 'emom') {
                        for(let i=1; i<=this.cfgEmom; i++) p.push({ name: `MINUTO ${i}`, type: 'down', duration: 60 });
                    } else if (this.type === 'tabata') {
                        for(let i=1; i<=this.cfgTabataRounds; i++) {
                            p.push({ name: '¡TRABAJO!', type: 'down', duration: this.cfgTabataWork });
                            if (i < this.cfgTabataRounds) p.push({ name: 'DESCANSO', type: 'down', duration: this.cfgTabataRest });
                        }
                    }
                    p.push({ name: '¡COMPLETADO!', type: 'end', duration: 0 });
                    this.phases = p;
                },

                async initAudio() {
                    if (!this.audioCtx) this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    if (this.audioCtx.state === 'suspended') await this.audioCtx.resume();
                },

                playBeep(freq, dur, type = 'sine') {
                    if (!this.audioCtx) return;
                    const osc = this.audioCtx.createOscillator(); const gain = this.audioCtx.createGain();
                    osc.type = type; osc.frequency.value = freq;
                    gain.gain.setValueAtTime(1, this.audioCtx.currentTime); gain.gain.exponentialRampToValueAtTime(0.001, this.audioCtx.currentTime + dur);
                    osc.connect(gain); gain.connect(this.audioCtx.destination);
                    osc.start(); osc.stop(this.audioCtx.currentTime + dur);
                },

                async startWorkout() {
                    await this.initAudio();
                    try { if ('wakeLock' in navigator) this.wakeLock = await navigator.wakeLock.request('screen'); } catch(e) {}
                    
                    document.body.classList.add('timer-is-active');

                    this.buildPhases(); this.currentPhaseIndex = 0; this.mode = 'running'; this.startPhase();
                },

                startPhase() {
                    if (this.currentPhase.type === 'end') {
                        this.playBeep(300, 1.5, 'sawtooth'); this.mode = 'finished'; this.displayTime = 0;
                        setTimeout(() => this.stopWorkout(), 4000); return;
                    }

                    if (this.currentPhaseIndex > 0) this.playBeep(1000, 0.6, 'square'); 

                    this.timeLeft = this.currentPhase.duration; this.updateDisplayTime();
                    this.expectedEndTime = Date.now() + (this.timeLeft * 1000);
                    
                    this.interval = setInterval(() => {
                        if (this.mode !== 'running') return;
                        let remaining = Math.round((this.expectedEndTime - Date.now()) / 1000);
                        if (remaining > 0) {
                            if (remaining !== this.timeLeft) {
                                this.timeLeft = remaining; this.updateDisplayTime();
                                if (this.timeLeft <= 3 && this.timeLeft > 0) this.playBeep(600, 0.15, 'sine');
                            }
                        } else { clearInterval(this.interval); this.currentPhaseIndex++; this.startPhase(); }
                    }, 100);
                },

                updateDisplayTime() {
                    this.displayTime = this.currentPhase.type === 'up' ? this.currentPhase.duration - this.timeLeft : this.timeLeft;
                },

                togglePause() {
                    if (this.mode === 'running') { this.mode = 'paused'; clearInterval(this.interval); } 
                    else if (this.mode === 'paused') {
                        this.mode = 'running'; this.expectedEndTime = Date.now() + (this.timeLeft * 1000);
                        this.interval = setInterval(() => { 
                            if (this.mode !== 'running') return;
                            let remaining = Math.round((this.expectedEndTime - Date.now()) / 1000);
                            if (remaining > 0) { if (remaining !== this.timeLeft) { this.timeLeft = remaining; this.updateDisplayTime(); if (this.timeLeft <= 3 && this.timeLeft > 0) this.playBeep(600, 0.15, 'sine'); }
                            } else { clearInterval(this.interval); this.currentPhaseIndex++; this.startPhase(); }
                        }, 100);
                    }
                },

                stopWorkout() {
                    this.mode = 'setup'; clearInterval(this.interval);
                    if (this.wakeLock) { this.wakeLock.release(); this.wakeLock = null; }
                    
                    document.body.classList.remove('timer-is-active');
                },

                formatTime(sec) {
                    const m = Math.floor(sec / 60).toString().padStart(2, '0'); const s = (sec % 60).toString().padStart(2, '0');
                    return `${m}:${s}`;
                }
            }));
        });
    </script>
</x-filament-panels::page>
