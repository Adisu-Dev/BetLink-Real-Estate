<template>
  <div :class="inline ? 'text-slate-900 dark:text-slate-100 transition-colors duration-200' : 'min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 py-6 lg:py-10 transition-colors duration-200'">
    <div :class="inline ? '' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8'">

      <!-- Loading skeleton -->
      <div v-if="isLoading" class="space-y-6 animate-pulse">
        <div class="h-6 w-48 bg-slate-200 dark:bg-slate-800 rounded-lg" />
        <div class="h-[380px] bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl" />
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="lg:col-span-2 space-y-6">
            <div class="h-32 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl" />
            <div class="h-48 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl" />
          </div>
          <div class="h-80 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl" />
        </div>
      </div>

      <!-- Error state -->
      <div v-else-if="errorMessage" class="text-center py-20 px-4 bg-white dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 rounded-2xl max-w-2xl mx-auto shadow-xs">
        <div class="w-14 h-14 rounded-full bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto mb-4">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Property Not Found or Unavailable</h2>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mb-6">{{ errorMessage }}</p>
        <div class="flex items-center justify-center gap-3">
          <button
            type="button"
            class="px-4 py-2 text-xs font-semibold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white transition-colors shadow-xs"
            @click="fetchPropertyDetails"
          >
            Try Again
          </button>
          <button
            type="button"
            @click="goBack"
            class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition-colors"
          >
            Go Back
          </button>
          <RouterLink
            :to="portalPropertiesRoute"
            class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-white/10 dark:hover:bg-white/20 transition-colors"
          >
            Browse Properties
          </RouterLink>
        </div>
      </div>

      <!-- Loaded content -->
      <div v-else-if="property" class="space-y-8">
        <!-- Navigation back button -->
        <div>
          <button
            type="button"
            @click="goBack"
            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors cursor-pointer group"
          >
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Back</span>
          </button>
        </div>

        <!-- Property Title and Quick Actions -->
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 pb-6 border-b border-slate-200/80 dark:border-slate-800">
          <div class="space-y-2">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">
              {{ property.title }}
            </h1>

            <div class="flex items-center text-xs sm:text-sm text-slate-500 dark:text-slate-400 gap-1.5 font-medium">
              <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>{{ locationText }}</span>
            </div>
          </div>

          <!-- Price & Actions -->
          <div class="flex flex-row md:flex-col md:items-end justify-between items-center gap-3">
            <div>
              <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                {{ formatPrice(property.price, property.currency || 'ETB') }}
                <span v-if="property.listing_type === 'rent'" class="text-xs sm:text-sm font-normal text-slate-500 dark:text-slate-400">/ month</span>
                <span v-else-if="property.listing_type === 'short_rent'" class="text-xs sm:text-sm font-normal text-slate-500 dark:text-slate-400">/ night</span>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <button
                type="button"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer"
                @click="toggleFavorite"
              >
                <svg class="w-4 h-4" :fill="isFavorite ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span>{{ isFavorite ? 'Saved' : 'Save' }}</span>
              </button>

              <button
                type="button"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer"
                @click="shareProperty"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
                <span>Share</span>
              </button>
            </div>
          </div>
        </div>

        <!-- ── Image Gallery Component ── -->
        <PropertyImageGallery :images="propertyImages" :title="property.title" />

        <!-- ── Details Layout ── -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left Column: Specs, Description, Amenities, Reviews -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Property Key Specs (Sleek Compact Inline Badges) -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs">
              <h2 class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider mb-3">Property Highlights</h2>
              <div class="flex flex-wrap gap-2.5 text-xs">
                <div v-if="property.bedrooms !== null && property.bedrooms !== undefined" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-950/70 rounded-xl border border-slate-200/70 dark:border-slate-800">
                  <span class="text-slate-500 dark:text-slate-400">Bedrooms:</span>
                  <span class="font-bold text-slate-900 dark:text-slate-100">{{ property.bedrooms }}</span>
                </div>
                <div v-if="property.bathrooms !== null && property.bathrooms !== undefined" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-950/70 rounded-xl border border-slate-200/70 dark:border-slate-800">
                  <span class="text-slate-500 dark:text-slate-400">Bathrooms:</span>
                  <span class="font-bold text-slate-900 dark:text-slate-100">{{ property.bathrooms }}</span>
                </div>
                <div v-if="property.area" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-950/70 rounded-xl border border-slate-200/70 dark:border-slate-800">
                  <span class="text-slate-500 dark:text-slate-400">Area:</span>
                  <span class="font-bold text-slate-900 dark:text-slate-100">{{ formatArea(property.area) }}</span>
                </div>
                <div v-if="property.furnished" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-950/70 rounded-xl border border-slate-200/70 dark:border-slate-800 capitalize">
                  <span class="text-slate-500 dark:text-slate-400">Furnishing:</span>
                  <span class="font-bold text-slate-900 dark:text-slate-100">{{ property.furnished.replace('_', ' ') }}</span>
                </div>
                <div v-if="property.parking_spaces !== null && property.parking_spaces !== undefined" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-950/70 rounded-xl border border-slate-200/70 dark:border-slate-800">
                  <span class="text-slate-500 dark:text-slate-400">Parking:</span>
                  <span class="font-bold text-slate-900 dark:text-slate-100">{{ property.parking_spaces }} Spaces</span>
                </div>
                <div v-if="property.floor_number !== null && property.floor_number !== undefined" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-950/70 rounded-xl border border-slate-200/70 dark:border-slate-800">
                  <span class="text-slate-500 dark:text-slate-400">Floor:</span>
                  <span class="font-bold text-slate-900 dark:text-slate-100">Floor {{ property.floor_number }}</span>
                </div>
                <div v-if="property.year_built" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-950/70 rounded-xl border border-slate-200/70 dark:border-slate-800">
                  <span class="text-slate-500 dark:text-slate-400">Year Built:</span>
                  <span class="font-bold text-slate-900 dark:text-slate-100">{{ property.year_built }}</span>
                </div>
                <div v-if="property.views_count" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-950/70 rounded-xl border border-slate-200/70 dark:border-slate-800">
                  <span class="text-slate-500 dark:text-slate-400">Views:</span>
                  <span class="font-bold text-slate-900 dark:text-slate-100">{{ property.views_count }}</span>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div v-if="property.description" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs">
              <h2 class="text-sm font-bold text-slate-900 dark:text-slate-200 uppercase tracking-wider mb-3">About This Property</h2>
              <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                {{ property.description }}
              </div>
            </div>

            <!-- Amenities -->
            <div v-if="property.amenities && property.amenities.length > 0" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs">
              <h2 class="text-sm font-bold text-slate-900 dark:text-slate-200 uppercase tracking-wider mb-4">Amenities & Features</h2>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div
                  v-for="amenity in property.amenities"
                  :key="amenity.id || amenity.name"
                  class="flex items-center gap-2 p-2.5 bg-slate-50 dark:bg-slate-950/60 rounded-xl border border-slate-200/60 dark:border-slate-800/60 text-xs text-slate-700 dark:text-slate-200"
                >
                  <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  <span>{{ amenity.name }}</span>
                </div>
              </div>
            </div>

            <!-- Additional Features (if key-value features exist) -->
            <div v-if="property.features && property.features.length > 0" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs">
              <h2 class="text-sm font-bold text-slate-900 dark:text-slate-200 uppercase tracking-wider mb-4">Additional Details</h2>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div
                  v-for="feat in property.features"
                  :key="feat.id || feat.name"
                  class="p-2.5 bg-slate-50 dark:bg-slate-950/60 rounded-xl border border-slate-200/60 dark:border-slate-800/60 text-xs"
                >
                  <span class="text-slate-500 dark:text-slate-400 block">{{ feat.name }}</span>
                  <span class="font-semibold text-slate-800 dark:text-slate-200">{{ feat.value }}</span>
                </div>
              </div>
            </div>

            <!-- ── Location & Interactive Map Section ── -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-4">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                  <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[11px] font-bold uppercase tracking-wider mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ t('property.location', 'Location') }}</span>
                  </div>
                  <h2 class="text-sm font-bold text-slate-900 dark:text-slate-200 uppercase tracking-wider">
                    {{ t('property.location_map', 'Property Location & Neighborhood Map') }}
                  </h2>
                </div>
                
                <a
                  :href="googleMapsExternalUrl"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition-colors shrink-0"
                >
                  <svg class="w-3.5 h-3.5 text-rose-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                  </svg>
                  <span>{{ t('property.open_google_maps', 'Open in Google Maps') }} &rarr;</span>
                </a>
              </div>

              <!-- Address Pill -->
              <div class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-950/60 rounded-xl border border-slate-200/70 dark:border-slate-800/80 text-xs text-slate-700 dark:text-slate-300">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="font-medium truncate">{{ locationText }}</span>
              </div>

              <!-- Map Embed Frame -->
              <div class="relative w-full h-72 sm:h-80 rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 shadow-inner">
                <iframe
                  :src="mapEmbedUrl"
                  width="100%"
                  height="100%"
                  style="border:0;"
                  loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"
                  title="Property Location Map"
                  class="w-full h-full filter saturate-105 contrast-105"
                ></iframe>
                
                <!-- Overlay Pin Badge -->
                <div class="absolute bottom-3 left-3 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md px-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-md flex items-center gap-2 text-xs">
                  <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                  <span class="font-bold text-slate-900 dark:text-white truncate max-w-[200px]">{{ property.title }}</span>
                </div>
              </div>

              <!-- Neighborhood Highlights -->
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-1 text-xs">
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800/60 flex items-center gap-2">
                  <span class="text-base">🚗</span>
                  <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase">Access</p>
                    <p class="font-semibold text-slate-800 dark:text-slate-200">Asphalt Road</p>
                  </div>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800/60 flex items-center gap-2">
                  <span class="text-base">⚡</span>
                  <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase">Utilities</p>
                    <p class="font-semibold text-slate-800 dark:text-slate-200">3-Phase & Water</p>
                  </div>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800/60 flex items-center gap-2">
                  <span class="text-base">🛡️</span>
                  <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase">Security</p>
                    <p class="font-semibold text-slate-800 dark:text-slate-200">Gated / Guarded</p>
                  </div>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800/60 flex items-center gap-2">
                  <span class="text-base">📍</span>
                  <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase">{{ t('property.location', 'Location') }}</p>
                    <p class="font-semibold text-slate-800 dark:text-slate-200 truncate">{{ property.address?.subCity?.name || property.address?.city?.name || 'Prime Urban' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- ── Verified Ownership Title Deed Certificate ── -->
            <div class="bg-white dark:bg-slate-900 border border-emerald-200 dark:border-emerald-900/60 rounded-2xl p-6 shadow-xs space-y-4">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-emerald-100 dark:border-emerald-950">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                  </div>
                  <div>
                    <h2 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5">
                      <span>{{ t('property.ownership_verification', 'Ownership Verification & Title Deed') }}</span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ t('property.ownership_verification_subtitle', 'Authenticity verified through Cadastral records and owner identification.') }}</p>
                  </div>
                </div>

                <span
                  :class="[
                    'inline-flex items-center gap-1 px-3 py-1 rounded-full font-bold text-xs shrink-0 self-start sm:self-auto border',
                    property.is_verified
                      ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/80'
                      : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700'
                  ]"
                >
                  <svg v-if="property.is_verified" class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <Clock v-else class="w-3.5 h-3.5 text-slate-500" />
                  <span>{{ property.is_verified ? 'Legally Cleared & Verified' : 'Pending Verification' }}</span>
                </span>
              </div>

              <!-- Certificate Details Grid -->
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-3 bg-slate-50 dark:bg-slate-950/50 rounded-xl border border-slate-200/70 dark:border-slate-800/70 space-y-1">
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ t('property.title_deed_status', 'Title Deed Status') }}</p>
                  <p class="font-bold text-slate-900 dark:text-white flex items-center gap-1">
                    <span :class="['w-2 h-2 rounded-full', property.is_verified ? 'bg-emerald-500' : 'bg-amber-500']"></span>
                    <span>{{ property.is_verified ? 'Authenticated & Cleared' : 'Under Cadastral Audit' }}</span>
                  </p>
                  <p class="text-[10px] text-slate-500">{{ property.is_verified ? 'Registered with municipal cadastre' : 'Awaiting admin document clearance' }}</p>
                </div>

                <div class="p-3 bg-slate-50 dark:bg-slate-950/50 rounded-xl border border-slate-200/70 dark:border-slate-800/70 space-y-1">
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ t('property.verification_reference', 'Verification Reference') }}</p>
                  <p class="font-mono font-bold text-slate-900 dark:text-white">
                    ET-CAD-{{ String(property.id).padStart(5, '0') }}-{{ property.is_verified ? 'V' : 'P' }}
                  </p>
                  <p class="text-[10px] text-slate-500">{{ property.is_verified ? 'Audit Reference Code' : 'Pending Audit Code' }}</p>
                </div>

                <div class="p-3 bg-slate-50 dark:bg-slate-950/50 rounded-xl border border-slate-200/70 dark:border-slate-800/70 space-y-1">
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ t('property.lien_dispute_check', 'Lien & Dispute Check') }}</p>
                  <p :class="['font-bold', property.is_verified ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-400']">
                    {{ property.is_verified ? '100% Dispute Free' : 'Review In Progress' }}
                  </p>
                  <p class="text-[10px] text-slate-500">{{ property.is_verified ? 'No court injunction or bank lien' : 'Admin background cross-check' }}</p>
                </div>
              </div>
            </div>

            <!-- ── Reviews Section ── -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs">
              <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800 mb-6">
                <div>
                  <h2 class="text-sm font-bold text-slate-900 dark:text-slate-200 uppercase tracking-wider">Ratings & Reviews</h2>
                  <div class="flex items-center gap-2 mt-1">
                    <div class="flex items-center text-amber-400">
                      <span class="font-bold text-base text-slate-900 dark:text-slate-100 mr-1">{{ averageRating }}</span>
                      <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                      </svg>
                    </div>
                    <span class="text-xs text-slate-500 dark:text-slate-400">({{ reviewsList.length }} verified review{{ reviewsList.length === 1 ? '' : 's' }})</span>
                  </div>
                </div>

                <button
                  type="button"
                  class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-100 border border-slate-200 dark:border-slate-700 transition-colors"
                  @click="openReviewModal"
                >
                  Write Review
                </button>
              </div>

              <!-- Reviews List -->
              <div v-if="reviewsList.length > 0" class="space-y-4">
                <div
                  v-for="rev in reviewsList"
                  :key="rev.id"
                  class="p-4 bg-slate-50 dark:bg-slate-950/60 rounded-xl border border-slate-200/80 dark:border-slate-800/80 space-y-2"
                >
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                      <img
                        :src="rev.reviewer?.avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=80&q=80'"
                        alt="Reviewer avatar"
                        class="w-7 h-7 rounded-full object-cover border border-slate-200 dark:border-slate-700"
                      />
                      <div>
                        <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ rev.reviewer?.name || 'Verified Resident' }}</p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500">{{ formatDate(rev.created_at) }}</p>
                      </div>
                    </div>

                    <!-- Stars -->
                    <div class="flex items-center text-amber-400">
                      <span v-for="star in 5" :key="star" class="text-xs">
                        {{ star <= rev.rating ? '★' : '☆' }}
                      </span>
                    </div>
                  </div>

                  <h3 v-if="rev.title" class="text-xs font-bold text-slate-900 dark:text-slate-100">{{ rev.title }}</h3>
                  <p v-if="rev.body" class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">{{ rev.body }}</p>

                  <div v-if="rev.pros || rev.cons" class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2 text-[11px]">
                    <p v-if="rev.pros" class="text-emerald-600 dark:text-emerald-400"><span class="font-bold">Pros:</span> {{ rev.pros }}</p>
                    <p v-if="rev.cons" class="text-rose-600 dark:text-rose-400"><span class="font-bold">Cons:</span> {{ rev.cons }}</p>
                  </div>

                  <!-- Owner Response if present -->
                  <div v-if="rev.response" class="mt-3 p-3 bg-white dark:bg-slate-900 rounded-lg border-l-2 border-emerald-500 text-xs shadow-2xs">
                    <p class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mb-1">Response from Owner:</p>
                    <p class="text-slate-700 dark:text-slate-300">{{ rev.response.body }}</p>
                  </div>
                </div>
              </div>

              <!-- No reviews state -->
              <div v-else class="text-center py-8">
                <p class="text-xs text-slate-400">No reviews yet for this property.</p>
                <button
                  type="button"
                  class="mt-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline"
                  @click="openReviewModal"
                >
                  Be the first to review &rarr;
                </button>
              </div>
            </div>
          </div>

          <!-- Right Column: Booking / Contact Action Sidebar -->
          <div class="space-y-5 lg:sticky lg:top-24">
            <!-- ── Short Stay Reservation Card (when listing_type === 'short_rent') ── -->
            <div
              v-if="property.listing_type === 'short_rent'"
              class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-md space-y-4"
            >
              <div class="flex items-baseline justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="text-xl font-extrabold text-slate-900 dark:text-white">
                  {{ formatPrice(property.price, property.currency || 'ETB') }}
                  <span class="text-xs font-normal text-slate-500 dark:text-slate-400">/ night</span>
                </div>
                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 rounded-lg border border-slate-200 dark:border-slate-700">
                  Short Rental
                </span>
              </div>

              <div class="space-y-3">
                <div class="grid grid-cols-2 gap-2 text-xs">
                  <div>
                    <label for="check-in-date" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Check-in</label>
                    <input
                      id="check-in-date"
                      v-model="bookingForm.check_in_date"
                      type="date"
                      :min="minBookingDate"
                      class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl p-2.5 focus:border-slate-500 focus:outline-none text-xs"
                      @change="onBookingDatesChanged"
                    />
                  </div>
                  <div>
                    <label for="check-out-date" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Check-out</label>
                    <input
                      id="check-out-date"
                      v-model="bookingForm.check_out_date"
                      type="date"
                      :min="minCheckOutDate"
                      class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl p-2.5 focus:border-slate-500 focus:outline-none text-xs"
                      @change="onBookingDatesChanged"
                    />
                  </div>
                </div>

                <div>
                  <label for="guests-count" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Guests</label>
                  <select
                    id="guests-count"
                    v-model.number="bookingForm.guests_count"
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-xs rounded-xl p-2.5 focus:border-slate-500 focus:outline-none"
                  >
                    <option v-for="g in 10" :key="g" :value="g">
                      {{ g }} Guest{{ g > 1 ? 's' : '' }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Price Breakdown Estimate -->
              <div v-if="bookingNights > 0" class="pt-3 border-t border-slate-100 dark:border-slate-800 text-xs space-y-1.5 text-slate-600 dark:text-slate-300">
                <div class="flex justify-between">
                  <span>{{ formatPrice(property.price) }} × {{ bookingNights }} night{{ bookingNights > 1 ? 's' : '' }}</span>
                  <span class="font-semibold">{{ formatPrice(baseBookingTotal) }}</span>
                </div>
                <div class="flex justify-between text-slate-400">
                  <span>Estimated BetLink Service Fee (5%)</span>
                  <span>{{ formatPrice(estimatedServiceFee) }}</span>
                </div>
                <div class="flex justify-between font-bold text-slate-900 dark:text-slate-100 pt-2 border-t border-slate-100 dark:border-slate-800 text-sm">
                  <span>Estimated Total</span>
                  <span class="text-slate-900 dark:text-white font-extrabold">{{ formatPrice(estimatedTotal) }}</span>
                </div>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 italic mt-1">
                  * Authoritative booking amount and availability are confirmed by the backend on submission.
                </p>
              </div>

              <button
                type="button"
                :disabled="isBookingSubmitting || bookingNights <= 0"
                class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 disabled:opacity-50 font-bold text-xs shadow-xs transition-all cursor-pointer"
                @click="submitShortStayBooking"
              >
                {{ isBookingSubmitting ? 'Reserving...' : 'Reserve Short Stay' }}
              </button>
            </div>

            <!-- ── Host / Agent Contact & Action Card ── -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-md space-y-4">
              <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">
                  Contact & Viewing
                </h3>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                  Verified Host
                </span>
              </div>

              <!-- Host / Agent Profile Box -->
              <div class="flex items-center gap-3.5 p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/70 dark:border-slate-700/60">
                <img
                  :src="propertyHost.avatar"
                  :alt="propertyHost.name"
                  @error="(e) => e.target.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(propertyHost.name) + '&background=0f172a&color=fff'"
                  class="w-12 h-12 rounded-full object-cover border-2 border-slate-200 dark:border-slate-700 shrink-0"
                />
                <div class="min-w-0 flex-1">
                  <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ propertyHost.name }}</p>
                  <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">{{ propertyHost.role }}</p>
                  <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 font-mono">{{ propertyHost.phone }}</p>
                </div>
              </div>

              <!-- Action CTAs -->
              <div class="space-y-2 pt-1">
                <div v-if="isOwnerOfThisProperty" class="p-3.5 bg-slate-50 dark:bg-slate-800/80 rounded-xl border border-slate-200/80 dark:border-slate-700 text-center space-y-2">
                  <p class="text-xs font-bold text-slate-900 dark:text-white">You are the owner of this listing</p>
                  <RouterLink
                    to="/owner/properties"
                    class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold transition-all shadow-xs flex items-center justify-center gap-2 cursor-pointer"
                  >
                    Manage Listing
                  </RouterLink>
                </div>

                <template v-else>
                  <button
                    type="button"
                    class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold transition-all shadow-xs flex items-center justify-center gap-2 cursor-pointer"
                    @click="appointmentModalOpen = true"
                  >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Schedule Tour / Viewing
                  </button>

                  <button
                    type="button"
                    class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold transition-colors flex items-center justify-center gap-2 cursor-pointer"
                    @click="inquiryModalOpen = true"
                  >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Send Inquiry Message
                  </button>

                  <a
                    v-if="propertyHost.phone"
                    :href="`tel:${propertyHost.phone}`"
                    class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold transition-colors flex items-center justify-center gap-2 cursor-pointer"
                  >
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    Call {{ propertyHost.phone }}
                  </a>
                </template>
              </div>
            </div>
          </div>
        </div>

        <!-- ── Mobile Sticky Bottom Action Bar ── -->
        <div class="lg:hidden fixed bottom-0 inset-x-0 bg-white/95 dark:bg-slate-950/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 p-3 z-40 flex items-center justify-between gap-3 shadow-2xl">
          <div>
            <span class="text-[10px] text-slate-400 block">Price</span>
            <span class="text-sm font-extrabold text-slate-900 dark:text-white">{{ formatPrice(property.price) }}</span>
          </div>

          <div class="flex items-center gap-2">
            <button
              type="button"
              class="px-3.5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold transition-colors"
              @click="inquiryModalOpen = true"
            >
              Inquire
            </button>

            <button
              type="button"
              class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold transition-colors"
              @click="property.listing_type === 'short_rent' ? scrollToBooking() : appointmentModalOpen = true"
            >
              {{ property.listing_type === 'short_rent' ? 'Reserve' : 'Tour' }}
            </button>
          </div>
        </div>

        <!-- ── Modals ── -->
        <PropertyAppointmentModal
          v-model="appointmentModalOpen"
          :property="property"
          @success="onAppointmentSuccess"
        />

        <PropertyInquiryModal
          v-model="inquiryModalOpen"
          :property="property"
          @success="onInquirySuccess"
        />

        <PropertyReviewModal
          v-model="reviewModalOpen"
          :property-id="property.id"
          @success="onReviewSuccess"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Clock } from 'lucide-vue-next'
