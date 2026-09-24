<template>
  <div class="space-y-6 sm:space-y-8">
    
    <!-- 1. Real-Time Global Search Bar Component -->
    <SearchBox />

    <!-- 2. Error State with Retry Button -->
    <div v-if="apiError" class="p-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-center space-y-3">
      <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ apiError }}</p>
      <button 
        @click="fetchDashboardData" 
        class="px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold rounded-xl hover:bg-slate-800 transition-colors shadow-xs cursor-pointer"
      >
        {{ t('retry_loading', 'Retry Loading') }}
      </button>
    </div>

    <!-- Loading Skeleton State -->
    <div v-else-if="isLoading" class="space-y-6">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        <div v-for="n in 4" :key="n" class="bg-white dark:bg-slate-900 rounded-xl p-4 border border-slate-200/80 dark:border-slate-800 animate-pulse h-24"></div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 animate-pulse h-64"></div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 animate-pulse h-64"></div>
      </div>
    </div>

    <template v-else>
      <!-- 3. Stat Cards: Buyer-Specific Key Metrics (Exact Uniform Design) -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        
        <!-- Card 1: SAVED FAVORITES -->
        <RouterLink 
          to="/buyer/favorites"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 flex items-center justify-center group-hover:scale-105 transition-transform shrink-0">
            <Heart class="w-4.5 h-4.5 fill-rose-500 text-rose-500" />
          </div>
          <div class="min-w-0">
            <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider truncate">Saved Favorites</p>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.savedProperties ?? 0 }}</p>
          </div>
        </RouterLink>

        <!-- Card 2: SCHEDULED APPOINTMENTS -->
        <RouterLink 
          to="/buyer/appointments"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center group-hover:scale-105 transition-transform shrink-0">
            <Calendar class="w-4.5 h-4.5" />
          </div>
          <div class="min-w-0">
            <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider truncate">Scheduled Tours</p>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.upcomingAppointments ?? 0 }}</p>
          </div>
        </RouterLink>

        <!-- Card 3: ACTIVE MESSAGE THREADS -->
        <RouterLink 
          to="/buyer/messages"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center group-hover:scale-105 transition-transform shrink-0">
            <MessageSquare class="w-4.5 h-4.5" />
          </div>
          <div class="min-w-0">
            <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider truncate">Active Inquiries</p>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.activeInquiries ?? 0 }}</p>
          </div>
        </RouterLink>

        <!-- Card 4: TOTAL PROPERTIES VIEWED -->
        <RouterLink 
          to="/buyer/properties"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center group-hover:scale-105 transition-transform shrink-0">
            <Eye class="w-4.5 h-4.5" />
          </div>
          <div class="min-w-0">
            <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider truncate">Properties Explored</p>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.recentViews ?? 0 }}</p>
          </div>
        </RouterLink>

      </div>

      <!-- 4. Row 2: Analytics & Sub-City Distribution (2-to-1 Asymmetrical Grid) -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Left (2 Columns): Inquiries vs. Bookings Trend Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between transition-colors">
          
          <!-- Header -->
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Activity Overview</h3>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Your monthly inquiry and tour booking trend</p>
            </div>
            <div class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700">
              <span>Last 6 Months</span>
            </div>
          </div>

          <!-- Chart Body with Dynamic Bars & Donut Badge -->
          <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 items-end pt-2">
            
            <!-- Bar Graph Area (3 Cols) -->
            <div class="sm:col-span-3">
              <div class="h-44 w-full flex items-end justify-between gap-3 sm:gap-4 pb-2 border-b border-slate-200/80 dark:border-slate-800">
                
                <div v-for="(m, idx) in monthlyTrends" :key="idx" class="flex flex-col items-center gap-2 flex-1">
                  <div class="w-full flex items-end justify-center gap-1.5 h-32">
                    <!-- Tours Booked Bar -->
                    <div 
                      class="w-3 sm:w-3.5 bg-slate-900 dark:bg-white rounded-t-md transition-all duration-700 group relative cursor-pointer"
                      :style="{ height: `${Math.min(100, Math.max(14, m.bookings * 22))}%` }"
                      :title="`${m.bookings} Tours in ${m.month}`"
                    >
                      <span class="opacity-0 group-hover:opacity-100 absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow whitespace-nowrap transition-opacity z-10">
                        {{ m.bookings }} Tours
                      </span>
                    </div>
                    <!-- Inquiries Sent Bar -->
                    <div 
                      class="w-3 sm:w-3.5 bg-slate-400 dark:bg-slate-600 rounded-t-md transition-all duration-700 group relative cursor-pointer"
                      :style="{ height: `${Math.min(100, Math.max(18, m.inquiries * 15))}%` }"
                      :title="`${m.inquiries} Inquiries in ${m.month}`"
                    >
                      <span class="opacity-0 group-hover:opacity-100 absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow whitespace-nowrap transition-opacity z-10">
                        {{ m.inquiries }} Inquiries
                      </span>
                    </div>
                  </div>
                  <span class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase">{{ m.month }}</span>
                </div>

              </div>

              <!-- Legend -->
              <div class="flex items-center gap-6 mt-3.5 justify-center sm:justify-start">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
                  <span class="w-2.5 h-2.5 rounded-xs bg-slate-900 dark:bg-white"></span>
                  <span>Tours Booked</span>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
                  <span class="w-2.5 h-2.5 rounded-xs bg-slate-400 dark:bg-slate-600"></span>
                  <span>Inquiries Sent</span>
                </div>
              </div>
            </div>

            <!-- Donut Progress Ring (1 Col) -->
            <div class="flex flex-col items-center justify-center p-2 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
              <div class="relative w-20 h-20 flex items-center justify-center">
                <!-- SVG Donut -->
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                  <circle cx="18" cy="18" r="15.915" fill="none" class="stroke-slate-200 dark:stroke-slate-700" stroke-width="3.5" />
                  <circle 
                    cx="18" 
                    cy="18" 
                    r="15.915" 
                    fill="none" 
                    class="stroke-slate-900 dark:stroke-white transition-all duration-1000" 
                    stroke-width="3.5" 
                    stroke-dasharray="78, 100" 
                    stroke-linecap="round" 
                  />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                  <span class="text-sm font-black text-slate-900 dark:text-white">78%</span>
                  <span class="text-[8px] font-bold text-slate-400 uppercase tracking-tight">Active</span>
                </div>
              </div>
              <p class="text-[11px] text-slate-600 dark:text-slate-400 font-bold mt-2 text-center">Response Rate</p>
            </div>

          </div>

        </div>

        <!-- Right (1 Column): Listings by Sub-City Distribution -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between transition-colors">
          <div>
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Listings by Sub-City</h3>
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Addis Ababa</span>
            </div>

            <!-- Dynamic Sub-City Distribution Progress Bars -->
            <div class="space-y-3.5 mt-4">
              <div v-for="item in subCityDistribution" :key="item.subCity" class="space-y-1.5">
                <div class="flex justify-between text-xs font-bold text-slate-700 dark:text-slate-300">
                  <span>{{ item.subCity }}</span>
                  <span class="text-slate-500 dark:text-slate-400 font-semibold">{{ item.percentage }}% ({{ item.count }})</span>
                </div>
                <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                  <div class="h-full bg-slate-900 dark:bg-white rounded-full transition-all duration-700" :style="{ width: `${item.percentage}%` }"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
            <span>Verified Listings</span>
            <RouterLink to="/buyer/properties" class="text-slate-900 dark:text-white font-bold hover:underline text-xs flex items-center gap-1">
              Explore All →
            </RouterLink>
          </div>
        </div>

      </div>

      <!-- 5. Row 3: Upcoming Tours & Bookings Overview Widget -->
      <div v-if="upcomingAppointments.length > 0" class="bg-white dark:bg-slate-900 rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 space-y-4 transition-colors">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <Calendar class="w-4.5 h-4.5 text-slate-900 dark:text-white" />
              <span>Upcoming Scheduled Tours</span>
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Your confirmed and pending property visits</p>
          </div>
          <RouterLink to="/buyer/appointments" class="text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:underline">
            Manage Tours →
          </RouterLink>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div 
            v-for="apt in upcomingAppointments" 
            :key="apt.id"
            class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 flex flex-col justify-between gap-3 hover:border-slate-400 dark:hover:border-slate-600 transition-colors"
          >
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-900 text-white dark:bg-white dark:text-slate-900">
                  {{ apt.type === 'virtual' ? 'Virtual Tour' : 'In-Person Visit' }}
                </span>
                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded border" :class="apt.status === 'confirmed' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border-blue-200' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border-slate-200'">
                  {{ apt.status }}
                </span>
              </div>
              <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white line-clamp-1">
                {{ apt.property_title }}
              </h4>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-1">
                <MapPin class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                <span>{{ apt.location }}</span>
              </p>
            </div>
            <div class="text-[11px] font-semibold text-slate-700 dark:text-slate-300 pt-2 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between">
              <span>📅 {{ apt.scheduled_at }}</span>
              <RouterLink :to="`/buyer/properties/${apt.property_id}`" class="text-slate-900 dark:text-white font-bold hover:underline">
                View →
              </RouterLink>
            </div>
          </div>
        </div>
      </div>

      <!-- 6. Row 4: Recommended Properties Grid -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 space-y-4 transition-colors">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">Recommended Properties</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Curated Ethiopian homes matching your search interests</p>
          </div>
          <RouterLink to="/buyer/properties" class="text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:underline flex items-center gap-1">
            <span>Explore All</span>
            <span aria-hidden="true">→</span>
          </RouterLink>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
          <div
            v-for="prop in recommendedProperties"
            :key="prop.id"
            class="group rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden hover:shadow-lg hover:border-slate-400 dark:hover:border-slate-600 transition-all flex flex-col justify-between"
          >
            <!-- Image Thumbnail -->
            <div class="relative h-48 overflow-hidden bg-slate-100 dark:bg-slate-800">
              <img 
                :src="prop.image || 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80'" 
                :alt="prop.title" 
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy" 
              />
              <!-- Tag -->
              <div class="absolute top-3 left-3 bg-slate-900/90 backdrop-blur-xs text-white text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider shadow">
                {{ prop.listing_type === 'rent' ? 'For Rent' : prop.listing_type === 'short_rent' ? 'Short Stay' : 'For Sale' }}
              </div>
              <!-- Price Badge -->
              <div class="absolute bottom-3 left-3 bg-slate-950/80 backdrop-blur-xs px-2.5 py-1 rounded-lg text-white font-black text-sm drop-shadow-md">
                ETB {{ Number(prop.price || 0).toLocaleString() }}
                <span v-if="prop.listing_type === 'rent'" class="text-[10px] font-normal text-slate-300">/mo</span>
                <span v-else-if="prop.listing_type === 'short_rent'" class="text-[10px] font-normal text-slate-300">/day</span>
              </div>
            </div>

            <!-- Content -->
            <div class="p-4 space-y-3 flex-1 flex flex-col justify-between">
              <div>
                <h4 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors line-clamp-1">
                  {{ prop.title }}
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-1.5 truncate">
                  <MapPin class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                  <span>{{ prop.location || 'Addis Ababa' }}</span>
                </p>

                <!-- Specs -->
                <div class="flex items-center gap-3 text-[11px] font-semibold text-slate-600 dark:text-slate-400 mt-2.5 pt-2.5 border-t border-slate-100 dark:border-slate-800">
                  <span>🛏️ {{ prop.bedrooms || 2 }} Beds</span>
                  <span>🚿 {{ prop.bathrooms || 2 }} Baths</span>
                  <span>📐 {{ prop.area_sqm || 120 }} m²</span>
                </div>
              </div>

              <!-- Action Link -->
              <RouterLink
                :to="`/buyer/properties/${prop.id}`"
                class="block w-full py-2.5 text-center text-xs font-bold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-900 hover:text-white dark:hover:bg-white dark:hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl transition-all duration-150 mt-1 cursor-pointer active:scale-98"
              >
                View Details
              </RouterLink>
            </div>
          </div>
        </div>
      </div>
    </template>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import {
  Heart,
  Calendar,
  MessageSquare,
  Eye,
  MapPin
} from 'lucide-vue-next'
import { useAuth } from '@/composables/useAuth'
import { useLanguage } from '@/composables/useLanguage'
import { useDashboardStore } from '@/stores/dashboardStore'
import SearchBox from '@/components/dashboard/SearchBox.vue'

