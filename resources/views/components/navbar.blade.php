<section>
  <nav id="navbar" class="flex items-center justify-between flex-wrap bg-transparent p-6 transition-all duration-300">
    <!-- Logo -->
    <div>
      <a href="{{ route('homepage') }}">
        <span class="text-[#F05E40] font-bold text-xl">
          Self Storage Bali
        </span>
      </a>
    </div>

    <!-- Hamburger Button (Mobile Only) -->
    <button id="menu-toggle" class="md:hidden text-orange-900 focus:outline-none">
      <i class="ph ph-list text-3xl"></i>
    </button>

    <!-- Menu List -->
   <div id="menu" class="w-full md:flex md:items-center md:space-x-4 md:w-auto hidden mt-4 md:mt-0">
      <ul class="flex flex-col md:flex-row md:space-x-4 space-y-2 md:space-y-0">
        <li><a href="/" class="text-orange-900 font-bold">Home</a></li>
        <li><a href="about" class="text-orange-900 font-bold">About</a></li>
        <li><a href="units-pricing" class="text-orange-900 font-bold">Units & Pricing</a></li>
        <li><a href="faq" class="text-orange-900 font-bold">FAQ</a></li>
        <li><a href="booking" class="text-orange-900 font-bold">Booking</a></li>
        <li><a href="contact" class="text-orange-900 font-bold">Contact</a></li>
        <li><a href="{{ route('auth.login') }}" class="text-orange-900 font-bold">Login</a></li>

      </ul>
    </div>
  </nav>
</section>

<!-- Phosphor Icons CDN -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>

<!-- Toggle Script & Scroll Behavior -->
<script>
  const navbar = document.getElementById('navbar');
  const menuToggle = document.getElementById('menu-toggle');
  const menu = document.getElementById('menu');

  // Scroll effect
  window.onscroll = function () {
    if (window.scrollY > 0) {
      navbar.classList.remove('bg-transparent');
      navbar.classList.add('bg-white', 'drop-shadow-md');
    } else {
      navbar.classList.remove('bg-white', 'drop-shadow-md');
      navbar.classList.add('bg-transparent');
    }
  };

  // Toggle menu
  menuToggle.addEventListener('click', () => {
    menu.classList.toggle('hidden');
  });
</script>