import BaseBadge from '../../components/common/BaseBadge.vue'
import PropertyImageGallery from '../../components/property/PropertyImageGallery.vue'
import PropertyAppointmentModal from '../../components/property/PropertyAppointmentModal.vue'
import PropertyInquiryModal from '../../components/property/PropertyInquiryModal.vue'
import PropertyReviewModal from '../../components/property/PropertyReviewModal.vue'
import { propertyService } from '../../services/propertyService'
import { favoriteService } from '../../services/favoriteService'
import { reviewService } from '../../services/reviewService'
import { bookingService } from '../../services/bookingService'
import { formatPrice, formatArea, formatDate, formatListingType, getListingTypeVariant } from '../../utils/formatters'
import { useAuthStore } from '../../stores/auth'
import { useToastStore } from '../../stores/toast'
import { useLanguage } from '../../composables/useLanguage'

const props = defineProps({
  propertyId: {
    type: [Number, String],
    default: null
  },
  inline: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['back'])

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const toastStore = useToastStore()
const { t } = useLanguage()

const property = ref(null)
const isLoading = ref(true)
const errorMessage = ref('')
const isFavorite = ref(false)
const isFavorited = computed(() => isFavorite.value)

const isOwnerOfThisProperty = computed(() => {
  if (!authStore.user?.id || !property.value) return false
  const currentUserId = Number(authStore.user.id)
  const ownerId = Number(property.value.user_id || property.value.user?.id || property.value.owner_id || property.value.owner?.id)
  return currentUserId === ownerId
})

const portalPropertiesRoute = computed(() => {
  if (route.path.startsWith('/owner')) return '/owner/properties'
  if (route.path.startsWith('/agent')) return '/agent/properties'
  if (route.path.startsWith('/admin')) return '/admin/properties'
  if (route.path.startsWith('/buyer')) return '/buyer/properties'
  return '/properties'
})

const goBack = () => {
  if (props.inline) {
    emit('back')
    return
  }
  if (window.history.length > 1) {
    router.back()
  } else {
    router.push(portalPropertiesRoute.value)
  }
}

const appointmentModalOpen = ref(false)
const inquiryModalOpen = ref(false)
const reviewModalOpen = ref(false)

// Short Stay Booking State
const isBookingSubmitting = ref(false)
const tomorrow = new Date()
tomorrow.setDate(tomorrow.getDate() + 1)
const minBookingDate = tomorrow.toISOString().split('T')[0]

const dayAfter = new Date()
dayAfter.setDate(dayAfter.getDate() + 2)
const minCheckOutDate = computed(() => {
  if (bookingForm.check_in_date) {
    const d = new Date(bookingForm.check_in_date)
    d.setDate(d.getDate() + 1)
    return d.toISOString().split('T')[0]
  }
  return dayAfter.toISOString().split('T')[0]
})

const bookingForm = reactive({
  check_in_date: minBookingDate,
  check_out_date: dayAfter.toISOString().split('T')[0],
  guests_count: 1,
  special_requests: '',
})

const bookingNights = computed(() => {
  if (!bookingForm.check_in_date || !bookingForm.check_out_date) return 0
  const inD = new Date(bookingForm.check_in_date)
  const outD = new Date(bookingForm.check_out_date)
  const diffTime = outD.getTime() - inD.getTime()
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  return diffDays > 0 ? diffDays : 0
})

const baseBookingTotal = computed(() => {
  if (!property.value || !property.value.price) return 0
  return property.value.price * bookingNights.value
})

const estimatedServiceFee = computed(() => {
  return Math.round(baseBookingTotal.value * 0.05)
})

const estimatedTotal = computed(() => {
  return baseBookingTotal.value + estimatedServiceFee.value
})

const listingBadgeVariant = computed(() => {
  return getListingTypeVariant(property.value?.listing_type)
})

const locationText = computed(() => {
  if (!property.value) return 'Addis Ababa, Ethiopia'
  const p = property.value
  const parts = []

  const extractName = (val) => {
    if (!val) return ''
    if (typeof val === 'string') return val.trim()
    if (typeof val === 'object') return val.name || val.title || val.slug || ''
    return String(val)
  }

  const street = extractName(p.address?.street_address) || extractName(p.address?.street)
  if (street && !street.includes('[object Object]')) parts.push(street)

  const subCity = extractName(p.address?.subCity) || extractName(p.address?.sub_city) || extractName(p.subcity) || extractName(p.sub_city)
  if (subCity && !subCity.includes('[object Object]')) parts.push(subCity)

  const city = extractName(p.address?.city) || extractName(p.city)
  if (city && !city.includes('[object Object]')) parts.push(city)

  if (parts.length > 0) return parts.join(', ')
  if (p.location && typeof p.location === 'string' && !p.location.includes('[object Object]')) return p.location
  return 'Addis Ababa, Ethiopia'
})

const mapEmbedUrl = computed(() => {
  const p = property.value
  const lat = p?.address?.latitude
  const lng = p?.address?.longitude
  if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
    return `https://maps.google.com/maps?q=${lat},${lng}&t=&z=16&ie=UTF8&iwloc=&output=embed`
  }
  const loc = locationText.value || 'Addis Ababa, Ethiopia'
  return `https://maps.google.com/maps?q=${encodeURIComponent(loc + ', Ethiopia')}&t=&z=15&ie=UTF8&iwloc=&output=embed`
})

