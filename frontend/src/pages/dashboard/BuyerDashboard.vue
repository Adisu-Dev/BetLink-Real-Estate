<template>
  <!-- Loading State -->
  <LoadingSkeleton v-if="isLoading" type="stats" :count="4" />

  <!-- Dashboard Content -->
  <div v-else class="space-y-6">
      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <QuickActionCard 
          title="Search Properties"
          description="Find your dream home"
          icon="search"
          isPrimary
          @click="$router.push('/properties')"
        />
        <QuickActionCard 
          title="Saved Properties"
          icon="heart"
          @click="$router.push('/buyer/favorites')"
        />
        <QuickActionCard 
          title="Compare Properties"
          icon="document"
          @click="$router.push('/buyer/compare')"
        />
        <QuickActionCard 
          title="Schedule Appointment"
          icon="calendar"
          @click="$router.push('/buyer/appointments')"
        />
        <QuickActionCard 
          title="View Messages"
          icon="message"
          @click="$router.push('/buyer/messages')"
        />
      </div>

      <!-- Buyer Statistics -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <DashboardStatCard
          title="Saved Properties"
          :value="stats.savedProperties"
          subtitle="Favorite listings"
          icon="heart"
          iconBgColor="bg-slate-100 dark:bg-slate-800"
          iconColor="text-slate-800 dark:text-slate-200"
          action="View All"
          @action="$router.push('/buyer/favorites')"
        />
        <DashboardStatCard
          title="Saved Searches"
          :value="stats.savedSearches"
          subtitle="Active search alerts"
          icon="document"
          iconBgColor="bg-slate-100 dark:bg-slate-800"
          iconColor="text-slate-800 dark:text-slate-200"
        />
        <DashboardStatCard
          title="Upcoming Appointments"
          :value="stats.upcomingAppointments"
          subtitle="Property viewings"
          icon="calendar"
          iconBgColor="bg-slate-100 dark:bg-slate-800"
          iconColor="text-slate-800 dark:text-slate-200"
          action="View"
          @action="$router.push('/buyer/appointments')"
        />
        <DashboardStatCard
          title="Active Inquiries"
          :value="stats.activeInquiries"
          subtitle="Ongoing conversations"
          icon="message"
          iconBgColor="bg-slate-100 dark:bg-slate-800"
          iconColor="text-slate-800 dark:text-slate-200"
        />
      </div>

      <!-- Additional Statistics -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <DashboardStatCard
          title="Compare List"
          :value="stats.compareList"
          subtitle="Properties to compare"
          icon="document"
          iconBgColor="bg-slate-100 dark:bg-slate-800"
          iconColor="text-slate-800 dark:text-slate-200"
          action="Compare"
          @action="$router.push('/buyer/compare')"
        />
        <DashboardStatCard
          title="Recent Views"
          :value="stats.recentViews"
          subtitle="Last 30 days"
          icon="eye"
          iconBgColor="bg-slate-100 dark:bg-slate-800"
          iconColor="text-slate-800 dark:text-slate-200"
        />
        <DashboardStatCard
          title="Short Stays"
          :value="stats.shortStays"
          subtitle="Bookings"
          icon="calendar"
          iconBgColor="bg-slate-100 dark:bg-slate-800"
          iconColor="text-slate-800 dark:text-slate-200"
          action="View"
          @action="$router.push('/buyer/bookings')"
        />
        <DashboardStatCard
          title="Reviews Written"
          :value="stats.reviewsWritten"
          subtitle="Your feedback"
          icon="star"
          iconBgColor="bg-yellow-100"
          iconColor="text-yellow-600"
        />
      </div>

      <!-- Recommended Properties -->
      <DashboardCard title="Recommended For You" description="Properties matching your preferences" divider>
        <template #actions>
          <button @click="$router.push('/properties')" class="text-sm text-active-blue hover:text-blue-700 font-medium">
            Browse All
          </button>
        </template>

        <div v-if="recommendedProperties.length === 0">
          <EmptyState
            title="No recommendations yet"
            description="Start exploring properties to get personalized recommendations"
            icon="home"
            actionText="Search Properties"
            @action="$router.push('/properties')"
          />
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="property in recommendedProperties" :key="property.id"
            class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg hover:border-active-blue transition-all cursor-pointer group"
            @click="viewProperty(property.id)">
            <div class="relative aspect-video">
              <img :src="property.image" :alt="property.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"/>
              <button 
                @click.stop="toggleFavorite(property.id)"
                class="absolute top-2 right-2 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition-colors"
              >
                <svg class="w-5 h-5" :class="property.isFavorited ? 'text-red-500 fill-current' : 'text-gray-600'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
              </button>
              <span class="absolute bottom-2 right-2 px-2 py-1 bg-black/70 text-white text-xs rounded">
                {{ property.listingType }}
              </span>
            </div>
            <div class="p-4">
              <h4 class="font-semibold text-navy-950 mb-1 truncate">{{ property.title }}</h4>
              <p class="text-xs text-gray-600 mb-2 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ property.location }}
              </p>
              <div class="flex items-center gap-3 text-xs text-gray-600 mb-3">
                <span v-if="property.bedrooms">{{ property.bedrooms }} Beds</span>
                <span v-if="property.bathrooms">{{ property.bathrooms }} Baths</span>
                <span v-if="property.area">{{ property.area }} m²</span>
              </div>
              <div class="flex items-center justify-between">
                <p class="text-lg font-bold text-navy-950">{{ formatCurrency(property.price) }}</p>
                <button class="px-3 py-1.5 bg-betlink-green text-white rounded-lg hover:bg-betlink-green-light text-xs font-medium">
                  View Details
                </button>
              </div>
            </div>
          </div>
        </div>
      </DashboardCard>

      <!-- Two Column Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Saved Properties -->
        <DashboardCard title="My Favorites" description="Your saved property listings" divider>
          <template #actions>
            <button @click="$router.push('/buyer/favorites')" class="text-sm text-active-blue hover:text-blue-700 font-medium">
              View All
            </button>
          </template>

          <div v-if="savedProperties.length === 0">
            <EmptyState
              title="No saved properties"
              description="Start saving properties you're interested in"
              icon="heart"
              actionText="Browse Properties"
              @action="$router.push('/properties')"
            />
          </div>
          <div v-else class="space-y-3">
            <div v-for="property in savedProperties.slice(0, 4)" :key="property.id"
              class="flex gap-3 p-3 border border-gray-200 rounded-lg hover:border-active-blue transition-colors cursor-pointer"
              @click="viewProperty(property.id)">
              <img :src="property.image" :alt="property.title" class="w-20 h-20 rounded-lg object-cover flex-shrink-0"/>
              <div class="flex-1 min-w-0">
                <h4 class="font-semibold text-sm text-navy-950 mb-1 truncate">{{ property.title }}</h4>
                <p class="text-xs text-gray-600 mb-2">{{ property.location }}</p>
                <div class="flex items-center justify-between">
                  <p class="text-sm font-bold text-navy-950">{{ formatCurrency(property.price) }}</p>
                  <button @click.stop="removeFavorite(property.id)" class="text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </DashboardCard>

        <!-- Saved Searches -->
        <DashboardCard title="Saved Searches" description="Your search alerts" divider>
          <template #actions>
            <button @click="$router.push('/buyer/saved-searches')" class="text-sm text-active-blue hover:text-blue-700 font-medium">
              View All
            </button>
          </template>

          <div v-if="savedSearches.length === 0">
            <EmptyState
              title="No saved searches"
              description="Save your searches to get instant alerts for new listings"
              icon="search"
            />
          </div>
          <div v-else class="space-y-3">
            <div v-for="search in savedSearches" :key="search.id"
              class="p-3 border border-gray-200 rounded-lg hover:border-active-blue transition-colors">
              <div class="flex items-start justify-between mb-2">
                <h4 class="font-semibold text-sm text-navy-950">{{ search.name }}</h4>
                <span :class="search.alertEnabled ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'" 
                  class="px-2 py-0.5 rounded text-xs font-medium">
                  {{ search.alertEnabled ? 'Active' : 'Paused' }}
                </span>
              </div>
              <div class="flex flex-wrap gap-2 mb-2">
                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded">{{ search.location }}</span>
                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded">{{ search.propertyType }}</span>
                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded">{{ formatCurrency(search.minPrice) }} - {{ formatCurrency(search.maxPrice) }}</span>
                <span v-if="search.bedrooms" class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded">{{ search.bedrooms }} Beds</span>
              </div>
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">Last alert: {{ search.lastAlert }}</span>
                <button class="px-3 py-1 bg-active-blue text-white rounded hover:bg-blue-700 font-medium">
                  Search Again
                </button>
              </div>
            </div>
          </div>
        </DashboardCard>
      </div>

      <!-- Appointments & Inquiries -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Appointments -->
        <DashboardCard title="Upcoming Property Viewings" description="Your scheduled appointments" divider>
          <template #actions>
            <button @click="$router.push('/buyer/appointments')" class="text-sm text-active-blue hover:text-blue-700 font-medium">
              View All
            </button>
          </template>

          <div v-if="appointments.length === 0">
            <EmptyState
              title="No appointments scheduled"
              description="Book property viewings to find your perfect home"
              icon="calendar"
            />
          </div>
          <div v-else class="space-y-3">
            <div v-for="appointment in appointments" :key="appointment.id"
              class="p-3 border border-gray-200 rounded-lg hover:border-active-blue transition-colors">
              <div class="flex items-start justify-between mb-2">
                <div>
                  <h4 class="font-semibold text-sm text-navy-950 mb-1">{{ appointment.propertyTitle }}</h4>
                  <p class="text-xs text-gray-600">{{ appointment.location }}</p>
                </div>
                <StatusBadge :status="appointment.status" type="appointment" />
              </div>
              <div class="flex items-center gap-2 text-xs text-gray-600 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>{{ appointment.date }} at {{ appointment.time }}</span>
                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded">{{ appointment.type }}</span>
              </div>
              <div class="flex items-center gap-2">
                <img :src="appointment.ownerAvatar" :alt="appointment.ownerName" class="w-6 h-6 rounded-full"/>
                <span class="text-xs text-gray-600">with {{ appointment.ownerName }}</span>
              </div>
            </div>
          </div>
        </DashboardCard>

        <!-- Short Stay Bookings -->
        <DashboardCard title="Short Stay Bookings" description="Your upcoming stays" divider>
          <template #actions>
            <button @click="$router.push('/buyer/bookings')" class="text-sm text-active-blue hover:text-blue-700 font-medium">
              View All
            </button>
          </template>

          <div v-if="bookings.length === 0">
            <EmptyState
              title="No active bookings"
              description="Book short-term rentals for your stay in Ethiopia"
              icon="calendar"
            />
          </div>
          <div v-else class="space-y-3">
            <div v-for="booking in bookings" :key="booking.id"
              class="p-3 border border-gray-200 rounded-lg hover:border-active-blue transition-colors">
              <div class="flex items-start justify-between mb-2">
                <h4 class="font-semibold text-sm text-navy-950">{{ booking.propertyTitle }}</h4>
                <StatusBadge :status="booking.status" type="booking" />
              </div>
              <p class="text-xs text-gray-600 mb-2">{{ booking.location }}</p>
              <div class="flex items-center justify-between text-xs text-gray-600 mb-2">
                <div class="flex items-center gap-2">
                  <span>{{ booking.checkIn }}</span>
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                  </svg>
                  <span>{{ booking.checkOut }}</span>
                </div>
                <span>{{ booking.nights }} nights</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-xs text-gray-600">{{ booking.guests }} guests</span>
                <span class="text-sm font-bold text-navy-950">{{ formatCurrency(booking.totalPrice) }}</span>
              </div>
            </div>
          </div>
        </DashboardCard>
      </div>

      <!-- Compare Properties -->
      <DashboardCard v-if="compareList.length > 0" title="Compare Properties" description="Side-by-side comparison">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-gray-200">
                <th class="text-left py-3 px-2 font-semibold text-navy-950">Feature</th>
                <th v-for="property in compareList" :key="property.id" class="text-left py-3 px-2">
                  <div class="flex flex-col gap-2">
                    <img :src="property.image" :alt="property.title" class="w-full h-24 object-cover rounded-lg"/>
                    <p class="font-semibold text-navy-950 text-xs">{{ property.title }}</p>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr>
                <td class="py-2 px-2 text-gray-600">Price</td>
                <td v-for="property in compareList" :key="property.id" class="py-2 px-2 font-semibold">
                  {{ formatCurrency(property.price) }}
                </td>
              </tr>
              <tr>
                <td class="py-2 px-2 text-gray-600">Location</td>
                <td v-for="property in compareList" :key="property.id" class="py-2 px-2">{{ property.location }}</td>
              </tr>
              <tr>
                <td class="py-2 px-2 text-gray-600">Bedrooms</td>
                <td v-for="property in compareList" :key="property.id" class="py-2 px-2">{{ property.bedrooms }}</td>
              </tr>
              <tr>
                <td class="py-2 px-2 text-gray-600">Bathrooms</td>
                <td v-for="property in compareList" :key="property.id" class="py-2 px-2">{{ property.bathrooms }}</td>
              </tr>
              <tr>
                <td class="py-2 px-2 text-gray-600">Area</td>
                <td v-for="property in compareList" :key="property.id" class="py-2 px-2">{{ property.area }} m²</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mt-4 text-center">
          <button @click="$router.push('/buyer/compare')" class="px-4 py-2 bg-active-blue text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
            View Full Comparison
          </button>
        </div>
      </DashboardCard>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../../composables/useAuth'
