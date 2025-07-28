<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalji Ponude #') }}{{ $proposal->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <dl class="row mb-4">
                        <dt class="col-sm-4">Naslov:</dt>
                        <dd class="col-sm-8">{{ $proposal->title }}</dd>

                        <dt class="col-sm-4">Zahtjev za ponudu:</dt>
                        <dd class="col-sm-8"><a href="{{ route('quote-requests.show', $proposal->quoteRequest) }}">#{{ $proposal->quoteRequest->id }} ({{ $proposal->quoteRequest->contact_name }})</a></dd>

                        <dt class="col-sm-4">Projektant:</dt>
                        <dd class="col-sm-8">{{ $proposal->designer->name }}</dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8"><span class="badge bg-{{ $proposal->status === 'draft' ? 'secondary' : ($proposal->status === 'sent' ? 'primary' : ($proposal->status === 'accepted' ? 'success' : 'danger')) }}">{{ ucfirst($proposal->status) }}</span></dd>

                        <dt class="col-sm-4">Ukupna cijena:</dt>
                        <dd class="col-sm-8">{{ $proposal->total_price ? $proposal->total_price . ' ' . $proposal->currency : 'N/A' }}</dd>

                        <dt class="col-sm-4">Opis:</dt>
                        <dd class="col-sm-8">{{ $proposal->description ?? 'Nema opisa.' }}</dd>

                        <dt class="col-sm-4">Kreirano:</dt>
                        <dd class="col-sm-8">{{ $proposal->created_at->format('d.m.Y H:i') }}</dd>
                    </dl>

                    <h4 class="mb-3">Sastav ponude:</h4>
                    @if ($proposal->products->isEmpty())
                        <p>Ova ponuda ne sadrži proizvode.</p>
                    @else
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Proizvod</th>
                                    <th>Tip</th>
                                    <th>Količina</th>
                                    <th>Cijena po komadu (u trenutku ponude)</th>
                                    <th>Ukupno</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($proposal->products as $product)
                                    <tr>
                                        <td><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></td>
                                        <td>{{ ucfirst($product->type ?? 'N/A') }}</td>
                                        <td>{{ $product->pivot->quantity }}</td>
                                        <td>{{ $product->pivot->price_at_time_of_proposal ? $product->pivot->price_at_time_of_proposal . ' ' . $proposal->currency : 'N/A' }}</td>
                                        <td>{{ $product->pivot->price_at_time_of_proposal && $product->pivot->quantity ? ($product->pivot->price_at_time_of_proposal * $product->pivot->quantity) . ' ' . $proposal->currency : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('proposals.index') }}" class="btn btn-secondary">Nazad na listu ponuda</a>
                        <a href="{{ route('proposals.edit', $proposal) }}" class="btn btn-warning ms-2">Izmijeni ponudu</a>
                        <form action="{{ route('proposals.destroy', $proposal) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger ms-2" onclick="return confirm('Da li ste sigurni da želite da obrišete ovu ponudu?')">Obriši</button>
                        </form>

                        @auth
                            @if (Auth::id() === $proposal->quoteRequest->user_id && $proposal->status === 'sent')
                                <hr class="my-4">
                                <h5 class="mb-3">Akcije za ponudu:</h5>
                                <form action="{{ route('proposals.accept', $proposal) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-success me-2" onclick="return confirm('Da li ste sigurni da želite da prihvatite ovu ponudu?')">Prihvati ponudu</button>
                                </form>
                                <form action="{{ route('proposals.reject', $proposal) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Da li ste sigurni da želite da odbijete ovu ponudu?')">Odbij ponudu</button>
                                </form>
                            @elseif (Auth::id() === $proposal->quoteRequest->user_id && $proposal->status === 'accepted')
                                <hr class="my-4">
                                <div class="alert alert-success">Ovu ponudu ste prihvatili.</div>
                            @elseif (Auth::id() === $proposal->quoteRequest->user_id && $proposal->status === 'rejected')
                                <hr class="my-4">
                                <div class="alert alert-danger">Ovu ponudu ste odbili.</div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
