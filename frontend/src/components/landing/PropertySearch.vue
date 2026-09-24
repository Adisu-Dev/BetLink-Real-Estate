<template>
  <!-- Search container -->
  <section
    class="relative z-20 w-full transition-all my-2"
    aria-label="Property search"
  >
    <div class="w-full">

      <!-- ======================================================== -->
      <!-- LISTING TYPE TABS (On top of search bar, Zillow style)   -->
      <!-- ======================================================== -->
      <div class="flex items-center justify-center sm:justify-start gap-1.5 sm:gap-2.5 mb-3">
        <button
          v-for="item in listingTypes"
          :key="item.value"
          type="button"
          class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-bold transition-all shadow-md cursor-pointer border"
          :class="searchForm.listingType === item.value 
            ? 'bg-white text-slate-900 border-white shadow-xl scale-105 ring-2 ring-white/50' 
            : 'bg-black/50 hover:bg-black/70 text-white/90 border-white/20 backdrop-blur-md'"
          @click="selectListingType(item.value)"
        >
          {{ item.label }}
        </button>
      </div>

      <!-- ======================================================== -->
      <!-- 1. MOBILE SEARCH EXPERIENCE (Zillow Mobile Style)       -->
      <!-- Single clean capsule search bar + quick filter pills     -->
      <!-- ======================================================== -->
      <div class="md:hidden">
        <!-- Single Capsule Search Bar -->
        <div 
          :class="[
            'bg-white dark:bg-slate-900 rounded-2xl sm:rounded-full shadow-2xl shadow-black/40 border p-1.5 flex items-center transition-all duration-200',
            isShaking ? 'border-rose-500 ring-2 ring-rose-500/40 animate-shake' : 'border-white/90 dark:border-slate-700'
          ]"
        >
          <div class="flex items-center flex-1 min-w-0 pl-3 pr-1 py-1">
            <svg class="w-5 h-5 text-slate-400 dark:text-slate-400 shrink-0 mr-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <input
              id="hero-search-location-mobile"
              v-model="searchForm.location"
              type="text"
              :placeholder="t('search.enter_location', 'Addis Ababa (Default)')"
              autocomplete="off"
              class="w-full bg-transparent border-none outline-none text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 font-semibold focus:ring-0 p-0 truncate"
              @input="handleLocationInput"
              @focus="handleLocationFocus"
              @keyup.enter="handleSearch"
            />
            <button
              v-if="searchForm.location"
              type="button"
              @click.stop="clearLocation"
              class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer"
              aria-label="Clear location"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Search Icon Button -->
          <button
            type="button"
            @click="handleSearch"
            class="w-10 h-10 rounded-xl sm:rounded-full bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 flex items-center justify-center shrink-0 shadow-md transition-transform active:scale-95 cursor-pointer ml-1"
            :aria-label="t('search.search_btn', 'Search properties')"
          >
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
            </svg>
          </button>
        </div>

        <!-- Search Bar (Property & Price) -->
        <div class="grid grid-cols-2 gap-2 mt-2.5 px-0.5">
          <button
            type="button"
            class="h-9 px-2 rounded-xl text-xs font-bold transition-all truncate flex items-center justify-between shadow-sm border cursor-pointer"
            :class="searchForm.propertyType 
              ? 'bg-white text-slate-900 border-white shadow-md' 
              : 'bg-black/40 backdrop-blur-md text-white border-white/20 hover:bg-black/60'"
            @click="toggleDropdown('propertyType')"
          >
            <span class="truncate">{{ searchForm.propertyType ? formatTypeLabel(searchForm.propertyType) : t('search.property', 'Property') }}</span>
            <svg class="w-3 h-3 ml-0.5 shrink-0 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
          </button>

          <button
            type="button"
            class="h-9 px-2 rounded-xl text-xs font-bold transition-all truncate flex items-center justify-between shadow-sm border cursor-pointer"
            :class="(searchForm.minPrice || searchForm.maxPrice) 
              ? 'bg-white text-slate-900 border-white shadow-md' 
              : 'bg-black/40 backdrop-blur-md text-white border-white/20 hover:bg-black/60'"
            @click="toggleDropdown('budget')"
          >
            <span class="truncate">{{ (searchForm.minPrice || searchForm.maxPrice) ? formatBudgetLabel(searchForm.minPrice, searchForm.maxPrice) : t('search.price', 'Price') }}</span>
            <svg class="w-3 h-3 ml-0.5 shrink-0 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
          </button>
        </div>

        <!-- Reset Button if any filter active -->
        <div 
          v-if="searchForm.listingType || searchForm.propertyType || searchForm.minPrice || searchForm.maxPrice || searchForm.location"
          class="flex justify-center mt-2"
        >
          <button
            type="button"
            class="py-1 px-3 rounded-full text-[11px] font-bold transition-all cursor-pointer bg-rose-500/85 hover:bg-rose-600 backdrop-blur-md text-white shadow-sm"
            @click="resetAllFilters"
          >
            ✕ {{ t('search.reset_filters', 'Reset Filters') }}
          </button>
        </div>

        <!-- ======================================================== -->
        <!-- MOBILE INLINE DROPDOWN PANELS (100% Responsive, w-full)  -->
        <!-- ======================================================== -->

        <div 
          v-if="openDropdown === 'propertyType'"
          class="mt-2.5 w-full bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200/90 dark:border-slate-800 p-3 z-30 animate-fadeIn"
          @click.stop
        >
          <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100 dark:border-slate-800">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ t('search.select_property', 'Select Property') }}</span>
            <button type="button" @click="openDropdown = null" class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 text-xs font-bold flex items-center justify-center cursor-pointer">✕</button>
          </div>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-60 overflow-y-auto pr-1">
            <button
              v-for="type in propertyTypes"
              :key="type.value"
              type="button"
              class="h-10 px-3 rounded-xl text-xs font-bold flex items-center justify-between transition-colors cursor-pointer border"
              :class="searchForm.propertyType === type.value 
                ? 'bg-slate-900 text-white border-slate-900 dark:bg-white dark:text-slate-900 dark:border-white shadow-sm' 
                : 'bg-slate-50 dark:bg-slate-800/70 text-slate-700 dark:text-slate-300 border-slate-200/60 dark:border-slate-700/60 hover:bg-slate-100 dark:hover:bg-slate-800'"
              @click="selectPropertyType(type.value)"
            >
              <span class="truncate">{{ type.label }}</span>
              <span v-if="searchForm.propertyType === type.value" class="text-xs font-bold shrink-0 ml-1">✓</span>
            </button>
          </div>
        </div>

        <!-- 2. Mobile Price Range Panel -->
        <div 
          v-if="openDropdown === 'budget'"
          class="mt-2.5 w-full bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200/90 dark:border-slate-800 p-3.5 z-30 animate-fadeIn"
          @click.stop
        >
          <div class="flex items-center justify-between pb-2 mb-2.5 border-b border-slate-100 dark:border-slate-800">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ t('search.price_presets', 'Price Presets (ETB)') }}</span>
            <button type="button" @click="openDropdown = null" class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 text-xs font-bold flex items-center justify-center cursor-pointer">✕</button>
          </div>
          <!-- Presets 2-column grid -->
          <div class="grid grid-cols-2 gap-2 mb-3">
            <button
              v-for="preset in pricePresets"
              :key="preset.label"
              type="button"
              class="h-9 px-2.5 rounded-xl text-xs font-semibold flex items-center justify-between transition-colors cursor-pointer border"
              :class="searchForm.minPrice === preset.min && searchForm.maxPrice === preset.max 
                ? 'bg-slate-300 text-slate-900 border-slate-300 dark:bg-slate-700 dark:text-white dark:border-slate-600 font-bold shadow-xs' 
                : 'bg-slate-50 dark:bg-slate-800/70 text-slate-700 dark:text-slate-300 border-slate-200/60 dark:border-slate-700/60 hover:bg-slate-100 dark:hover:bg-slate-800'"
              @click="applyPreset(preset)"
            >
              <span class="truncate">{{ preset.label }}</span>
              <span v-if="searchForm.minPrice === preset.min && searchForm.maxPrice === preset.max" class="font-bold shrink-0 ml-1 text-xs text-slate-900 dark:text-white">✓</span>
            </button>
          </div>

          <!-- Custom min/max inputs -->
          <div class="grid grid-cols-2 gap-2 mb-3 pt-2 border-t border-slate-100 dark:border-slate-800">
            <div>
              <label class="text-[10px] text-slate-400 block mb-1 font-semibold uppercase">{{ t('search.min_price', 'Min Price') }}</label>
              <input
                v-model.number="searchForm.minPrice"
                type="number"
                placeholder="Min"
                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-slate-900 dark:text-white outline-none focus:border-slate-900 dark:focus:border-white"
              />
            </div>
            <div>
              <label class="text-[10px] text-slate-400 block mb-1 font-semibold uppercase">{{ t('search.max_price', 'Max Price') }}</label>
              <input
                v-model.number="searchForm.maxPrice"
                type="number"
                placeholder="Max"
                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-slate-900 dark:text-white outline-none focus:border-slate-900 dark:focus:border-white"
              />
            </div>
          </div>

          <div class="flex items-center justify-between pt-1">
            <button
              type="button"
              class="text-xs font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors cursor-pointer"
              @click="resetBudget"
            >
              {{ t('search.reset', 'Reset') }}
            </button>
            <button
              type="button"
              class="px-4 py-1.5 text-xs font-bold bg-slate-300 hover:bg-slate-400 text-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-white rounded-xl shadow-xs transition-colors cursor-pointer"
              @click="applyCustomBudget"
            >
              {{ t('search.set_price', 'Set Price') }}
            </button>
          </div>
        </div>


      </div>

      <!-- ======================================================== -->
      <!-- 2. DESKTOP SEARCH EXPERIENCE (Horizontal Cockpit)       -->
      <!-- 4-column luxury bar: Location, Property, Price, Search   -->
      <!-- ======================================================== -->
      <div 
        :class="[
          'hidden md:block bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl rounded-3xl shadow-2xl shadow-black/35 border p-3.5 transition-all duration-200',
          isShaking ? 'border-rose-500 ring-2 ring-rose-500/40 animate-shake' : 'border-white/60 dark:border-slate-700/60'
        ]"
      >
        <div class="grid grid-cols-12 divide-x divide-slate-100 dark:divide-slate-800 items-center">
          
          <!-- 1. Location (col-span-5 on desktop) -->
          <div class="col-span-5 p-2 sm:px-3 sm:py-2.5 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <label for="hero-search-location" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-0.5 truncate">
                  {{ t('search.location', 'LOCATION') }}
                </label>
                <div class="relative flex items-center">
                  <input
                    id="hero-search-location"
                    v-model="searchForm.location"
                    type="text"
                    :placeholder="t('search.enter_location', 'Addis Ababa (Default)')"
                    autocomplete="off"
                    class="w-full bg-transparent border-none outline-none text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 font-semibold focus:ring-0 p-0"
                    @input="handleLocationInput"
                    @focus="handleLocationFocus"
                    @keyup.enter="handleSearch"
                  />
                  <button
                    v-if="searchForm.location"
                    type="button"
                    @click.stop="clearLocation"
                    class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer"
                    aria-label="Clear location"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Live Auto-Search Results Dropdown (Desktop) -->
            <div
              v-if="showLiveResults && isUserActivelyTyping && liveSearchResults.length > 0"
              class="absolute top-full left-0 w-80 sm:w-96 mt-2 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 p-3 z-50 animate-fadeIn"
              @click.stop
            >
              <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100 dark:border-slate-800">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                  {{ t('search.live_results', 'Live Matches') }} ({{ liveSearchResults.length }})
                </span>
                <button
                  type="button"
                  @click="showLiveResults = false; isUserActivelyTyping = false"
                  class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                >
                  ✕
                </button>
              </div>

              <div class="space-y-1.5 max-h-72 overflow-y-auto">
                <div
                  v-for="p in liveSearchResults"
                  :key="p.id"
                  @click="goToProperty(p)"
                  class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/80 cursor-pointer transition-colors group"
                >
                  <img
                    :src="p.featured_image || p.image || (p.images && p.images[0] && p.images[0].url) || 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=200'"
                    :alt="p.title"
                    class="w-12 h-12 rounded-lg object-cover shrink-0 border border-slate-200/80 dark:border-slate-700"
                  />
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-emerald-600 transition-colors">
                      {{ getPropertyDisplayTitle(p) }}
                    </p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate font-medium">
                      {{ getPropertyCityName(p) }}
                    </p>
                    <p class="text-[11px] font-black text-slate-900 dark:text-slate-100 mt-0.5">
                      ETB {{ Number(p.price || 0).toLocaleString() }}
                    </p>
                  </div>
                </div>
              </div>

              <button
                type="button"
                @click="handleSearch"
                class="w-full mt-2 py-2 px-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white text-center transition-colors cursor-pointer"
              >
                {{ t('search.view_all', 'View All Results') }} →
              </button>
            </div>
          </div>

          <!-- 2. Property Type (col-span-3 on desktop) -->
          <div
            class="relative col-span-3 p-2 sm:px-3 sm:py-2.5 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer"
            ref="propertyTypeRef"
            @click="toggleDropdown('propertyType')"
          >
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-0.5 truncate">
                  {{ t('search.property', 'PROPERTY') }}
                </label>
                <div class="flex items-center justify-between text-xs sm:text-sm truncate">
                  <span v-if="searchForm.propertyType" class="font-semibold text-slate-900 dark:text-white truncate">
                    {{ formatTypeLabel(searchForm.propertyType) }}
                  </span>
                  <span v-else class="text-slate-400 font-normal truncate">
                    {{ t('search.select_here', 'Select here') }}
                  </span>
                  <svg class="w-4 h-4 text-slate-400 shrink-0 ml-1 transition-transform" :class="{ 'rotate-180': openDropdown === 'propertyType' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Property Type Menu (Desktop inline) -->
            <div
              v-if="!isMobile && openDropdown === 'propertyType'"
              class="absolute top-full left-0 w-48 mt-2 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 py-1.5 z-50 animate-fadeIn"
              @click.stop
            >
              <div class="space-y-1 p-1.5">
                <button
                  v-for="type in propertyTypes"
                  :key="type.value"
                  type="button"
                  class="w-full px-3 py-2 text-left text-xs sm:text-sm font-medium rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-between"
                  :class="searchForm.propertyType === type.value ? 'text-slate-900 dark:text-white font-bold bg-slate-100 dark:bg-slate-800' : 'text-slate-700 dark:text-slate-300'"
                  @click="selectPropertyType(type.value)"
                >
                  <span>{{ type.label }}</span>
                  <span v-if="searchForm.propertyType === type.value" class="text-emerald-500 font-bold">✓</span>
                </button>
              </div>
            </div>
          </div>

          <!-- 3. Price (col-span-2 on desktop) -->
          <div
            class="relative col-span-2 p-2 sm:px-3 sm:py-2.5 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer"
            ref="budgetRef"
            @click="toggleDropdown('budget')"
          >
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-0.5 truncate">
                  {{ t('search.price', 'PRICE') }}
                </label>
                <div class="flex items-center justify-between text-xs sm:text-sm truncate">
                  <span v-if="searchForm.minPrice || searchForm.maxPrice" class="font-semibold text-slate-900 dark:text-white truncate">
                    {{ formatBudgetLabel(searchForm.minPrice, searchForm.maxPrice) }}
                  </span>
                  <span v-else class="text-slate-400 font-normal truncate">
                    {{ t('search.select_here', 'Select here') }}
                  </span>
                  <svg class="w-4 h-4 text-slate-400 shrink-0 ml-1 transition-transform" :class="{ 'rotate-180': openDropdown === 'budget' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Price Popover (Desktop inline) -->
            <div
              v-if="!isMobile && openDropdown === 'budget'"
              class="absolute top-full right-0 w-80 mt-2 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 p-3.5 z-50 animate-fadeIn"
              @click.stop
            >
              <!-- Quick Presets in 2-Column Grid -->
              <div class="mb-3">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ t('search.price_presets', 'Price Presets (ETB)') }}</p>
                <div class="grid grid-cols-2 gap-1.5">
                  <button
                    v-for="preset in pricePresets"
                    :key="preset.label"
                    type="button"
                    class="px-2.5 py-1.5 text-left text-xs font-semibold rounded-xl flex items-center justify-between transition-colors cursor-pointer"
                    :class="searchForm.minPrice === preset.min && searchForm.maxPrice === preset.max 
                      ? 'bg-slate-300 text-slate-900 dark:bg-slate-700 dark:text-white font-bold' 
                      : 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200'"
                    @click="applyPreset(preset)"
                  >
                    <span class="truncate">{{ preset.label }}</span>
                    <span v-if="searchForm.minPrice === preset.min && searchForm.maxPrice === preset.max" class="font-bold shrink-0 ml-1 text-slate-900 dark:text-white">✓</span>
                  </button>
                </div>
              </div>

              <!-- Custom Inputs -->
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ t('search.custom_price', 'Custom Price (ETB)') }}</p>
              <div class="grid grid-cols-2 gap-2 mb-3">
                <div>
                  <label class="text-[10px] text-slate-500 block mb-1 font-semibold">{{ t('search.min_price', 'Min Price') }}</label>
                  <input
                    v-model.number="searchForm.minPrice"
                    type="number"
                    placeholder="e.g. 1,000,000"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-slate-900 dark:text-white outline-none focus:border-slate-900 dark:focus:border-white"
                  />
                </div>
                <div>
                  <label class="text-[10px] text-slate-500 block mb-1 font-semibold">{{ t('search.max_price', 'Max Price') }}</label>
                  <input
                    v-model.number="searchForm.maxPrice"
                    type="number"
                    placeholder="e.g. 15,000,000"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-slate-900 dark:text-white outline-none focus:border-slate-900 dark:focus:border-white"
                  />
                </div>
              </div>

              <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                <button
                  type="button"
                  class="text-xs font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors cursor-pointer"
                  @click="resetBudget"
                >
                  {{ t('search.reset', 'Reset') }}
                </button>
                <button
                  type="button"
                  class="px-4 py-1.5 text-xs font-bold bg-slate-300 hover:bg-slate-400 text-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-white rounded-xl shadow-xs transition-colors cursor-pointer"
                  @click="applyCustomBudget"
                >
                  {{ t('search.set_price', 'Set Price') }}
                </button>
              </div>
            </div>
          </div>

          <!-- 4. Search Action Button (col-span-2 on desktop) -->
          <div class="col-span-2 p-1 sm:p-1.5">
            <button
              type="button"
              @click="handleSearch"
              class="w-full h-11 sm:h-12 md:h-13 inline-flex items-center justify-center gap-2 px-3 rounded-xl sm:rounded-2xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 font-bold text-xs sm:text-sm shadow-md transition-all active:scale-98 cursor-pointer"
            >
              <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
              </svg>
              <span class="truncate">{{ t('search.search_btn', 'Search') }}</span>
            </button>
          </div>

        </div>
      </div>

      <!-- ======================================================== -->
      <!-- 4. SEARCH VALIDATION ALERT NOTIFICATION                  -->
      <!-- ======================================================== -->
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div 
          v-if="validationError" 
          class="mt-2.5 px-4 py-2 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl flex items-center justify-between gap-3 text-rose-700 dark:text-rose-300 text-xs font-semibold shadow-xs"
        >
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ validationError }}</span>
          </div>
          <button type="button" class="text-rose-400 hover:text-rose-600 text-sm font-bold" @click="validationError = ''">✕</button>
        </div>
      </transition>
    </div>
  </section>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useLanguage } from '../../composables/useLanguage'
