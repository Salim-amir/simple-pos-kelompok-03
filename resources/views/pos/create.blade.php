@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
<h1 class="text-lg font-semibold mb-4">Transaksi Kasir</h1>

<div x-data="{
    cart: [],

    addToCart(id, name, price) {
        this.cart.push({ id, name, price });
    },

    removeFromCart(id) {
        this.cart = this.cart.filter(item => item.id !== id);
    },

    subtotal() {
        return this.cart.reduce((sum, item) => sum + item.price, 0);
    }
}">
<div class="grid grid-cols-3 gap-4">
    @foreach ($products as $product)
        <div
            class="border rounded-md p-3 cursor-pointer hover:bg-slate-50 transition"
            @click="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})"
        >
            <p class="font-medium">{{ $product->name }}</p>

            <p class="text-sm text-slate-500">
                Rp {{ number_format($product->price) }}
            </p>

            @if ($product->stock < 10)
                <span class="bg-amber-100 text-amber-700">
                    Stok Menipis
                </span>
            @endif
        </div>
    @endforeach
</div>

    <div class="mt-4 border-t pt-3">
        <h2 class="font-semibold mb-2">Keranjang Belanja</h2>
        
        <template x-for="item in cart" :key="item.id">
            <div class="flex justify-between items-center bg-white border rounded px-3 py-2 mb-2">
                <span x-text="item.name + ' - Rp ' + item.price"></span>
                <button 
                    @click="removeFromCart(item.id)" 
                    class="text-red-500 hover:text-red-700 font-bold px-2 py-1 text-sm rounded hover:bg-red-50 transition"
                >
                    ✕
                </button>
            </div>
        </template>

        <p class="font-semibold mt-4">
            Subtotal: Rp <span x-text="subtotal()"></span>
        </p>
    </div>
</div>
@endsection