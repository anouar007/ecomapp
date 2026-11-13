<div class="subnav-pills-bar">
    @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('manage_users')))
    <a href="{{ route('users.index') }}" class="subnav-pill {{ request()->routeIs('users.*') ? 'active' : '' }}">
        <i class="fas fa-users-gear text-primary"></i>
        <span>Équipe, Collaborateurs & Rôles</span>
    </a>
    @endif
    @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('manage_roles')))
    <a href="{{ route('roles.index') }}" class="subnav-pill {{ request()->routeIs('roles.*') ? 'active' : '' }}">
        <i class="fas fa-user-shield text-danger"></i>
        <span>Rôles & Profils d'Accès</span>
    </a>
    <a href="{{ route('permissions.index') }}" class="subnav-pill {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
        <i class="fas fa-key text-warning"></i>
        <span>Permissions Système</span>
    </a>
    @endif
    @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('view_activity_logs')))
    <a href="{{ route('activity-logs.index') }}" class="subnav-pill {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
        <i class="fas fa-history text-info"></i>
        <span>Journal d'Activité & Traçabilité</span>
    </a>
    @endif
    @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('manage_settings')))
    <a href="{{ route('settings.index') }}" class="subnav-pill {{ request()->routeIs('settings.*') ? 'active' : '' }}">
        <i class="fas fa-cog text-secondary"></i>
        <span>Paramètres Système</span>
    </a>
    @endif
</div>