import { locationService } from '../../services/locationService'
import { propertyService } from '../../services/propertyService'

defineProps({
  standalone: {
    type: Boolean,
    default: false
  }
})

const router = useRouter()
const route = useRoute()
const { t } = useLanguage()

const openDropdown = ref(null)
const showLocationMenu = ref(false)
const validationError = ref('')
const isShaking = ref(false)
const selectedRegionTab = ref('all')
const recentSearches = ref([])

const isMobile = ref(false)
const checkMobile = () => {
  if (typeof window !== 'undefined') {
    isMobile.value = window.innerWidth < 768
  }
}

const dynamicPropertyTypes = ref([])
const dynamicLocations = ref([])

// Listing Type Definitions (Tabs on top of search)
const listingTypes = computed(() => [
  { label: t('search.all', 'All'), value: '' },
  { label: t('search.for_sale', 'For Sale'), value: 'sale' },
  { label: t('search.for_rent', 'For Rent'), value: 'rent' },
  { label: t('search.short_stay', 'Short Stay'), value: 'short_rent' },
])

const defaultPropertyTypes = computed(() => [
  { label: t('search.all', 'All'), value: '' },
  { label: t('property_types.apartment', 'Apartment'), value: 'apartment' },
  { label: t('property_types.house', 'House'), value: 'house' },
  { label: t('property_types.condominium', 'Condo'), value: 'condominium' },
  { label: t('property_types.commercial', 'Commercial'), value: 'commercial' },
  { label: t('property_types.land', 'Land'), value: 'land' },
])

