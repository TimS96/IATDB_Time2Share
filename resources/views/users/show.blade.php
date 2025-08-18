@extends('layouts.base')

@section('content')
  <style>
    .stars { font-size: 1rem; line-height: 1; }
    .stars .on::before { content: '★'; }
    .stars .off::before { content: '☆'; }
    .card { background:#fff; border:1px solid #e5e7eb; border-radius:.5rem; padding:1rem; }
    .grid { display:grid; gap:1rem; grid-template-columns: 1fr; }
    @media (min-width: 900px) { .grid { grid-template-columns: 2fr 1fr; } }

    .items-list { list-style:none; padding:0; margin:0; }
    .items-list li { display:flex; align-items:center; gap:.5rem; margin:.4rem 0; }
    .item-thumb {
      width:60px; height:45px;
      object-fit:cover; border-radius:.35rem;
      border:1px solid #e5e7eb; background:#f9fafb;
      flex-shrink:0;
    }

    .btn-danger {
      display:inline-block;
      padding:.45rem .7rem;
      background:#fee2e2;
      color:#991b1b;
      border:1px solid #dc2626;
      border-radius:.375rem;
      text-decoration:none;
    }
    .btn-danger:hover { background:#fecaca; }
  </style>

  <h1 style="margin-bottom:.5rem;">Profiel</h1>
  <p class="muted" style="margin-top:0;">Publiek profiel van {{ $user->name }}</p>

  {{-- Admin-only block/unblock --}}
  @if(auth()->check() && (auth()->user()->is_admin ?? false))
    <form method="POST" action="{{ route('admin.users.toggleBlock', $user->id) }}" style="margin:.5rem 0 1rem;">
      @csrf
      <button type="submit" class="btn-danger">
        {{ $user->blocked_at ? 'Deblokkeer gebruiker' : 'Blokkeer gebruiker' }}
      </button>
    </form>
  @endif

  <div class="grid">
    {{-- LEFT: Items this user offers --}}
    <section>
      <div class="card">
        <h2 style="margin:0 0 .5rem;">Aangeboden spullen</h2>

        @if($items->count())
          <ul class="items-list">
            @foreach ($items as $item)
              @php
                $img = $item->cover_image
                      ? asset('storage/'.$item->cover_image)
                      : (($item->images && $item->images->count())
                            ? asset('storage/'.$item->images->first()->path)
                            : null);
              @endphp
              <li>
                @if($img)
                  <img src="{{ $img }}" alt="Afbeelding van {{ $item->title }}" class="item-thumb">
                @endif
                <div>
                  <a href="{{ route('items.show', $item->id) }}">
                    <strong>{{ $item->title }}</strong>
                  </a>
                  <div class="muted">{{ $item->status ?? 'onbekend' }}</div>
                </div>
              </li>
            @endforeach
          </ul>
        @else
          <p class="muted">Geen spullen gevonden.</p>
        @endif
      </div>
    </section>

    {{-- RIGHT: User summary + reviews --}}
    <aside>
      <div class="card" style="margin-bottom:1rem;">
        <h2 style="margin:0 0 .5rem;">Over {{ $user->name }}</h2>
        <div class="stars" aria-label="Gemiddelde beoordeling">
          @php
            $avg = (float)($avgRating ?? 0);
            $full = (int) floor($avg);
          @endphp
          @for ($i = 1; $i <= 5; $i++)
            @if ($i <= $full)
              <span class="on"></span>
            @else
              <span class="off"></span>
            @endif
          @endfor
          <span class="muted" style="margin-left:.25rem;">
            {{ $avgRating ? $avgRating.'/5' : 'nog geen beoordelingen' }}
          </span>
        </div>
      </div>

      <div class="card">
        <h2 style="margin:0 0 .5rem;">Beoordelingen</h2>
        @if($reviews->count())
          <ul>
            @foreach ($reviews as $review)
              <li style="margin:.5rem 0;">
                <div>
                  {{-- stars --}}
                  <span class="stars" aria-label="Score {{ $review->rating }} van 5">
                    @for ($i = 1; $i <= 5; $i++)
                      @if ($i <= $review->rating)
                        <span class="on"></span>
                      @else
                        <span class="off"></span>
                      @endif
                    @endfor
                  </span>
                  <span class="muted">— door {{ $review->reviewer->name ?? 'Onbekend' }}</span>
                </div>
                @if($review->body)
                  <div>{{ $review->body }}</div>
                @endif
                @if($review->loan && $review->loan->item)
                  <div class="muted">
                    Betrof: <a href="{{ route('items.show', $review->loan->item->id) }}">{{ $review->loan->item->title }}</a>
                  </div>
                @endif
              </li>
            @endforeach
          </ul>
        @else
          <p class="muted">Nog geen beoordelingen.</p>
        @endif
      </div>
    </aside>
  </div>
@endsection
