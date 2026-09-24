<template>
  <div class="h-[calc(100vh-4rem)] lg:h-[calc(100vh-5rem)] flex flex-col bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 overflow-hidden transition-colors duration-200">
    
    <!-- ── Ultra-Slim Pinned Top Header & Controls Bar ── -->
    <header class="flex-shrink-0 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 px-3 sm:px-6 lg:px-8 py-2 z-10 transition-colors">
      <div class="max-w-7xl mx-auto flex items-center justify-between gap-2 sm:gap-3">
        <!-- Back Button + Compact Title & Count -->
        <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
          <button
            type="button"
            @click="handleGoBack"
            class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors shrink-0 cursor-pointer shadow-2xs"
            title="Go Back"
            aria-label="Go Back"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </button>
          
          <h1 class="text-xs sm:text-sm md:text-base font-bold text-slate-900 dark:text-white tracking-tight truncate">
            {{ selectedPropertyId ? 'Property Details' : pageTitle }}
          </h1>
          <span v-if="!selectedPropertyId" class="hidden sm:inline text-xs text-slate-400 dark:text-slate-500 font-medium whitespace-nowrap">
            ({{ pagination.total || properties.length }} listings)
          </span>
        </div>

        <!-- Controls when inside single property view -->
        <div v-if="selectedPropertyId" class="flex items-center gap-2 flex-shrink-0">
          <button
            type="button"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition-colors cursor-pointer"
            @click="clearSelectedProperty"
          >
            All Listings
          </button>
        </div>

        <!-- Controls when in feed view -->
        <div v-else class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
          <!-- Mobile Filter Toggle Button -->
          <button
            type="button"
            class="lg:hidden inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold transition-colors cursor-pointer"
            @click="mobileFiltersOpen = true"
          >
            <svg class="w-3.5 h-3.5 text-slate-600 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <span>Filters</span>
            <span v-if="activeFilterCount > 0" class="w-4 h-4 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[10px] flex items-center justify-center font-bold">
              {{ activeFilterCount }}
            </span>
          </button>

          <!-- Compact Sort Dropdown -->
          <div class="flex items-center gap-1">
            <label for="sort-select" class="text-xs text-slate-500 dark:text-slate-400 hidden sm:inline font-medium">Sort:</label>
            <select
              id="sort-select"
              v-model="filters.sort"
              class="bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs rounded-xl px-2.5 py-1.5 focus:outline-none transition-colors cursor-pointer font-bold border border-slate-200 dark:border-slate-700"
              @change="applyFilters(1)"
            >
              <option value="latest">Newest</option>
              <option value="oldest">Oldest</option>
              <option value="price_asc">Low to High</option>
              <option value="price_desc">High to Low</option>
              <option value="popular">Popular</option>
            </select>
          </div>
        </div>
      </div>
    </header>

    <!-- ── Dual Split Panes (Like Telegram / Dashboard) ── -->
    <div class="flex-1 min-h-0 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-3 flex gap-5 overflow-hidden">
      <!-- ── LEFT PANE: Desktop Sidebar Filters (Scrolls ONLY when cursor is on left) ── -->
      <aside class="hidden lg:block w-72 xl:w-80 flex-shrink-0 h-full overflow-hidden">
        <div class="h-full bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 overflow-y-auto overscroll-contain filter-sidebar-scroll space-y-5 shadow-xs transition-colors pr-3">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Filters</h2>
            <button
              v-if="activeFilterCount > 0"
              type="button"
              class="text-xs text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-semibold transition-colors"
              @click="resetFilters"
            >
              Reset All ({{ activeFilterCount }})
            </button>
          </div>

            <!-- Listing Type Dropdown -->
            <div>
              <label for="listing-type-select" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                Listing Type
              </label>
              <select
                id="listing-type-select"
                v-model="filters.listing_type"
                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors cursor-pointer"
                @change="applyFilters(1)"
              >
                <option value="">All Listings</option>
                <option value="sale">For Sale (Buy)</option>
                <option value="rent">For Rent</option>
                <option value="short_rent">Short Stay (Furnished)</option>
              </select>
            </div>

            <!-- Property Type -->
            <div>
              <label for="type-select" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                Property Type
              </label>
              <select
                id="type-select"
                v-model="filters.type"
                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors cursor-pointer"
                @change="applyFilters(1)"
              >
                <option value="">All Types</option>
                <option value="apartment">Apartment</option>
                <option value="house">House</option>
                <option value="villa">Villa</option>
                <option value="condo">Condominium</option>
                <option value="studio">Studio</option>
                <option value="commercial">Commercial</option>
                <option value="office">Office</option>
                <option value="land">Land</option>
                <option value="hotel">Hotel / Lodge</option>
                <option value="warehouse">Warehouse</option>
              </select>
            </div>

            <!-- City / Region -->
            <div>
              <label for="city-select" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                City / Region
              </label>
              <select
                id="city-select"
                v-model="filters.city_id"
                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors cursor-pointer"
                @change="onCityChange"
              >
                <option value="">All Cities</option>
                <option v-for="c in cities" :key="c.id" :value="c.id">
                  {{ c.name }}
                </option>
              </select>
            </div>

            <!-- Town Input Field -->
            <div>
              <label for="town-input" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                Town
              </label>
              <div class="relative">
                <input
                  id="town-input"
                  v-model="townInput"
                  list="town-suggestions-desktop"
                  type="text"
                  placeholder="e.g. Bole, Bahir Dar, Assosa..."
                  autocomplete="off"
                  class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 placeholder-slate-400 text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors pr-8"
                  @input="handleTownInput"
                  @change="handleTownChange"
                  @keyup.enter="applyFilters(1)"
                />
                <button
                  v-if="townInput"
                  type="button"
                  class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 dark:hover:text-white text-xs"
                  @click="clearTownInput"
                >
                  ✕
                </button>
                <datalist id="town-suggestions-desktop">
                  <option v-for="(sc, idx) in townSuggestions" :key="'desk-town-' + (sc.id || idx) + '-' + idx" :value="sc.name">
                    {{ sc.cityName ? `${sc.cityName} Region` : '' }}
                  </option>
                </datalist>
              </div>
            </div>

            <!-- Amenities Dropdown -->
            <div>
              <label for="amenity-select" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                Amenities & Features
              </label>
              <select
                id="amenity-select"
                v-model="filters.amenity_id"
                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors cursor-pointer"
                @change="applyFilters(1)"
              >
                <option value="">All Amenities</option>
                <option v-for="am in availableAmenities" :key="am.id || am.slug" :value="am.id">
                  {{ am.name }}
                </option>
              </select>
            </div>

            <!-- Price Range (ETB) -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                Price Range (ETB)
              </label>
              <div class="grid grid-cols-2 gap-2">
                <input
                  v-model.number="filters.min_price"
                  type="number"
                  placeholder="Min ETB"
                  class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 placeholder-slate-400 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors"
                  @change="applyFilters(1)"
                />
                <input
                  v-model.number="filters.max_price"
                  type="number"
                  placeholder="Max ETB"
                  class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 placeholder-slate-400 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors"
                  @change="applyFilters(1)"
                />
              </div>
            </div>

            <!-- Bedrooms Dropdown -->
            <div>
              <label for="bedrooms-select" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                Bedrooms
              </label>
              <select
                id="bedrooms-select"
                v-model="filters.bedrooms"
                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors cursor-pointer"
                @change="applyFilters(1)"
              >
                <option :value="null">Any Bedrooms</option>
                <option :value="1">1 Bedroom</option>
                <option :value="2">2 Bedrooms</option>
                <option :value="3">3 Bedrooms</option>
                <option :value="4">4 Bedrooms</option>
                <option :value="5">5+ Bedrooms</option>
              </select>
            </div>

            <!-- Bathrooms Dropdown -->
            <div>
              <label for="bathrooms-select" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                Bathrooms
              </label>
              <select
                id="bathrooms-select"
                v-model="filters.bathrooms"
                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors cursor-pointer"
                @change="applyFilters(1)"
              >
                <option :value="null">Any Bathrooms</option>
                <option :value="1">1 Bathroom</option>
                <option :value="2">2 Bathrooms</option>
                <option :value="3">3 Bathrooms</option>
                <option :value="4">4+ Bathrooms</option>
              </select>
            </div>

            <!-- Apply / Reset Buttons -->
            <div class="pt-2 space-y-2">
              <button
                type="button"
                class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition-colors cursor-pointer"
                @click="applyFilters(1)"
              >
                Apply Filters
              </button>
            </div>
          </div>
        </aside>

        <!-- ── RIGHT PANE: Main Properties Feed (Scrolls independently like Telegram) ── -->
        <!-- ── RIGHT PANE: Main Properties Feed / Inline Details (Telegram / Dashboard Style) ── -->
        <main
          ref="propertiesFeedRef"
          class="flex-1 min-w-0 h-full overflow-y-auto overscroll-contain filter-sidebar-scroll pr-1 space-y-5"
        >
          <!-- ── INLINE PROPERTY DETAILS VIEW (Swaps right pane while keeping left filter intact) ── -->
          <div v-if="selectedPropertyId" class="pb-10">
            <PropertyDetails
              :property-id="selectedPropertyId"
              :inline="true"
              @back="clearSelectedProperty"
            />
          </div>

          <!-- ── PROPERTIES FEED (When no property selected) ── -->
          <template v-else>
            <!-- Active Filter Badges Bar -->
            <div v-if="activeFilterCount > 0" class="flex flex-wrap items-center gap-2 mb-6">
              <span class="text-xs text-slate-500 dark:text-slate-400 mr-1 font-medium">Active filters:</span>
              <span v-if="filters.q" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200">
                "{{ filters.q }}"
                <button type="button" @click="filters.q = ''; applyFilters(1)">✕</button>
              </span>
              <span v-if="filters.listing_type" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200">
                {{ filters.listing_type === 'sale' ? 'For Sale' : filters.listing_type === 'rent' ? 'For Rent' : 'Short Stay' }}
                <button type="button" @click="setListingType('')">✕</button>
              </span>
              <span v-if="filters.type" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200 capitalize">
                {{ filters.type }}
                <button type="button" @click="filters.type = ''; applyFilters(1)">✕</button>
              </span>
              <span v-if="filters.city_id" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200">
                {{ getCityName(filters.city_id) }}
                <button type="button" @click="filters.city_id = ''; onCityChange()">✕</button>
              </span>
              <span v-if="filters.sub_city_id || townInput" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200">
                {{ filters.sub_city_id ? getSubCityName(filters.sub_city_id) : townInput }}
                <button type="button" @click="clearTownInput()">✕</button>
              </span>
              <span v-if="filters.amenity_id" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200">
                {{ getAmenityName(filters.amenity_id) }}
                <button type="button" @click="filters.amenity_id = ''; applyFilters(1)">✕</button>
              </span>
              <span v-if="filters.min_price || filters.max_price" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200">
                ETB {{ filters.min_price || 0 }} - {{ filters.max_price || 'Any' }}
                <button type="button" @click="filters.min_price = null; filters.max_price = null; applyFilters(1)">✕</button>
              </span>
              <span v-if="filters.bedrooms" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200">
                {{ filters.bedrooms }}+ Beds
                <button type="button" @click="filters.bedrooms = null; applyFilters(1)">✕</button>
              </span>
              <span v-if="filters.bathrooms" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200">
                {{ filters.bathrooms }}+ Baths
                <button type="button" @click="filters.bathrooms = null; applyFilters(1)">✕</button>
              </span>
              <button
                type="button"
                class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline font-bold ml-2"
                @click="resetFilters"
              >
                Clear all
              </button>
            </div>

            <!-- Loading Skeleton -->
            <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="i in 6"
                :key="i"
                class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl h-80 animate-pulse flex flex-col justify-between p-5"
              >
                <div class="w-full h-44 bg-slate-100 dark:bg-slate-800 rounded-xl" />
                <div class="space-y-2 mt-4">
                  <div class="w-3/4 h-4 bg-slate-100 dark:bg-slate-800 rounded" />
                  <div class="w-1/2 h-3 bg-slate-100 dark:bg-slate-800 rounded" />
                </div>
              </div>
            </div>

            <!-- Error State with Retry -->
            <div v-else-if="errorMessage" class="text-center py-16 px-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs">
              <div class="w-12 h-12 rounded-full bg-rose-500/10 text-rose-500 dark:text-rose-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">Failed to load properties</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-5">{{ errorMessage }}</p>
              <button
                type="button"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white transition-colors shadow-xs"
                @click="fetchProperties"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Try Again
              </button>
            </div>

            <!-- Empty State -->
            <div v-else-if="properties.length === 0" class="text-center py-16 px-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs">
              <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">No matching properties found</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-5">
                Try adjusting your price range, removing bedrooms filter, or searching across all cities.
              </p>
              <button
                type="button"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold transition-colors"
                @click="resetFilters"
              >
                Reset Filters
              </button>
            </div>

            <!-- Properties Grid & Pagination -->
            <div v-else class="space-y-8">
              <div :class="propertiesGridClass">
                <PropertyCard
                  v-for="property in properties"
                  :key="property.id"
                  :property="property"
                  :inline-mode="true"
                  @select="handleSelectProperty"
                />
              </div>

              <!-- Pagination Component -->
              <div class="pt-4 border-t border-slate-200/80 dark:border-slate-800">
                <BasePagination
                  :current-page="pagination.current_page"
                  :total-pages="pagination.last_page"
                  :total-items="pagination.total"
                  :per-page="pagination.per_page"
                  @change="onPageChange"
                />
              </div>
            </div>
          </template>
        </main>
      </div>

    <!-- ── Mobile Slide-Over Filter Drawer ── -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="mobileFiltersOpen" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex justify-end" @click.self="mobileFiltersOpen = false">
          <div class="w-full max-w-sm bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 h-full overflow-y-auto p-6 flex flex-col justify-between space-y-6">
            <div class="space-y-6">
              <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Filter Properties</h3>
                <button type="button" class="p-1.5 text-slate-400 hover:text-slate-900 dark:hover:text-white" @click="mobileFiltersOpen = false">
                  ✕
                </button>
              </div>

              <!-- Mobile Filters Controls -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Listing Type</label>
                <select
                  v-model="filters.listing_type"
                  class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer"
                >
                  <option value="">All Listings</option>
                  <option value="sale">For Sale (Buy)</option>
                  <option value="rent">For Rent</option>
                  <option value="short_rent">Short Stay (Furnished)</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Property Type</label>
                <select
                  v-model="filters.type"
                  class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer"
                >
                  <option value="">All Types</option>
                  <option value="apartment">Apartment</option>
                  <option value="house">House</option>
                  <option value="villa">Villa</option>
                  <option value="condo">Condominium</option>
                  <option value="studio">Studio</option>
                  <option value="commercial">Commercial</option>
                  <option value="office">Office</option>
                  <option value="land">Land</option>
                  <option value="hotel">Hotel / Lodge</option>
                  <option value="warehouse">Warehouse</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">City / Region</label>
                <select
                  v-model="filters.city_id"
                  class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer"
                  @change="onCityChange"
                >
                  <option value="">All Cities</option>
                  <option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
              </div>

              <!-- Town Input Field (Mobile) -->
              <div>
                <label for="town-input-mobile" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                  Town
                </label>
                <div class="relative">
                  <input
                    id="town-input-mobile"
                    v-model="townInput"
                    list="town-suggestions-mobile"
                    type="text"
                    placeholder="e.g. Bole, Bahir Dar, Assosa..."
                    autocomplete="off"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 placeholder-slate-400 text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors pr-8"
                    @input="handleTownInput"
                    @change="handleTownChange"
                    @keyup.enter="applyFilters(1); mobileFiltersOpen = false"
                  />
                  <button
                    v-if="townInput"
                    type="button"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 dark:hover:text-white text-xs"
                    @click="clearTownInput"
                  >
                    ✕
                  </button>
                  <datalist id="town-suggestions-mobile">
                    <option v-for="(sc, idx) in townSuggestions" :key="'mob-town-' + (sc.id || idx) + '-' + idx" :value="sc.name">
                      {{ sc.cityName ? `${sc.cityName} Region` : '' }}
                    </option>
                  </datalist>
                </div>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Amenities & Features</label>
                <select
                  v-model="filters.amenity_id"
                  class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer"
                >
                  <option value="">All Amenities</option>
                  <option v-for="am in availableAmenities" :key="am.id || am.slug" :value="am.id">
                    {{ am.name }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Bedrooms</label>
                <select
                  v-model="filters.bedrooms"
                  class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer"
                >
                  <option :value="null">Any Bedrooms</option>
                  <option :value="1">1 Bedroom</option>
                  <option :value="2">2 Bedrooms</option>
                  <option :value="3">3 Bedrooms</option>
                  <option :value="4">4 Bedrooms</option>
                  <option :value="5">5+ Bedrooms</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Bathrooms</label>
                <select
                  v-model="filters.bathrooms"
                  class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 text-xs rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer"
                >
                  <option :value="null">Any Bathrooms</option>
                  <option :value="1">1 Bathroom</option>
                  <option :value="2">2 Bathrooms</option>
                  <option :value="3">3 Bathrooms</option>
                  <option :value="4">4+ Bathrooms</option>
                </select>
              </div>

              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Min Price (ETB)</label>
                  <input
                    v-model.number="filters.min_price"
                    type="number"
                    placeholder="0"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500"
                  />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Max Price (ETB)</label>
                  <input
                    v-model.number="filters.max_price"
                    type="number"
                    placeholder="Max"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500"
                  />
                </div>
              </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
              <button
                type="button"
                class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs transition-colors cursor-pointer"
                @click="applyFilters(1); mobileFiltersOpen = false"
              >
                Apply ({{ activeFilterCount }} filters)
              </button>
              <button
                type="button"
                class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition-colors cursor-pointer"
                @click="resetFilters(); mobileFiltersOpen = false"
              >
                Reset Filters
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PropertyCard from '../../components/property/PropertyCard.vue'
import PropertyDetails from './PropertyDetails.vue'
import BasePagination from '../../components/common/BasePagination.vue'
import { propertyService } from '../../services/propertyService'
import { searchService } from '../../services/searchService'
import { locationService } from '../../services/locationService'

const route = useRoute()
const router = useRouter()

const selectedPropertyId = ref(route.query.selected || null)

const propertiesGridClass = computed(() => {
  const count = properties.value?.length || 0
  if (count === 1) return 'grid grid-cols-1 max-w-xl mx-auto'
  if (count === 2) return 'grid grid-cols-1 sm:grid-cols-2 gap-6'
  if (count === 3) return 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6'
  if (count === 4) return 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-6'
  return 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6'
})

const handleGoBack = () => {
  if (selectedPropertyId.value) {
    clearSelectedProperty()
    return
  }
  // If user came from another page (e.g. Home), go back; otherwise push to Home
  if (window.history.state && window.history.state.back) {
    router.back()
  } else {
    router.push('/')
  }
}

const handleSelectProperty = (prop) => {
  selectedPropertyId.value = prop.id || prop.slug
  const query = { ...route.query, selected: selectedPropertyId.value }
  router.replace({ query })
  if (propertiesFeedRef.value) {
    propertiesFeedRef.value.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const clearSelectedProperty = () => {
  selectedPropertyId.value = null
  const query = { ...route.query }
  delete query.selected
  router.replace({ query })
}

watch(() => route.query.selected, (newVal) => {
  selectedPropertyId.value = newVal || null
})

const defaultCitiesFallback = [
  { id: 1, name: 'Addis Ababa', slug: 'addis-ababa' },
  { id: 2, name: 'Dire Dawa', slug: 'dire-dawa' },
  { id: 3, name: 'Tigray', slug: 'tigray' },
  { id: 4, name: 'Afar', slug: 'afar' },
  { id: 5, name: 'Amhara', slug: 'amhara' },
  { id: 6, name: 'Oromia', slug: 'oromia' },
  { id: 7, name: 'Somali', slug: 'somali' },
  { id: 8, name: 'Benishangul-Gumuz', slug: 'benishangul-gumuz' },
  { id: 9, name: 'Southern Nations, Nationalities, and Peoples (SNNP)', slug: 'snnp' },
  { id: 10, name: 'Gambela', slug: 'gambela' },
  { id: 11, name: 'Harari', slug: 'harari' },
  { id: 12, name: 'Sidama', slug: 'sidama' },
  { id: 13, name: 'South West Ethiopia Peoples', slug: 'south-west-ethiopia' },
  { id: 14, name: 'Central Ethiopia', slug: 'central-ethiopia' },
  { id: 15, name: 'South Ethiopia', slug: 'south-ethiopia' },
]

const properties = ref([])
const cities = ref(defaultCitiesFallback)
const subCities = ref([])
const allSubCities = ref([])
const townInput = ref('')
const amenities = ref([])
const isLoading = ref(true)
const errorMessage = ref('')
const mobileFiltersOpen = ref(false)
const propertiesFeedRef = ref(null)

const filters = reactive({
  q: '',
  listing_type: '',
  type: '',
  city_id: '',
  sub_city_id: '',
  amenity_id: '',
  user_id: '',
  min_price: null,
  max_price: null,
  bedrooms: null,
  bathrooms: null,
  sort: 'latest',
  page: 1,
})

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 15,
})

const defaultSubCitiesFallback = [
  // 1. Addis Ababa (city_id: 1)
  { id: 1, city_id: 1, cityName: 'Addis Ababa', name: 'Bole' },
  { id: 2, city_id: 1, cityName: 'Addis Ababa', name: 'Kirkos' },
  { id: 3, city_id: 1, cityName: 'Addis Ababa', name: 'Yeka' },
  { id: 4, city_id: 1, cityName: 'Addis Ababa', name: 'Arada' },
  { id: 5, city_id: 1, cityName: 'Addis Ababa', name: 'Lideta' },
  { id: 6, city_id: 1, cityName: 'Addis Ababa', name: 'Addis Ketema' },
  { id: 7, city_id: 1, cityName: 'Addis Ababa', name: 'Gulele' },
  { id: 8, city_id: 1, cityName: 'Addis Ababa', name: 'Kolfe Keranio' },
  { id: 9, city_id: 1, cityName: 'Addis Ababa', name: 'Nifas Silk-Lafto' },
  { id: 10, city_id: 1, cityName: 'Addis Ababa', name: 'Akaki Kaliti' },
  { id: 11, city_id: 1, cityName: 'Addis Ababa', name: 'Lemi Kura' },
  { id: 101, city_id: 1, cityName: 'Addis Ababa', name: 'Saris' },
  { id: 102, city_id: 1, cityName: 'Addis Ababa', name: 'CMC' },
  { id: 103, city_id: 1, cityName: 'Addis Ababa', name: 'Ayat' },
  { id: 104, city_id: 1, cityName: 'Addis Ababa', name: 'Lebu' },
  { id: 105, city_id: 1, cityName: 'Addis Ababa', name: 'Jemo' },

  // 2. Dire Dawa (city_id: 2)
  { id: 12, city_id: 2, cityName: 'Dire Dawa', name: 'Dire Dawa City' },
  { id: 13, city_id: 2, cityName: 'Dire Dawa', name: 'Gurgura' },
  { id: 106, city_id: 2, cityName: 'Dire Dawa', name: 'Sabian' },
  { id: 107, city_id: 2, cityName: 'Dire Dawa', name: 'Kezira' },
  { id: 108, city_id: 2, cityName: 'Dire Dawa', name: 'Melka Jebdu' },
  { id: 109, city_id: 2, cityName: 'Dire Dawa', name: 'Magala' },
  { id: 110, city_id: 2, cityName: 'Dire Dawa', name: 'Legehare' },
  { id: 111, city_id: 2, cityName: 'Dire Dawa', name: 'Dechatu' },
  { id: 112, city_id: 2, cityName: 'Dire Dawa', name: 'Ganda Kore' },
  { id: 113, city_id: 2, cityName: 'Dire Dawa', name: 'Shinile' },

  // 3. Tigray (city_id: 3)
  { id: 14, city_id: 3, cityName: 'Tigray', name: 'Mekelle' },
  { id: 15, city_id: 3, cityName: 'Tigray', name: 'Adigrat' },
  { id: 16, city_id: 3, cityName: 'Tigray', name: 'Axum' },
  { id: 17, city_id: 3, cityName: 'Tigray', name: 'Shire' },
  { id: 114, city_id: 3, cityName: 'Tigray', name: 'Adwa' },
  { id: 115, city_id: 3, cityName: 'Tigray', name: 'Wukro' },
  { id: 116, city_id: 3, cityName: 'Tigray', name: 'Humera' },
  { id: 117, city_id: 3, cityName: 'Tigray', name: 'Alamata' },
  { id: 118, city_id: 3, cityName: 'Tigray', name: 'Maychew' },
  { id: 119, city_id: 3, cityName: 'Tigray', name: 'Korem' },

  // 4. Afar (city_id: 4)
  { id: 18, city_id: 4, cityName: 'Afar', name: 'Semera' },
  { id: 19, city_id: 4, cityName: 'Afar', name: 'Asayita' },
  { id: 20, city_id: 4, cityName: 'Afar', name: 'Awash' },
  { id: 120, city_id: 4, cityName: 'Afar', name: 'Dubti' },
  { id: 121, city_id: 4, cityName: 'Afar', name: 'Logiya' },
  { id: 122, city_id: 4, cityName: 'Afar', name: 'Gewane' },
  { id: 123, city_id: 4, cityName: 'Afar', name: 'Mille' },
  { id: 124, city_id: 4, cityName: 'Afar', name: 'Abala' },
  { id: 125, city_id: 4, cityName: 'Afar', name: 'Chifra' },
  { id: 126, city_id: 4, cityName: 'Afar', name: 'Amibara' },

  // 5. Amhara (city_id: 5)
  { id: 21, city_id: 5, cityName: 'Amhara', name: 'Bahir Dar' },
  { id: 22, city_id: 5, cityName: 'Amhara', name: 'Gondar' },
  { id: 23, city_id: 5, cityName: 'Amhara', name: 'Dessie' },
  { id: 24, city_id: 5, cityName: 'Amhara', name: 'Debre Birhan' },
  { id: 25, city_id: 5, cityName: 'Amhara', name: 'Debre Markos' },
  { id: 127, city_id: 5, cityName: 'Amhara', name: 'Kombolcha' },
  { id: 128, city_id: 5, cityName: 'Amhara', name: 'Woldiya' },
  { id: 129, city_id: 5, cityName: 'Amhara', name: 'Lalibela' },
  { id: 130, city_id: 5, cityName: 'Amhara', name: 'Debre Tabor' },
  { id: 131, city_id: 5, cityName: 'Amhara', name: 'Finote Selam' },
  { id: 132, city_id: 5, cityName: 'Amhara', name: 'Enjibara' },
  { id: 133, city_id: 5, cityName: 'Amhara', name: 'Shewa Robit' },

  // 6. Oromia (city_id: 6)
  { id: 26, city_id: 6, cityName: 'Oromia', name: 'Adama (Nazret)' },
  { id: 27, city_id: 6, cityName: 'Oromia', name: 'Bishoftu (Debre Zeyit)' },
  { id: 28, city_id: 6, cityName: 'Oromia', name: 'Jimma' },
  { id: 29, city_id: 6, cityName: 'Oromia', name: 'Nekemte' },
  { id: 30, city_id: 6, cityName: 'Oromia', name: 'Ambo' },
  { id: 31, city_id: 6, cityName: 'Oromia', name: 'Shashamane' },
  { id: 32, city_id: 6, cityName: 'Oromia', name: 'Bale Robe' },
  { id: 134, city_id: 6, cityName: 'Oromia', name: 'Dukem' },
  { id: 135, city_id: 6, cityName: 'Oromia', name: 'Sebeta' },
  { id: 136, city_id: 6, cityName: 'Oromia', name: 'Burayu' },
  { id: 137, city_id: 6, cityName: 'Oromia', name: 'Sululta' },
  { id: 138, city_id: 6, cityName: 'Oromia', name: 'Asella' },
  { id: 139, city_id: 6, cityName: 'Oromia', name: 'Mojo' },

  // 7. Somali (city_id: 7)
  { id: 33, city_id: 7, cityName: 'Somali', name: 'Jigjiga' },
  { id: 34, city_id: 7, cityName: 'Somali', name: 'Gode' },
  { id: 35, city_id: 7, cityName: 'Somali', name: 'Degehabur' },
  { id: 140, city_id: 7, cityName: 'Somali', name: 'Kebri Dahar' },
  { id: 141, city_id: 7, cityName: 'Somali', name: 'Warder' },
  { id: 142, city_id: 7, cityName: 'Somali', name: 'Shinile' },
  { id: 143, city_id: 7, cityName: 'Somali', name: 'Dolo Ado' },
  { id: 144, city_id: 7, cityName: 'Somali', name: 'Filtu' },
  { id: 145, city_id: 7, cityName: 'Somali', name: 'Kelafo' },
  { id: 146, city_id: 7, cityName: 'Somali', name: 'Tog Wajale' },

  // 8. Benishangul-Gumuz (city_id: 8)
  { id: 36, city_id: 8, cityName: 'Benishangul-Gumuz', name: 'Asosa' },
  { id: 361, city_id: 8, cityName: 'Benishangul-Gumuz', name: 'Assosa' },
  { id: 37, city_id: 8, cityName: 'Benishangul-Gumuz', name: 'Pawe' },
  { id: 147, city_id: 8, cityName: 'Benishangul-Gumuz', name: 'Gilgel Beles' },
  { id: 148, city_id: 8, cityName: 'Benishangul-Gumuz', name: 'Bambasi' },
  { id: 149, city_id: 8, cityName: 'Benishangul-Gumuz', name: 'Kamashi' },
  { id: 150, city_id: 8, cityName: 'Benishangul-Gumuz', name: 'Kurmuk' },
  { id: 151, city_id: 8, cityName: 'Benishangul-Gumuz', name: 'Mengi' },
  { id: 152, city_id: 8, cityName: 'Benishangul-Gumuz', name: 'Bulen' },
  { id: 153, city_id: 8, cityName: 'Benishangul-Gumuz', name: 'Dibate' },
  { id: 154, city_id: 8, cityName: 'Benishangul-Gumuz', name: 'Dangur' },
  { id: 155, city_id: 8, cityName: 'Benishangul-Gumuz', name: 'Wombera' },

  // 9. SNNP / South Ethiopia (city_id: 9)
  { id: 38, city_id: 9, cityName: 'SNNP', name: 'Hawassa' },
  { id: 39, city_id: 9, cityName: 'SNNP', name: 'Arba Minch' },
  { id: 40, city_id: 9, cityName: 'SNNP', name: 'Wolaita Sodo' },
  { id: 156, city_id: 9, cityName: 'SNNP', name: 'Jinka' },
  { id: 157, city_id: 9, cityName: 'SNNP', name: 'Dilla' },
  { id: 158, city_id: 9, cityName: 'SNNP', name: 'Sawla' },
  { id: 159, city_id: 9, cityName: 'SNNP', name: 'Konso' },
  { id: 160, city_id: 9, cityName: 'SNNP', name: 'Karat' },
  { id: 161, city_id: 9, cityName: 'SNNP', name: 'Turmi' },
  { id: 162, city_id: 9, cityName: 'SNNP', name: 'Dimeka' },

  // 10. Gambela (city_id: 10)
  { id: 41, city_id: 10, cityName: 'Gambela', name: 'Gambela City' },
  { id: 163, city_id: 10, cityName: 'Gambela', name: 'Itang' },
  { id: 164, city_id: 10, cityName: 'Gambela', name: 'Abobo' },
  { id: 165, city_id: 10, cityName: 'Gambela', name: 'Pugnido' },
  { id: 166, city_id: 10, cityName: 'Gambela', name: 'Dimma' },
  { id: 167, city_id: 10, cityName: 'Gambela', name: 'Lare' },
  { id: 168, city_id: 10, cityName: 'Gambela', name: 'Jor' },
  { id: 169, city_id: 10, cityName: 'Gambela', name: 'Gog' },
  { id: 170, city_id: 10, cityName: 'Gambela', name: 'Metu' },
  { id: 171, city_id: 10, cityName: 'Gambela', name: 'Akobo' },

  // 11. Harari (city_id: 11)
  { id: 42, city_id: 11, cityName: 'Harari', name: 'Harar' },
  { id: 172, city_id: 11, cityName: 'Harari', name: 'Jugol' },
  { id: 173, city_id: 11, cityName: 'Harari', name: 'Shenkor' },
  { id: 174, city_id: 11, cityName: 'Harari', name: 'Abadir' },
  { id: 175, city_id: 11, cityName: 'Harari', name: 'Amir Nur' },
  { id: 176, city_id: 11, cityName: 'Harari', name: 'Jin\'Eala' },
  { id: 177, city_id: 11, cityName: 'Harari', name: 'Sofi' },
  { id: 178, city_id: 11, cityName: 'Harari', name: 'Erer' },
  { id: 179, city_id: 11, cityName: 'Harari', name: 'Hakim' },
  { id: 180, city_id: 11, cityName: 'Harari', name: 'Dire Teyara' },

  // 12. Sidama (city_id: 12)
  { id: 43, city_id: 12, cityName: 'Sidama', name: 'Hawassa' },
  { id: 44, city_id: 12, cityName: 'Sidama', name: 'Yirgalem' },
  { id: 181, city_id: 12, cityName: 'Sidama', name: 'Aleta Wondo' },
  { id: 182, city_id: 12, cityName: 'Sidama', name: 'Leku' },
  { id: 183, city_id: 12, cityName: 'Sidama', name: 'Wondo Genet' },
  { id: 184, city_id: 12, cityName: 'Sidama', name: 'Hula' },
  { id: 185, city_id: 12, cityName: 'Sidama', name: 'Bona' },
  { id: 186, city_id: 12, cityName: 'Sidama', name: 'Daye' },
  { id: 187, city_id: 12, cityName: 'Sidama', name: 'Dara' },
  { id: 188, city_id: 12, cityName: 'Sidama', name: 'Chuko' },

  // 13. South West Ethiopia (city_id: 13)
  { id: 45, city_id: 13, cityName: 'South West Ethiopia', name: 'Bonga' },
  { id: 46, city_id: 13, cityName: 'South West Ethiopia', name: 'Mizan Teferi' },
  { id: 189, city_id: 13, cityName: 'South West Ethiopia', name: 'Tepi' },
  { id: 190, city_id: 13, cityName: 'South West Ethiopia', name: 'Aman' },
  { id: 191, city_id: 13, cityName: 'South West Ethiopia', name: 'Masha' },
  { id: 192, city_id: 13, cityName: 'South West Ethiopia', name: 'Tercha' },
  { id: 193, city_id: 13, cityName: 'South West Ethiopia', name: 'Chena' },
  { id: 194, city_id: 13, cityName: 'South West Ethiopia', name: 'Gesha' },
  { id: 195, city_id: 13, cityName: 'South West Ethiopia', name: 'Sheko' },
  { id: 196, city_id: 13, cityName: 'South West Ethiopia', name: 'Bench Maji' },

  // 14. Central Ethiopia (city_id: 14)
  { id: 47, city_id: 14, cityName: 'Central Ethiopia', name: 'Gelan' },
  { id: 49, city_id: 14, cityName: 'Central Ethiopia', name: 'Butajira' },
  { id: 50, city_id: 14, cityName: 'Central Ethiopia', name: 'Hosanna' },
  { id: 197, city_id: 14, cityName: 'Central Ethiopia', name: 'Welkite' },
  { id: 198, city_id: 14, cityName: 'Central Ethiopia', name: 'Worabe' },
  { id: 199, city_id: 14, cityName: 'Central Ethiopia', name: 'Halaba Kulito' },
  { id: 200, city_id: 14, cityName: 'Central Ethiopia', name: 'Durame' },
  { id: 201, city_id: 14, cityName: 'Central Ethiopia', name: 'Shinshicho' },
  { id: 202, city_id: 14, cityName: 'Central Ethiopia', name: 'Buee' },
  { id: 203, city_id: 14, cityName: 'Central Ethiopia', name: 'Agena' },

  // 15. South Ethiopia (city_id: 15)
  { id: 48, city_id: 15, cityName: 'South Ethiopia', name: 'Jinka' },
  { id: 205, city_id: 15, cityName: 'South Ethiopia', name: 'Arba Minch' },
  { id: 206, city_id: 15, cityName: 'South Ethiopia', name: 'Wolaita Sodo' },
  { id: 207, city_id: 15, cityName: 'South Ethiopia', name: 'Dilla' },
  { id: 208, city_id: 15, cityName: 'South Ethiopia', name: 'Sawla' },
  { id: 209, city_id: 15, cityName: 'South Ethiopia', name: 'Konso' },
  { id: 210, city_id: 15, cityName: 'South Ethiopia', name: 'Karat' },
  { id: 211, city_id: 15, cityName: 'South Ethiopia', name: 'Turmi' },
  { id: 212, city_id: 15, cityName: 'South Ethiopia', name: 'Omorate' },
  { id: 213, city_id: 15, cityName: 'South Ethiopia', name: 'Dimeka' },
]

const allSubCitiesList = computed(() => {
  const byId = new Map()
  const byName = new Set()

  // 1. Real database records from allSubCities.value take highest priority
  if (Array.isArray(allSubCities.value) && allSubCities.value.length > 0) {
    allSubCities.value.forEach(item => {
      if (item && item.id != null) {
        const norm = (item.name || '').toLowerCase().trim().replace(/[^a-z0-9]/g, '')
        byId.set(String(item.id), item)
        if (norm) byName.add(norm)
      }
    })
  }

  // 2. Add fallback sub-cities only if neither their ID nor normalized name exists
  defaultSubCitiesFallback.forEach(item => {
    if (item && item.id != null) {
      const norm = (item.name || '').toLowerCase().trim().replace(/[^a-z0-9]/g, '')
      const idKey = String(item.id)
      if (!byId.has(idKey) && (!norm || !byName.has(norm))) {
        byId.set(idKey, item)
        if (norm) byName.add(norm)
      }
    }
  })

  return Array.from(byId.values())
})

// Sub-cities for the currently selected city
const currentCitySubCities = computed(() => {
  if (!filters.city_id) return []
  return allSubCitiesList.value.filter(sc => String(sc.city_id) === String(filters.city_id))
})

// Grouped sub-cities for when no city is selected
const groupedSubCities = computed(() => {
  const groups = {}
  allSubCitiesList.value.forEach(sc => {
    const cName = sc.cityName || getCityName(sc.city_id) || 'Ethiopia'
    if (!groups[cName]) {
      groups[cName] = { cityName: cName, subCities: [] }
    }
    groups[cName].subCities.push(sc)
  })
  return Object.values(groups)
})

const getSubCityName = (scId) => {
  const sc = allSubCitiesList.value.find(item => String(item.id) === String(scId) || item.slug === scId)
  return sc ? sc.name : 'Sub-City'
}

const defaultAmenitiesFallback = [
  { id: 1, name: 'WiFi / Internet', slug: 'wifi' },
  { id: 23, name: 'Backup Generator', slug: 'generator' },
  { id: 15, name: 'Parking Space', slug: 'parking' },
  { id: 25, name: 'Water Tank (Reserve)', slug: 'water-tank' },
  { id: 18, name: 'CCTV & Security', slug: 'cctv' },
  { id: 4, name: 'Fully Furnished', slug: 'fully-furnished' },
  { id: 11, name: 'Swimming Pool', slug: 'swimming-pool' },
  { id: 16, name: 'Fitness Gym', slug: 'gym' },
  { id: 13, name: 'Balcony / Terrace', slug: 'balcony' },
  { id: 27, name: 'Elevator / Lift', slug: 'elevator' },
]

const availableAmenities = computed(() => {
  if (amenities.value && amenities.value.length > 0) {
    return amenities.value
  }
  return defaultAmenitiesFallback
})

const getCityName = (cityId) => {
  const c = cities.value.find(item => String(item.id) === String(cityId))
  return c ? c.name : 'City'
}

const getAmenityName = (id) => {
  const am = availableAmenities.value.find(item => String(item.id) === String(id) || item.slug === id)
  return am ? am.name : 'Amenity'
}

const activeFilterCount = computed(() => {
  let count = 0
  if (filters.q) count++
  if (filters.listing_type) count++
  if (filters.type) count++
  if (filters.city_id) count++
  if (filters.sub_city_id || (townInput && townInput.value)) count++
  if (filters.amenity_id) count++
  if (filters.min_price) count++
  if (filters.max_price) count++
  if (filters.bedrooms) count++
  if (filters.bathrooms) count++
  return count
})

const pageTitle = computed(() => {
  if (filters.listing_type === 'sale') return 'Properties for Sale'
  if (filters.listing_type === 'rent') return 'Properties for Rent'
  if (filters.listing_type === 'short_rent') return 'Short Stay Rentals'
  if (filters.type) return `${filters.type.charAt(0).toUpperCase() + filters.type.slice(1)} Properties`
  return 'Explore All Properties'
})

const pageSubtitle = computed(() => {
  if (pagination.total > 0) {
    return `Showing ${properties.value.length} of ${pagination.total} verified property listings across Ethiopia.`
  }
  return 'Discover verified apartments, houses, villas, and commercial real estate across Ethiopia.'
})

const setListingType = (type) => {
  filters.listing_type = type
  applyFilters(1)
}

const setBedrooms = (beds) => {
  filters.bedrooms = filters.bedrooms === beds ? null : beds
  applyFilters(1)
}

const setBathrooms = (baths) => {
  filters.bathrooms = filters.bathrooms === baths ? null : baths
  applyFilters(1)
}

const townSuggestions = computed(() => {
  // Always include all towns across all Ethiopian regions so the dropdown list has every town!
  return allSubCitiesList.value
})

const normalizeTown = (str) => {
  return (str || '')
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]/g, '')
}

