<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 py-8 lg:py-12 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
      <!-- Back Navigation Bar -->
      <div class="flex items-center gap-3">
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
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Back</span>
      </div>
      
      <!-- Header -->
      <div class="text-center max-w-2xl mx-auto space-y-2">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Verified Real Estate Agents</h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Connect with certified, experienced professionals to buy, sell, or rent properties across Ethiopia.</p>
      </div>

      <!-- Search and Filters Toolbar -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 shadow-xs">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 text-xs">
          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Search</label>
            <input
              v-model="searchQuery"
              @input="handleFilter"
              type="text"
              placeholder="Search by agent name, phone or email..."
              class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 placeholder-slate-400 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-slate-400 transition-colors"
            />
          </div>
          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Specialization</label>
            <select
              v-model="selectedSpecialization"
              @change="handleFilter"
              class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-slate-400 transition-colors cursor-pointer"
            >
              <option value="">All Specializations</option>
              <option value="residential">Residential & Luxury Homes</option>
              <option value="commercial">Commercial & Retail Spaces</option>
              <option value="apartments">Apartments & Short Stays</option>
              <option value="villas">Villas & Estates</option>
            </select>
          </div>
          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Location</label>
            <select
              v-model="selectedLocation"
              @change="handleFilter"
              class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-slate-400 transition-colors cursor-pointer"
            >
              <option value="">All Locations</option>
              <option value="bole">Bole, Addis Ababa</option>
              <option value="kazanchis">Kazanchis, Addis Ababa</option>
              <option value="cmc">CMC, Addis Ababa</option>
              <option value="sarbet">Sarbet, Addis Ababa</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div v-for="n in 4" :key="n" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 h-56 animate-pulse"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredAgents.length === 0" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-10 text-center space-y-2">
        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">No verified agents match your filters</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400">Try adjusting your search query or location filter.</p>
      </div>

      <!-- Agents Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <div
          v-for="agent in filteredAgents"
          :key="agent.id"
          class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 rounded-2xl p-5 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col justify-between"
        >
          <div class="space-y-3.5">
            <!-- Avatar & Header -->
            <div class="flex items-center gap-3">
              <img
                :src="agent.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(agent.name) + '&background=0f172a&color=fff'"
                :alt="agent.name"
                class="w-12 h-12 rounded-full object-cover border border-slate-200 dark:border-slate-700 shrink-0"
              />
              <div class="min-w-0 flex-1">
                <div class="flex items-center gap-1.5">
                  <h2 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ agent.name }}</h2>
                  <span v-if="agent.verified" class="text-emerald-500 dark:text-emerald-400 text-xs shrink-0" title="Verified Agent">✓</span>
                </div>
                <p class="text-[11px] text-slate-600 dark:text-slate-300 font-medium truncate">{{ agent.specialization }}</p>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 truncate">{{ agent.location }}</p>
              </div>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-3 gap-2 py-2 px-3 bg-slate-50 dark:bg-slate-950/80 rounded-xl border border-slate-200/80 dark:border-slate-800/80 text-center text-xs">
              <div>
                <span class="text-slate-500 dark:text-slate-400 block text-[10px]">Listings</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">{{ agent.listingsCount || 0 }}</span>
              </div>
              <div>
                <span class="text-slate-500 dark:text-slate-400 block text-[10px]">Rating</span>
                <span class="font-bold text-amber-500 dark:text-amber-400">★ {{ agent.rating || 4.9 }}</span>
              </div>
              <div>
                <span class="text-slate-500 dark:text-slate-400 block text-[10px]">Exp</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">{{ agent.experience || '3+ yrs' }}</span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="pt-3.5 mt-3.5 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
            <a
              :href="`tel:${agent.phone}`"
              class="flex-1 py-2 text-center rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold transition-colors cursor-pointer"
            >
              Call Agent
            </a>
            <RouterLink
              :to="{ path: '/properties', query: { user_id: agent.id, q: agent.name } }"
              class="py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-colors"
            >
              Listings
            </RouterLink>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { agentService } from '@/services/agentService'

const router = useRouter()
const isLoading = ref(true)
const searchQuery = ref('')
const selectedSpecialization = ref('')
const selectedLocation = ref('')
const agents = ref([])

const handleGoBack = () => {
  if (window.history.state && window.history.state.back) {
    router.back()
  } else {
    router.push('/')
  }
}

const filteredAgents = computed(() => {
  return agents.value.filter(agent => {
    const matchesSearch = !searchQuery.value ||
      agent.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      agent.phone?.includes(searchQuery.value) ||
      agent.location?.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchesSpec = !selectedSpecialization.value ||
      agent.specialization?.toLowerCase().includes(selectedSpecialization.value.toLowerCase())

    const matchesLoc = !selectedLocation.value ||
      agent.location?.toLowerCase().includes(selectedLocation.value.toLowerCase())

    return matchesSearch && matchesSpec && matchesLoc
  })
})

async function fetchAgents() {
  isLoading.value = true
  try {
    const res = await agentService.getPublicAgents()
    const data = res.data?.data || res.data || []
    agents.value = data
  } catch (err) {
    console.error('Failed to load public agents:', err)
  } finally {
    isLoading.value = false
  }
}

function handleFilter() {
  // Real-time filtering handled by computed property
}

onMounted(() => {
  fetchAgents()
})
</script>