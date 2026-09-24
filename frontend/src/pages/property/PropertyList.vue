<template>
  <div class="space-y-4">
    <!-- Filter & Search Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Search Properties</label>
          <input
            v-model="searchQuery"
            @input="debounceSearch"
            type="text"
            placeholder="Search title, city, owner..."
            class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-sm text-slate-800 dark:text-white focus:outline-none focus:border-emerald-500"
          />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Status</label>
          <select 
            v-model="filterStatus" 
            @change="fetchProperties"
            class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-sm text-slate-800 dark:text-white focus:outline-none focus:border-emerald-500"
          >
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="pending">Pending</option>
            <option value="rejected">Rejected</option>
            <option value="sold">Sold</option>
            <option value="rented">Rented</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Listing Type</label>
          <select 
            v-model="filterListing" 
            @change="fetchProperties"
            class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-sm text-slate-800 dark:text-white focus:outline-none focus:border-emerald-500"
          >
            <option value="">All Listings</option>
            <option value="for_sale">For Sale</option>
            <option value="for_rent">For Rent</option>
            <option value="short_stay">Short Stay</option>
          </select>
        </div>
        <div class="flex items-end gap-2">
          <button
            @click="resetFilters"
            class="w-full px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
          >
            Reset Filters
          </button>
        </div>
      </div>

      <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500">
        <span>Showing <strong class="text-slate-900 dark:text-white">{{ properties.length }}</strong> of <strong class="text-slate-900 dark:text-white">{{ totalItems }}</strong> live database listings</span>
        <button
          @click="fetchProperties"
          class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 font-semibold"
        >
          <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': isLoading }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Refresh
        </button>
      </div>
    </div>

    <!-- DataGrid Table -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800">
            <tr>
              <th class="px-4 py-3 text-xs font-semibold text-slate-600 dark:text-slate-400">Property</th>
              <th class="px-4 py-3 text-xs font-semibold text-slate-600 dark:text-slate-400">Location</th>
              <th class="px-4 py-3 text-xs font-semibold text-slate-600 dark:text-slate-400">Price</th>
              <th class="px-4 py-3 text-xs font-semibold text-slate-600 dark:text-slate-400">Status</th>
              <th class="px-4 py-3 text-xs font-semibold text-slate-600 dark:text-slate-400">Featured Criteria</th>
              <th class="px-4 py-3 text-xs font-semibold text-slate-600 dark:text-slate-400">Owner</th>
              <th class="px-4 py-3 text-xs font-semibold text-slate-600 dark:text-slate-400 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr v-if="isLoading" class="text-center">
              <td colspan="7" class="py-12 text-sm text-slate-400">
                <div class="flex items-center justify-center gap-2">
                  <span class="w-4 h-4 rounded-full border-2 border-emerald-500 border-t-transparent animate-spin"></span>
                  Loading properties from database...
                </div>
              </td>
            </tr>
            <tr v-else-if="properties.length === 0" class="text-center">
              <td colspan="7" class="py-12 text-sm text-slate-400">
                No properties match the selected criteria.
              </td>
            </tr>
            <tr v-for="prop in properties" :key="prop.id" class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 shrink-0 border border-slate-200 dark:border-slate-700">
                    <img 
                      :src="prop.primary_image?.image_url || prop.primary_image?.url || prop.images?.[0]?.url || 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=100'" 
                      :alt="prop.title" 
                      class="w-full h-full object-cover"
                    />
                  </div>
                  <div>
                    <RouterLink :to="`/properties/${prop.slug || prop.id}`" class="text-sm font-bold text-slate-900 dark:text-white hover:text-emerald-600 line-clamp-1">
                      {{ prop.title }}
                    </RouterLink>
                    <p class="text-[11px] text-slate-400 uppercase font-semibold">{{ formatListingType(prop.listing_type) }} &bull; {{ prop.property_type?.name || 'Property' }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300">
                {{ prop.address?.city?.name || prop.address?.subcity || 'Addis Ababa' }}
              </td>
              <td class="px-4 py-3 text-xs font-bold text-slate-900 dark:text-white">
                ETB {{ Number(prop.price).toLocaleString() }}
              </td>
              <td class="px-4 py-3">
                <span :class="getStatusBadge(prop.status)" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">
                  {{ prop.status }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div v-if="prop.is_featured" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/50">
                  <span class="text-amber-500 font-bold text-xs">★</span>
                  <div>
                    <p class="text-[11px] font-bold text-amber-800 dark:text-amber-300 leading-none">
                      {{ formatFeaturedReason(prop.featured_reason) }}
                    </p>
                    <span class="text-[9px] text-amber-600 dark:text-amber-400">Weight: {{ prop.featured_priority || 1 }}/10</span>
                  </div>
                </div>
                <span v-else class="text-[11px] text-slate-400">Standard</span>
              </td>
              <td class="px-4 py-3">
                <p class="text-xs text-slate-800 dark:text-slate-200 font-medium">{{ prop.owner?.name || 'Owner' }}</p>
                <p class="text-[10px] text-slate-400">{{ prop.owner?.email }}</p>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <!-- Feature Rules Modal Button -->
                  <button
                    @click="openFeatureModal(prop)"
                    class="p-1.5 text-xs font-medium rounded-lg hover:bg-amber-50 dark:hover:bg-amber-950/40 text-amber-600 dark:text-amber-400"
                    :title="prop.is_featured ? 'Edit Featured Criteria' : 'Promote to Featured'"
                  >
                    ★
                  </button>

                  <!-- Approve Button (if pending) -->
                  <button
                    v-if="prop.status === 'pending'"
                    @click="handleApprove(prop)"
                    class="p-1.5 text-xs font-medium rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400"
                    title="Approve Listing"
                  >
                    ✓
                  </button>

                  <!-- Reject Button (if pending) -->
                  <button
                    v-if="prop.status === 'pending'"
                    @click="handleRejectPrompt(prop)"
                    class="p-1.5 text-xs font-medium rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-600 dark:text-rose-400"
                    title="Reject Listing"
                  >
                    ✕
                  </button>

                  <!-- Delete Button -->
                  <button
                    @click="handleDelete(prop)"
                    class="p-1.5 text-xs font-medium rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-rose-600"
                    title="Delete Property"
                  >
                    🗑️
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between px-4 py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/60 text-xs">
        <span class="text-slate-500">Page {{ currentPage }} of {{ lastPage }} ({{ totalItems }} items)</span>
        <div class="flex items-center gap-1">
          <button
            @click="changePage(currentPage - 1)"
            :disabled="currentPage <= 1"
            class="px-2.5 py-1 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-40 disabled:cursor-not-allowed"
          >
            ← Prev
          </button>
          <button
            @click="changePage(currentPage + 1)"
            :disabled="currentPage >= lastPage"
            class="px-2.5 py-1 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-40 disabled:cursor-not-allowed"
          >
            Next →
          </button>
        </div>
      </div>
    </div>

    <!-- Featured Property Criteria & Rules Modal -->
    <div 
      v-if="showFeatureModal" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs animate-fade-in"
      @click.self="showFeatureModal = false"
    >
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
          <div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <span class="text-amber-500">★</span>
              Featured Property Rules & Criteria
            </h3>
            <p class="text-xs text-slate-400 truncate max-w-xs mt-0.5">{{ selectedProperty?.title }}</p>
          </div>
          <button @click="showFeatureModal = false" class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
        </div>

        <div class="space-y-3.5">
          <!-- Toggle Active Feature -->
          <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
            <div>
              <p class="text-xs font-bold text-slate-800 dark:text-white">Featured Status</p>
              <p class="text-[11px] text-slate-400">Display prominently across hero and discovery sections</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="featureForm.is_featured" class="sr-only peer" />
              <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
            </label>
          </div>

          <div v-if="featureForm.is_featured" class="space-y-3">
            <!-- Criteria Reason -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Featured Criteria Reason</label>
              <select 
                v-model="featureForm.featured_reason" 
                class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs text-slate-800 dark:text-white focus:outline-none focus:border-amber-500"
              >
                <option value="admin_spotlight">Curated Editorial Pick (Admin Spotlight)</option>
                <option value="promoted">Paid Promotion / Boosted Listing</option>
                <option value="verified_badge">Vetted &amp; Verified Badge</option>
                <option value="high_rating">Top Rated &amp; High User Engagement</option>
              </select>
            </div>

            <!-- Priority Weight -->
            <div>
              <div class="flex justify-between items-center mb-1">
                <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Priority Weight (1 to 10)</label>
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400">{{ featureForm.featured_priority }} / 10</span>
              </div>
              <input 
                type="range" 
                min="1" 
                max="10" 
                v-model.number="featureForm.featured_priority" 
                class="w-full accent-amber-500 cursor-pointer"
              />
              <p class="text-[10px] text-slate-400 mt-0.5">Higher priority listings display first on the homepage and search headers.</p>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Start Date</label>
                <input 
                  type="date" 
                  v-model="featureForm.featured_from" 
                  class="w-full px-3 py-1.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs text-slate-800 dark:text-white"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">End Date</label>
                <input 
                  type="date" 
                  v-model="featureForm.featured_until" 
                  class="w-full px-3 py-1.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs text-slate-800 dark:text-white"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
          <button
            type="button"
            @click="showFeatureModal = false"
            class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl"
          >
            Cancel
          </button>
          <button
            type="button"
            @click="saveFeatureCriteria"
            :disabled="isSavingFeature"
            class="px-4 py-2 text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-xl transition-colors shadow-xs disabled:opacity-50"
          >
            {{ isSavingFeature ? 'Saving...' : 'Apply Featured Rules' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Reject Reason Modal -->
    <div 
      v-if="showRejectModal" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs animate-fade-in"
      @click.self="showRejectModal = false"
    >
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-sm w-full p-5 space-y-4">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Reject Property Listing</h3>
        <p class="text-xs text-slate-500">Please provide a reason to notify the property owner.</p>
        <textarea
          v-model="rejectReason"
          rows="3"
          placeholder="e.g. Missing valid title deed, blurry images, inaccurate pricing..."
          class="w-full p-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs text-slate-800 dark:text-white focus:outline-none focus:border-rose-500"
        ></textarea>
        <div class="flex items-center justify-end gap-2">
          <button @click="showRejectModal = false" class="px-3 py-1.5 text-xs text-slate-500">Cancel</button>
          <button @click="submitReject" class="px-3 py-1.5 text-xs font-bold text-white bg-rose-600 rounded-xl hover:bg-rose-700">Confirm Rejection</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminService } from '@/services/adminService'
import { useToastStore } from '@/stores/toast'

const toastStore = useToastStore()

const properties = ref([])
const totalItems = ref(0)
const currentPage = ref(1)
const lastPage = ref(1)
const pageSize = ref(15)
const isLoading = ref(false)

const searchQuery = ref('')
const filterStatus = ref('')
const filterListing = ref('')
let searchDebounceTimer = null

// Modals
const showFeatureModal = ref(false)
const isSavingFeature = ref(false)
const selectedProperty = ref(null)
const featureForm = ref({
  is_featured: false,
  featured_priority: 5,
  featured_reason: 'admin_spotlight',
  featured_from: '',
  featured_until: '',
})

const showRejectModal = ref(false)
const rejectReason = ref('')
const propertyToReject = ref(null)

const fetchProperties = async () => {
  isLoading.value = true
  try {
    const params = {
      page: currentPage.value,
      per_page: pageSize.value,
    }
    if (searchQuery.value) params.q = searchQuery.value
    if (filterStatus.value) params.status = filterStatus.value
    if (filterListing.value) params.listing_type = filterListing.value

    const res = await adminService.getProperties(params)
    const raw = res.data || res
    properties.value = raw.data || []
    totalItems.value = raw.meta?.total || raw.total || properties.value.length
    lastPage.value = raw.meta?.last_page || raw.last_page || 1
  } catch (err) {
    toastStore.error('Failed to load properties: ' + (err.message || 'Server error'))
  } finally {
    isLoading.value = false
  }
}

const debounceSearch = () => {
  clearTimeout(searchDebounceTimer)
  searchDebounceTimer = setTimeout(() => {
    currentPage.value = 1
    fetchProperties()
  }, 350)
}

const changePage = (page) => {
  if (page < 1 || page > lastPage.value) return
  currentPage.value = page
  fetchProperties()
}

const resetFilters = () => {
  searchQuery.value = ''
  filterStatus.value = ''
  filterListing.value = ''
  currentPage.value = 1
  fetchProperties()
}

const formatListingType = (type) => {
  if (!type) return 'Listing'
  return type.replace('_', ' ')
}

const formatFeaturedReason = (reason) => {
  const map = {
    'admin_spotlight': 'Spotlight',
    'promoted': 'Promoted',
    'verified_badge': 'Verified Badge',
    'high_rating': 'High Rating',
  }
  return map[reason] || 'Featured'
}

const getStatusBadge = (status) => {
  const map = {
    'active': 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800',
    'pending': 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800',
    'rejected': 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800',
    'sold': 'bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800',
    'rented': 'bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800',
  }
  return map[status?.toLowerCase()] || 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'
}

// Open Feature Criteria Modal
const openFeatureModal = (prop) => {
  selectedProperty.value = prop
  featureForm.value = {
    is_featured: !!prop.is_featured,
    featured_priority: prop.featured_priority || 5,
    featured_reason: prop.featured_reason || 'admin_spotlight',
    featured_from: prop.featured_from ? prop.featured_from.substring(0, 10) : new Date().toISOString().substring(0, 10),
    featured_until: prop.featured_until ? prop.featured_until.substring(0, 10) : new Date(Date.now() + 30 * 86400000).toISOString().substring(0, 10),
  }
  showFeatureModal.value = true
}

const saveFeatureCriteria = async () => {
  if (!selectedProperty.value) return
  isSavingFeature.value = true
  try {
    const res = await adminService.toggleFeature(selectedProperty.value.id, featureForm.value)
    const updated = res.data?.data || res.data || {}
    selectedProperty.value.is_featured = featureForm.value.is_featured
    selectedProperty.value.featured_priority = featureForm.value.featured_priority
    selectedProperty.value.featured_reason = featureForm.value.featured_reason
    toastStore.success(featureForm.value.is_featured ? 'Property featured with active criteria!' : 'Property unfeatured.')
    showFeatureModal.value = false
    fetchProperties()
  } catch (err) {
    toastStore.error('Failed to update featured criteria: ' + (err.message || 'Error'))
  } finally {
    isSavingFeature.value = false
  }
}

// Approve
const handleApprove = async (prop) => {
  try {
    await adminService.approveProperty(prop.id)
    prop.status = 'active'
    toastStore.success(`"${prop.title}" approved and published!`)
  } catch (err) {
    toastStore.error('Failed to approve property: ' + (err.message || 'Error'))
  }
}

// Reject
const handleRejectPrompt = (prop) => {
  propertyToReject.value = prop
  rejectReason.value = ''
  showRejectModal.value = true
}

const submitReject = async () => {
  if (!propertyToReject.value) return
  try {
    await adminService.rejectProperty(propertyToReject.value.id, rejectReason.value || 'Listing does not meet quality guidelines')
    propertyToReject.value.status = 'rejected'
    toastStore.success(`"${propertyToReject.value.title}" has been rejected.`)
    showRejectModal.value = false
  } catch (err) {
    toastStore.error('Failed to reject property: ' + (err.message || 'Error'))
  }
}

// Delete
const handleDelete = async (prop) => {
  if (!confirm(`Are you sure you want to permanently delete "${prop.title}"?`)) return
  try {
    await adminService.deleteProperty(prop.id)
    properties.value = properties.value.filter(p => p.id !== prop.id)
    totalItems.value = Math.max(0, totalItems.value - 1)
    toastStore.success('Property deleted from database.')
  } catch (err) {
    toastStore.error('Failed to delete property: ' + (err.message || 'Error'))
  }
}

onMounted(() => {
  fetchProperties()
})
</script>