// Property Type Definitions (Dynamic with Fallback)
const propertyTypes = computed(() => {
  if (dynamicPropertyTypes.value.length > 0) {
    return dynamicPropertyTypes.value.map(tItem => ({
      ...tItem,
      label: formatTypeLabel(tItem.value)
    }))
  }
  return defaultPropertyTypes.value
})

// Region Tabs
const regionTabs = [
  { key: 'all', label: 'All Regions' },
  { key: 'addis', label: 'Addis Ababa' },
  { key: 'oromia', label: 'Oromia' },
  { key: 'amhara', label: 'Amhara' },
  { key: 'tigray', label: 'Tigray' },
  { key: 'sidama', label: 'Sidama' },
  { key: 'south', label: 'South / Central' },
  { key: 'diredawa', label: 'Dire Dawa' },
  { key: 'harari', label: 'Harari' },
  { key: 'somali', label: 'Somali' },
  { key: 'benishangul', label: 'Benishangul' },
  { key: 'afar', label: 'Afar' },
  { key: 'gambella', label: 'Gambella' },
]

const ethiopianLocations = [
  { name: 'Bole', region: 'Addis Ababa', tag: 'addis' },
  { name: 'CMC & Summit', region: 'Addis Ababa', tag: 'addis' },
  { name: 'Kazanchis & Kirkos', region: 'Addis Ababa', tag: 'addis' },
  { name: 'Sarbet & Old Airport', region: 'Addis Ababa', tag: 'addis' },
  { name: 'Ayat', region: 'Addis Ababa', tag: 'addis' },
  { name: 'Megenagna', region: 'Addis Ababa', tag: 'addis' },
  { name: 'Gerji', region: 'Addis Ababa', tag: 'addis' },
  { name: 'Yeka & Kotebe', region: 'Addis Ababa', tag: 'addis' },
  { name: 'Lideta & Piassa', region: 'Addis Ababa', tag: 'addis' },
  { name: 'Nifas Silk & Lafto', region: 'Addis Ababa', tag: 'addis' },
  { name: 'Kolfe Keranio', region: 'Addis Ababa', tag: 'addis' },
  { name: 'Akaki Kality', region: 'Addis Ababa', tag: 'addis' },
  { name: 'Lemi Kura', region: 'Addis Ababa', tag: 'addis' },
  { name: 'Gullele', region: 'Addis Ababa', tag: 'addis' },
  { name: 'Addis Ketema & Merkato', region: 'Addis Ababa', tag: 'addis' },

 
  { name: 'Adama (Nazret)', region: 'Oromia', tag: 'oromia' },
  { name: 'Bishoftu (Debre Zeyit)', region: 'Oromia', tag: 'oromia' },
  { name: 'Jimma', region: 'Oromia', tag: 'oromia' },
  { name: 'Shashemene', region: 'Oromia', tag: 'oromia' },
  { name: 'Sebeta', region: 'Oromia', tag: 'oromia' },
  { name: 'Burayu', region: 'Oromia', tag: 'oromia' },
  { name: 'Dukem', region: 'Oromia', tag: 'oromia' },
  { name: 'Batu (Ziway)', region: 'Oromia', tag: 'oromia' },
  { name: 'Ambo', region: 'Oromia', tag: 'oromia' },
  { name: 'Nekemte', region: 'Oromia', tag: 'oromia' },
  { name: 'Woliso', region: 'Oromia', tag: 'oromia' },
  { name: 'Mojo', region: 'Oromia', tag: 'oromia' },
  { name: 'Legetafo', region: 'Oromia', tag: 'oromia' },
  { name: 'Sendafa', region: 'Oromia', tag: 'oromia' },
  { name: 'Asella', region: 'Oromia', tag: 'oromia' },
  { name: 'Robe (Bale)', region: 'Oromia', tag: 'oromia' },

  { name: 'Bahir Dar', region: 'Amhara', tag: 'amhara' },
  { name: 'Gondar', region: 'Amhara', tag: 'amhara' },
  { name: 'Dessie', region: 'Amhara', tag: 'amhara' },
  { name: 'Debre Birhan', region: 'Amhara', tag: 'amhara' },
  { name: 'Debre Markos', region: 'Amhara', tag: 'amhara' },
  { name: 'Kombolcha', region: 'Amhara', tag: 'amhara' },
  { name: 'Woldiya', region: 'Amhara', tag: 'amhara' },
  { name: 'Lalibela', region: 'Amhara', tag: 'amhara' },
  { name: 'Injibara', region: 'Amhara', tag: 'amhara' },
  { name: 'Motta', region: 'Amhara', tag: 'amhara' },
  { name: 'Shewa Robit', region: 'Amhara', tag: 'amhara' },
  { name: 'Kobo', region: 'Amhara', tag: 'amhara' },

  { name: 'Mekelle', region: 'Tigray', tag: 'tigray' },
  { name: 'Axum', region: 'Tigray', tag: 'tigray' },
  { name: 'Adigrat', region: 'Tigray', tag: 'tigray' },
  { name: 'Shire (Inda Selassie)', region: 'Tigray', tag: 'tigray' },
  { name: 'Alamata', region: 'Tigray', tag: 'tigray' },
  { name: 'Wukro', region: 'Tigray', tag: 'tigray' },
  { name: 'Maychew', region: 'Tigray', tag: 'tigray' },
  { name: 'Humera', region: 'Tigray', tag: 'tigray' },
  { name: 'Adwa', region: 'Tigray', tag: 'tigray' },
  { name: 'Abi Adi', region: 'Tigray', tag: 'tigray' },

  { name: 'Hawassa', region: 'Sidama', tag: 'sidama' },
  { name: 'Yirgalem', region: 'Sidama', tag: 'sidama' },
  { name: 'Aleta Wondo', region: 'Sidama', tag: 'sidama' },
  { name: 'Leku', region: 'Sidama', tag: 'sidama' },
  { name: 'Wondo Genet', region: 'Sidama', tag: 'sidama' },
  { name: 'Bona', region: 'Sidama', tag: 'sidama' },
  { name: 'Daye', region: 'Sidama', tag: 'sidama' },
  { name: 'Hula', region: 'Sidama', tag: 'sidama' },
  { name: 'Chuko', region: 'Sidama', tag: 'sidama' },
  { name: 'Bensa', region: 'Sidama', tag: 'sidama' },

  { name: 'Arba Minch', region: 'Central / South', tag: 'south' },
  { name: 'Dilla', region: 'Central / South', tag: 'south' },
  { name: 'Wolkite', region: 'Central / South', tag: 'south' },
  { name: 'Hosaena', region: 'Central / South', tag: 'south' },
  { name: 'Butajira', region: 'Central / South', tag: 'south' },
  { name: 'Sodo (Wolaita)', region: 'Central / South', tag: 'south' },
  { name: 'Jinka', region: 'Central / South', tag: 'south' },
  { name: 'Halaba Kulito', region: 'Central / South', tag: 'south' },
  { name: 'Bonga', region: 'Central / South', tag: 'south' },
  { name: 'Sawla', region: 'Central / South', tag: 'south' },
  { name: 'Durame', region: 'Central / South', tag: 'south' },
  { name: 'Mizan Teferi', region: 'Central / South', tag: 'south' },

  { name: 'Dire Dawa City', region: 'Dire Dawa', tag: 'diredawa' },
  { name: 'Sabian', region: 'Dire Dawa', tag: 'diredawa' },
  { name: 'Gende Kore', region: 'Dire Dawa', tag: 'diredawa' },
  { name: 'Melka Jebdu', region: 'Dire Dawa', tag: 'diredawa' },
  { name: 'Taiwan Area', region: 'Dire Dawa', tag: 'diredawa' },
  { name: 'Gende Gerada', region: 'Dire Dawa', tag: 'diredawa' },
  { name: 'Dechatu', region: 'Dire Dawa', tag: 'diredawa' },
  { name: 'Addis Ketema (Dire)', region: 'Dire Dawa', tag: 'diredawa' },
  { name: 'Shinile Gate', region: 'Dire Dawa', tag: 'diredawa' },
  { name: 'Kezira', region: 'Dire Dawa', tag: 'diredawa' },

  { name: 'Harar City', region: 'Harari', tag: 'harari' },
  { name: 'Jugol (Old City)', region: 'Harari', tag: 'harari' },
  { name: 'Shenkor', region: 'Harari', tag: 'harari' },
  { name: 'Abadir', region: 'Harari', tag: 'harari' },
  { name: 'Jin’Eala', region: 'Harari', tag: 'harari' },
  { name: 'Amir Nur', region: 'Harari', tag: 'harari' },
  { name: 'Hakim', region: 'Harari', tag: 'harari' },
  { name: 'Erer', region: 'Harari', tag: 'harari' },
  { name: 'Sofi', region: 'Harari', tag: 'harari' },
  { name: 'Dire Teyara', region: 'Harari', tag: 'harari' },

  { name: 'Jigjiga', region: 'Somali', tag: 'somali' },
  { name: 'Gode', region: 'Somali', tag: 'somali' },
  { name: 'Kebri Dahar', region: 'Somali', tag: 'somali' },
  { name: 'Degehabur', region: 'Somali', tag: 'somali' },
  { name: 'Warder', region: 'Somali', tag: 'somali' },
  { name: 'Kelafo', region: 'Somali', tag: 'somali' },
  { name: 'Filtu', region: 'Somali', tag: 'somali' },
  { name: 'Dolo Odo', region: 'Somali', tag: 'somali' },
  { name: 'Shinile (Somali)', region: 'Somali', tag: 'somali' },
  { name: 'Aware', region: 'Somali', tag: 'somali' },

  { name: 'Assosa', region: 'Benishangul-Gumuz', tag: 'benishangul' },
  { name: 'Bambasi', region: 'Benishangul-Gumuz', tag: 'benishangul' },
  { name: 'Kamashi', region: 'Benishangul-Gumuz', tag: 'benishangul' },
  { name: 'Gilgel Beles', region: 'Benishangul-Gumuz', tag: 'benishangul' },
  { name: 'Kurmuk', region: 'Benishangul-Gumuz', tag: 'benishangul' },
  { name: 'Menge', region: 'Benishangul-Gumuz', tag: 'benishangul' },
  { name: 'Oda', region: 'Benishangul-Gumuz', tag: 'benishangul' },
  { name: 'Bullen', region: 'Benishangul-Gumuz', tag: 'benishangul' },
  { name: 'Guba', region: 'Benishangul-Gumuz', tag: 'benishangul' },
  { name: 'Sherkole', region: 'Benishangul-Gumuz', tag: 'benishangul' },


  { name: 'Semera', region: 'Afar', tag: 'afar' },
  { name: 'Awash', region: 'Afar', tag: 'afar' },
  { name: 'Logiya', region: 'Afar', tag: 'afar' },
  { name: 'Asaita', region: 'Afar', tag: 'afar' },
  { name: 'Dubti', region: 'Afar', tag: 'afar' },
  { name: 'Mille', region: 'Afar', tag: 'afar' },
  { name: 'Gewane', region: 'Afar', tag: 'afar' },
  { name: 'Chifra', region: 'Afar', tag: 'afar' },
  { name: 'Aba’ala', region: 'Afar', tag: 'afar' },
  { name: 'Yalo', region: 'Afar', tag: 'afar' },

  { name: 'Gambella City', region: 'Gambella', tag: 'gambella' },
  { name: 'Itang', region: 'Gambella', tag: 'gambella' },
  { name: 'Abobo', region: 'Gambella', tag: 'gambella' },
  { name: 'Dimma', region: 'Gambella', tag: 'gambella' },
  { name: 'Pugnido', region: 'Gambella', tag: 'gambella' },
  { name: 'Gog', region: 'Gambella', tag: 'gambella' },
  { name: 'Lare', region: 'Gambella', tag: 'gambella' },
  { name: 'Jor', region: 'Gambella', tag: 'gambella' },
  { name: 'Jikawo', region: 'Gambella', tag: 'gambella' },
  { name: 'Wantawo', region: 'Gambella', tag: 'gambella' },
]

