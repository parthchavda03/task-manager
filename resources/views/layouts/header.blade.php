<!-- Header Navigation -->
<header class="glass-header py-3 mb-5">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('tasks.index') }}" class="d-flex align-items-center text-decoration-none">
            <div class="mr-3" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);">
                <i class="fa-solid fa-list-check text-white" style="font-size: 1.2rem;"></i>
            </div>
            <div>
                <h4 class="m-0 font-weight-bold tracking-tight text-white">TaskSpace</h4>
                <span style="font-size: 0.75rem; color: var(--text-secondary); letter-spacing: 0.1em; text-transform: uppercase;">Workspace</span>
            </div>
        </a>
        <div class="d-flex align-items-center">
            <div class="d-none d-md-flex flex-column text-right mr-3">
                <span style="font-size: 0.85rem; font-weight: 600; color: white;">{{ auth()->user()->name }}</span>
                <span style="font-size: 0.75rem; color: var(--text-secondary);">{{ auth()->user()->email }}</span>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 42px; height: 42px; background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.25);">
                <i class="fa-regular fa-user text-indigo" style="color: #818cf8;"></i>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn-glass-secondary py-2 px-3" style="font-size: 0.8rem; border-color: rgba(239, 68, 68, 0.25); color: #ef4444; transition: all 0.2s ease;" title="Log Out">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</header>