const googleMapsExternalUrl = computed(() => {
  const p = property.value
  const lat = p?.address?.latitude
  const lng = p?.address?.longitude
  if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
    return `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`
  }
  const loc = locationText.value || 'Addis Ababa, Ethiopia'
  return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(loc + ', Ethiopia')}`
})

const propertyImages = computed(() => {
  if (!property.value) return []
  if (Array.isArray(property.value.images) && property.value.images.length > 0) {
    return property.value.images
  }
  if (property.value.primaryImage) {
    return [property.value.primaryImage]
  }
  if (property.value.primary_image) {
    return [property.value.primary_image]
  }
  if (property.value.primary_image_url) {
    return [property.value.primary_image_url]
  }
  if (property.value.image) {
    return [property.value.image]
  }
  return []
})

const reviewsList = computed(() => {
  if (!property.value || !property.value.reviews) return []
  return property.value.reviews
})

const averageRating = computed(() => {
  if (property.value?.average_rating) return property.value.average_rating
  if (!reviewsList.value || reviewsList.value.length === 0) return '5.0'
  const sum = reviewsList.value.reduce((acc, r) => acc + (r.rating || 0), 0)
  return (sum / reviewsList.value.length).toFixed(1)
})

const propertyHost = computed(() => {
  const p = property.value

  // 1. If current logged in user is the owner of this property, use their live profile data
  if (isOwnerOfThisProperty.value && authStore.user) {
    const name = authStore.userName || 'Verified Property Owner'
    const liveAvatar = authStore.userAvatar || authStore.user.avatar_url || authStore.user.avatar || authStore.user.profile?.avatar
    return {
      id: authStore.user.id,
      name: name,
      avatar: liveAvatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=0f172a&color=fff`,
      phone: authStore.user.phone || authStore.user.phone_number || '+251 91 123 4567',
      role: authStore.primaryRole === 'agent' ? 'Licensed Real Estate Agent' : 'Verified Property Owner'
    }
  }

  // 2. Check property owner relation
  const ownerObj = p?.owner || p?.user
  if (ownerObj && (ownerObj.name || ownerObj.first_name)) {
    const name = ownerObj.name || `${ownerObj.first_name || ''} ${ownerObj.last_name || ''}`.trim() || 'Verified Landlord'
    const rawAvatar = ownerObj.avatar_url || ownerObj.avatar || ownerObj.profile?.avatar_url || ownerObj.profile?.avatar
    return {
      id: ownerObj.id || 1,
      name: name,
      avatar: rawAvatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=0f172a&color=fff`,
      phone: ownerObj.phone_number || ownerObj.phone || '+251 91 123 4567',
      role: ownerObj.role === 'agent' ? 'Licensed Real Estate Agent' : 'Verified Property Owner'
    }
  }

  // 3. Check property agent relation
  if (p?.agent && (p.agent.name || p.agent.first_name)) {
    const name = p.agent.name || `${p.agent.first_name || ''} ${p.agent.last_name || ''}`.trim() || 'Certified Agent'
    const rawAvatar = p.agent.avatar_url || p.agent.avatar || p.agent.profile?.avatar_url || p.agent.profile?.avatar
    return {
      id: p.agent.id || 2,
      name: name,
      avatar: rawAvatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=0f172a&color=fff`,
      phone: p.agent.phone_number || p.agent.phone || '+251 92 345 6789',
      role: 'Licensed Real Estate Agent'
    }
  }

  const defaultName = p?.owner_name || 'Verified Property Lister'
  return {
    id: p?.user_id || 0,
    name: defaultName,
    avatar: `https://ui-avatars.com/api/?name=${encodeURIComponent(defaultName)}&background=0f172a&color=fff`,
    phone: p?.contact_phone || '+251 91 123 4567',
    role: 'Verified Property Owner'
  }
})