// Collapses repeated consecutive letters: e.g. "assossa" -> "asosa"
const collapseRepeat = (str) => {
  return (str || '').replace(/(.)\1+/g, '$1')
}

const findMatchingTown = (query) => {
  if (!query || !query.trim()) return null
  const cleanQ = normalizeTown(query)
  if (!cleanQ) return null
  const collapsedQ = collapseRepeat(cleanQ)

  const aliasMap = {
    assossa: 'asosa',
    assosa: 'asosa',
    asosa: 'asosa',
    benishangul: 'asosa',
    bahirdar: 'bahir dar',
    bahir: 'bahir dar',
    gonder: 'gondar',
    gondar: 'gondar',
    awassa: 'hawassa',
    hawasa: 'hawassa',
    hawwassa: 'hawassa',
    hawassa: 'hawassa',
    nazret: 'adama (nazret)',
    nazreth: 'adama (nazret)',
    adama: 'adama (nazret)',
    debrezeit: 'bishoftu (debre zeyit)',
    debrezeyit: 'bishoftu (debre zeyit)',
    bishoftu: 'bishoftu (debre zeyit)',
    mekele: 'mekelle',
    mekelle: 'mekelle',
    adwa: 'adwa',
    jijiga: 'jigjiga',
    jigjiga: 'jigjiga',
    diredawa: 'dire dawa city',
    samara: 'semera',
    semera: 'semera',
    harrar: 'harar',
    harar: 'harar',
    debrebirhan: 'debre birhan',
    debremarkos: 'debre markos',
    arbaminch: 'arba minch',
    sodo: 'wolaita sodo',
    wolaitasodo: 'wolaita sodo',
    bonga: 'bonga',
    mizan: 'mizan teferi',
    mizanteferi: 'mizan teferi',
    hosanna: 'hosanna',
    hossana: 'hosanna',
    butajira: 'butajira',
    welkite: 'welkite',
    jinka: 'jinka',
    dilla: 'dilla',
    gambela: 'gambela city',
    gambella: 'gambela city',
    pawe: 'pawe',
    gilgelbeles: 'gilgel beles',
    bambasi: 'bambasi',
    kamashi: 'kamashi',
    kurmuk: 'kurmuk',
    addisketema: 'addis ketema',
    akaki: 'akaki kaliti',
    kaliti: 'akaki kaliti',
    lemikura: 'lemi kura',
    nifassilk: 'nifas silk-lafto',
    kolfekeranio: 'kolfe keranio',
  }

  const mappedName = aliasMap[cleanQ] || aliasMap[collapsedQ]

  return allSubCitiesList.value.find(sc => {
    const scClean = normalizeTown(sc.name)
    const scCollapsed = collapseRepeat(scClean)

    if (scClean === cleanQ || scCollapsed === collapsedQ) return true
    if (mappedName && (scClean === normalizeTown(mappedName) || scCollapsed === collapseRepeat(normalizeTown(mappedName)))) return true
    if (scClean.startsWith(cleanQ) || cleanQ.startsWith(scClean)) return true
    if (scCollapsed.startsWith(collapsedQ) || collapsedQ.startsWith(scCollapsed)) return true
    return false
  })
}

