<header class="main-header">
    <div class="header-container">
        <div class="header-content">
            {{-- title can be plain text or HTML; title_class optional --}}
            <h1 class="{{ $title_class ?? '' }}">{{ $title ?? 'Falcon Teams' }}</h1>
            @if (!empty($subtitle))
                <p>{{ $subtitle }}</p>
            @endif
        </div>

        <div class="header-buttons">
            @guest
                <a href="{{ route('login') }}" class="header-btn login-btn">Login</a>
                <a href="{{ route('register') }}" class="header-btn register-btn">Register</a>
            @else
                @if (\Illuminate\Support\Facades\Route::currentRouteName() !== 'home')
                    <a href="{{ route('home') }}" class="header-btn login-btn">Home</a>
                @endif
                @if (Auth::user()->isCoach() && \Illuminate\Support\Facades\Route::currentRouteName() !== 'coach-dashboard')
                    <a href="{{ route('coach-dashboard') }}" class="header-btn dashboard-btn">Coach Dashboard</a>
                @endif
                @if (Auth::user()->isCoachAdmin() && \Illuminate\Support\Facades\Route::currentRouteName() !== 'admin.dashboard')
                    <a href="{{ route('admin.dashboard') }}" class="header-btn dashboard-btn">Admin Dashboard</a>
                @endif
                @if (\Illuminate\Support\Facades\Route::currentRouteName() !== 'user-profile')
                    <a href="{{ route('user-profile') }}" class="header-btn login-btn">Profile</a>
                @endif
                <button type="button" class="header-btn logout-btn" onclick="openLogoutModal()">Logout</button>
            @endguest
        </div>
    </div>
</header>

<!-- Logout Confirmation Modal -->
<div id="logout-modal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Confirm Logout</h2>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to log out?</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeLogoutModal()">Cancel</button>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="modal-btn modal-btn-confirm">Logout</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openLogoutModal() {
        document.getElementById('logout-modal').style.display = 'flex';
    }

    function closeLogoutModal() {
        document.getElementById('logout-modal').style.display = 'none';
    }

    // Close modal when clicking outside of it
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('logout-modal');
        if (modal) {
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeLogoutModal();
                }
            });
        }
    });
</script>
