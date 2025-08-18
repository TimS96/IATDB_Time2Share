<!DOCTYPE html>
<html lang="nl">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="utf-8">
    <title>{{ $title ?? 'Time2Share' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        :root {
            --muted:#6b7280; --border:#e5e7eb; --bg:#f3f4f6; --text:#111827;
        }

        * { box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin:0; color:var(--text); }
        a { color: inherit; }
        img { max-width: 100%; height: auto; display: block; }

        .container { width: 100%; margin: 0 auto; padding: 0 1rem; max-width: 100%; }
        @media (min-width: 640px) { .container { max-width: 640px; } }
        @media (min-width: 768px) { .container { max-width: 768px; } }
        @media (min-width: 1024px){ .container { max-width: 1024px; } }
        @media (min-width: 1280px){ .container { max-width: 1200px; } }

        header { background: var(--bg); border-bottom: 1px solid var(--border); }
        .topbar { display:flex; align-items:center; gap:.75rem; height:56px; }
        .brand { text-decoration:none; font-weight:700; }
        .nav { display:flex; align-items:center; gap:1rem; }

        .nav a {
            text-decoration:none;
            padding:.4rem .6rem;
            border-radius:.375rem;
            color:#374151;
            transition: background-color .15s ease, color .15s ease;
        }
        .nav a:hover,
        .nav a:focus-visible {
            background:#b5cadf;
            color:#111827;
            outline: none;
        }
        .nav a.is-active {
            background:#b5cadf;
            color:#111827;
        }

        .spacer { flex:1; }
        .auth { display:flex; align-items:center; gap:.75rem; }

        .hamburger { display:inline-flex; background:#fff; border:1px solid var(--border); padding:.35rem .5rem; border-radius:.375rem; cursor:pointer; }
        .mobile-panel {
            display:none; border-top:1px solid var(--border); background:#fff;
        }
        .mobile-links { display:flex; flex-direction:column; gap:.75rem; padding: .75rem 0; }
        .mobile-links a {
            text-decoration:none;
            padding:.5rem .75rem;
            border-radius:.375rem;
            color:#374151;
            transition: background-color .15s ease, color .15s ease;
        }
        .mobile-links a:hover,
        .mobile-links a:focus-visible {
            background:#b5cadf;
            color:#111827;
            outline:none;
        }
        .mobile-auth { display:flex; align-items:center; gap:.75rem; padding-bottom: .75rem; border-top:1px solid var(--border); margin-top:.75rem; padding-top:.75rem; }

        @media (min-width: 768px) {
            .hamburger, .mobile-panel { display:none !important; }
        }
        @media (max-width: 767.98px) {
            .nav-desktop, .auth-desktop { display:none; }
        }

        .btn { padding:.4rem .65rem; border:1px solid var(--border); background:#fff; border-radius:.375rem; cursor:pointer; }
        .btn:hover { background:#f9fafb; }
        .flash { background:#ecfdf5; border:1px solid #10b981; color:#065f46; padding:.5rem .75rem; border-radius:.375rem; margin:.75rem 0; }
        .muted { color: var(--muted); }

        .grid-2 { display:grid; grid-template-columns: 1fr; gap: 1rem; }
        @media (min-width: 900px) { .grid-2 { grid-template-columns: repeat(2, minmax(0,1fr)); gap: 2rem; } }

        .card { padding:.75rem; border:1px solid var(--border); border-radius:.5rem; background:#fff; }
        main { padding: 1rem 0 2rem; }
    </style>
</head>
<body>
<header>
    <div class="container">
        <div class="topbar">
            <a class="brand" href="{{ url('/') }}">Time2Share</a>

            <nav class="nav nav-desktop">
                <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.*') ? 'is-active' : '' }}">Items</a>
                <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'is-active' : '' }}">Categorieën</a>
                <a href="{{ route('loans.index') }}" class="{{ request()->routeIs('loans.*') ? 'is-active' : '' }}">Mijn (uit)geleende spullen</a>
            </nav>

            <div class="spacer"></div>

            <div class="auth auth-desktop">
                @auth
                    <span class="muted">Hallo, {{ auth()->user()->name }}</span>
                    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'is-active' : '' }}">Profiel</a>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button class="btn" type="submit">Log uit</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'is-active' : '' }}">Log in</a>
                    <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'is-active' : '' }}">Registreren</a>
                @endauth
            </div>

            <button class="hamburger" id="navToggle" aria-label="menu">
                ☰
            </button>
        </div>

        <div class="mobile-panel" id="mobilePanel">
            <div class="mobile-links container">
                <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.*') ? 'is-active' : '' }}">Items</a>
                <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'is-active' : '' }}">Categorieën</a>
                <a href="{{ route('loans.index') }}" class="{{ request()->routeIs('loans.*') ? 'is-active' : '' }}">Mijn (uit)geleende spullen</a>

                <div class="mobile-auth">
                    @auth
                        <span class="muted">Hallo, {{ auth()->user()->name }}</span>
                        <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'is-active' : '' }}">Profiel</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn" type="submit">Log uit</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'is-active' : '' }}">Log in</a>
                        <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'is-active' : '' }}">Registreren</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</header>

<main class="container">
    {{ $slot }}
</main>

<script>
  const btn = document.getElementById('navToggle');
  const panel = document.getElementById('mobilePanel');
  if (btn && panel) {
    btn.addEventListener('click', () => {
      panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
    });
  }
</script>
</body>
</html>