let townSearchTimer = null

// When typing in Town field: match and auto-select parent City / Region!
const handleTownInput = () => {
  const query = townInput.value.trim()
  if (!query) {
    filters.sub_city_id = ''
    clearTimeout(townSearchTimer)
    townSearchTimer = setTimeout(() => {
      applyFilters(1)
    }, 400)
    return
  }

  const matched = findMatchingTown(query)
  if (matched) {
    filters.sub_city_id = matched.id
    if (matched.city_id) {
      const foundCity = cities.value.find(c => String(c.id) === String(matched.city_id) || c.slug === String(matched.cityName).toLowerCase().replace(/[^a-z0-9]/g, '-'))
      const targetCityId = foundCity ? foundCity.id : matched.city_id
      if (String(filters.city_id) !== String(targetCityId)) {
        filters.city_id = targetCityId
        fetchSubCities(targetCityId)
      }
    }
  } else {
    filters.sub_city_id = ''
  }

  clearTimeout(townSearchTimer)
  townSearchTimer = setTimeout(() => {
    applyFilters(1)
  }, 500)
}

const handleTownChange = () => {
  clearTimeout(townSearchTimer)
  const query = townInput.value.trim()
  if (query) {
    const matched = findMatchingTown(query)
    if (matched) {
      filters.sub_city_id = matched.id
      townInput.value = matched.name
      if (matched.city_id) {
        const foundCity = cities.value.find(c => String(c.id) === String(matched.city_id) || c.slug === String(matched.cityName).toLowerCase().replace(/[^a-z0-9]/g, '-'))
        const targetCityId = foundCity ? foundCity.id : matched.city_id
        if (String(filters.city_id) !== String(targetCityId)) {
          filters.city_id = targetCityId
          fetchSubCities(targetCityId)
        }
      }
    }
  }
  applyFilters(1)
}

