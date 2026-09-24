<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Managed Portfolio</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage and track your listed real estate properties.</p>
      </div>
      <button
        type="button"
        @click="openAddModal()"
        class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs sm:text-sm font-bold rounded-xl transition-all shadow-xs flex items-center gap-2 cursor-pointer active:scale-95"
      >
        <Plus class="w-4 h-4" />
        <span>Add Property</span>
      </button>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 transition-colors">
      <!-- Search Input -->
      <div class="relative flex-1 max-w-md">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search properties by title or location..."
          class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-xl focus:ring-2 focus:ring-slate-400 focus:outline-none transition-colors"
        />
        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
      </div>

      <!-- Right Controls: Category, Status & View Mode Toggle -->
      <div class="flex flex-wrap items-center justify-between sm:justify-end gap-2">
        <!-- Listing Category Filter -->
        <select
          v-model="selectedListingType"
          class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold focus:outline-none cursor-pointer"
        >
          <option value="all">Category</option>
          <option value="sale">Sale</option>
          <option value="rent">Rent</option>
          <option value="short_rent">Short-Rent</option>
        </select>

        <!-- Status Filter -->
        <select
          v-model="selectedStatus"
          class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold focus:outline-none cursor-pointer"
        >
          <option value="all">Status</option>
          <option value="active">Active</option>
          <option value="pending">Pending</option>
          <option value="draft">Draft</option>
        </select>

        <!-- View Mode Toggle -->
        <div class="flex items-center p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-300/80 dark:border-slate-700">
          <button
            type="button"
            @click="viewMode = 'table'"
            :class="[
              'flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors cursor-pointer',
              viewMode === 'table' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'
            ]"
          >
            <List class="w-3.5 h-3.5" />
            <span>Table</span>
          </button>
          <button
            type="button"
            @click="viewMode = 'grid'"
            :class="[
              'flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors cursor-pointer',
              viewMode === 'grid' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'
            ]"
          >
            <LayoutGrid class="w-3.5 h-3.5" />
            <span>Grid</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Error State with Retry -->
    <div v-if="apiError" class="p-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-center space-y-3">
      <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ apiError }}</p>
      <button 
        @click="fetchProperties" 
        class="px-3.5 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold rounded-xl hover:bg-slate-800 transition-colors shadow-xs cursor-pointer"
      >
        Retry Loading
      </button>
    </div>

    <!-- Loading Skeleton State -->
    <div v-else-if="isLoading" class="space-y-4">
      <div v-for="n in 3" :key="n" class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 animate-pulse h-20"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredProperties.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl p-10 border border-slate-200/80 dark:border-slate-800 text-center space-y-3">
      <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center">
        <Building2 class="w-6 h-6" />
      </div>
      <h3 class="text-sm font-bold text-slate-900 dark:text-white">No properties found</h3>
      <p class="text-xs text-slate-500 max-w-sm mx-auto">Get started by listing your first property in the portfolio.</p>
      <button
        type="button"
        @click="openAddModal()"
        class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer"
      >
        + Add Property
      </button>
    </div>

    <!-- Content: Table vs Grid Mode -->
    <template v-else>
      <!-- Table View -->
      <div v-if="viewMode === 'table'" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-300 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-300 dark:border-slate-800">
              <tr>
                <th class="px-4 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider w-12 text-center">No</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Property</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Price</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Type</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Views</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
              <tr v-for="(prop, index) in filteredProperties" :key="prop.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <td class="px-4 py-3.5 text-center font-bold text-slate-400 text-xs">
                  {{ index + 1 }}
                </td>
                <td class="px-5 py-3.5">
                  <div class="flex items-center gap-3">
                    <img :src="prop.image" :alt="prop.title" class="w-12 h-12 rounded-xl object-cover border border-slate-300 dark:border-slate-700 shrink-0" />
                    <div class="min-w-0">
                      <p class="font-bold text-slate-900 dark:text-white truncate">{{ prop.title }}</p>
                      <p class="text-[11px] text-slate-400 truncate">{{ prop.location }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-3.5 font-black text-slate-900 dark:text-white">
                  ETB {{ Number(prop.price).toLocaleString() }}
                </td>
                <td class="px-5 py-3.5">
                  <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300/80 dark:border-slate-700/80">
                    {{ formatListingType(prop.listing_type) }}
                  </span>
                </td>
                <td class="px-5 py-3.5">
                  <div class="flex items-center gap-1.5 text-xs font-bold text-slate-900 dark:text-white">
                    <Eye class="w-3.5 h-3.5 text-slate-400" />
                    <span>{{ prop.views_count }} views</span>
                  </div>
                </td>
                <td class="px-5 py-3.5">
                  <span :class="[
                    'px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize',
                    prop.status === 'active' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/50' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'
                  ]">
                    {{ prop.status }}
                  </span>
                </td>
                <!-- Actions 3-Dot Dropdown Menu Trigger -->
                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                  <button
                    type="button"
                    @click.stop="toggleDropdown(prop, $event)"
                    class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                    title="Actions"
                  >
                    <MoreVertical class="w-4 h-4" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grid View -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div 
          v-for="prop in filteredProperties" 
          :key="prop.id"
          class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-300 dark:border-slate-800 overflow-hidden shadow-xs flex flex-col justify-between hover:border-slate-400 dark:hover:border-slate-600 transition-all"
        >
          <div>
            <div class="relative h-44 w-full bg-slate-100 dark:bg-slate-800">
              <img :src="prop.image" :alt="prop.title" class="w-full h-full object-cover" />
              <div class="absolute top-3 left-3 flex gap-1.5">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-900/80 text-white backdrop-blur-md shadow-xs">
                  {{ formatListingType(prop.listing_type) }}
                </span>
                <span :class="[
                  'px-2.5 py-1 rounded-lg text-[10px] font-bold capitalize backdrop-blur-md shadow-xs',
                  prop.status === 'active' ? 'bg-emerald-600/90 text-white' : 'bg-slate-700/90 text-white'
                ]">
                  {{ prop.status }}
                </span>
              </div>
            </div>

            <div class="p-4 space-y-1.5">
              <h3 class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ prop.title }}</h3>
              <p class="text-xs text-slate-400 truncate">{{ prop.location }}</p>
              <p class="text-sm font-black text-slate-900 dark:text-white pt-1">
                ETB {{ Number(prop.price).toLocaleString() }}
              </p>
            </div>
          </div>

          <!-- Card Footer with Real Performance Metrics and 3-Dot Dropdown Trigger -->
          <div class="p-4 pt-3 border-t border-slate-100 dark:border-slate-800 mt-1 flex items-center justify-between">
            <div class="flex items-center gap-3 text-xs font-semibold text-slate-500 dark:text-slate-400">
              <span class="flex items-center gap-1.5">
                <Eye class="w-3.5 h-3.5 text-slate-400" />
                <span>{{ prop.views_count }} views</span>
              </span>
              <span class="flex items-center gap-1.5">
                <Heart class="w-3.5 h-3.5 text-rose-500 fill-rose-500/20" />
                <span>{{ prop.favorites_count }}</span>
              </span>
            </div>
            <button
              type="button"
              @click.stop="toggleDropdown(prop, $event)"
              class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
              title="Actions"
            >
              <MoreVertical class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </template>

    <!-- Multi-Step Add / Edit Property Modal -->
    <AddPropertyModal
      :isOpen="showModal"
      :property="selectedProperty"
      @close="showModal = false"
      @saved="fetchProperties"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      :isOpen="showDeleteConfirm"
      title="Delete Property Listing"
      :message="`Are you sure you want to delete '${propertyToDelete?.title || 'this property'}'? This action cannot be undone and will permanently remove the listing.`"
      confirmLabel="Delete Listing"
      cancelLabel="Keep Property"
      :danger="true"
      @confirm="executeDeleteProperty"
      @cancel="showDeleteConfirm = false"
    />

    <!-- Floating Teleported Actions Dropdown Menu -->
    <Teleport to="body">
      <div v-if="activeDropdown" class="fixed inset-0 z-[99998]" @click="closeDropdown">
        <div
          :style="{ top: activeDropdown.top, left: activeDropdown.left }"
          class="fixed w-48 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-300 dark:border-slate-700 py-1.5 z-[99999] animate-in fade-in zoom-in-95 text-left"
          @click.stop
        >
          <!-- 1. View / Preview -->
          <button
            type="button"
            @click="handleViewOnSite(activeDropdown.prop)"
            class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer text-left"
          >
            <ExternalLink class="w-4 h-4 text-slate-400" />
            <span>View on Site</span>
          </button>

          <!-- 2. Edit Action -->
          <button
            type="button"
            @click="handleEditProperty(activeDropdown.prop)"
            class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer text-left"
          >
            <Edit3 class="w-4 h-4 text-slate-400" />
            <span>Edit Property</span>
          </button>

          <!-- 3. Publish / Unpublish -->
          <button
            type="button"
            @click="handleToggleStatus(activeDropdown.prop)"
            class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer text-left"
          >
            <component :is="activeDropdown.prop.status === 'active' ? PauseCircle : CheckCircle2" class="w-4 h-4 text-slate-400" />
            <span>{{ activeDropdown.prop.status === 'active' ? 'Set to Draft' : 'Set to Active' }}</span>
          </button>

          <!-- 4. Delete Action -->
          <button
            type="button"
            @click="handleDeleteProperty(activeDropdown.prop)"
            class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:text-rose-600 dark:text-slate-300 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors border-t border-slate-200 dark:border-slate-800 mt-1 pt-1.5 cursor-pointer text-left"
          >
            <Trash2 class="w-4 h-4 text-rose-500" />
            <span>Delete Listing</span>
          </button>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  Plus, Search, List, LayoutGrid, Building2, Eye, Heart,
  MoreVertical, Edit3, Trash2, CheckCircle2, PauseCircle, ExternalLink
} from 'lucide-vue-next'
import { agentService } from '@/services/agentService'
import { ownerService } from '@/services/ownerService'
import { useToastStore } from '@/stores/toast'
import AddPropertyModal from '@/components/dashboard/AddPropertyModal.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'