const { user } = useAuth()
const { t } = useLanguage()
const dashboardStore = useDashboardStore()

const isLoading = ref(false)
const apiError = ref(null)

const stats = computed(() => dashboardStore.stats || {
  savedProperties: 0,
  upcomingAppointments: 0,
  activeInquiries: 0,
  recentViews: 0,
  shortStays: 0
})

function generateRecentMonths(count = 6) {
  const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  const now = new Date()
  const list = []
  for (let i = count - 1; i >= 0; i--) {
    const d = new Date(now.getFullYear(), now.getMonth() - i, 1)
    list.push({
      month: monthNames[d.getMonth()],
      inquiries: i === 0 ? 3 : (i === 1 ? 2 : (i === 2 ? 4 : 1)),
      bookings: i === 0 ? 2 : (i === 1 ? 1 : (i === 2 ? 2 : 0))
    })
  }
  return list
}

const defaultTrends = generateRecentMonths(6)

const defaultSubCities = [
  { subCity: 'Bole', count: 42, percentage: 38 },
  { subCity: 'CMC', count: 26, percentage: 24 },
  { subCity: 'Kazanchis', count: 18, percentage: 16 },
  { subCity: 'Sarbet', count: 14, percentage: 12 },
  { subCity: 'Mexico', count: 11, percentage: 10 },
]

