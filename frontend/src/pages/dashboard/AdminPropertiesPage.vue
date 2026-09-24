<template>
  <div class="space-y-6" @click="closeAllDropdowns">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
          {{ t('property_management_title', 'Property Management') }}
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">
          {{ t('property_management_subtitle', 'Audit active listings, review pending submissions, and manage marketplace inventory.') }}
        </p>
      </div>
      <button 
        @click="loadProperties"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl font-bold text-xs sm:text-sm shadow-xs transition-colors self-start sm:self-auto cursor-pointer"
      >
        <span>🔄</span> {{ t('refresh', 'Refresh') }}
      </button>
    </div>

    <!-- Search and Filters Bar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 transition-colors">
      <div class="flex flex-wrap items-center gap-3 flex-1">
        <!-- Search Input -->
        <div class="relative flex-1 min-w-[200px] max-w-md">
          <input
            v-model="searchQuery"
            @keyup.enter="handleSearch"
            type="text"
            :placeholder="t('search_properties_placeholder', 'Search by title, location, or owner...')"
            class="w-full pl-9 pr-9 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs sm:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 transition-colors"
          />
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35"/>
          </svg>
          <button
            v-if="searchQuery"
            type="button"
            @click="searchQuery = ''"
            class="absolute right-2.5 top-2.5 p-1 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors cursor-pointer rounded-lg"
            title="Clear search"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>

        <!-- Active Owner Filter Pill -->
        <div v-if="selectedOwnerName" class="flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 rounded-xl text-xs font-bold shadow-xs">
          <span>Owner: {{ selectedOwnerName }}</span>
          <button
            type="button"
            @click="clearOwnerFilter"
            class="p-0.5 hover:bg-rose-200 dark:hover:bg-rose-900 rounded-full transition-colors cursor-pointer"
            title="Remove owner filter"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>

        <!-- Status Filter -->
        <select
          v-model="selectedStatus"
          @change="handleFilterChange"
          class="px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 transition-colors cursor-pointer"
        >
          <option value="">{{ t('all_status', 'All Status') }}</option>
          <option value="active">{{ t('active', 'Active') }}</option>
          <option value="pending">{{ t('pending', 'Pending Review') }}</option>
          <option value="draft">{{ t('draft', 'Draft') }}</option>
          <option value="rejected">{{ t('rejected', 'Rejected') }}</option>
          <option value="sold">{{ t('property.sold', 'Sold') }}</option>
          <option value="rented">{{ t('property.rented', 'Rented') }}</option>
        </select>

        <!-- Listing Type Filter -->
        <select
          v-model="selectedListingType"
          @change="handleFilterChange"
          class="px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 transition-colors cursor-pointer"
        >
          <option value="">{{ t('all_types', 'All Listing Types') }}</option>
          <option value="sale">{{ t('property.for_sale', 'For Sale') }}</option>
          <option value="rent">{{ t('property.for_rent', 'For Rent') }}</option>
          <option value="short_stay">{{ t('property.short_stay', 'Short Stay') }}</option>
        </select>
      </div>
    </div>

    <!-- Error State with Retry -->
    <div v-if="apiError" class="p-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-center space-y-3">
      <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ apiError }}</p>
      <button
        @click="loadProperties"
        class="px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-xl hover:bg-rose-700 transition-colors shadow-xs cursor-pointer"
      >
        {{ t('retry_loading', 'Retry Loading') }}
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-else-if="loading" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 p-6 space-y-4">
      <div v-for="n in 5" :key="n" class="animate-pulse flex items-center gap-4 py-3 border-b border-slate-100 dark:border-slate-800">
        <div class="w-16 h-12 bg-slate-200 dark:bg-slate-800 rounded-lg"></div>
        <div class="flex-1 space-y-2">
          <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-1/3"></div>
          <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-1/4"></div>
        </div>
        <div class="h-6 bg-slate-200 dark:bg-slate-800 rounded w-20"></div>
        <div class="h-6 bg-slate-200 dark:bg-slate-800 rounded w-28"></div>
      </div>
    </div>

    <!-- Empty State -->
    <EmptyState
      v-else-if="properties.length === 0"
      :title="selectedOwnerName ? `No properties found for &quot;${selectedOwnerName}&quot;` : (searchQuery ? `No properties matching &quot;${searchQuery}&quot;` : t('no_properties_found', 'No properties found'))"
      :description="selectedOwnerName ? `This owner has not published any property listings matching current filters.` : (searchQuery ? 'We could not find any property listings matching your search keyword. Please verify spelling or try another term.' : t('no_properties_description', 'No properties matched your search or filter parameters.'))"
      :action-label="searchQuery || selectedOwnerName || selectedStatus || selectedListingType ? 'Clear Search &amp; Filters' : ''"
      @action="clearSearchAndFilters"
    />

    <!-- Properties Table -->
    <div v-else class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 transition-colors">
      <div class="overflow-x-auto min-h-[300px]">
        <table class="w-full text-left">
          <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800">
            <tr>
              <th class="px-4 py-3.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-12 text-center">No</th>
              <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ t('property', 'Property') }}</th>
              <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ t('owner', 'Owner') }}</th>
              <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ t('property.price', 'Price') }}</th>
              <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ t('property.status', 'Status') }}</th>
              <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ t('views', 'Views') }}</th>
              <th class="px-6 py-3.5 text-right text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ t('actions', 'Actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr v-for="(property, index) in properties" :key="property.id" class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
              <td class="px-4 py-4 whitespace-nowrap text-center text-xs font-mono font-bold text-slate-400 dark:text-slate-500">
                {{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <img
                    :src="getPropertyImage(property)"
                    :alt="property.title"
                    class="w-14 h-11 rounded-lg object-cover border border-slate-200 dark:border-slate-700 flex-shrink-0"
                  />
                  <div class="max-w-xs truncate">
                    <p class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm truncate" :title="property.title">
                      {{ property.title }}
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate">
                      {{ property.address?.city?.name || property.location || 'Addis Ababa, Ethiopia' }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-xs sm:text-sm">
                <button
                  v-if="property.owner?.name"
                  type="button"
                  @click.stop="filterByOwner(property.owner)"
                  class="group flex items-center gap-1.5 text-slate-700 dark:text-slate-300 font-bold hover:text-rose-600 dark:hover:text-rose-400 cursor-pointer transition-colors"
                  :title="`Click to filter all properties owned by ${property.owner.name}`"
                >
                  <span class="underline decoration-dotted decoration-slate-300 dark:decoration-slate-600 underline-offset-4 group-hover:decoration-rose-500">{{ property.owner.name }}</span>
                  <span class="opacity-0 group-hover:opacity-100 text-[10px] text-rose-500 font-bold transition-opacity">🔍</span>
                </button>
                <span v-else class="text-slate-400 font-medium">—</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-900 dark:text-white text-xs sm:text-sm">
                ETB {{ formatPrice(property.price) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <StatusBadge :status="property.status ? property.status.toLowerCase() : 'active'" />
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-slate-600 dark:text-slate-300 font-medium">
                {{ property.views_count ?? property.views ?? 0 }}
              </td>
              
              <!-- Actions 3-Dot Dropdown Menu -->
              <td class="px-6 py-4 whitespace-nowrap text-right text-xs sm:text-sm">
                <button
                  @click.stop="toggleDropdown(property, $event)"
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
      <div v-if="pagination.last_page > 1" class="px-6 py-3 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-800 rounded-b-2xl">
        <BasePagination
          :current-page="pagination.current_page"
          :total-pages="pagination.last_page"
          :total-items="pagination.total"
          :per-page="pagination.per_page"
          @change="handlePageChange"
        />
      </div>
    </div>

    <!-- Reject Property Modal -->
    <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
        <h3 class="text-base font-bold text-slate-900 dark:text-white">Reject Property Submission</h3>
        <p class="text-xs text-slate-500">Provide feedback to the owner on why this property cannot be approved:</p>
        <textarea
          v-model="rejectionReason"
          rows="3"
          :placeholder="t('rejection_reason_placeholder', 'e.g. Incomplete title deed, blurry photos...')"
          class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-400"
        ></textarea>
        <div class="flex justify-end gap-2 pt-2">
          <button
            @click="showRejectModal = false; propertyToReject = null"
            class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold cursor-pointer"
          >
            {{ t('cancel', 'Cancel') }}
          </button>
          <button
            @click="confirmRejectProperty"
            class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold cursor-pointer"
          >
            {{ t('reject', 'Reject Property') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      :is-open="showDeleteModal"
      :title="t('delete_property_title', 'Delete Property Permanently')"
      :message="t('delete_property_desc', `Are you sure you want to delete '${propertyToDelete?.title || 'this property'}'?`)"
      :confirm-label="t('delete', 'Delete')"
      :danger="true"
      @confirm="confirmDeleteProperty"
      @cancel="showDeleteModal = false; propertyToDelete = null"
    />
    <!-- Change Status Modal -->
    <div v-if="showStatusModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
          <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
            <span>⚙</span>
            <span>Change Property Status</span>
          </h3>
          <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
            Current: {{ propertyToChangeStatus?.status?.toUpperCase() }}
          </span>
        </div>

        <p class="text-xs text-slate-500 font-medium">
          Select a new marketplace status for <span class="font-bold text-slate-800 dark:text-slate-200">"{{ propertyToChangeStatus?.title }}"</span>:
        </p>

        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">New Status</label>
          <select
            v-model="newPropertyStatus"
            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-bold text-slate-900 dark:text-slate-100 focus:outline-none transition-colors cursor-pointer"
          >
            <option value="active">Active — (Live &amp; visible to buyers on marketplace)</option>
            <option value="draft">Draft — (Work-in-progress / hidden from public search)</option>
            <option value="pending">Pending Review — (Awaiting administrative moderation)</option>
            <option value="rejected">Rejected — (Declined by admin with reason)</option>
            <option value="sold">Sold — (Marked completed / closes inquiries)</option>
            <option value="rented">Rented — (Marked leased / closes inquiries)</option>
          </select>
        </div>

        <!-- Real-time Impact Warning Box -->
        <div class="p-3 rounded-xl border text-xs leading-relaxed transition-all"
             :class="getStatusImpactClass(newPropertyStatus)">
          <p class="font-bold mb-0.5">Notice on this status change:</p>
          <p>{{ getStatusImpactExplanation(newPropertyStatus) }}</p>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
          <button
            @click="showStatusModal = false; propertyToChangeStatus = null"
            class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold cursor-pointer transition-colors"
          >
            Cancel
          </button>
          <button
            @click="confirmChangePropertyStatus"
            :disabled="isUpdatingStatus || newPropertyStatus === propertyToChangeStatus?.status"
            class="px-5 py-2 bg-rose-600 hover:bg-rose-700 disabled:bg-slate-300 dark:disabled:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer disabled:opacity-50"
          >
            {{ isUpdatingStatus ? 'Updating...' : 'Confirm Status Change' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Confirm Delete Modal -->
    <ConfirmModal
      :is-open="showDeleteModal"
      title="Delete Property Listing"
      :message="`Are you sure you want to permanently delete '${propertyToDelete?.title || 'this property'}'? This action cannot be undone.`"
      confirm-text="Delete Property"
      confirm-variant="danger"
      @confirm="confirmDeleteProperty"
      @cancel="showDeleteModal = false; propertyToDelete = null"
    />

    <!-- Floating Teleported Actions Dropdown Menu -->
    <Teleport to="body">
      <div v-if="activeDropdown" class="fixed inset-0 z-[99998]" @click="activeDropdown = null">
        <div
          :style="{ top: activeDropdown.top, left: activeDropdown.left }"
          class="fixed w-44 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 py-1.5 z-[99999] animate-in fade-in zoom-in-95 text-left"
          @click.stop
        >
          <button
            @click="viewProperty(activeDropdown.property); activeDropdown = null"
            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <Eye class="w-3.5 h-3.5 text-slate-500" />
            <span>View Details</span>
          </button>
          <button
            @click="promptChangeStatus(activeDropdown.property); activeDropdown = null"
            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <Sliders class="w-3.5 h-3.5 text-blue-500" />
            <span>Change Status</span>
          </button>
          <button
            @click="toggleFeature(activeDropdown.property); activeDropdown = null"
            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-amber-600 dark:text-amber-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <Star class="w-3.5 h-3.5" />
            <span>{{ activeDropdown.property.is_featured ? 'Unfeature' : 'Feature' }}</span>
          </button>
          <button
            v-if="activeDropdown.property.status === 'pending'"
            @click="approveProperty(activeDropdown.property); activeDropdown = null"
            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <CheckCircle2 class="w-3.5 h-3.5" />
            <span>Approve</span>
          </button>
          <button
            v-if="activeDropdown.property.status === 'pending'"
            @click="promptRejectProperty(activeDropdown.property); activeDropdown = null"
            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-amber-600 dark:text-amber-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <XCircle class="w-3.5 h-3.5" />
            <span>Reject</span>
          </button>
          <button
            @click="promptDeleteProperty(activeDropdown.property); activeDropdown = null"
            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors border-t border-slate-100 dark:border-slate-800 mt-1 pt-1.5 cursor-pointer"
          >
            <Trash2 class="w-3.5 h-3.5" />
            <span>Delete Listing</span>
          </button>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import {
  MoreVertical,
  Eye,
  CheckCircle2,
  XCircle,
  Trash2,
  Sliders,
  Star,
  X
} from 'lucide-vue-next'
import { adminService } from '../../services/adminService'
import { useLanguage } from '../../composables/useLanguage'
import { useToast } from '../../composables/useToast'
import StatusBadge from '../../components/dashboard/StatusBadge.vue'
import EmptyState from '../../components/dashboard/EmptyState.vue'
import BasePagination from '../../components/common/BasePagination.vue'
import ConfirmModal from '../../components/dashboard/ConfirmModal.vue'

const router = useRouter()
const route = useRoute()
const { t } = useLanguage()
const { success, error } = useToast()

const properties = ref([])
const loading = ref(true)
const apiError = ref(null)

const searchQuery = ref('')
const selectedStatus = ref('')
const selectedListingType = ref('')
const selectedOwnerId = ref(null)
const selectedOwnerName = ref('')

const activeDropdown = ref(null)
const showRejectModal = ref(false)
const propertyToReject = ref(null)
const rejectionReason = ref('')

const showDeleteModal = ref(false)
const propertyToDelete = ref(null)

// Status change modal state
const showStatusModal = ref(false)
const propertyToChangeStatus = ref(null)
const newPropertyStatus = ref('active')
const isUpdatingStatus = ref(false)

const FALLBACK_IMAGES = [
  'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80',
  'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=600&q=80',
  'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=600&q=80',
  'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600&q=80',
  'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=600&q=80'
]

function getPropertyImage(p) {
  if (p.primary_image?.url) return p.primary_image.url
  if (p.primary_image?.image_url) return p.primary_image.image_url
  if (p.images?.[0]?.url) return p.images[0].url
  const id = Number(p.id || 0)
  return FALLBACK_IMAGES[id % FALLBACK_IMAGES.length]
}

function toggleDropdown(property, event) {
  if (activeDropdown.value && activeDropdown.value.id === property.id) {
    activeDropdown.value = null
    return
  }
  const rect = event.currentTarget.getBoundingClientRect()
  const menuWidth = 176
  const menuHeight = 180
  let top = rect.bottom + 4
  let left = rect.right - menuWidth

  if (top + menuHeight > window.innerHeight) {
    top = rect.top - menuHeight - 4
  }

  activeDropdown.value = {
    id: property.id,
    property,
    top: `${Math.max(10, top)}px`,
    left: `${Math.max(10, left)}px`
  }
}

function closeAllDropdowns() {
  activeDropdown.value = null
}

function viewProperty(property) {
  window.open(`/properties/${property.id}`, '_blank')
}

function filterByOwner(owner) {
  if (!owner) return
  if (typeof owner === 'object') {
    selectedOwnerId.value = owner.id
    selectedOwnerName.value = owner.name
    searchQuery.value = ''
  } else {
    selectedOwnerName.value = owner
    searchQuery.value = owner
  }
  pagination.current_page = 1
  loadProperties()
}

function clearOwnerFilter() {
  selectedOwnerId.value = null
  selectedOwnerName.value = ''
  pagination.current_page = 1
  loadProperties()
}

function getStatusImpactClass(status) {
  switch (status) {
    case 'active':
      return 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60'
    case 'draft':
      return 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700'
    case 'pending':
      return 'bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800/60'
    case 'rejected':
      return 'bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-800/60'
    case 'sold':
    case 'rented':
      return 'bg-blue-50 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800/60'
    default:
      return 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200'
  }
}

function getStatusImpactExplanation(status) {
  switch (status) {
    case 'active':
      return 'Visible to all portal visitors and buyers. Open for inquiries, tours, and price negotiations.'
    case 'draft':
      return 'Kept in private work-in-progress mode. Inaccessible to public search and buyers until published.'
    case 'pending':
      return 'Sent to administrative moderation queue. Requires admin review and approval before going live.'
    case 'rejected':
      return 'Declined by the moderation team. The listing remains offline and owner will be notified to revise details.'
    case 'sold':
      return 'Marks transaction as successfully completed. Prevents new tour bookings and marks property as off-market.'
    case 'rented':
      return 'Marks rental contract as active/leased. Inquiries are disabled until the lease term expires.'
    default:
      return 'Status change will be saved to the database immediately.'
  }
}

function promptChangeStatus(property) {
  propertyToChangeStatus.value = property
  newPropertyStatus.value = property.status || 'active'
  showStatusModal.value = true
}

async function confirmChangePropertyStatus() {
  if (!propertyToChangeStatus.value) return
  isUpdatingStatus.value = true
  try {
    await adminService.updatePropertyStatus(propertyToChangeStatus.value.id, newPropertyStatus.value)
    success('Status Updated', `Property status has been updated to '${newPropertyStatus.value}'.`)
    showStatusModal.value = false
    propertyToChangeStatus.value = null
    await loadProperties()
  } catch (err) {
    console.error('Failed to update property status:', err)
    error('Error', err.message || 'Could not update property status.')
  } finally {
    isUpdatingStatus.value = false
  }
}

async function toggleFeature(property) {
  try {
    await adminService.toggleFeature(property.id)
    success('Featured Updated', `Property ${property.is_featured ? 'unfeatured' : 'featured'} successfully!`)
    await loadProperties()
  } catch (err) {
    console.error('Failed to toggle feature:', err)
    error('Error', err.message || 'Could not update featured status.')
  }
}

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
})

const loadProperties = async () => {
  loading.value = true
  apiError.value = null

  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
    }
    if (searchQuery.value) params.q = searchQuery.value
    if (selectedOwnerId.value) params.owner_id = selectedOwnerId.value
    if (selectedStatus.value) params.status = selectedStatus.value
    if (selectedListingType.value) params.listing_type = selectedListingType.value

    const res = await adminService.getProperties(params)
    
    if (res && res.data) {
      if (Array.isArray(res.data)) {
        properties.value = res.data
      } else if (Array.isArray(res.data.data)) {
        properties.value = res.data.data
      } else {
        properties.value = []
      }
    } else if (Array.isArray(res)) {
      properties.value = res
    } else {
      properties.value = []
    }

    const meta = res?.meta || res?.data?.meta
    if (meta) {
      pagination.current_page = meta.current_page || 1
      pagination.last_page = meta.last_page || 1
      pagination.total = meta.total || properties.value.length
      pagination.per_page = meta.per_page || pagination.per_page
    } else {
      pagination.total = properties.value.length
      pagination.last_page = 1
    }
  } catch (err) {
    console.error('Failed to load properties:', err)
    apiError.value = err.message || 'Failed to load property listings.'
  } finally {
    loading.value = false
  }
}

// Real-time search with debounce
let searchDebounceTimer = null
watch(searchQuery, (newVal) => {
  clearTimeout(searchDebounceTimer)
  if (!newVal) {
    pagination.current_page = 1
    loadProperties()
    return
  }
  searchDebounceTimer = setTimeout(() => {
    pagination.current_page = 1
    loadProperties()
  }, 150)
})

function clearSearchAndFilters() {
  searchQuery.value = ''
  selectedOwnerId.value = null
  selectedOwnerName.value = ''
  selectedStatus.value = ''
  selectedListingType.value = ''
  pagination.current_page = 1
  loadProperties()
}

const handleSearch = () => {
  clearTimeout(searchDebounceTimer)
  pagination.current_page = 1
  loadProperties()
}

const handleFilterChange = () => {
  pagination.current_page = 1
  loadProperties()
}

const handlePageChange = (page) => {
  pagination.current_page = page
  loadProperties()
}

const approveProperty = async (property) => {
  try {
    await adminService.approveProperty(property.id)
    success(t('approved', 'Approved'), `Property '${property.title}' has been approved and published.`)
    await loadProperties()
  } catch (err) {
    console.error('Failed to approve property:', err)
    error(t('error', 'Error'), err.message || 'Could not approve property.')
  }
}

const promptRejectProperty = (property) => {
  propertyToReject.value = property
  rejectionReason.value = ''
  showRejectModal.value = true
}

const confirmRejectProperty = async () => {
  if (!propertyToReject.value || !rejectionReason.value.trim()) {
    error(t('error', 'Error'), 'Please provide a rejection reason.')
    return
  }
  try {
    await adminService.rejectProperty(propertyToReject.value.id, rejectionReason.value.trim())
    success(t('rejected', 'Rejected'), 'Property submission was rejected.')
    showRejectModal.value = false
    propertyToReject.value = null
    await loadProperties()
  } catch (err) {
    console.error('Failed to reject property:', err)
    error(t('error', 'Error'), err.message || 'Could not reject property.')
  }
}

const promptDeleteProperty = (property) => {
  propertyToDelete.value = property
  showDeleteModal.value = true
}


const confirmDeleteProperty = async () => {
  if (!propertyToDelete.value) return
  try {
    await adminService.deleteProperty(propertyToDelete.value.id)
    success(t('deleted', 'Deleted'), 'Property was deleted.')
    showDeleteModal.value = false
    propertyToDelete.value = null
    await loadProperties()
  } catch (err) {
    console.error('Failed to delete property:', err)
    error(t('error', 'Error'), err.message || 'Could not delete property.')
  } finally {
    showDeleteModal.value = false
  }
}

const formatPrice = (val) => {
  if (!val) return '0'
  return Number(val).toLocaleString()
}

onMounted(() => {
  if (route.query.status) {
    selectedStatus.value = route.query.status
  }
  loadProperties()
})
</script>
