@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 bg-white text-slate-800 focus:border-brand-500 focus:ring-brand-500 rounded-lg shadow-sm text-sm py-2 px-3 focus:outline-none transition duration-150']) }}>
