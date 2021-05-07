<div class="card shadow">
    <div class="card-body">
        <ul class="nav row g-2">
            <li class="nav-item bg-danger rounded">
                <a class="nav-link text-light" href="{{ route('admin.home') }}">
                    <i class="fas fa-home"></i>
                    Painel
                </a>
            </li>
            @if (Auth::user()->group == 'Organizador')
                <li class="nav-item bg-danger rounded">
                    <a class="nav-link text-light" href="{{ route('users.index') }}">
                        <i class="fas fa-user"></i>
                        Usuários
                    </a>
                </li>
                <li class="nav-item bg-danger rounded">
                    <a class="nav-link text-light" href="{{ route('events.index') }}">
                    <i class="fas fa-calendar-alt"></i>
                        Eventos
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>

