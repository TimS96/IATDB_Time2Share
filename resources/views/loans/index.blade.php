@extends('layouts.base')

@section('content')

  {{-- Star rating (unchanged) --}}
  <style>
    /* Simple, accessible star rating */
    .star-rating {
      display: inline-flex;
      flex-direction: row-reverse; /* easiest way to fill left-to-right */
      gap: .25rem;
      font-size: 1.35rem;
      line-height: 1;
    }
    .star-rating input {
      position: absolute;
      opacity: 0;
      pointer-events: none;
    }
    .star-rating label {
      cursor: pointer;
      user-select: none;
      transition: transform .05s ease-in-out;
    }
    .star-rating label::before { content: '☆'; }
    .star-rating input:checked ~ label::before { content: '★'; } /* fill selected and all lower stars */
    .star-rating label:hover::before,
    .star-rating label:hover ~ label::before,
    .star-rating input:focus ~ label::before { content: '★'; }   /* hover preview */
    .star-rating label:focus {
      outline: 2px solid #94a3b8; /* slate-ish */
      outline-offset: 2px;
    }
  </style>

  <h1 style="margin-bottom:1rem;">Mijn (uit)geleende spullen</h1>

  @if (session('status'))
    <div class="flash">{{ session('status') }}</div>
  @endif

  @php
    // Status buckets
    $activeStatuses  = ['aangevraagd', 'geaccepteerd', 'actief'];
    $historyStatuses = ['teruggebracht', 'geannuleerd', 'geweigerd'];

    $borrowedActive  = $geleendDoorMij->whereIn('status', $activeStatuses);
    $borrowedHistory = $geleendDoorMij->whereIn('status', $historyStatuses);

    $lentActive      = $uitgeleendDoorMij->whereIn('status', $activeStatuses);
    $lentHistory     = $uitgeleendDoorMij->whereIn('status', $historyStatuses);

    // Step 1 + 2 flags
    $promptReviewLoanId = session('prompt_review_loan_id');   // open form
    $highlightReviewId  = session('highlight_review_id');     // highlight created review
  @endphp

  <style>
    /* Layout for two columns */
    .loans-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1rem;
    }
    @media (min-width: 1000px) {
      .loans-grid { grid-template-columns: 1fr 1fr; }
    }

    /* Section headings */
    .col-title { margin-bottom:.5rem; font-weight:700; font-size:1.15rem; }
    .sub-title { margin:.25rem 0; font-weight:600; font-size:1rem; }

    /* Cards spacing inside columns */
    .stack > .card { margin-bottom:.6rem; }

    /* Card with thumbnail */
    .card-loan { display:flex; gap:.75rem; align-items:flex-start; }
    .loan-thumb {
      flex-shrink:0;
      width:96px; height:72px;          /* compact */
      border-radius:.35rem;
      object-fit:cover;
      border:1px solid #e5e7eb;
      background:#f9fafb;
    }
    .loan-body { flex:1; }

    /* Subtle highlight after creating a review */
    .pulse { animation: pulse-bg 1.5s ease-in-out 2; }
    @keyframes pulse-bg {
      0% { background-color:#fff; }
      50% { background-color:#fef3c7; }   /* amber-100 */
      100% { background-color:#fff; }
    }

    /* Tiny helpers */
    .muted { color:#666; }
    .btn { border:1px solid #e5e7eb; padding:.45rem .7rem; border-radius:.45rem; background:#f9fafb; }
    .btn:hover { filter:brightness(.98); }
  </style>

  <div class="loans-grid">
    {{-- LEFT COLUMN: items I borrowed --}}
    <section class="stack">
      <div class="col-title">Door mij geleend</div>

      <div class="sub-title">Actief</div>
      @if ($borrowedActive->count())
        @foreach ($borrowedActive as $loan)
          @php
            $img = $loan->item->cover_image
                  ? asset('storage/'.$loan->item->cover_image)
                  : (($loan->item->images && $loan->item->images->count())
                        ? asset('storage/'.$loan->item->images->first()->path)
                        : null);
          @endphp
          <div class="card card-loan">
            @if($img)
              <img src="{{ $img }}" class="loan-thumb" alt="Afbeelding van {{ $loan->item->title }}">
            @endif
            <div class="loan-body">
              <div>
                <strong>
                  <a href="{{ route('items.show', $loan->item->id) }}">
                    {{ $loan->item->title ?? 'Onbekend item' }}
                  </a>
                </strong>
                — eigenaar:
                <a href="{{ route('users.show', $loan->item->user->id) }}">
                  {{ $loan->item->user->name ?? 'Onbekend' }}
                </a>
              </div>

              <div class="muted" style="margin-top:.25rem;">
                status: <em>{{ $loan->status }}</em> — periode:
                {{ \Illuminate\Support\Carbon::parse($loan->start_date)->format('d-m-Y') }}
                t/m
                {{ \Illuminate\Support\Carbon::parse($loan->due_date)->format('d-m-Y') }}
              </div>

              {{-- Borrower may cancel while "aangevraagd" --}}
              @if ($loan->status === 'aangevraagd')
                <form method="POST" action="{{ route('loans.update', $loan->id) }}" style="margin-top:.5rem;">
                  @csrf @method('PATCH')
                  <input type="hidden" name="action" value="cancel">
                  <button class="btn" type="submit">Aanvraag annuleren</button>
                </form>
              @endif
            </div>
          </div>
        @endforeach
      @else
        <p class="muted">Je leent op dit moment niets.</p>
      @endif

      <div class="sub-title" style="margin-top:.75rem;">Historisch</div>
      @if ($borrowedHistory->count())
        @foreach ($borrowedHistory as $loan)
          @php
            $img = $loan->item->cover_image
                  ? asset('storage/'.$loan->item->cover_image)
                  : (($loan->item->images && $loan->item->images->count())
                        ? asset('storage/'.$loan->item->images->first()->path)
                        : null);
          @endphp
          <div class="card card-loan">
            @if($img)
              <img src="{{ $img }}" class="loan-thumb" alt="Afbeelding van {{ $loan->item->title }}">
            @endif
            <div class="loan-body">
              <div>
                <strong>
                  <a href="{{ route('items.show', $loan->item->id) }}">
                    {{ $loan->item->title ?? 'Onbekend item' }}
                  </a>
                </strong>
                — eigenaar:
                <a href="{{ route('users.show', $loan->item->user->id) }}">
                  {{ $loan->item->user->name ?? 'Onbekend' }}
                </a>
              </div>
              <div class="muted" style="margin-top:.25rem;">
                status: <em>{{ $loan->status }}</em> — periode:
                {{ \Illuminate\Support\Carbon::parse($loan->start_date)->format('d-m-Y') }}
                t/m
                {{ \Illuminate\Support\Carbon::parse($loan->due_date)->format('d-m-Y') }}
              </div>
            </div>
          </div>
        @endforeach
      @else
        <p class="muted">Geen historische leningen.</p>
      @endif
    </section>

    {{-- RIGHT COLUMN: items I lent out (I am the owner) --}}
    <section class="stack">
      <div class="col-title">Door mij uitgeleend</div>

      <div class="sub-title">Actief</div>
      @if ($lentActive->count())
        @foreach ($lentActive as $loan)
          @php
            $img = $loan->item->cover_image
                  ? asset('storage/'.$loan->item->cover_image)
                  : (($loan->item->images && $loan->item->images->count())
                        ? asset('storage/'.$loan->item->images->first()->path)
                        : null);
          @endphp
          <div class="card card-loan">
            @if($img)
              <img src="{{ $img }}" class="loan-thumb" alt="Afbeelding van {{ $loan->item->title }}">
            @endif
            <div class="loan-body">
              <div>
                <strong>
                  <a href="{{ route('items.show', $loan->item->id) }}">
                    {{ $loan->item->title ?? 'Onbekend item' }}
                  </a>
                </strong>
                — aanvrager:
                <a href="{{ route('users.show', $loan->borrower->id) }}">
                  {{ $loan->borrower->name ?? 'Onbekend' }}
                </a>
              </div>

              <div class="muted" style="margin-top:.25rem;">
                status: <em>{{ $loan->status }}</em> — periode:
                {{ \Illuminate\Support\Carbon::parse($loan->start_date)->format('d-m-Y') }}
                t/m
                {{ \Illuminate\Support\Carbon::parse($loan->due_date)->format('d-m-Y') }}
              </div>

              {{-- Owner actions --}}
              @if ($loan->status === 'aangevraagd')
                <form method="POST" action="{{ route('loans.update', $loan->id) }}" style="display:inline-block; margin-top:.5rem; margin-right:.25rem;">
                  @csrf @method('PATCH')
                  <input type="hidden" name="action" value="accept">
                  <button class="btn" type="submit">Accepteren</button>
                </form>

                <form method="POST" action="{{ route('loans.update', $loan->id) }}" style="display:inline-block; margin-top:.5rem;">
                  @csrf @method('PATCH')
                  <input type="hidden" name="action" value="reject">
                  <button class="btn" type="submit">Weigeren</button>
                </form>
              @elseif ($loan->status === 'geaccepteerd')
                <form method="POST" action="{{ route('loans.update', $loan->id) }}" style="margin-top:.5rem;">
                  @csrf @method('PATCH')
                  <input type="hidden" name="action" value="start">
                  <button class="btn" type="submit">Starten</button>
                </form>
              @elseif ($loan->status === 'actief')
                <form method="POST" action="{{ route('loans.update', $loan->id) }}" style="margin-top:.5rem;">
                  @csrf @method('PATCH')
                  <input type="hidden" name="action" value="returned">
                  <button class="btn" type="submit">Terug ontvangen</button>
                </form>
              @endif
            </div>
          </div>
        @endforeach
      @else
        <p class="muted">Je leent op dit moment aan niemand uit.</p>
      @endif

      <div class="sub-title" style="margin-top:.75rem;">Historisch</div>
      @if ($lentHistory->count())
        @foreach ($lentHistory as $loan)
          @php
            $img = $loan->item->cover_image
                  ? asset('storage/'.$loan->item->cover_image)
                  : (($loan->item->images && $loan->item->images->count())
                        ? asset('storage/'.$loan->item->images->first()->path)
                        : null);
          @endphp
          <div class="card card-loan" id="loan-{{ $loan->id }}">
            @if($img)
              <img src="{{ $img }}" class="loan-thumb" alt="Afbeelding van {{ $loan->item->title }}">
            @endif
            <div class="loan-body">
              <div>
                <strong>
                  <a href="{{ route('items.show', $loan->item->id) }}">
                    {{ $loan->item->title ?? 'Onbekend item' }}
                  </a>
                </strong>
                — aanvrager:
                <a href="{{ route('users.show', $loan->borrower->id) }}">
                  {{ $loan->borrower->name ?? 'Onbekend' }}
                </a>
              </div>
              <div class="muted" style="margin-top:.25rem;">
                status: <em>{{ $loan->status }}</em> — periode:
                {{ \Illuminate\Support\Carbon::parse($loan->start_date)->format('d-m-Y') }}
                t/m
                {{ \Illuminate\Support\Carbon::parse($loan->due_date)->format('d-m-Y') }}
              </div>

              {{-- Review section (only for returned loans) --}}
              @if ($loan->status === 'teruggebracht')
                @php $openNow = ($promptReviewLoanId == $loan->id); @endphp

                @if ($loan->review)
                  <div class="card {{ ($highlightReviewId && $loan->review->id == $highlightReviewId) ? 'pulse' : '' }}"
                       id="review-{{ $loan->review->id }}"
                       style="margin-top:.6rem;">
                    <strong>Review</strong><br>
                    <span>Score: {{ $loan->review->rating }}/5</span><br>
                    <span>{{ $loan->review->body ?? '—' }}</span>

                    <form method="POST" action="{{ route('reviews.update', $loan->review->id) }}" style="margin-top:.5rem;">
                      @csrf @method('PATCH')
                      <label class="muted" style="display:block; margin-top:.5rem;">Score</label>
                      <div class="star-rating" role="radiogroup" aria-label="Wijzig score">
                        @for ($i = 5; $i >= 1; $i--)
                          <input
                            type="radio"
                            id="edit-{{ $loan->review->id }}-star{{ $i }}"
                            name="rating"
                            value="{{ $i }}"
                            {{ $loan->review->rating == $i ? 'checked' : '' }}
                            required
                          >
                          <label for="edit-{{ $loan->review->id }}-star{{ $i }}" aria-label="{{ $i }} sterren"></label>
                        @endfor
                      </div>

                      <label>Opmerking</label>
                      <input type="text" name="body" value="{{ $loan->review->body }}">
                      <button class="btn" type="submit">Bijwerken</button>
                    </form>

                    <form method="POST" action="{{ route('reviews.destroy', $loan->review->id) }}" style="display:inline;">
                      @csrf @method('DELETE')
                      <button class="btn" type="submit">Verwijderen</button>
                    </form>
                  </div>

                @else
                  <div class="card" id="review-form-{{ $loan->id }}" style="margin-top:.6rem;">
                    <strong>Review</strong>
                    @if($openNow)
                      <p class="muted" style="margin:.25rem 0 0;">Bedankt, je kunt nu direct een review plaatsen.</p>
                    @endif

                    <form method="POST" action="{{ route('reviews.store') }}" style="margin-top:.5rem;">
                      @csrf
                      <input type="hidden" name="loan_id" value="{{ $loan->id }}">
                      <label class="muted" style="display:block; margin-top:.25rem;">Score</label>
                      <div class="star-rating" role="radiogroup" aria-label="Geef een score">
                        @for ($i = 5; $i >= 1; $i--)
                          <input
                            type="radio"
                            id="new-{{ $loan->id }}-star{{ $i }}"
                            name="rating"
                            value="{{ $i }}"
                            required
                          >
                          <label for="new-{{ $loan->id }}-star{{ $i }}" aria-label="{{ $i }} sterren"></label>
                        @endfor
                      </div>
                      <label>Opmerking (optioneel)</label>
                      <input type="text" name="body" placeholder="Schrijf een opmerking">
                      <button class="btn" type="submit">Review plaatsen</button>
                    </form>
                  </div>

                  @if($openNow)
                    <script>location.hash = '#review-form-{{ $loan->id }}';</script>
                  @endif
                @endif
              @endif
            </div>
          </div>
        @endforeach
      @else
        <p class="muted">Geen historische uitleningen.</p>
      @endif
    </section>
  </div>

  {{-- Scroll to highlighted review after create --}}
  @if($highlightReviewId)
    <script>
      const el = document.getElementById('review-{{ $highlightReviewId }}');
      if (el) { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
    </script>
  @endif
@endsection