const clearTownInput = () => {
  clearTimeout(townSearchTimer)
  townInput.value = ''
  filters.sub_city_id = ''
  applyFilters(1)
}

// When sub-city is selected: auto-select its parent City / Region!
const onSubCityChange = async () => {
  if (filters.sub_city_id) {
    const sc = allSubCitiesList.value.find(item => String(item.id) === String(filters.sub_city_id) || item.slug === filters.sub_city_id)
    if (sc) {
      townInput.value = sc.name
      if (sc.city_id) {
        filters.city_id = sc.city_id
        await fetchSubCities(sc.city_id)
      }
    }
  }
  applyFilters(1)
}

const onCityChange = async () => {
  if (filters.city_id) {
    await fetchSubCities(filters.city_id)
    // If current town doesn't belong to selected city, clear townInput
    const currentSc = allSubCitiesList.value.find(item => String(item.id) === String(filters.sub_city_id))
    if (currentSc && String(currentSc.city_id) !== String(filters.city_id)) {
      filters.sub_city_id = ''
      townInput.value = ''
    }
  }
  applyFilters(1)
}

const fetchCities = async () => {
  try {
    const res = await locationService.getCities()
    if (res && res.success && Array.isArray(res.data)) {
      cities.value = res.data
    } else if (Array.isArray(res)) {
      cities.value = res
    }
  } catch (err) {
    console.warn('Could not load cities:', err)
  }
}