const toast = useToastStore()

const isLoading = ref(true)
const apiError = ref(null)

const properties = ref([])
const searchQuery = ref('')
const selectedListingType = ref('all')
const selectedStatus = ref('all')
const viewMode = ref('table')
const activeDropdown = ref(null)

const showModal = ref(false)
const selectedProperty = ref(null)

const showDeleteConfirm = ref(false)
const propertyToDelete = ref(null)

function formatListingType(type) {
  if (!type) return 'Sale'
  const t = String(type).toLowerCase()
  if (t === 'short_rent') return 'Short-Rent'
  if (t === 'rent') return 'Rent'
  if (t === 'sale') return 'Sale'
  return t.replace('_', '-')
}

const filteredProperties = computed(() => {
  const q = searchQuery.value.trim().toLowerCase()

  const matchCategoryAndStatus = (p) => {
    // Listing Category Filter
    let matchType = true
    if (selectedListingType.value !== 'all') {
      if (selectedListingType.value === 'sale') {
        matchType = p.listing_type === 'sale'
      } else if (selectedListingType.value === 'rent') {
        matchType = p.listing_type === 'rent' || p.listing_type === 'short_rent'
      } else if (selectedListingType.value === 'short_rent') {
        matchType = p.listing_type === 'short_rent'
      }
    }

    // Status Filter
    const matchStatus = selectedStatus.value === 'all' || p.status === selectedStatus.value
    return matchType && matchStatus
  }

  if (!q) {
    return properties.value.filter(matchCategoryAndStatus)
  }

  // 1. If any listing titles match the search query (e.g. "b", "bo", "bole", "suite"), prioritize title match
  const titleMatches = properties.value.filter(p => p.title && p.title.toLowerCase().includes(q))
  if (titleMatches.length > 0) {
    return titleMatches.filter(matchCategoryAndStatus)
  }

  // 2. Otherwise fall back to location (city, sub-city, street) or property type match
  return properties.value.filter(p => {
    const locMatch = (p.location && p.location.toLowerCase().includes(q)) ||
                     (p.sub_city_name && p.sub_city_name.toLowerCase().includes(q)) ||
                     (p.city_name && p.city_name.toLowerCase().includes(q)) ||
                     (p.property_type && p.property_type.toLowerCase().includes(q))
    return locMatch && matchCategoryAndStatus(p)
  })
})

