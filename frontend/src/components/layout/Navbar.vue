<template>
  <header class="fixed top-0 inset-x-0 z-50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 shadow-xs transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <nav class="flex items-center justify-between h-16 lg:h-20 w-full" aria-label="Main navigation">
        
        <!-- ── 1. Left Section: Logo + Left-Aligned Navigation Links (Explore, Agents) ── -->
        <div class="flex items-center gap-6 xl:gap-8">
          <!-- Logo (Acts as Home) -->
          <RouterLink
            to="/"
            class="flex items-center gap-2.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 rounded-xl group flex-shrink-0"
            aria-label="BetLink home"
          >
            <div class="w-9 h-9 bg-slate-900 dark:bg-white rounded-xl flex items-center justify-center shadow-md shadow-slate-900/15 flex-shrink-0 group-hover:scale-105 transition-transform">
              <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 text-white dark:text-slate-900" aria-hidden="true">
                <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                <path d="M9 21V12h6v9" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              </svg>
            </div>
            <span class="text-xl font-black tracking-tight text-slate-900 dark:text-white whitespace-nowrap">
              BetLink
            </span>
          </RouterLink>

          <!-- Navigation Links grouped immediately next to the Logo -->
          <div class="hidden lg:flex items-center gap-1 xl:gap-1.5">
            <!-- Combined Listings / Explore Dropdown -->
            <div 
              class="relative" 
              ref="listingsDropdownRef"
              @mouseenter="isListingsHovered = true"
              @mouseleave="isListingsHovered = false"
            >
              <button
                type="button"
                @click="isListingsOpen = !isListingsOpen"
                class="px-3.5 py-2 rounded-xl text-xs xl:text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors duration-150 flex items-center gap-1.5 focus:outline-none"
                :class="{ 'text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 font-bold': isListingsActive }"
                aria-haspopup="true"
                :aria-expanded="isListingsOpen || isListingsHovered"
              >
                <span>{{ t('nav.listings', 'Explore') }}</span>
                <svg 
                  class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" 
                  :class="{ 'rotate-180 text-emerald-600': isListingsOpen || isListingsHovered }" 
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- Dropdown Menu -->
              <transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="opacity-0 translate-y-1 scale-95"
                enter-to-class="opacity-100 translate-y-0 scale-100"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100 translate-y-0 scale-100"
                leave-to-class="opacity-0 translate-y-1 scale-95"
              >
                <div
                  v-if="isListingsOpen || isListingsHovered"
                  class="absolute left-0 mt-1.5 w-40 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 p-1.5 z-50 overflow-hidden"
                  @click="isListingsOpen = false; isListingsHovered = false"
                >
                  <RouterLink
                    to="/properties"
                    class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors"
                    :class="{ 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white': route.path === '/properties' && !route.query.listing_type }"
                  >
                    {{ t('nav.properties', 'Properties') }}
                  </RouterLink>

                  <RouterLink
                    to="/properties?listing_type=rent"
                    class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors"
                    :class="{ 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white': route.query.listing_type === 'rent' }"
                  >
                    {{ t('nav.rent', 'Rent') }}
                  </RouterLink>

                  <RouterLink
                    to="/properties?listing_type=short_rent"
                    class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors"
                    :class="{ 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white': route.query.listing_type === 'short_rent' }"
                  >
                    {{ t('nav.stays', 'Stays') }}
                  </RouterLink>

                  <RouterLink
                    to="/locations"
                    class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors"
                    :class="{ 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white': route.path === '/locations' }"
                  >
                    {{ t('nav.locations', 'Locations') }}
                  </RouterLink>
                </div>
              </transition>
            </div>

            <!-- Agents Link -->
            <RouterLink
              to="/agents"
              class="px-3.5 py-2 rounded-xl text-xs xl:text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors duration-150 whitespace-nowrap flex items-center"
              :class="{ 'text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 font-bold': isRouteActive('/agents') }"
            >
              {{ t('nav.agents', 'Agents') }}
            </RouterLink>
          </div>
        </div>

        <!-- ── 2. Right Section: Utility Actions (Theme, Language, Favorites, List Property, Sign In/Register) ── -->
        <div class="hidden lg:flex items-center justify-end gap-2 xl:gap-2.5 flex-shrink-0">
          
          <!-- Theme Toggle -->
          <button 
            @click="toggleTheme" 
            class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex-shrink-0"
            :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
            aria-label="Toggle theme mode"
          >
            <svg v-if="isDark" class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg v-else class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
          </button>

          <!-- Multilingual Language Selector -->
          <LanguageSwitcher class="flex-shrink-0" />

          <!-- Favorites / Wishlist -->
          <button
            v-if="!authStore.isAuthenticated"
            type="button"
            @click="handleFavoritesClick"
            class="relative p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:text-rose-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex-shrink-0 cursor-pointer"
            aria-label="Favorites"
            title="Saved Favorites"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
          </button>
          <RouterLink
            v-else
            to="/buyer/favorites"
            class="relative p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:text-rose-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex-shrink-0"
            aria-label="Favorites"
            title="Saved Favorites"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
          </RouterLink>

          <!-- Subtle Divider separating Utilities from Action CTAs -->
          <div class="h-5 w-px bg-slate-200 dark:bg-slate-800 mx-0.5" aria-hidden="true" />

          <!-- Dual-Role Persona Switcher (Non-Admin authenticated users) -->
          <button
            v-if="authStore.isAuthenticated && isRegularUser"
            type="button"
            @click="togglePersona"
            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 border border-emerald-200/60 dark:border-emerald-800/40 transition-colors whitespace-nowrap flex-shrink-0 cursor-pointer"
            :title="isOwnerSection ? t('nav.switch_to_buyer') : t('nav.switch_to_owner')"
          >
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            <span>{{ isOwnerSection ? t('nav.buyer_dashboard', 'Buyer Mode') : t('nav.owner_dashboard', 'Owner Mode') }}</span>
          </button>

          <!-- List Property CTA Link -->
          <RouterLink
            :to="listPropertyTarget"
            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-xl text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors whitespace-nowrap flex-shrink-0"
            :class="{ 'ring-2 ring-slate-900/10 dark:ring-white/20': route.path.startsWith('/owner/properties/create') }"
          >
            <span>+ {{ t('nav.list_property', 'List Property') }}</span>
          </RouterLink>

          <!-- Authenticated Profile Dropdown OR Login/Register -->
          <div v-if="authStore.isAuthenticated" class="relative flex-shrink-0" ref="dropdownRef">
            <button
              type="button"
              class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none cursor-pointer"
              @click="profileMenuOpen = !profileMenuOpen"
            >
              <div v-if="authStore.userAvatar && !avatarLoadError" class="w-8 h-8 rounded-full overflow-hidden border border-slate-200 dark:border-slate-700 shadow-xs shrink-0">
                <img
                  :src="authStore.userAvatar"
                  alt="User avatar"
                  loading="eager"
                  decoding="async"
                  @error="avatarLoadError = true"
                  class="w-full h-full object-cover"
                />
              </div>
              <div v-else class="w-8 h-8 rounded-full bg-slate-900 dark:bg-slate-800 text-white font-bold text-xs flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-xs shrink-0">
                {{ (authStore.userName || 'U').charAt(0).toUpperCase() }}
              </div>
              <span class="text-xs font-bold text-slate-800 dark:text-slate-200 max-w-[110px] truncate">
                {{ authStore.userName }}
              </span>
              <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Profile Dropdown Menu -->
            <div
              v-if="profileMenuOpen"
              class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl py-1.5 z-50"
              @click="profileMenuOpen = false"
            >
              <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-800">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">{{ t('nav.signed_in_as', 'Signed in as') }}</p>
                <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ authStore.user?.email }}</p>
                <span class="inline-block mt-1 text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                  {{ authStore.primaryRole }}
                </span>
              </div>

              <!-- Persona Switcher -->
              <button
                v-if="isRegularUser"
                type="button"
                class="w-full flex items-center justify-between px-4 py-2.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors text-left border-b border-slate-100 dark:border-slate-800 cursor-pointer"
                @click="togglePersona"
              >
                <span class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                  </svg>
                  {{ isOwnerSection ? t('nav.switch_to_buyer', 'Switch to Buyer Mode') : t('nav.switch_to_owner', 'Switch to Owner Mode') }}
                </span>
                <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">
                  {{ isOwnerSection ? 'Buyer' : 'Owner' }}
                </span>
              </button>

              <RouterLink
                :to="authStore.dashboardRoute"
                class="flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors"
              >
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                {{ t('nav.dashboard', 'Dashboard') }}
              </RouterLink>

              <RouterLink
                to="/buyer/favorites"
                class="flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors"
              >
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                {{ t('nav.saved_favorites', 'Saved Favorites') }}
              </RouterLink>

              <button
                type="button"
                class="w-full flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors text-left border-t border-slate-100 dark:border-slate-800"
                @click="handleLogout"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                {{ t('nav.logout', 'Logout') }}
              </button>
            </div>
          </div>

          <!-- Guest: Sign In & Register Links -->
          <div v-else class="flex items-center gap-1.5 xl:gap-2 flex-shrink-0">
            <RouterLink
              to="/login"
              class="text-xs font-bold px-3 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors whitespace-nowrap flex-shrink-0"
              :class="{ 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white': route.path === '/login' }"
            >
              {{ t('nav.sign_in', 'Sign In') }}
            </RouterLink>
            <RouterLink
              to="/register"
              class="text-xs font-bold px-3 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors whitespace-nowrap flex-shrink-0"
              :class="{ 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white': route.path === '/register' }"
            >
              {{ t('nav.register', 'Register') }}
            </RouterLink>
          </div>
        </div>

        <!-- ── Mobile right icons ── -->
        <div class="flex lg:hidden items-center gap-1.5 sm:gap-2">
          <!-- Mobile Language Switcher (Dropdown) -->
          <LanguageSwitcher class="flex-shrink-0" />

          <!-- Mobile Theme Toggle -->
          <button 
            @click="toggleTheme" 
            class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
            aria-label="Toggle theme mode"
          >
            <svg v-if="isDark" class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg v-else class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
          </button>

          <!-- Hamburger Button -->
          <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="p-2 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800"
            :aria-expanded="mobileMenuOpen"
            aria-label="Toggle navigation menu"
          >
            <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </nav>
    </div>

    <!-- ── Mobile Drawer ── -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div
        v-if="mobileMenuOpen"
        id="mobile-menu"
        class="lg:hidden bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 shadow-2xl px-4 py-4 space-y-1.5 max-h-[85vh] overflow-y-auto"
      >
        <!-- Mobile Listings Section Header -->
        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider px-3.5 pt-1">
          {{ t('nav.listings', 'Explore') }}
        </p>

        <!-- All Properties -->
        <RouterLink
          to="/properties"
          @click="mobileMenuOpen = false"
          class="block px-3.5 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold text-sm transition-colors whitespace-nowrap"
          :class="{ 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold': route.path === '/properties' && !route.query.listing_type }"
        >
          {{ t('nav.properties', 'Properties') }}
        </RouterLink>

        <!-- Long Rent -->
        <RouterLink
          to="/properties?listing_type=rent"
          @click="mobileMenuOpen = false"
          class="block px-3.5 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold text-sm transition-colors whitespace-nowrap"
          :class="{ 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold': route.query.listing_type === 'rent' }"
        >
          {{ t('nav.rent', 'Rent') }}
        </RouterLink>

        <!-- Short Stays -->
        <RouterLink
          to="/properties?listing_type=short_rent"
          @click="mobileMenuOpen = false"
          class="block px-3.5 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold text-sm transition-colors whitespace-nowrap"
          :class="{ 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold': route.query.listing_type === 'short_rent' }"
        >
          {{ t('nav.stays', 'Stays') }}
        </RouterLink>

        <!-- Popular Locations -->
        <RouterLink
          to="/locations"
          @click="mobileMenuOpen = false"
          class="block px-3.5 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold text-sm transition-colors whitespace-nowrap"
          :class="{ 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold': route.path === '/locations' }"
        >
          {{ t('nav.locations', 'Locations') }}
        </RouterLink>

        <!-- Agents -->
        <RouterLink
          to="/agents"
          @click="mobileMenuOpen = false"
          class="block px-3.5 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold text-sm transition-colors whitespace-nowrap"
          :class="{ 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold': isRouteActive('/agents') }"
        >
          {{ t('nav.agents', 'Agents') }}
        </RouterLink>

        <!-- Favorites (Mobile) -->
        <button
          v-if="!authStore.isAuthenticated"
          type="button"
          @click="handleFavoritesClick"
          class="w-full text-left block px-3.5 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold text-sm transition-colors whitespace-nowrap cursor-pointer"
        >
          {{ t('nav.favorites', 'Favorites') }}
        </button>
        <RouterLink
          v-else
          to="/buyer/favorites"
          @click="mobileMenuOpen = false"
          class="block px-3.5 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold text-sm transition-colors whitespace-nowrap"
          :class="{ 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold': route.path === '/buyer/favorites' }"
        >
          {{ t('nav.favorites', 'Favorites') }}
        </RouterLink>

        <!-- Mobile Language Selector Dropdown (opens up above the button without scrolling) -->
        <div class="border-t border-slate-100 dark:border-slate-800 pt-3 mt-3 flex items-center justify-between px-3.5">
          <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ t('nav.language', 'Language') }}</span>
          <LanguageSwitcher direction="up" />
        </div>

        <!-- Mobile Action Buttons: 1 horizontal row with gap-2, same color styling as Sign In / Register -->
        <div class="border-t border-slate-100 dark:border-slate-800 pt-3 mt-3">
          <template v-if="authStore.isAuthenticated">
            <div class="px-3 py-2 bg-slate-50 dark:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-700 mb-2 flex items-center justify-between">
              <div class="truncate mr-2">
                <p class="text-[10px] text-slate-400 uppercase font-bold">{{ t('nav.signed_in_as', 'Signed in as') }}</p>
                <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ authStore.user?.email }}</p>
              </div>
              <button
                v-if="isRegularUser"
                type="button"
                @click="togglePersona"
                class="px-2.5 py-1 text-[11px] font-bold rounded-lg text-emerald-700 dark:text-emerald-400 bg-emerald-100/70 dark:bg-emerald-950/60 border border-emerald-300 dark:border-emerald-800 shrink-0"
              >
                {{ isOwnerSection ? t('nav.switch_to_buyer', 'Switch to Buyer') : t('nav.switch_to_owner', 'Switch to Owner') }}
              </button>
            </div>
            <div class="flex items-center gap-2">
              <RouterLink
                :to="listPropertyTarget"
                @click="mobileMenuOpen = false"
                class="flex-1 flex items-center justify-center px-2 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold whitespace-nowrap transition-colors"
              >
                + {{ t('nav.list_property', 'List Property') }}
              </RouterLink>
              <RouterLink
                :to="authStore.dashboardRoute"
                @click="mobileMenuOpen = false"
                class="flex-1 flex items-center justify-center px-2 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold whitespace-nowrap transition-colors"
              >
                {{ t('nav.dashboard', 'Dashboard') }}
              </RouterLink>
              <button
                type="button"
                class="flex-1 flex items-center justify-center px-2 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 text-xs font-bold whitespace-nowrap transition-colors"
                @click="handleLogout"
              >
                {{ t('nav.logout', 'Logout') }}
              </button>
            </div>
          </template>
          <template v-else>
            <div class="flex items-center gap-2">
              <RouterLink
                :to="listPropertyTarget"
                @click="mobileMenuOpen = false"
                class="flex-1 flex items-center justify-center px-2 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold whitespace-nowrap transition-colors"
              >
                + {{ t('nav.list_property', 'List Property') }}
              </RouterLink>
              <RouterLink 
                to="/login" 
                @click="mobileMenuOpen = false" 
                class="flex-1 flex items-center justify-center px-2 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold whitespace-nowrap transition-colors"
              >
                {{ t('nav.sign_in', 'Sign In') }}
              </RouterLink>
              <RouterLink 
                to="/register" 
                @click="mobileMenuOpen = false" 
                class="flex-1 flex items-center justify-center px-2 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold whitespace-nowrap transition-colors"
              >
                {{ t('nav.register', 'Register') }}
              </RouterLink>
            </div>
          </template>
        </div>
      </div>
    </Transition>

    <!-- Logout Confirmation Modal -->
    <ConfirmModal
      :isOpen="showLogoutModal"
      title="Logout of BetLink?"
      message="Are you sure you want to logout?"
      confirmLabel="Logout"
      cancelLabel="Cancel"
      :danger="true"
      @confirm="handleLogoutConfirm"
      @cancel="showLogoutModal = false"
    />
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'
import { useTheme } from '@/composables/useTheme'
import { useLanguage } from '@/composables/useLanguage'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'
import LanguageSwitcher from '@/components/common/LanguageSwitcher.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const toastStore = useToastStore()
const { isDark, toggleTheme } = useTheme()
const { currentLang, currentLanguageObject, languages, setLanguage, t } = useLanguage()

