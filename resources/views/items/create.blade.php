@extends('layouts.base')

@section('content')
  <style>
    .form-wrap { display:grid; gap:1rem; }
    .section-title { font-weight:600; margin:.25rem 0 .25rem; }
    .form-grid { display:grid; grid-template-columns: 1fr; gap:.75rem; }
    @media (min-width: 900px) { .form-grid { grid-template-columns: repeat(2, minmax(0,1fr)); gap:1rem; } }

    .field { display:flex; flex-direction:column; gap:.35rem; }
    .field label { color: var(--muted); font-size:.95rem; }
    .control {
      padding:.55rem .65rem; border:1px solid var(--border); border-radius:.45rem;
      background:#fff; width:100%;
    }
    .control:focus { outline:2px solid #b5cadf; outline-offset:0; }
    textarea.control { min-height:110px; resize:vertical; }

    .inline { display:flex; gap:.5rem; align-items:center; flex-wrap:wrap; }
    .muted-xs { color: var(--muted); font-size:.9rem; }

    .actions { display:flex; gap:.5rem; flex-wrap:wrap; }
    .btn-primary { background:#1d4ed8; color:#fff; border:1px solid #1d4ed8; }
    .btn-primary:hover { filter:brightness(0.95); }
    .btn-secondary { background:#e5e7eb; color:#111827; }

    .btn-add {
      padding:.45rem .6rem; border:1px solid #10b981; background:#ecfdf5;
      color:#065f46; border-radius:.375rem; cursor:pointer;
    }
    .btn-add:hover { background:#d1fae5; }

      .btn-cancel {
        padding:.45rem .6rem; border:1px solid #dc2626; background:#fef2f2;
        color:#991b1b; border-radius:.375rem; cursor:pointer; text-decoration:none;
        display:inline-block;
      }
      .btn-cancel:hover { background:#fee2e2; }

  </style>

  <h1 style="margin-bottom:.75rem;">Nieuw item</h1>

  @if ($errors->any())
    <div class="flash" style="background:#fee2e2; border-color:#fecaca;">
      <strong>Er ging iets mis:</strong>
      <ul style="margin:.25rem 0 0 .9rem;">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if (session('status'))
    <div class="flash">{{ session('status') }}</div>
  @endif

  <form method="POST"
        action="{{ route('items.store') }}"
        enctype="multipart/form-data"
        class="card form-wrap">
    @csrf

    {{-- Basisgegevens --}}
    <div>
      <div class="section-title">Basisgegevens</div>
      <div class="form-grid">
        <div class="field">
          <label for="title">Titel</label>
          <input id="title" type="text" name="title" value="{{ old('title') }}" class="control" required>
        </div>

        <div class="field">
          <label for="condition">Conditie</label>
          <input id="condition" type="text" name="condition" value="{{ old('condition') }}" class="control" placeholder="bijv. gebruikt">
        </div>

        <div class="field" style="grid-column:1 / -1;">
          <label for="description">Omschrijving</label>
          <textarea id="description" name="description" class="control" rows="4">{{ old('description') }}</textarea>
        </div>

        <div class="field">
          <label for="location">Locatie</label>
          <input id="location" type="text" name="location" value="{{ old('location') }}" class="control">
        </div>

        <div class="field">
          <label for="price_per_day">Prijs per dag (€)</label>
          <input id="price_per_day" type="text" name="price_per_day" value="{{ old('price_per_day') }}" class="control">
        </div>

        <div class="field">
          <label for="status">Status</label>
          @php $st = old('status', 'beschikbaar'); @endphp
          <select id="status" name="status" class="control" required>
            <option value="beschikbaar" @selected($st==='beschikbaar')>Beschikbaar</option>
            <option value="uitgeleend" @selected($st==='uitgeleend')>Uitgeleend</option>
            <option value="gearchiveerd" @selected($st==='gearchiveerd')>Gearchiveerd</option>
          </select>
        </div>

        <div class="field">
          <label for="category_id">Categorie</label>
          <select id="category_id" name="category_id" class="control" required>
            <option value="">Kies een categorie</option>
            @foreach (\App\Models\Category::orderBy('name')->get(['id','name']) as $c)
              <option value="{{ $c->id }}" @selected(old('category_id')==$c->id)>{{ $c->name }}</option>
            @endforeach
          </select>

          {{-- Admin-only inline category add (no nested form; posts to admin route) --}}
          @if(auth()->check() && (auth()->user()->is_admin ?? false))
            <div class="inline" style="margin-top:.5rem;">
              <input type="text"
                     name="name"
                     placeholder="Nieuwe categorie..."
                     required
                     class="control"
                     style="max-width: 260px;"
                     form="catCreateForm">
              <button type="submit"
                      class="btn-add"
                      form="catCreateForm"
                      formmethod="POST"
                      formaction="{{ route('admin.categories.store') }}"
                      formnovalidate>
                Toevoegen
              </button>
            </div>
            <div class="muted-xs">Alleen admins kunnen categorieën toevoegen.</div>
          @endif
        </div>
      </div>
    </div>

    {{-- Afbeeldingen --}}
    <div>
      <div class="section-title">Afbeeldingen</div>
      <div class="form-grid">
        <div class="field">
          <label for="cover_image">Hoofdafbeelding</label>
          <input id="cover_image" type="file" name="cover_image" accept="image/*" class="control">
          <div class="muted-xs">JPG/PNG, bij voorkeur liggend formaat.</div>
        </div>

        <div class="field">
          <label for="images">Extra afbeeldingen</label>
          <input id="images" type="file" name="images[]" accept="image/*" multiple class="control">
          <div class="muted-xs">Je kunt meerdere afbeeldingen selecteren.</div>
        </div>
      </div>
    </div>

    {{-- Acties --}}
    <div class="actions" style="margin-top:.25rem;">
      <button class="btn-add" type="submit">Nieuw item toevoegen</button>
      <a href="{{ route('items.index') }}" class="btn-cancel">Annuleren</a>
    </div>
    </form>


  @if(auth()->check() && (auth()->user()->is_admin ?? false))
    <form id="catCreateForm" action="{{ route('admin.categories.store') }}" method="POST" style="display:none;">
      @csrf
      {{-- Input with name="name" is associated via form="catCreateForm" above --}}
    </form>
  @endif
@endsection
