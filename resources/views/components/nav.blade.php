<nav class="bg-slate-900 text-white px-4 py-3 flex gap-4">
<div class="flex items-center gap-4">
<span class="font-semibold">Simple POS</span>
</div>
<div class="flex gap-4"> 
<a href="{{ route('pos.create') }}" class="hover:underline rounded-md px-2 py-2 transition-colors {{request()->routeIs('pos.create') ? 'bg-slate-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Kasir</a>
<a href="{{ route('transactions.index') }}" class="hover:underline rounded-md px-2 py-2 transition-colors {{request()->routeIs('transactions.index') ? 'bg-slate-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Transaksi</a>
</div>
</nav>