const fetchSubCities = async (cityId) => {
  try {
    const res = await locationService.getSubCities(cityId)
    if (res && res.success && Array.isArray(res.data)) {
      subCities.value = res.data
    } else if (Array.isArray(res)) {
      subCities.value = res
    }
  } catch (err) {
    subCities.value = []
  }
}

const fetchAllSubCities = async () => {
  try {
    const res = await locationService.getAllSubCities()
    if (Array.isArray(res) && res.length > 0) {
      allSubCities.value = res
    }
  } catch (err) {
    console.warn('Could not load all subcities:', err)
  }
}

const fetchAmenities = async () => {
  try {
    const res = await propertyService.getAmenities()
    if (res && res.success && Array.isArray(res.data)) {
      amenities.value = res.data
    } else if (Array.isArray(res)) {
      amenities.value = res
    } else if (res && res.data && Array.isArray(res.data.data)) {
      amenities.value = res.data.data
    }
  } catch (err) {
    console.warn('Could not load amenities:', err)
  }
}

const initFiltersFromRoute = () => {
  const q = route.query
  filters.q = q.q || ''
  filters.listing_type = q.listing_type || (q.type === 'sale' ? 'sale' : q.type === 'rent' ? 'rent' : q.type === 'short-stay' || q.type === 'short_rent' ? 'short_rent' : '')
  let rawType = q.type || q.property_type || q.propertyType || ''
  if (rawType && rawType.toLowerCase() === 'condominium') rawType = 'condo'
  filters.type = rawType
  filters.city_id = q.city_id || ''
  filters.sub_city_id = q.sub_city_id || ''

  if (q.town) {
    townInput.value = q.town
  } else if (q.location) {
    townInput.value = q.location
    if (!filters.q) filters.q = q.location
  } else if (filters.sub_city_id) {
    const sc = allSubCitiesList.value.find(item => String(item.id) === String(filters.sub_city_id) || item.slug === filters.sub_city_id)
    if (sc) {
      townInput.value = sc.name
    }
  } else if (filters.q) {
    townInput.value = filters.q
  } else {
    townInput.value = ''
  }

  filters.amenity_id = q.amenity || q.amenity_id || q.amenities || ''
  filters.user_id = q.user_id || q.agent_id || q.owner_id || ''
  filters.min_price = q.min_price ? Number(q.min_price) : null
  filters.max_price = q.max_price ? Number(q.max_price) : null
  filters.bedrooms = q.bedrooms ? Number(q.bedrooms) : null
  filters.bathrooms = q.bathrooms ? Number(q.bathrooms) : null
  filters.sort = q.sort || 'latest'
  filters.page = q.page ? Number(q.page) : 1
}

