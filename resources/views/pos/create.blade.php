@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
<h1 class="text-lg font-semibold mb-4">Transaksi Kasir</h1>

@if (session('success'))
<div class="bg-green-50 text-green-700 p-3 rounded-md mb-4">
    {{ session('success') }}
</div>
@endif

@error('items')
<div class="bg-red-50 text-red-700 p-3 rounded-md mb-4">{{ $message }}</div>
@enderror

<form method="POST" action="{{ route('transactions.store') }}" x-data="{
    cart: [],
    addToCart(id, name, price) {
        let existingItem = this.cart.find(item => item.id === id);
        if (existingItem) {
            existingItem.qty++;
        } else {
            this.cart.push({ id: id, name: name, price: price, qty: 1 });
        }
    },
    subtotal() {
        return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    }
}">
    @csrf

    <div class="grid grid-cols-3 gap-4">
        @foreach ($products as $product)
        <div class="border rounded-md p-3 cursor-pointer select-none hover:bg-slate-50"
            @click="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">
            <p class="font-medium">{{ $product->name }}</p>
            <p class="text-sm text-slate-500">Rp {{ number_format($product->price) }}</p>
        </div>
        @endforeach
    </div>

    <div class="mt-4 border-t pt-3">
        <h2 class="font-semibold mb-2">Keranjang Belanja:</h2>
        
        <template x-for="(item, index) in cart" :key="item.id">
            <div class="flex justify-between items-center mb-2 border-b pb-1">
                <div>
                    <p class="font-medium" x-text="item.name"></p>
                    <p class="text-sm text-slate-500">
                        Rp <span x-text="item.price.toLocaleString()"></span> x <span x-text="item.qty"></span>
                    </p>
                </div>
                <div>
                    <p class="font-semibold">Rp <span x-text="(item.price * item.qty).toLocaleString()"></span></p>
                </div>

                <!-- Input tersembunyi yang dikirimkan ke StoreTransactionRequest -->
                <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.id">
                <input type="hidden" :name="'items[' + index + '][qty]'" :value="item.qty">
            </div>
        </template>

        <p class="font-semibold mt-4 text-lg">Subtotal: Rp <span x-text="subtotal().toLocaleString()"></span></p>

        <button type="submit" class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            Bayar
        </button>
    </div>
</form>
@endsection