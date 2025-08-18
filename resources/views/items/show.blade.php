@extends('layouts.base')

@section('content')
  @if (session('status'))
    <div class="flash">{{ session('status') }}</div>
  @endif

  <style>
    
    .item-layout {
      display: grid;
      grid-template-columns: 1fr;          
      gap: 1rem;
    }
    @media (min-width: 900px) {            
      .item-layout {
        grid-template-columns: minmax(380px, 1fr) minmax(480px, 1fr);
        align-items: start;
      }
      .item-media {
        position: sticky;
        top: 1rem;                         
      }
    }

   
    .item-media { display:flex; flex-direction:column; gap:.75rem; }


    .hero {
      width: 70%;
      border: 1px solid #ddd;
      border-radius: 12px;
      overflow: hidden;
      background: #f7f7f7;
    }
    .hero img {
      display:block;
      width:100%;
      height:auto;
      object-fit: contain;
      background: #fff;
    }
    @media (min-width: 900px) {
      .hero img { max-height: 72vh; width: 100%; object-fit: contain; }
    }


    .thumb-grid {
      display: grid;
      grid-template-columns: repeat(6, 1fr);
      gap: .5rem;
      margin-top: .25rem;
    }
    .thumb {
      width: 100%;
      aspect-ratio: 1 / 1;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #e5e7eb;
      display: block;
      background: #fff;
      cursor: pointer;
      transition: transform .1s ease-in;
    }
    .thumb:hover { transform: scale(1.02); }


    h1.item-title {
      font-size: clamp(1.4rem, 2.2vw + 1rem, 2rem);
      font-weight: 700;
      margin: .25rem 0 .35rem;
    }
    .muted { color:#666; font-size:.9rem; }


    .status-pill {
      display:inline-flex; align-items:center; gap:.45rem;
      padding:.3rem .6rem; border-radius:999px; font-weight:600;
      border:1px solid #e5e7eb; background:#f9fafb;
    }
    .dot { width:.55rem; height:.55rem; border-radius:999px; display:inline-block; }
    .dot-green { background:#10b981; }
    .dot-red { background:#ef4444; }


    .grid-2 { display:grid; grid-template-columns: 1fr; gap:.6rem; }
    @media (min-width: 600px) { .grid-2 { grid-template-columns: 1fr 1fr; } }
    .control {
      padding:.55rem .65rem; border:1px solid #e5e7eb; border-radius:.45rem; background:#fff; width:100%;
    }
    .control:focus { outline:2px solid #b5cadf; outline-offset:0; }
    .btn-add {
      padding:.48rem .75rem; border:1px solid #10b981; background:#ecfdf5;
      color:#065f46; border-radius:.375rem; cursor:pointer;
    }
    .btn-add:hover { background:#d1fae5; }
    .btn-cancel {
      padding:.48rem .75rem; border:1px solid #dc2626; background:#fef2f2;
      color:#991b1b; border-radius:.375rem; text-decoration:none; display:inline-block;
    }
    .btn-cancel:hover { background:#fee2e2; }

 
    .btn-primary {
      background:#2563eb; border:1px solid #2563eb;
      color:white; font-weight:600;
      padding:.55rem 1rem; border-radius:.45rem;
      text-decoration:none; display:inline-block;
    }
    .btn-primary:hover { background:#1d4ed8; }

    .btn-danger {
      background:#dc2626; border:1px solid #dc2626;
      color:white; font-weight:600;
      padding:.55rem 1rem; border-radius:.45rem;
      text-decoration:none; display:inline-block;
    }
    .btn-danger:hover { background:#b91c1c; }

    .btn-secondary {
      background:#e5e7eb; border:1px solid #d1d5db;
      color:#111827; font-weight:500;
      padding:.55rem 1rem; border-radius:.45rem;
      text-decoration:none; display:inline-block;
    }
    .btn-secondary:hover { background:#d1d5db; }
  </style>

  <div class="card">
    <div class="item-layout">
      {{-- LEFT: details & actions --}}
      <div>
        <h1 class="item-title">{{ $item->title }}</h1>

        <div class="muted" style="margin-bottom:.5rem;">
          Categorie:
          @if($item->category)
            <a href="{{ route('categories.items', $item->category->id) }}">{{ $item->category->name }}</a>
          @else
            —
          @endif
          — eigenaar:
          <a href="{{ route('users.show', $item->user->id) }}">{{ $item->user->name }}</a>
        </div>

        <dl style="margin:.5rem 0;">
          <div><dt class="muted">Beschrijving</dt><dd>{{ $item->description ?? '—' }}</dd></div>
          <div><dt class="muted">Conditie</dt><dd>{{ $item->condition ?? '—' }}</dd></div>
          <div><dt class="muted">Locatie</dt><dd>{{ $item->location ?? '—' }}</dd></div>
          <div><dt class="muted">Prijs per dag</dt><dd>{{ $item->price_per_day ? '€ '.$item->price_per_day : '—' }}</dd></div>
          <div>
            <dt class="muted">Status</dt>
            <dd>
              @php $isAvailable = ($item->status ?? 'beschikbaar') === 'beschikbaar'; @endphp
              <span class="status-pill">
                <span class="dot {{ $isAvailable ? 'dot-green' : 'dot-red' }}"></span>
                {{ $isAvailable ? 'beschikbaar' : ($item->status ?? '—') }}
              </span>
            </dd>
          </div>
        </dl>

        @auth
          @if (auth()->id() === $item->user_id)
            {{-- Owner: edit/delete only --}}
            <div style="display:flex; gap:.5rem; margin-top:.75rem; flex-wrap:wrap;">
              <a class="btn-primary" href="{{ route('items.edit', $item->id) }}">Bewerken</a>

              <form method="POST" action="{{ route('items.destroy', $item->id) }}"
                    onsubmit="return confirm('Weet je zeker dat je dit item wilt archiveren?');">
                @csrf @method('DELETE')
                <button class="btn-danger" type="submit">Archiveren</button>
              </form>
            </div>

          @elseif (auth()->user()->is_admin)
            {{-- Admin: keep manage buttons AND allow requesting if available --}}
            <div style="display:flex; gap:.5rem; margin-top:.75rem; flex-wrap:wrap;">
              <a class="btn-primary" href="{{ route('items.edit', $item->id) }}">Bewerken</a>

              <form method="POST" action="{{ route('items.destroy', $item->id) }}"
                    onsubmit="return confirm('Weet je zeker dat je dit item wilt archiveren?');">
                @csrf @method('DELETE')
                <button class="btn-danger" type="submit">Archiveren</button>
              </form>
            </div>

            @if ($item->status === 'beschikbaar')
              <hr style="margin:1rem 0;">
              <h2 style="margin:0 0 .5rem;">Aanvragen</h2>
              <form method="POST" action="{{ route('loans.store') }}" class="grid-2" style="gap:.6rem;">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                <div>
                  <label for="start_date" class="muted">Van</label>
                  <input type="date" id="start_date" name="start_date" class="control" required>
                </div>
                <div>
                  <label for="due_date" class="muted">Tot en met</label>
                  <input type="date" id="due_date" name="due_date" class="control" required>
                </div>
                <div style="grid-column:1 / -1; display:flex; gap:.5rem; flex-wrap:wrap;">
                  <button class="btn-add" type="submit">Aanvraag versturen</button>
                </div>
              </form>
            @else
              <p class="muted" style="margin-top:.75rem;">Dit item is momenteel niet beschikbaar.</p>
            @endif

          @else
            {{-- Regular user (not owner): can request if available --}}
            @if ($item->status === 'beschikbaar')
              <hr style="margin:1rem 0;">
              <h2 style="margin:0 0 .5rem;">Aanvragen</h2>
              <form method="POST" action="{{ route('loans.store') }}" class="grid-2" style="gap:.6rem;">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                <div>
                  <label for="start_date" class="muted">Van</label>
                  <input type="date" id="start_date" name="start_date" class="control" required>
                </div>
                <div>
                  <label for="due_date" class="muted">Tot en met</label>
                  <input type="date" id="due_date" name="due_date" class="control" required>
                </div>
                <div style="grid-column:1 / -1; display:flex; gap:.5rem; flex-wrap:wrap;">
                  <button class="btn-add" type="submit">Aanvraag versturen</button>
                </div>
              </form>
            @else
              <p class="muted" style="margin-top:.75rem;">Dit item is momenteel niet beschikbaar.</p>
            @endif
          @endif
        @endauth
      </div>

      {{-- RIGHT: media --}}
      <aside class="item-media">
        @php $item->loadMissing('images'); @endphp

        @php
          $heroPath = null;
          if ($item->cover_image) {
            $heroPath = 'storage/'.$item->cover_image;
          } elseif ($item->images && $item->images->count()) {
            $heroPath = 'storage/'.$item->images->first()->path;
          }
        @endphp

        @if ($heroPath)
          <div class="muted" style="margin:.25rem 0 .35rem;">Hoofdafbeelding</div>
          <figure class="hero">
            <img id="heroImage"
                 src="{{ asset($heroPath) }}"
                 alt="Hoofdafbeelding van {{ $item->title }}"
                 loading="eager">
          </figure>
        @endif

        @if ($item->images && $item->images->count() > 0)
          <div class="muted" style="margin:.75rem 0 .25rem;">Meer foto’s</div>
          <div class="thumb-grid">
            @foreach ($item->images as $img)
              @php $src = asset('storage/'.$img->path); @endphp
              <img class="thumb"
                   src="{{ $src }}"
                   alt="Foto van {{ $item->title }}"
                   loading="lazy"
                   data-full="{{ $src }}">
            @endforeach
          </div>
        @endif
      </aside>
    </div>
  </div>

<script>

  document.addEventListener('DOMContentLoaded', function () {
    const hero = document.getElementById('heroImage');
    if (!hero) return;

    document.querySelectorAll('.thumb-grid .thumb').forEach(function (thumb) {
      thumb.addEventListener('click', function () {
        const full = this.getAttribute('data-full');
        if (!full) return;

    
        const currentHeroSrc = hero.src;
        hero.src = full;

   
        this.src = currentHeroSrc;
        this.setAttribute('data-full', currentHeroSrc);

        if (window.innerWidth < 900) {
          hero.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });
  });
</script>

@endsection