import { useDashboardStore } from '../../stores/dashboardStore'
import { favoriteService } from '../../services/favoriteService'
import DashboardCard from '../../components/dashboard/DashboardCard.vue'
import DashboardStatCard from '../../components/dashboard/DashboardStatCard.vue'
import QuickActionCard from '../../components/dashboard/QuickActionCard.vue'
import StatusBadge from '../../components/dashboard/StatusBadge.vue'
import EmptyState from '../../components/dashboard/EmptyState.vue'
import LoadingSkeleton from '../../components/dashboard/LoadingSkeleton.vue'

const router = useRouter()
const { user } = useAuth()
const dashboardStore = useDashboardStore()

const isLoading = ref(false)

const stats = computed(() => dashboardStore.stats || {
  savedProperties: 0,
  savedSearches: 0,
  upcomingAppointments: 0,
  activeInquiries: 0,
  compareList: 0,
  recentViews: 0,
  shortStays: 0,
  reviewsWritten: 0
})

const recommendedProperties = computed(() => {
  return dashboardStore.recommended.length > 0 ? dashboardStore.recommended : [
    { id: 1, title: 'Modern 2 Bedroom Apartment', location: 'Bole, Addis Ababa', price: 6500000, listingType: 'For Rent', image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&q=80&auto=format&fit=crop&ixlib=rb-4.0.3', bedrooms: 2, bathrooms: 2, area: 120, isFavorited: false },
    { id: 2, title: 'Luxury Penthouse with View', location: 'CMC, Addis Ababa', price: 35000000, listingType: 'For Sale', image: 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=400&h=300&q=80&auto=format&fit=crop&ixlib=rb-4.0.3', bedrooms: 3, bathrooms: 3, area: 200, isFavorited: true },
    { id: 3, title: 'Cozy Studio Downtown', location: 'Piazza, Addis Ababa', price: 3500000, listingType: 'For Rent', image: 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=300&q=80&auto=format&fit=crop&ixlib=rb-4.0.3', bedrooms: 1, bathrooms: 1, area: 45, isFavorited: false }
  ]
})

const savedProperties = ref([])
const savedSearches = ref([])
const appointments = ref([])
const bookings = ref([])
const compareList = ref([])

function formatCurrency(amount) {
  return new Intl.NumberFormat('en-ET', {
    style: 'currency',
    currency: 'ETB',
    minimumFractionDigits: 0
  }).format(amount)
}

function viewProperty(id) {
  router.push(`/buyer/properties/${id}`)
}

async function toggleFavorite(id) {
  const property = recommendedProperties.value.find(p => p.id === id)
  if (property) {
    if (property.isFavorited) {
      await favoriteService.removeFavorite(id)
      property.isFavorited = false
    } else {
      await favoriteService.addFavorite(id)
      property.isFavorited = true
    }
  }
}

async function removeFavorite(id) {
  savedProperties.value = savedProperties.value.filter(p => p.id !== id)
  await favoriteService.removeFavorite(id)
}

async function loadData() {
  if (!dashboardStore.lastFetched && (!dashboardStore.stats || !dashboardStore.stats.savedProperties)) {
    isLoading.value = true
  }
  try {
    await dashboardStore.fetchDashboardData(false)
    const favRes = await favoriteService.getFavorites()
    const favItems = favRes?.data?.data || favRes?.data || []
    savedProperties.value = Array.isArray(favItems) ? favItems : []
  } catch (err) {
    console.error('Failed to load buyer dashboard data:', err)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>
