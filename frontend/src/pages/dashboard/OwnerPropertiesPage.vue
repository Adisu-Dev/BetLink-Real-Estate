<template>
  <div class="space-y-6" @click="closeAllDropdowns">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">My Properties</h1>
        <p class="text-xs text-slate-500 mt-1">Manage and track performance for your listed real estate portfolio.</p>
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
    <div class="bg-white dark:bg-slate-900 p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 transition-colors">
      <!-- Search Input -->
      <div class="relative flex-1 max-w-md">
        <input
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          placeholder="Search records / properties..."
          class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-xl focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:outline-none transition-colors"
        />
        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
      </div>

      <!-- Right Controls: Category, Type, Status & View Mode Toggle -->
      <div class="flex flex-wrap items-center justify-between sm:justify-end gap-2.5">
        <!-- Listing Category Filter -->
        <select
          v-model="selectedListingType"
          @change="onFilterChange"
          class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:outline-none cursor-pointer"
        >
          <option value="all">Category: All</option>
          <option value="sale">Sale</option>
          <option value="rent">Rent</option>
        </select>

        <!-- Property Type Filter (Apartment, Condominium, Villa, etc.) -->
        <select
          v-model="selectedPropertyType"
          @change="onFilterChange"
          class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:outline-none cursor-pointer"
        >
          <option value="all">Type: All</option>
          <option v-for="t in availablePropertyTypes" :key="t" :value="t">
            {{ t }}
          </option>
        </select>

        <!-- Status Filter -->
        <select
          v-model="selectedStatus"
          @change="onFilterChange"
          class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:outline-none cursor-pointer"
        >
          <option value="all">Status: All</option>
          <option value="active">Active</option>
          <option value="draft">Draft</option>
        </select>

        <!-- View Mode Toggle (Table vs Grid) -->
        <div class="flex items-center p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200/60 dark:border-slate-700">
          <button
            type="button"
            @click="viewMode = 'table'"
            :class="[
              'flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors cursor-pointer',
              viewMode === 'table' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'
            ]"
            title="Table View"
          >
            <List class="w-3.5 h-3.5" />
            <span>Table</span>
          </button>
          <button
            type="button"
            @click="viewMode = 'grid'"
            :class="[
              'flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors cursor-pointer',
              viewMode === 'grid' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'
            ]"
            title="Grid View"
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
        @click="loadProperties"
        class="px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-xl hover:bg-rose-700 transition-colors cursor-pointer"
      >
        Retry Loading
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-else-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="n in 3" :key="n" class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs animate-pulse space-y-4">
        <div class="h-44 bg-slate-200 dark:bg-slate-800 rounded-xl"></div>
        <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-3/4"></div>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="properties.length === 0"
      class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 p-12 text-center"
    >
      <Building2 class="w-14 h-14 text-slate-300 dark:text-slate-700 mx-auto mb-3" />
      <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">
        No properties match your filter
      </h3>
      <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">
        Try adjusting your search query, type, or status filter.
      </p>
      <button
        type="button"
        @click="resetFilters"
        class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl transition-colors cursor-pointer"
      >
        Reset Filters
      </button>
    </div>

    <!-- 1. TABLE VIEW -->
    <div v-else-if="viewMode === 'table'" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
      <div class="overflow-x-auto min-h-[260px]">
        <table class="w-full text-left text-xs border-collapse">
          <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800">
            <tr>
              <th class="px-2.5 py-3 font-bold text-slate-500 uppercase text-center w-10">No</th>
              <th class="px-3 py-3 font-bold text-slate-500 uppercase">Property</th>
              <th class="px-3 py-3 font-bold text-slate-500 uppercase whitespace-nowrap">Type</th>
              <th class="px-3 py-3 font-bold text-slate-500 uppercase whitespace-nowrap">Price</th>
              <th class="px-2.5 py-3 font-bold text-slate-500 uppercase text-center whitespace-nowrap">Saves / Likes</th>
              <th class="px-2.5 py-3 font-bold text-slate-500 uppercase text-center whitespace-nowrap">Status</th>
              <th class="px-2.5 py-3 font-bold text-slate-500 uppercase text-right whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr v-for="(prop, index) in properties" :key="prop.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
              <td class="px-2.5 py-2.5 text-center font-bold text-slate-400 dark:text-slate-500">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>
              <td class="px-3 py-2.5 flex items-center gap-2.5 min-w-[200px]">
                <img :src="prop.image || 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80'" class="w-10 h-10 rounded-xl object-cover shrink-0" />
                <div class="min-w-0">
                  <p class="font-bold text-slate-900 dark:text-white truncate max-w-xs">{{ prop.title }}</p>
                  <p class="text-[11px] text-slate-400 truncate">{{ prop.location }}</p>
                </div>
              </td>
              <td class="px-3 py-2.5 font-semibold text-slate-600 dark:text-slate-300 whitespace-nowrap">
                <span class="inline-block px-2 py-0.5 bg-slate-100 dark:bg-slate-800 rounded-md text-[11px]">
                  {{ prop.property_type || 'Residential' }}
                </span>
              </td>
              <td class="px-3 py-2.5 font-bold text-slate-900 dark:text-white whitespace-nowrap">
                ETB {{ Number(prop.price || 0).toLocaleString() }}
              </td>

              <!-- Favorites / Saves Indicator -->
              <td class="px-2.5 py-2.5 text-center whitespace-nowrap">
                <button
                  type="button"
                  @click="openFavoritesModal(prop)"
                  :class="[
                    'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold transition-all cursor-pointer',
                    prop.favorites_count > 0
                      ? 'bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-800'
                      : 'bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500'
                  ]"
                  :title="prop.favorites_count > 0 ? 'Click to see interested clients' : 'No saves yet'"
                >
                  <Heart :class="['w-3.5 h-3.5', prop.favorites_count > 0 ? 'fill-rose-500 text-rose-500' : 'text-slate-400']" />
                  <span>{{ prop.favorites_count || 0 }}</span>
                </button>
              </td>

              <!-- Uniform Status Badge Matching Cancelled Style -->
              <td class="px-2.5 py-2.5 text-center whitespace-nowrap">
                <span
                  class="inline-flex items-center px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60 capitalize"
                >
                  {{ prop.status }}
                </span>
              </td>
              
              <!-- 3-Dots Action Menu -->
              <td class="px-2.5 py-2.5 text-right whitespace-nowrap">
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

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="border-t border-slate-100 dark:border-slate-800 px-4 py-2">
        <BasePagination
          :currentPage="currentPage"
          :totalPages="totalPages"
          :totalItems="totalItemsCount"
          :perPage="perPage"
          @change="onPageChange"
        />
      </div>
    </div>

    <!-- 2. GRID VIEW -->
    <div v-else class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="prop in properties"
          :key="prop.id"
          class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-xl hover:border-slate-300 dark:hover:border-slate-700 transition-all overflow-hidden flex flex-col justify-between"
        >
          <div class="relative h-48 overflow-hidden bg-slate-100 dark:bg-slate-800">
            <img :src="prop.image || 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80'" :alt="prop.title" class="w-full h-full object-cover" />
            
            <!-- Type Badge -->
            <div class="absolute top-3 left-3 flex items-center gap-1.5">
              <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-lg bg-slate-900/80 text-white backdrop-blur-xs shadow-xs">
                {{ prop.listing_type === 'rent' ? 'For Rent' : 'For Sale' }}
              </span>
              <span class="px-2 py-1 text-[10px] font-bold rounded-lg bg-white/90 text-slate-800 dark:bg-slate-900/90 dark:text-slate-200 backdrop-blur-xs">
                {{ prop.property_type }}
              </span>
            </div>

            <!-- Top Right: Quick Saves & Status -->
            <div class="absolute top-3 right-3 flex items-center gap-1.5">
              <button
                type="button"
                @click.stop="openFavoritesModal(prop)"
                class="flex items-center gap-1 px-2 py-1 bg-white/95 dark:bg-slate-900/95 rounded-lg text-[10px] font-bold text-rose-600 dark:text-rose-400 shadow-sm cursor-pointer hover:scale-105 transition-all"
                title="View interested buyers"
              >
                <Heart class="w-3 h-3 fill-rose-500 text-rose-500" />
                <span>{{ prop.favorites_count || 0 }}</span>
              </button>
              
              <span
                class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100/90 dark:bg-slate-800/90 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60 shadow-xs backdrop-blur-xs capitalize"
              >
                {{ prop.status }}
              </span>
            </div>

            <div class="absolute bottom-3 left-3 text-white font-black text-lg drop-shadow-md">
              ETB {{ Number(prop.price || 0).toLocaleString() }}
              <span v-if="prop.listing_type === 'rent'" class="text-xs font-normal text-white/80">/month</span>
            </div>
          </div>

          <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
            <div>
              <h3 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white line-clamp-1">
                {{ prop.title }}
              </h3>
              <p class="text-xs text-slate-500 flex items-center gap-1.5 mt-1 truncate">
                <MapPin class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                {{ prop.location || 'Addis Ababa' }}
              </p>

              <div class="flex items-center gap-4 text-xs text-slate-600 dark:text-slate-400 pt-3 mt-3 border-t border-slate-100 dark:border-slate-800">
                <span class="flex items-center gap-1"><BedDouble class="w-3.5 h-3.5" /> {{ prop.bedrooms || 0 }} Beds</span>
                <span class="flex items-center gap-1"><Bath class="w-3.5 h-3.5" /> {{ prop.bathrooms || 0 }} Baths</span>
                <span class="flex items-center gap-1"><Maximize2 class="w-3.5 h-3.5" /> {{ prop.area || 120 }} m²</span>
              </div>
            </div>

            <!-- Actions Footer -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800 gap-2">
              <button
                type="button"
                @click="openEditModal(prop)"
                class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl transition-all shadow-xs flex items-center gap-1.5 cursor-pointer active:scale-95"
              >
                <Edit3 class="w-3.5 h-3.5" />
                <span>Manage</span>
              </button>

              <!-- 3-Dots Action Dropdown Trigger -->
              <button
                type="button"
                @click.stop="toggleDropdown(prop, $event)"
                class="p-1.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer"
                title="More Actions"
              >
                <MoreVertical class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination in Grid View -->
      <div v-if="totalPages > 1" class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <BasePagination
          :currentPage="currentPage"
          :totalPages="totalPages"
          :totalItems="totalItemsCount"
          :perPage="perPage"
          @change="onPageChange"
        />
      </div>
    </div>

    <!-- Interested Buyers / Favorites Modal -->
    <div v-if="showFavoritesModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-md p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
          <div class="flex items-center gap-2">
            <div class="p-2 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-xl">
              <Heart class="w-4 h-4 fill-rose-500" />
            </div>
            <div>
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Interested Clients</h3>
              <p class="text-[11px] text-slate-400">Favorited by prospective buyers</p>
            </div>
          </div>
          <button @click="showFavoritesModal = false" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-white cursor-pointer">
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Property Context Info -->
        <div v-if="selectedFavoritesProp" class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl">
          <img :src="selectedFavoritesProp.image" class="w-12 h-12 rounded-lg object-cover bg-slate-200 shrink-0" />
          <div class="min-w-0">
            <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ selectedFavoritesProp.title }}</p>
            <p class="text-[11px] font-semibold text-slate-500">ETB {{ Number(selectedFavoritesProp.price || 0).toLocaleString() }}</p>
          </div>
        </div>

        <!-- List of Interested Clients -->
        <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
          <div
            v-for="client in selectedFavoritesProp?.favorited_by || []"
            :key="client.id"
            class="py-3 flex items-center justify-between gap-3"
          >
            <div class="min-w-0">
              <p class="text-xs font-bold text-slate-900 dark:text-white">{{ client.user_name }}</p>
              <p class="text-[11px] text-slate-500 truncate">{{ client.phone !== '—' ? client.phone : client.email }}</p>
            </div>
            <span class="text-[10px] text-slate-400 font-medium shrink-0">{{ client.saved_at }}</span>
          </div>

          <div v-if="!selectedFavoritesProp?.favorited_by || selectedFavoritesProp.favorited_by.length === 0" class="py-8 text-center text-xs text-slate-400">
            No client records found for this property yet.
          </div>
        </div>

        <div class="flex justify-end pt-2 border-t border-slate-100 dark:border-slate-800">
          <button
            type="button"
            @click="showFavoritesModal = false"
            class="px-4 py-2 text-xs font-bold bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl cursor-pointer"
          >
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- Floating Teleported Actions Dropdown Menu -->
    <Teleport to="body">
      <div v-if="activeDropdown" class="fixed inset-0 z-[99998]" @click="activeDropdown = null">
        <div
          :style="{ top: activeDropdown.top, left: activeDropdown.left }"
          class="fixed w-44 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200/80 dark:border-slate-800 py-1.5 z-[99999] animate-in fade-in zoom-in-95 text-left"
          @click.stop
        >
          <!-- 1. Edit Action -->
          <button
            @click="openEditModal(activeDropdown.prop); activeDropdown = null"
            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <Edit3 class="w-3.5 h-3.5 text-slate-500" />
            <span>Edit Property</span>
          </button>

          <!-- 2. Dynamic Status: Set to Draft if Active -->
          <button
            v-if="activeDropdown.prop.status === 'active'"
            @click="toggleStatus(activeDropdown.prop); activeDropdown = null"
            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 transition-colors cursor-pointer"
          >
            <PauseCircle class="w-3.5 h-3.5 text-amber-500" />
            <span>Set to Draft</span>
          </button>

          <!-- 2. Dynamic Status: Set to Active if Draft -->
          <button
            v-else
            @click="toggleStatus(activeDropdown.prop); activeDropdown = null"
            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors cursor-pointer"
          >
            <CheckCircle2 class="w-3.5 h-3.5 text-emerald-500" />
            <span>Set to Active</span>
          </button>

          <!-- 3. Delete Action -->
          <button
            @click="confirmDelete(activeDropdown.prop); activeDropdown = null"
            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors border-t border-slate-100 dark:border-slate-800 mt-1 pt-1.5 cursor-pointer"
          >
            <Trash2 class="w-3.5 h-3.5 text-rose-500" />
            <span>Delete</span>
          </button>
        </div>
      </div>
    </Teleport>

    <!-- Multi-Step Add / Edit Property Modal -->
    <AddPropertyModal
      :isOpen="showModal"
      :property="selectedProperty"
      @close="showModal = false"
      @saved="loadProperties"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import {
  Plus,
  Search,
  LayoutGrid,
  List,
  Building2,
  MapPin,
  BedDouble,
  Bath,
  Maximize2,
  Edit3,
  Trash2,
  MoreVertical,
  CheckCircle2,
  PauseCircle,
  Heart,
  X
} from 'lucide-vue-next'
import { ownerService } from '@/services/ownerService'
import { useToastStore } from '@/stores/toast'
import AddPropertyModal from '@/components/dashboard/AddPropertyModal.vue'
import BasePagination from '@/components/common/BasePagination.vue'

