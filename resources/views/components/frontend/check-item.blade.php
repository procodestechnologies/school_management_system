<li {{ $attributes->class(['flex items-start gap-2.5']) }}>
    <flux:icon icon="check-circle" class="mt-0.5 size-4 shrink-0 text-indigo-600 dark:text-indigo-400" />
    <span>{{ $slot }}</span>
</li>