// Dynamically combined locations
const allLocations = computed(() => {
  if (dynamicLocations.value.length > 0) {
    const combined = [...dynamicLocations.value]
    ethiopianLocations.forEach(loc => {
      if (!combined.some(c => c.name.toLowerCase() === loc.name.toLowerCase())) {
        combined.push(loc)
      }
    })
    return combined
  }
  return ethiopianLocations
})

const defaultRegionsAndCities = [
  { name: 'Addis Ababa', region: 'Capital City' },
  { name: 'Oromia', region: 'Adama, Bishoftu, Jimma' },
  { name: 'Amhara', region: 'Bahir Dar, Gondar, Dessie' },
  { name: 'Sidama', region: 'Hawassa, Yirgalem' },
  { name: 'Dire Dawa', region: 'Charter City' },
  { name: 'Tigray', region: 'Mekelle, Axum' },
  { name: 'Somali', region: 'Jigjiga, Gode' },
  { name: 'Harari', region: 'Harar City' },
  { name: 'Central / South', region: 'Arba Minch, Wolkite' },
  { name: 'Afar', region: 'Semera, Awash' },
  { name: 'Benishangul-Gumuz', region: 'Assosa' },
  { name: 'Gambella', region: 'Gambella City' },
]

// location filter (all regions visible in 2 columns when open, filtered on typing)
const filteredLocations = computed(() => {
  const q = searchForm.location?.trim().toLowerCase() || ''

  if (!q) {
    return defaultRegionsAndCities
  }

  return allLocations.value.filter(loc => 
    loc.name.toLowerCase().includes(q) || 
    loc.region.toLowerCase().includes(q)
  ).slice(0, 12)
})