const fallbackProperties = [
  {
    id: 1,
    title: 'Modern Luxury Apartment in Bole Medhanialem',
    listing_type: 'rent',
    price: 45000,
    bedrooms: 3,
    bathrooms: 2,
    area_sqm: 160,
    image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&q=80',
    location: 'Bole, Addis Ababa'
  },
  {
    id: 2,
    title: 'Exclusive Furnished Villa with Garden in CMC',
    listing_type: 'sale',
    price: 18500000,
    bedrooms: 4,
    bathrooms: 3,
    area_sqm: 350,
    image: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800&q=80',
    location: 'Yeka CMC, Addis Ababa'
  },
  {
    id: 3,
    title: 'Cozy Executive Studio in Kazanchis',
    listing_type: 'short_rent',
    price: 4200,
    bedrooms: 1,
    bathrooms: 1,
    area_sqm: 55,
    image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80',
    location: 'Kirkos, Addis Ababa'
  },
  {
    id: 4,
    title: 'Contemporary Townhouse in Old Airport',
    listing_type: 'sale',
    price: 14000000,
    bedrooms: 3,
    bathrooms: 3,
    area_sqm: 220,
    image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80',
    location: 'Nifas Silk-Lafto, Addis Ababa'
  },
  {
    id: 5,
    title: 'High-rise 2-Bedroom Condo in Sarbet',
    listing_type: 'rent',
    price: 32000,
    bedrooms: 2,
    bathrooms: 2,
    area_sqm: 110,
    image: 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80',
    location: 'Sarbet, Addis Ababa'
  },
  {
    id: 6,
    title: 'Furnished Penthouse with Panoramic City Views',
    listing_type: 'short_rent',
    price: 8500,
    bedrooms: 3,
    bathrooms: 3,
    area_sqm: 200,
    image: 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800&q=80',
    location: 'Bole Atlas, Addis Ababa'
  }
]

const monthlyTrends = computed(() => {
  return dashboardStore.monthlyTrends && dashboardStore.monthlyTrends.length > 0 
    ? dashboardStore.monthlyTrends 
    : defaultTrends
})

const subCityDistribution = computed(() => {
  const list = dashboardStore.subCityDistribution && dashboardStore.subCityDistribution.length > 0 
    ? dashboardStore.subCityDistribution 
    : defaultSubCities
  return list.slice(0, 5)
})

const recommendedProperties = computed(() => {
  if (dashboardStore.recommended && dashboardStore.recommended.length > 0) {
    return dashboardStore.recommended.slice(0, 6)
  }
  return fallbackProperties
})

const upcomingAppointments = computed(() => {
  return dashboardStore.upcomingAppointmentsList || []
})

const fetchDashboardData = async () => {
  if (!dashboardStore.lastFetched && (!dashboardStore.stats || !dashboardStore.stats.savedProperties)) {
    isLoading.value = true
  }

  try {
    await dashboardStore.fetchDashboardData(false)
  } catch (err) {
    apiError.value = err.message || 'Failed to load dashboard metrics.'
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script>
