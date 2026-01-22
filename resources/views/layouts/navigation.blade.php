<nav x-data id="navigation" class="bg-white shadow md:static lg:w-64 lg:min-w-64 transform transition-transform duration-300 ease-in-out hidden md:hidden lg:block fixed top-0 left-0 w-full h-[100%] lg:h-auto z-50"
     :class="{ 'translate-x-0': $store.menu.open, '-translate-x-full': !$store.menu.open, 'md:translate-x-0': true }">

  <div :class="{ 'block': $store.menu.open, 'hidden': !$store.menu.open }" class="md:block px-4 py-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class=" text-2xl font-bold text-slate-700">Self Storage Bali</h2>
      <button id="close-menu" class="lg:hidden text-white active:bg-gray-500 hover:bg-gray-500 px-2 py-1 rounded-lg bg-gray-400"><i class="ph-bold ph-x"></i></button>
    </div>
      <ul class="space-y-2">
      <x-nav-link href="{{ route('dashboard.admin') }}" :active="request()->routeIs('dashboard.admin*')">
        <p class="mr-2"> <i class="ph ph-house"></i> Dashboard </p>
      </x-nav-link>

        @auth
          @if(Auth::user()->role === 'admin')
            <x-nav-link href="{{ route('data-storage.index') }}" :active="request()->routeIs('data-storage.*')">
              <p class="mr-2"> <i class="ph ph-cube"></i>Storage </p>
            </x-nav-link>
            <x-nav-link href="{{ route('data-booking.index') }}" :active="request()->routeIs('data-booking.*')">
              <p class="mr-2"> <i class="ph ph-user-list"></i>Booking </p>
            </x-nav-link>
            <x-nav-link href="{{ route('storage-management.index') }}" :active="request()->routeIs('storage-management.*')">
              <p class="mr-2"> <i class="ph ph-shipping-container"></i>Storage Management </p>
            </x-nav-link>
            <x-nav-link href="{{ route('data-customer.index') }}" :active="request()->routeIs('data-customer.*')">
              <p class="mr-2"> <i class="ph ph-user"></i>Customers </p>
            </x-nav-link>
            <x-nav-link href="{{ route('data-payment.index') }}" :active="request()->routeIs('data-payment.*')">
              <p class="mr-2"> <i class="ph ph-credit-card"></i>Payment </p>
            </x-nav-link>
            <x-nav-link href="{{ route('expenses.index') }}" :active="request()->routeIs('expenses.*')">
              <p class="mr-2"> <i class="ph ph-money"></i>Expenses </p>
            </x-nav-link>
            <x-nav-link href="{{ route('report.index') }}" :active="request()->routeIs('report.*')">
              <p class="mr-2"> <i class="ph ph-chart-line"></i>Reports </p>
            </x-nav-link>
          @endif
        @endauth


      </ul>
    </div>
</nav>