const pricePresets = [
  { label: '< 3M', min: null, max: 3000000 },
  { label: '3M - 8M', min: 3000000, max: 8000000 },
  { label: '8M - 20M', min: 8000000, max: 20000000 },
  { label: '20M - 50M', min: 20000000, max: 50000000 },
  { label: '50M+', min: 50000000, max: null }
]

const searchForm = reactive({
  location: '',
  listingType: '',
  propertyType: '',
  minPrice: null,
  maxPrice: null,
})

const liveSearchResults = ref([])
const isSearchingLive = ref(false)
const showLiveResults = ref(false)
const isUserActivelyTyping = ref(false)
let debounceTimer = null

const getPropertyDisplayTitle = (p) => {
  if (!p) return ''
  const slug = (p.slug || '').replace(/-\d+$/, '')
  const titleSlug = (p.title || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')
  return t(`property_titles.${p.slug}`, '') || t(`property_titles.${slug}`, '') || t(`property_titles.${titleSlug}`, '') || p.title || ''
}

const getPropertyCityName = (p) => {
  if (!p) return 'Addis Ababa'
  const city = p.address?.city || p.city
  if (typeof city === 'string') return city
  if (typeof city === 'object' && city) return city.name || city.slug || 'Addis Ababa'
  return p.location || 'Addis Ababa'
}

const goToProperty = (p) => {
  showLiveResults.value = false
  isUserActivelyTyping.value = false
  const target = p.slug || p.id
  router.push(`/properties/${target}`)
}

const handleLocationInput = () => {
  isUserActivelyTyping.value = true
  clearTimeout(debounceTimer)
  const q = searchForm.location?.trim() || ''
  if (q.length < 2) {
    liveSearchResults.value = []
    showLiveResults.value = false
    return
  }
  debounceTimer = setTimeout(() => {
    performLiveSearch(q)
  }, 350)
}

const handleLocationFocus = () => {
  if (isUserActivelyTyping.value && liveSearchResults.value.length > 0) {
    showLiveResults.value = true
  }
}

const clearLocation = () => {
  searchForm.location = ''
  liveSearchResults.value = []
  showLiveResults.value = false
  isUserActivelyTyping.value = false
}

const performLiveSearch = async (query) => {
  if (!query || query.trim().length < 2 || !isUserActivelyTyping.value) {
    liveSearchResults.value = []
    showLiveResults.value = false
    return
  }
  isSearchingLive.value = true
  try {
    const params = {
      q: query.trim(),
      per_page: 5
    }
    if (searchForm.listingType) params.listing_type = searchForm.listingType
    if (searchForm.propertyType) params.property_type = searchForm.propertyType

    const res = await propertyService.getProperties(params)
    const list = res?.data?.data || res?.data || (Array.isArray(res) ? res : [])
    liveSearchResults.value = list.slice(0, 5)
    showLiveResults.value = isUserActivelyTyping.value && liveSearchResults.value.length > 0
  } catch (err) {
    liveSearchResults.value = []
    showLiveResults.value = false
  } finally {
    isSearchingLive.value = false
  }
}

const toggleDropdown = (name) => {
  openDropdown.value = openDropdown.value === name ? null : name
  if (name !== 'location') showLocationMenu.value = false
  validationError.value = ''
}

const onLocationInput = () => {
  showLocationMenu.value = true
}

const openLocationDropdown = () => {
  showLocationMenu.value = true
  openDropdown.value = null
  validationError.value = ''
}

const toggleLocationDropdown = () => {
  showLocationMenu.value = !showLocationMenu.value
  if (showLocationMenu.value) openDropdown.value = null
  validationError.value = ''
}

// Fill Listing Type 
const selectListingType = (val) => {
  searchForm.listingType = val
  openDropdown.value = null
  validationError.value = ''
}

// Fill Property Type
const selectPropertyType = (val) => {
  searchForm.propertyType = val
  openDropdown.value = null
  validationError.value = ''
}

// Fill Location 
const selectLocation = (hub) => {
  searchForm.location = hub
  showLocationMenu.value = false
  validationError.value = ''
}

// Fill Budget 
const applyPreset = (preset) => {
  searchForm.minPrice = preset.min
  searchForm.maxPrice = preset.max
  openDropdown.value = null
  validationError.value = ''
}

const applyCustomBudget = () => {
  openDropdown.value = null
  validationError.value = ''
}

const resetBudget = () => {
  searchForm.minPrice = null
  searchForm.maxPrice = null
  openDropdown.value = null
}

const resetAllFilters = () => {
  searchForm.location = ''
  searchForm.listingType = ''
  searchForm.propertyType = ''
  searchForm.minPrice = null
  searchForm.maxPrice = null
  openDropdown.value = null
  showLocationMenu.value = false
  validationError.value = ''
}

const formatListingTypeLabel = (val) => {
  if (!val) return t('search.property', 'Type')
  const found = listingTypes.value.find(t => t.value === val)
  return found ? found.label : val
}

const formatTypeLabel = (val) => {
  if (!val) return t('search.property', 'Property')
  const slug = String(val).toLowerCase().replace(/[\s/_-]+/g, '_')
  const translated = t(`property_types.${slug}`, '') || t(`categories.${slug}`, '')
  if (translated) return translated
  const found = (propertyTypes.value || []).find(t => t.value === val)
  return found ? found.label : val
}

const formatBudgetLabel = (min, max) => {
  if (!min && !max) return t('search.price', 'Price')
  const formatM = (num) => {
    if (num >= 1000000) return `${(num / 1000000).toFixed(num % 1000000 === 0 ? 0 : 1)}M`
    if (num >= 1000) return `${(num / 1000).toFixed(0)}k`
    return num.toLocaleString()
  }
  if (min && max) return `${formatM(min)} - ${formatM(max)}`
  if (min) return `>${formatM(min)}`
  if (max) return `<${formatM(max)}`
  return t('search.price', 'Price')
}

// Execute search with strict validation
const handleSearch = () => {
  openDropdown.value = null
  showLocationMenu.value = false

  const hasLocation = Boolean(searchForm.location && searchForm.location.trim())
  const hasListingType = Boolean(searchForm.listingType)
  const hasPropertyType = Boolean(searchForm.propertyType)
  const hasMinPrice = searchForm.minPrice !== null && searchForm.minPrice !== '' && !isNaN(searchForm.minPrice)
  const hasMaxPrice = searchForm.maxPrice !== null && searchForm.maxPrice !== '' && !isNaN(searchForm.maxPrice)

  validationError.value = ''

  const query = {}
  if (hasLocation) {
    query.q = searchForm.location.trim()
  } else if (!hasListingType && !hasPropertyType && !hasMinPrice && !hasMaxPrice) {
    query.q = 'Addis Ababa'
  }
  if (hasListingType) {
    query.listing_type = searchForm.listingType
  }
  if (hasPropertyType) {
    query.property_type = searchForm.propertyType
  }
  if (hasMinPrice) {
    query.min_price = searchForm.minPrice
  }
  if (hasMaxPrice) {
    query.max_price = searchForm.maxPrice
  }

  const targetName = route.name === 'search' ? 'search' : 'properties'
  router.push({ name: targetName, query })
}

const handleClickOutside = (e) => {
  if (e.target.closest('.mobile-sheet-content')) return
  if (!e.target.closest('[aria-label="Property search"]')) {
    openDropdown.value = null
    showLocationMenu.value = false
    showLiveResults.value = false
    isUserActivelyTyping.value = false
  }
}


const syncWithRoute = () => {
  if (route.query.listing_type !== undefined) {
    searchForm.listingType = route.query.listing_type || ''
  } else if (route.query.type && ['sale', 'rent', 'short_rent'].includes(route.query.type)) {
    searchForm.listingType = route.query.type
  }
  if (route.query.property_type) {
    searchForm.propertyType = route.query.property_type
  }
  if (route.query.q || route.query.location) {
    searchForm.location = route.query.q || route.query.location
  }
  if (route.query.min_price) {
    searchForm.minPrice = Number(route.query.min_price)
  }
  if (route.query.max_price) {
    searchForm.maxPrice = Number(route.query.max_price)
  }
}

watch(() => route.query, () => {
  syncWithRoute()
})

const loadDynamicData = async () => {
  try {
    const [typesRes, citiesRes] = await Promise.allSettled([
      propertyService.getPropertyTypes(),
      locationService.getCities()
    ])
    if (typesRes.status === 'fulfilled' && typesRes.value) {
      const list = typesRes.value?.data || (Array.isArray(typesRes.value) ? typesRes.value : [])
      if (list.length > 0) {
        dynamicPropertyTypes.value = [
          { label: 'All', value: '' },
          ...list.map(t => {
            const raw = (t.name || '').trim().split(/[\s/]+/)[0]
            const word = raw ? raw.charAt(0).toUpperCase() + raw.slice(1).toLowerCase() : 'Property'
            return {
              label: word,
              value: t.slug || t.name.toLowerCase()
            }
          })
        ]
      }
    }
    if (citiesRes.status === 'fulfilled' && citiesRes.value) {
      const citiesList = citiesRes.value?.data || (Array.isArray(citiesRes.value) ? citiesRes.value : [])
      if (citiesList.length > 0) {
        dynamicLocations.value = citiesList.map(c => {
          const regName = c.region || c.state || c.name || ''
          const lower = regName.toLowerCase()
          let tag = 'all'
          if (lower.includes('addis')) tag = 'addis'
          else if (lower.includes('orom')) tag = 'oromia'
          else if (lower.includes('amh')) tag = 'amhara'
          else if (lower.includes('tig')) tag = 'tigray'
          else if (lower.includes('sid')) tag = 'sidama'
          else if (lower.includes('dire')) tag = 'diredawa'
          else if (lower.includes('harar')) tag = 'harari'
          else if (lower.includes('somal')) tag = 'somali'
          else if (lower.includes('afar')) tag = 'afar'
          else if (lower.includes('benish')) tag = 'benishangul'
          else if (lower.includes('gamb')) tag = 'gambella'
          else if (lower.includes('south') || lower.includes('central')) tag = 'south'
          return {
            name: c.name,
            region: c.region || c.name,
            tag
          }
        })
      }
    }
  } catch (err) {
    // Graceful fallback
  }
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
  document.addEventListener('click', handleClickOutside)
  syncWithRoute()
  loadDynamicData()
  showLiveResults.value = false
  isUserActivelyTyping.value = false
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-6px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(100%);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  20%, 60% { transform: translateX(-6px); }
  40%, 80% { transform: translateX(6px); }
}

.animate-fadeIn {
  animation: fadeIn 0.18s ease-out forwards;
}

.animate-slideUp {
  animation: slideUp 0.24s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.animate-shake {
  animation: shake 0.4s ease-in-out;
}

.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
