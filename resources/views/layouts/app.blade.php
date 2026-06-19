<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NPCCAP')</title>
    <link rel="shortcut icon" href="{{ asset('icon/favicon-16x16.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('icon/favicon-32x32.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://kit.fontawesome.com/a81368914c.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="app-shell d-flex flex-column flex-lg-row min-vh-100">
        <aside class="app-sidebar offcanvas-lg offcanvas-start text-bg-dark" tabindex="-1" id="appSidebar"
            aria-labelledby="appSidebarLabel">
            <div class="offcanvas-header border-bottom border-secondary">
                <h5 class="offcanvas-title fw-bold" id="appSidebarLabel">Menu</h5>
                <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas"
                    aria-label="Fechar"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column flex-shrink-0 p-3 text-white bg-dark sidebar-frame">
                <a href="{{ route('certificates.index') }}"
                    class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo NPCCAP" class="brand-logo me-2">
                    <span class="fs-4">NPCCAP</span>
                </a>
                <hr>
                <ul class="nav nav-pills flex-column mb-auto gap-2">
                    <li class="nav-item">
                        <a href="{{ route('home') }}"
                            class="nav-link {{ request()->routeIs('home') ? 'active' : 'text-white' }}"
                            aria-current="page">
                            <i class="bi bi-house nav-icon"></i>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('courses.index') }}"
                            class="nav-link {{ request()->routeIs('courses.*') ? 'active' : 'text-white' }}">
                            <i class="bi bi-mortarboard"></i>
                            Cursos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('signature.index') }}"
                            class="nav-link {{ request()->routeIs('signature.*') ? 'active' : 'text-white' }}">
                            <i class="fa-solid fa-signature"></i>
                            <i class="bi bi-pen"></i>
                            Assinaturas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('certificates.index') }}"
                            class="nav-link {{ request()->routeIs('certificates.*') ? 'active' : 'text-white' }}">
                            <i class="bi bi-patch-check"></i>
                            Certificados
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('students.index') }}"
                            class="nav-link {{ request()->routeIs('students.*') ? 'active' : 'text-white' }}">
                            <i class="bi bi-people"></i>
                            Alunos
                        </a>
                    </li>
                </ul>
                <hr>
            </div>
        </aside>

        <div class="flex-grow-1 d-flex flex-column">
            <header class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary sticky-top">
                <div class="container-fluid px-3 px-lg-4 py-2 py-lg-0">
                    <button class="btn btn-outline-light d-lg-none me-2" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#appSidebar" aria-controls="appSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <span class="navbar-brand fw-semibold mb-0 d-block">@yield('page_title', 'NPCCAP')</span>
                            <span class="text-soft small d-none d-md-inline">Núcleo Pedagógico de Capacitação Continuada</span>
                        </div>
                    </div>
                    <div class="ms-auto d-flex align-items-center gap-2 gap-lg-3">
                        <span
                            class="badge text-bg-primary-subtle text-primary-emphasis border border-primary-subtle d-none d-md-inline">Institucional</span>

                        <div class="dropdown">
                            <a href="#"
                                class="d-flex align-items-center text-white text-decoration-none dropdown-toggle px-1 py-1"
                                id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="/icon/android-chrome-192x192.png" alt="mdo" width="32" height="32"
                                    class="rounded-circle me-0 me-sm-2">
                                <strong class="d-none d-sm-inline">Admin</strong>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark text-small shadow"
                                aria-labelledby="dropdownUser1">
                                <li><a class="dropdown-item" href="">Novo certificado...</a></li>
                                <li><a class="dropdown-item" href="#">Configurações</a></li>
                                <li><a class="dropdown-item" href="#">Perfil</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#">Sair</a></li>
                            </ul>
                        </div>

                    </div>
                </div>
            </header>

            <main class="app-content flex-grow-1 p-3 p-lg-4">
                <div class="container-fluid px-0">
                    @if (session('success'))
                    <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm">
                        <strong>Erros encontrados:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>