const syncRouteWithFilters = () => {
  const query = {}
  if (filters.q) query.q = filters.q
  if (filters.listing_type) query.listing_type = filters.listing_type
  if (filters.type) query.type = filters.type
  if (filters.city_id) query.city_id = filters.city_id
  if (filters.sub_city_id) query.sub_city_id = filters.sub_city_id
  if (townInput.value && !filters.sub_city_id) query.town = townInput.value
  if (filters.amenity_id) query.amenity = filters.amenity_id
  if (filters.user_id) query.user_id = filters.user_id
  if (filters.min_price) query.min_price = filters.min_price
  if (filters.max_price) query.max_price = filters.max_price
  if (filters.bedrooms) query.bedrooms = filters.bedrooms
  if (filters.bathrooms) query.bathrooms = filters.bathrooms
  if (filters.sort && filters.sort !== 'latest') query.sort = filters.sort
  if (filters.page && filters.page > 1) query.page = filters.page

  router.replace({ path: '/properties', query })
}

const fetchProperties = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const params = {
      page: filters.page,
      per_page: pagination.per_page,
    }

    if (filters.q) params.q = filters.q
    if (filters.listing_type) params.listing_type = filters.listing_type
    if (filters.type) params.type = filters.type
    if (filters.city_id) params.city_id = filters.city_id
    if (filters.user_id) params.user_id = filters.user_id
    if (filters.sub_city_id) {
      params.sub_city_id = filters.sub_city_id
    }
    if (townInput.value.trim()) {
      params.town = townInput.value.trim()
    }
    if (filters.amenity_id) params.amenities = [filters.amenity_id]
    if (filters.min_price) params.min_price = filters.min_price
    if (filters.max_price) params.max_price = filters.max_price
    if (filters.bedrooms) params.bedrooms = filters.bedrooms
    if (filters.bathrooms) params.bathrooms = filters.bathrooms
    if (filters.sort) params.sort = filters.sort

    const res = await searchService.search(params)

    if (res && res.success && res.data) {
      if (Array.isArray(res.data)) {
        properties.value = res.data
        pagination.total = res.meta?.total || res.data.length
        pagination.current_page = res.meta?.current_page || 1
        pagination.last_page = res.meta?.last_page || 1
      } else if (res.data.data && Array.isArray(res.data.data)) {
        properties.value = res.data.data
        pagination.total = res.data.total || res.data.data.length
        pagination.current_page = res.data.current_page || 1
        pagination.last_page = res.data.last_page || 1
      } else {
        properties.value = []
      }
    } else if (res && Array.isArray(res.data)) {
      properties.value = res.data
    } else {
      properties.value = []
    }
  } catch (err) {
    errorMessage.value = err.message || 'Unable to connect to the properties catalog.'
    properties.value = []
  } finally {
    isLoading.value = false
  }
}

