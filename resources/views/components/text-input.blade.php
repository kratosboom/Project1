@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-xl border border-white/10 bg-black/40 px-4 py-2.5 text-white shadow-sm placeholder:text-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/50 disabled:cursor-not-allowed disabled:opacity-50']) }}>