const fetchPropertyDetails = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const slugOrId = props.propertyId || route.params.id
    if (!slugOrId) return
    const res = await propertyService.getPropertyBySlug(slugOrId)

    if (res && res.success && res.data) {
      property.value = res.data
    } else if (res && res.data) {
      property.value = res.data
    } else if (res && res.id) {
      property.value = res
    } else {
      throw new Error('Property listing could not be found.')
    }

    // Check favorite status if authenticated
    if (authStore.isAuthenticated && property.value?.id) {
      try {
        const favRes = await favoriteService.checkFavorite(property.value.id)
        if (favRes && favRes.data && favRes.data.is_favorite !== undefined) {
          isFavorite.value = Boolean(favRes.data.is_favorite)
        }
      } catch {
        // ignore favorite check failure
      }
    }
  } catch (err) {
    errorMessage.value = err.message || 'Property not found.'
  } finally {
    isLoading.value = false
  }
}

const isOwnerOfProperty = computed(() => {
  if (!authStore.isAuthenticated || !authStore.user?.id || !property.value) return false
  const ownerId = property.value.user_id || property.value.owner_id || property.value.owner?.id || property.value.user?.id
  return ownerId && String(ownerId) === String(authStore.user.id)
})

const toggleFavorite = async () => {
  if (!authStore.isAuthenticated) {
    toastStore.info('Please log in to save properties to your favorites', 'Authentication Required')
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }

  const role = (authStore.user?.role || authStore.user?.roles?.[0]?.name || '').toLowerCase()
  if (role === 'admin') {
    toastStore.info('Administrators cannot favorite properties (buyer feature only).')
    return
  }

  if (isOwnerOfProperty.value) {
    toastStore.info('You cannot favorite your own property listing.')
    return
  }

  const previousState = isFavorite.value
  isFavorite.value = !previousState

  try {
    if (previousState) {
      await favoriteService.removeFavorite(property.value.id)
      toastStore.success('Removed from favorites')
    } else {
      await favoriteService.addFavorite(property.value.id)
      toastStore.success('Added to favorites!')
    }
  } catch (err) {
    isFavorite.value = previousState
    toastStore.error(err.message || 'Failed to update favorites')
  }
}