const mobileMenuOpen = ref(false)
const profileMenuOpen = ref(false)
const avatarLoadError = ref(false)
const isLangMenuOpen = ref(false)
const isListingsOpen = ref(false)
const isListingsHovered = ref(false)
const showLogoutModal = ref(false)

const dropdownRef = ref(null)
const langDropdownRef = ref(null)
const listingsDropdownRef = ref(null)

const isOwnerSection = computed(() => route.path.startsWith('/owner'))
const isBuyerSection = computed(() => route.path.startsWith('/buyer'))
const isRegularUser = computed(() => !authStore.isAdmin)

const togglePersona = () => {
  profileMenuOpen.value = false
  mobileMenuOpen.value = false
  if (isOwnerSection.value) {
    router.push('/buyer/dashboard')
  } else {
    router.push('/owner/dashboard')
  }
}

const isListingsActive = computed(() => {
  return route.path === '/properties' || route.path.startsWith('/properties/') || route.path === '/locations'
})

const listPropertyTarget = computed(() => {
  if (!authStore.isAuthenticated) {
    return '/login?redirect=' + encodeURIComponent('/owner/properties?add=1')
  }
  if (authStore.user?.role === 'agent') {
    return '/agent/properties?add=1'
  }
  return '/owner/properties?add=1'
})

const isRouteActive = (targetPath) => {
  if (targetPath.includes('?')) {
    return route.fullPath === targetPath
  }
  return route.path === targetPath || route.path.startsWith(targetPath + '/')
}

const handleFavoritesClick = (e) => {
  if (!authStore.isAuthenticated) {
    if (e && e.preventDefault) e.preventDefault()
    toastStore.info('Please sign in to view your saved favorites', 'Sign In Required')
    router.push({ path: '/login', query: { redirect: '/buyer/favorites' } })
    mobileMenuOpen.value = false
    return false
  }
}

const handleLogout = () => {
  profileMenuOpen.value = false
  mobileMenuOpen.value = false
  showLogoutModal.value = true
}

const handleLogoutConfirm = async () => {
  showLogoutModal.value = false
  profileMenuOpen.value = false
  mobileMenuOpen.value = false
  await authStore.logout()
  toastStore.success('Successfully logged out!')
  router.push('/login')
}

const handleClickOutside = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    profileMenuOpen.value = false
  }
  if (langDropdownRef.value && !langDropdownRef.value.contains(e.target)) {
    isLangMenuOpen.value = false
  }
  if (listingsDropdownRef.value && !listingsDropdownRef.value.contains(e.target)) {
    isListingsOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
