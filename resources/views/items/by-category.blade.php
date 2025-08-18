@extends('layouts.base')

@section('content')
  <h1 style="margin-bottom:.25rem;">Categorie: {{ $category->name }}</h1>
  <p class="muted" style="margin-top:0;">Alle items in deze categorie.</p>

  @if ($items->count())
    <div class="cards">
      @foreach ($items as $item)
        <article class="card">
          <h3 style="margin:0 0 .25rem;">
            <a href="{{ route('items.show', $item->id) }}">{{ $item->title }}</a>
          </h3>
          <div class="muted" style="margin-bottom:.5rem;">
            eigenaar: <a href="{{ route('users.show', $item->user->id) }}">{{ $item->user->name }}</a>
          </div>
          <div class="muted">
            {{ $item->location ?? 'Onbekende locatie' }} — status: <strong>{{ $item->status }}</strong>
          </div>
        </article>
      @endforeach
    </div>

    <div style="margin-top:1rem;">
      {{ $items->links() }}
    </div>
  @else
    <p class="muted">Geen items in deze categorie.</p>
  @endif
@endsection
