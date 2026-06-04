<div class="grid gap-4 lg:grid-cols-3">
    @foreach ($stats as $stat)
        <div class="rounded-lg bg-white/80 p-4 shadow-sm ring-1 ring-slate-900/5 dark:bg-slate-950/80 dark:ring-slate-200/5">
            <div class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $stat['label'] }}</div>
            <div class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">{{ $stat['value'] }}</div>
        </div>
    @endforeach
</div>
