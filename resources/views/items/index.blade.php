@extends('layouts.base')

@section('content')
  <h1 style="margin-bottom:.75rem;">Aangeboden items</h1>

  @if (session('status'))
    <div class="flash">{{ session('status') }}</div>
  @endif

  <form method="GET" action="{{ route('items.index') }}" class="card" style="margin-bottom:1rem; padding:1rem;">
  <style>
    .search-grid {
      display: grid;
      gap: .5rem;
      grid-template-columns: 1fr;
    }
    @media (min-width: 768px) {
      .search-grid {
        grid-template-columns: 2fr 1fr 1fr 1fr auto; 
      }
    }

    .search-grid input,
    .search-grid select {
      border: 1px solid #d1d5db;
      border-radius: 6px;
      padding: .5rem .75rem;
      font-size: .95rem;
      width: 100%;
    }

    .search-grid button {
      border: 1px solid #2563eb;
      background: #2563eb;
      color: white;
      font-weight: 600;
      padding: .5rem 1.2rem;
      border-radius: 6px;
      cursor: pointer;
      transition: background .15s ease;
      width: 100%;
    }
    .search-grid button:hover {
      background: #1d4ed8;
    }
  </style>

  <div class="search-grid">
    <input type="text" name="q" placeholder="Zoek titel/omschrijving" value="{{ request('q') }}">
    <input type="text" name="location" placeholder="Locatie" value="{{ request('location') }}">

    @php($categories = \App\Models\Category::all(['id','name']))
    <select name="category">
      <option value="">Alle categorieën</option>
      @foreach ($categories as $c)
        <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
      @endforeach
    </select>

    <select name="status">
      <option value="">Alle status</option>
      <option value="beschikbaar" @selected(request('status')=='beschikbaar')>Beschikbaar</option>
      <option value="uitgeleend" @selected(request('status')=='uitgeleend')>Uitgeleend</option>
      <option value="gearchiveerd" @selected(request('status')=='gearchiveerd')>Gearchiveerd</option>
    </select>

    <button type="submit">Zoeken</button>
  </div>
</form>


  <style>
    /* Fixed columns: 4 on desktop, then 3/2/1 on smaller screens */
    .cards-grid {
      display: grid;
      gap: 1rem;
      grid-template-columns: repeat(4, 1fr);
    }
    @media (max-width: 1200px) {
      .cards-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 900px) {
      .cards-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
      .cards-grid { grid-template-columns: 1fr; }
    }

    .card-item {
      display: block;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      overflow: hidden;
      text-decoration: none;
      color: inherit;
      background: #fff;
      transition: box-shadow .12s ease-in;
    }
    .card-item:hover { box-shadow: 0 2px 10px rgba(0,0,0,.06); }

    /* Square thumbnail area (looks good in a fixed grid) */
    .card-thumb {
      aspect-ratio: 3 / 3;
      background: #f7f7f7;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }
    .card-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }
    .thumb-placeholder {
      width: 100%;
      height: 100%;
      display:flex; align-items:center; justify-content:center;
      color:#9aa0a6; font-size:.9rem;
    }

    .card-info { padding: .6rem .75rem .8rem; }
    .card-title {
      margin: 0;
      font-size: 1rem;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .card-price {
      margin-top: .25rem;
      font-weight: 700;
    }
    .card-meta {
      margin-top: .15rem;
      color: #666;
      font-size: .85rem;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
      display:flex; align-items:center; gap:.35rem;
    }

    /* status dot */
    .status-dot {
      display:inline-block;
      width:10px; height:10px;
      border-radius:50%;
    }
    .status-dot.green { background:#16a34a; } /* Tailwind green-600 */
    .status-dot.red { background:#dc2626; }   /* Tailwind red-600 */
  </style>

    <div style="margin-bottom:1rem;">
    @auth
        <a class="btn"
        href="{{ route('items.create') }}"
        style="background:#16a34a; color:white; font-weight:600;
                padding:.6rem 1.2rem; border-radius:8px;
                display:inline-flex; align-items:center; gap:.5rem;
                text-decoration:none;">
        <span style="font-size:1.2rem; line-height:1;">＋</span>
        Nieuw item toevoegen
        </a>
    @endauth
    </div>


  @if ($items->count())
    <div class="cards-grid">
      @foreach ($items as $item)
        <a href="{{ route('items.show', $item->id) }}" class="card-item">
          <div class="card-thumb">
            {{-- Cover image > first gallery image > placeholder --}}
            @if ($item->cover_image)
              <img src="{{ asset('storage/'.$item->cover_image) }}" alt="Afbeelding van {{ $item->title }}">
            @elseif ($item->images && $item->images->count())
              <img src="{{ asset('storage/'.$item->images->first()->path) }}" alt="Afbeelding van {{ $item->title }}">
            @else
              <div class="thumb-placeholder">Geen foto</div>
            @endif
          </div>

          <div class="card-info">
            <h3 class="card-title">{{ $item->title }}</h3>

            <div class="card-price">
              @if (!is_null($item->price_per_day) && $item->price_per_day !== '')
                € {{ $item->price_per_day }} <span class="muted" style="font-weight:400;">/ dag</span>
              @else
                <span class="muted">Prijs onbekend</span>
              @endif
            </div>

            <div class="card-meta">
              {{ $item->location ?? 'Onbekende locatie' }}
              @if($item->status)
                — {{ $item->status }}
                @if ($item->status === 'beschikbaar')
                  <span class="status-dot green"></span>
                @else
                  <span class="status-dot red"></span>
                @endif
              @endif
            </div>
          </div>
        </a>
      @endforeach
    </div>

    <div style="margin-top:1rem;">
      {{ $items->withQueryString()->links() }}
    </div>
  @else
    <p class="muted">Geen items gevonden.</p>
  @endif
@endsection