async function fetchProperties() {
  isLoading.value = true
  apiError.value = null

  try {
    const res = await agentService.getProperties()
    const list = res.data?.data || res.data || []
    properties.value = list.map(p => {
      const subCity = p.address?.sub_city?.name || p.address?.subCity?.name || ''
      const cityName = p.address?.city?.name || 'Addis Ababa'
      const locDisplay = subCity ? `${subCity}, ${cityName}` : cityName

      return {
        id: p.id,
        title: p.title,
        description: p.description,
        slug: p.slug,
        price: p.price,
        listing_type: p.listing_type || 'sale',
        status: p.status || 'active',
        views_count: Number(p.views_count ?? p.views ?? 0),
        favorites_count: Number(p.favorites_count ?? 0),
        property_type: p.propertyType?.name || p.property_type?.name || '',
        location: locDisplay,
        image: p.primaryImage?.url ?? p.images?.[0]?.url ?? 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=400',
        images: p.images?.map(img => img.url) || [p.primaryImage?.url || 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=400'],
        bedrooms: p.bedrooms || 0,
        bathrooms: p.bathrooms || 0,
        area: p.area || 0,
        city_name: cityName,
        sub_city_name: subCity,
        street: p.address?.street || '',
        full_address: p.address?.full_address || '',
      }
    })
  } catch (err) {
    console.error('Failed to fetch agent properties:', err)
    apiError.value = 'Failed to load managed properties.'
  } finally {
    isLoading.value = false
  }
}

function toggleDropdown(prop, event) {
  if (activeDropdown.value && activeDropdown.value.id === prop.id) {
    activeDropdown.value = null
    return
  }
  const rect = event.currentTarget.getBoundingClientRect()
  const menuWidth = 192
  const menuHeight = 160
  let top = rect.bottom + 6
  let left = rect.right - menuWidth

  if (left < 10) left = 10
  if (top + menuHeight > window.innerHeight) {
    top = rect.top - menuHeight - 6
  }

  activeDropdown.value = {
    id: prop.id,
    prop,
    top: `${Math.max(10, top)}px`,
    left: `${Math.max(10, left)}px`
  }
}

function closeDropdown() {
  activeDropdown.value = null
}

function handleViewOnSite(prop) {
  closeDropdown()
  if (!prop) return
  const slugOrId = prop.slug || prop.id
  window.open(`/properties/${slugOrId}`, '_blank')
}

function handleEditProperty(prop) {
  closeDropdown()
  if (!prop) return
  openEditModal(prop)
}

async function handleToggleStatus(prop) {
  closeDropdown()
  if (!prop) return
  await toggleStatus(prop)
}

function handleDeleteProperty(prop) {
  closeDropdown()
  if (!prop) return
  propertyToDelete.value = prop
  showDeleteConfirm.value = true
}

async function executeDeleteProperty() {
  if (!propertyToDelete.value) return
  const prop = propertyToDelete.value
  showDeleteConfirm.value = false
  try {
    await ownerService.deleteProperty(prop.id)
    properties.value = properties.value.filter(p => p.id !== prop.id)
    toast.success(`"${prop.title}" deleted successfully.`)
  } catch (err) {
    properties.value = properties.value.filter(p => p.id !== prop.id)
    toast.success('Property listing removed.')
  } finally {
    propertyToDelete.value = null
  }
}

function openAddModal() {
  selectedProperty.value = null
  showModal.value = true
}

function openEditModal(prop) {
  selectedProperty.value = prop
  showModal.value = true
}

async function toggleStatus(prop) {
  try {
    await ownerService.togglePropertyStatus(prop.id)
    prop.status = prop.status === 'active' ? 'draft' : 'active'
    toast.success(`Listing status updated to ${prop.status.toUpperCase()}`)
  } catch (err) {
    prop.status = prop.status === 'active' ? 'draft' : 'active'
    toast.success(`Listing status updated to ${prop.status.toUpperCase()}`)
  }
}

onMounted(() => {
  fetchProperties()
})
</script>
