<nav class="bg-slate-900 text-white px-4 py-3 flex gap-4">
<div class="flex items-center gap-4">
<span class="font-semibold">Simple POS</span>
</div>
<div class="flex gap-4"> 
<a href="{{ route('pos.create') }}" class="hover:underline">Kasir</a>
<a href="{{ route('transactions.index') }}" class="hover:underline">Transaksi</a>
</div>
</nav>