const shareProperty = async () => {
  if (navigator.share) {
    try {
      await navigator.share({
        title: property.value?.title || 'BetLink Property',
        text: `Check out this property on BetLink: ${property.value?.title}`,
        url: window.location.href,
      })
    } catch {
      // User cancelled share
    }
  } else {
    try {
      await navigator.clipboard.writeText(window.location.href)
      toastStore.success('Link copied!')
    } catch {
      toastStore.info(window.location.href, 'Property URL')
    }
  }
}

const openReviewModal = () => {
  if (!authStore.isAuthenticated) {
    toastStore.info('Please sign in to write a review')
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  reviewModalOpen.value = true
}

const onAppointmentSuccess = () => {
  toastStore.success('Appointment scheduled!')
}

const onInquirySuccess = () => {
  toastStore.success('Inquiry sent!')
}

const onReviewSuccess = async () => {
  try {
    if (property.value?.id) {
      const revRes = await reviewService.getPropertyReviews(property.value.id)
      if (revRes && revRes.data) {
        property.value.reviews = Array.isArray(revRes.data) ? revRes.data : revRes.data.data || []
      }
    }
  } catch {
    // ignore review reload failure
  }
}

const onBookingDatesChanged = () => {
  // Trigger reactive re-calc
}

const submitShortStayBooking = async () => {
  if (!authStore.isAuthenticated) {
    toastStore.info('Please sign in to book short stays')
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }

  if (bookingNights.value <= 0) {
    toastStore.error('Please select valid check-in and check-out dates.')
    return
  }

  isBookingSubmitting.value = true

  try {
    await bookingService.createBooking({
      property_id: property.value.id,
      check_in_date: bookingForm.check_in_date,
      check_out_date: bookingForm.check_out_date,
      guests_count: bookingForm.guests_count,
      special_requests: bookingForm.special_requests || undefined,
    })

    toastStore.success('Booking submitted!')
    router.push('/buyer/bookings')
  } catch (err) {
    toastStore.error(err.message || 'Failed to submit booking.')
  } finally {
    isBookingSubmitting.value = false
  }
}

const scrollToBooking = () => {
  window.scrollTo({ top: 300, behavior: 'smooth' })
}

watch(() => props.propertyId, (newId) => {
  if (newId) {
    fetchPropertyDetails()
  }
})

watch(() => route.params.id, (newId) => {
  if (newId && !props.inline) {
    fetchPropertyDetails()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
})

onMounted(() => {
  fetchPropertyDetails()
})
</script>