const toastStore = useToastStore()

const isLoading = ref(true)
const apiError = ref(null)
const properties = ref([])
const searchQuery = ref('')
const selectedListingType = ref('all')
const selectedPropertyType = ref('all')
const selectedStatus = ref('all')
const viewMode = ref('table')

// Pagination state
const currentPage = ref(1)
const totalPages = ref(1)
const totalItemsCount = ref(0)
const perPage = ref(12)

const activeDropdown = ref(null)
const showModal = ref(false)
const selectedProperty = ref(null)

// Favorites Flyout / Modal
const showFavoritesModal = ref(false)
const selectedFavoritesProp = ref(null)

const availablePropertyTypes = [
  'Apartment',
  'Commercial',
  'Condominium',
  'House',
  'Office',
  'Villa'
]

function toggleDropdown(prop, event) {
  if (activeDropdown.value && activeDropdown.value.id === prop.id) {
    activeDropdown.value = null
    return
  }
  const rect = event.currentTarget.getBoundingClientRect()
  const menuWidth = 176
  const menuHeight = 130
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

function closeAllDropdowns() {
  activeDropdown.value = null
}

function openFavoritesModal(prop) {
  selectedFavoritesProp.value = prop
  showFavoritesModal.value = true
}

async function loadProperties(page = 1) {
  isLoading.value = true
  apiError.value = null
  currentPage.value = page

  try {
    const res = await ownerService.getProperties({
      search: searchQuery.value,
      status: selectedStatus.value,
      listing_type: selectedListingType.value,
      property_type: selectedPropertyType.value,
      page: currentPage.value,
      per_page: perPage.value
    })
    
    // Check pagination response from backend ApiResponse trait
    if (res && Array.isArray(res.data)) {
      properties.value = res.data
      totalItemsCount.value = res.meta?.total ?? res.data.length
      totalPages.value = res.meta?.last_page ?? 1
      currentPage.value = res.meta?.current_page ?? page
    } else if (res && res.data && Array.isArray(res.data.data)) {
      properties.value = res.data.data
      totalItemsCount.value = res.data.total ?? res.meta?.total ?? res.data.data.length
      totalPages.value = res.data.last_page ?? res.meta?.last_page ?? 1
      currentPage.value = res.data.current_page ?? res.meta?.current_page ?? page
    } else if (Array.isArray(res)) {
      properties.value = res
      totalItemsCount.value = res.length
      totalPages.value = 1
    } else {
      properties.value = []
      totalItemsCount.value = 0
      totalPages.value = 1
    }
  } catch (err) {
    console.error('Failed to load owner properties:', err)
    apiError.value = 'Failed to load properties list.'
  } finally {
    isLoading.value = false
  }
}

let searchTimer = null
function handleSearch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    currentPage.value = 1
    loadProperties(1)
  }, 300)
}

function onFilterChange() {
  currentPage.value = 1
  loadProperties(1)
}

function onPageChange(newPage) {
  loadProperties(newPage)
}

function resetFilters() {
  searchQuery.value = ''
  selectedListingType.value = 'all'
  selectedPropertyType.value = 'all'
  selectedStatus.value = 'all'
  currentPage.value = 1
  loadProperties(1)
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
    toastStore.success(`Status updated to ${prop.status}`)
  } catch (err) {
    prop.status = prop.status === 'active' ? 'draft' : 'active'
    toastStore.success(`Status updated`)
  }
}

async function confirmDelete(prop) {
  if (confirm(`Are you sure you want to delete "${prop.title}"?`)) {
    try {
      await ownerService.deleteProperty(prop.id)
      properties.value = properties.value.filter(p => p.id !== prop.id)
      totalItemsCount.value = Math.max(0, totalItemsCount.value - 1)
      toastStore.success('Property deleted.')
    } catch (err) {
      console.error('Delete failed:', err)
      toastStore.error('Could not delete listing.')
    }
  }
}

const route = useRoute()

onMounted(() => {
  loadProperties(1)
  if (route.query.add === '1' || route.query.add === 'true') {
    openAddModal()
  }
})
</script>