const applyFilters = (page = 1) => {
  selectedPropertyId.value = null
  filters.page = page
  syncRouteWithFilters()
  fetchProperties()
}

const resetFilters = () => {
  selectedPropertyId.value = null
  filters.q = ''
  filters.listing_type = ''
  filters.type = ''
  filters.city_id = ''
  filters.sub_city_id = ''
  townInput.value = ''
  filters.amenity_id = ''
  filters.min_price = null
  filters.max_price = null
  filters.bedrooms = null
  filters.bathrooms = null
  filters.sort = 'latest'
  filters.page = 1
  syncRouteWithFilters()
  fetchProperties()
}

const onPageChange = (newPage) => {
  filters.page = newPage
  syncRouteWithFilters()
  fetchProperties()
  if (propertiesFeedRef.value) {
    propertiesFeedRef.value.scrollTo({ top: 0, behavior: 'smooth' })
  } else {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

watch(() => route.query, () => {
  initFiltersFromRoute()
  fetchProperties()
})

onMounted(async () => {
  initFiltersFromRoute()
  await Promise.all([fetchCities(), fetchAmenities(), fetchAllSubCities()])
  if (filters.city_id) {
    await fetchSubCities(filters.city_id)
  }
  await fetchProperties()
})
</script>

<style scoped>
.filter-sidebar-scroll {
  scrollbar-width: thin;
  scrollbar-color: rgba(156, 163, 175, 0.35) transparent;
}
.filter-sidebar-scroll::-webkit-scrollbar {
  width: 5px;
}
.filter-sidebar-scroll::-webkit-scrollbar-track {
  background: transparent;
}
.filter-sidebar-scroll::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.35);
  border-radius: 9999px;
}
.filter-sidebar-scroll::-webkit-scrollbar-thumb:hover {
  background-color: rgba(16, 185, 129, 0.7);
}
</style>