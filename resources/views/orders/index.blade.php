<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Orders') }}</h2>
    </x-slot>

    <div class="container mt-4">

        <div class="row">
            <div class="col-8 mx-auto">

                <div class="card shadow-sm">
                    <div class="table-responsive">

                        <table class="table table-hover table-condensed mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($orders as $order)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>#{{ $order->order_id }}</td>
                                        <td>Rp {{ number_format($order->total_price, 2, ',', '.') }}</td>
                                        <td>{{ $order->status }}</td>
                                        <td>
                                            <a href="{{ route('orders.show', $order->id) }}" 
                                               class="btn btn-primary btn-sm">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3">
                                            Tidak ada data
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>

                    </div>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
