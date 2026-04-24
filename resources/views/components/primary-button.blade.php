<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-full border-0 bg-primary px-6 py-2.5 text-sm font-bold text-black shadow-none transition focus:outline-none focus:ring-2 focus:ring-primary/50 focus:ring-offset-2 focus:ring-offset-[#111827] enabled:hover:brightness-110 enabled:hover:shadow-[0_10px_20px_-5px_#fb2323] enabled:active:translate-y-px disabled:opacity-50']) }}>
    {{ $slot }}
</button>
