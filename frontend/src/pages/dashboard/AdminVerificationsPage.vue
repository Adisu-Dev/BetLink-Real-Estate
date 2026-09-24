<template>
  <div class="space-y-6 w-full">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
          {{ t('verifications_title', 'Identity & Document Verifications') }}
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
          {{ t('verifications_subtitle', 'Verify government IDs, property ownership deeds, and agent licenses for marketplace trust.') }}
        </p>
      </div>

      <!-- Quick Summary Badges -->
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800 text-amber-700 dark:text-amber-300 text-xs font-bold shadow-2xs">
          <Clock class="w-3.5 h-3.5" />
          <span>{{ pendingCount }} Pending</span>
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold shadow-2xs">
          <ShieldCheck class="w-3.5 h-3.5" />
          <span>{{ pagination.total }} Total Records</span>
        </span>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3 transition-colors">
      <!-- Search Input -->
      <div class="relative flex-1">
        <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
        <input
          v-model="searchQuery"
          @input="handleSearchInput"
          type="text"
          placeholder="Search by applicant name, email, or notes..."
          class="w-full pl-10 pr-9 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 transition-colors"
        />
        <button
          v-if="searchQuery"
          type="button"
          @click="clearSearch"
          class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors cursor-pointer rounded-lg"
          title="Clear search"
        >
          <X class="w-3.5 h-3.5" />
        </button>
      </div>

      <!-- Filters & Refresh Action -->
      <div class="flex flex-wrap items-center gap-2.5">
        <!-- Status Filter -->
        <select
          v-model="selectedStatus"
          @change="handleFilterChange"
          class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-slate-400 transition-colors cursor-pointer"
        >
          <option value="">{{ t('all_status', 'All Status') }}</option>
          <option value="pending">{{ t('pending', 'Pending Review') }}</option>
          <option value="approved">{{ t('approved', 'Approved') }}</option>
          <option value="rejected">{{ t('rejected', 'Rejected') }}</option>
        </select>

        <!-- Document Type Filter -->
        <select
          v-model="selectedType"
          @change="handleFilterChange"
          class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-slate-400 transition-colors cursor-pointer"
        >
          <option value="">{{ t('all_types', 'All Document Types') }}</option>
          <option value="property">Property Deed</option>
          <option value="identity">National ID / Passport</option>
          <option value="agent">Agent License</option>
        </select>

        <!-- Refresh Button -->
        <button
          @click="loadVerifications"
          :disabled="loading"
          class="p-2 text-slate-500 hover:text-slate-900 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700 transition-colors cursor-pointer disabled:opacity-50"
          title="Refresh List"
        >
          <RotateCcw class="w-4 h-4" :class="{ 'animate-spin': loading }" />
        </button>
      </div>
    </div>

    <!-- Error State with Retry -->
    <div v-if="apiError" class="p-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-center space-y-3">
      <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ apiError }}</p>
      <button
        @click="loadVerifications"
        class="px-4 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl transition-colors shadow-xs cursor-pointer"
      >
        {{ t('retry_loading', 'Retry Loading') }}
      </button>
    </div>

    <!-- Loading Skeleton Table -->
    <div v-else-if="loading" class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4 animate-pulse">
      <div class="h-8 bg-slate-200 dark:bg-slate-800 rounded w-full"></div>
      <div v-for="n in 4" :key="n" class="h-12 bg-slate-100 dark:bg-slate-800/60 rounded w-full"></div>
    </div>

    <!-- Empty State -->
    <EmptyState
      v-else-if="verifications.length === 0"
      :title="t('no_verifications_title', 'No verification records found')"
      :description="searchQuery || selectedStatus || selectedType ? 'No records match your selected search or filter criteria. Try clearing filters.' : t('no_verifications_description', 'All identity and document verification requests have been processed.')"
      :action-label="searchQuery || selectedStatus || selectedType ? 'Clear Filters' : ''"
      @action="clearFilters"
    />

    <!-- Executive Verifications Table View -->
    <div v-else class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 overflow-hidden transition-colors">
      <div class="overflow-x-auto min-h-[320px]">
        <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
          <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200/80 dark:border-slate-800">
            <tr>
              <th class="px-3.5 py-3 text-center text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-12">No</th>
              <th class="px-4 py-3 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Applicant / User</th>
              <th class="px-3.5 py-3 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Type</th>
              <th class="px-3.5 py-3 text-center text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
              <th class="px-3.5 py-3 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Submitted Document</th>
              <th class="px-3.5 py-3 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Notes & Reason</th>
              <th class="px-3.5 py-3 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Submitted Date</th>
              <th class="px-4 py-3 text-right text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr
              v-for="(v, index) in verifications"
              :key="v.id"
              class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors"
            >
              <!-- Sequential Numbering (1, 2, 3...) -->
              <td class="px-3.5 py-3.5 whitespace-nowrap text-center text-xs font-mono font-bold text-slate-400 dark:text-slate-500">
                {{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}
              </td>

              <!-- Applicant Column -->
              <td class="px-4 py-3.5 whitespace-nowrap">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center font-bold text-xs text-slate-700 dark:text-slate-300 shrink-0 overflow-hidden">
                    <img
                      v-if="v.user?.avatar"
                      :src="v.user.avatar"
                      :alt="v.user?.name"
                      class="w-full h-full object-cover"
                    />
                    <span v-else>{{ getUserInitials(v.user?.name || v.name) }}</span>
                  </div>
                  <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate">
                      {{ v.user?.name || v.name || 'User Verification' }}
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono truncate">
                      {{ v.user?.email || '—' }}
                    </p>
                  </div>
                </div>
              </td>

              <!-- Verification Type Column -->
              <td class="px-3.5 py-3.5 whitespace-nowrap">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold border"
                  :class="getTypeBadgeClass(v.type)"
                >
                  <component :is="getTypeIcon(v.type)" class="w-3.5 h-3.5" />
                  <span>{{ formatTypeName(v.type) }}</span>
                </span>
              </td>

              <!-- Status Badge Column (Prominent Column 4) -->
              <td class="px-3.5 py-3.5 whitespace-nowrap text-center">
                <span
                  v-if="(v.status || '').toLowerCase() === 'pending'"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-700 shadow-2xs"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                  <span>Pending</span>
                </span>
                <span
                  v-else-if="(v.status || '').toLowerCase() === 'approved'"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 shadow-2xs"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  <span>Approved</span>
                </span>
                <span
                  v-else-if="(v.status || '').toLowerCase() === 'rejected'"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-700 shadow-2xs"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                  <span>Rejected</span>
                </span>
                <StatusBadge v-else :status="v.status ? v.status.toLowerCase() : 'pending'" />
              </td>

              <!-- Submitted Documents Column -->
              <td class="px-3.5 py-3.5">
                <div v-if="getDocuments(v).length" class="flex flex-wrap items-center gap-1.5 max-w-xs">
                  <a
                    v-for="doc in getDocuments(v)"
                    :key="doc.name"
                    :href="doc.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200/70 dark:border-slate-700 transition-colors shadow-2xs truncate max-w-[180px]"
                    :title="doc.name"
                  >
                    <FileText class="w-3 h-3 text-slate-400 shrink-0" />
                    <span class="truncate">{{ doc.name }}</span>
                    <ExternalLink class="w-2.5 h-2.5 text-slate-400 shrink-0" />
                  </a>
                </div>
                <span v-else class="text-xs text-slate-400 italic">No files attached</span>
              </td>

              <!-- Notes Column -->
              <td class="px-3.5 py-3.5 max-w-[200px]">
                <p
                  v-if="v.notes"
                  class="text-xs text-slate-600 dark:text-slate-300 truncate"
                  :title="v.notes"
                >
                  {{ v.notes }}
                </p>
                <span v-else class="text-xs text-slate-400">—</span>
              </td>

              <!-- Submitted Date Column -->
              <td class="px-3.5 py-3.5 whitespace-nowrap text-xs text-slate-600 dark:text-slate-400">
                <div class="flex items-center gap-1.5">
                  <Calendar class="w-3.5 h-3.5 text-slate-400" />
                  <span>{{ formatDate(v.created_at || v.submitted) }}</span>
                </div>
              </td>

              <!-- Actions: Quick Action buttons + 3-Dot Menu Dropdown -->
              <td class="px-4 py-3.5 whitespace-nowrap text-right text-xs">
                <div class="flex items-center justify-end gap-1.5">
                  <template v-if="(v.status || '').toLowerCase() === 'pending'">
                    <button
                      type="button"
                      @click.stop="approveVerification(v)"
                      :disabled="actionLoading === v.id"
                      class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 border border-emerald-300 dark:border-emerald-700 rounded-lg transition-colors cursor-pointer disabled:opacity-50"
                      title="Approve verification directly"
                    >
                      <Check class="w-3.5 h-3.5" />
                      <span>Approve</span>
                    </button>
                    <button
                      type="button"
                      @click.stop="openRejectModal(v)"
                      :disabled="actionLoading === v.id"
                      class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/50 border border-rose-300 dark:border-rose-700 rounded-lg transition-colors cursor-pointer disabled:opacity-50"
                      title="Reject verification"
                    >
                      <X class="w-3.5 h-3.5" />
                      <span>Reject</span>
                    </button>
                  </template>

                  <button
                    type="button"
                    @click.stop="toggleActionMenu(v, $event)"
                    class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer border border-transparent hover:border-slate-200 dark:hover:border-slate-700"
                    title="More actions"
                  >
                    <MoreVertical class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="px-6 py-3 bg-slate-50/50 dark:bg-slate-850 border-t border-slate-200/80 dark:border-slate-800">
        <BasePagination
          :current-page="pagination.current_page"
          :total-pages="pagination.last_page"
          :total-items="pagination.total"
          :per-page="pagination.per_page"
          @change="handlePageChange"
        />
      </div>
    </div>

    <!-- 3-Dot Floating Action Dropdown Menu (Teleported for perfect positioning) -->
    <Teleport to="body">
      <div
        v-if="activeMenu"
        class="fixed inset-0 z-[9998] bg-transparent"
        @click="activeMenu = null"
      >
        <div
          :style="{ top: activeMenu.top, left: activeMenu.left }"
          class="fixed w-52 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 py-1.5 z-[9999] animate-in fade-in zoom-in-95 text-left"
          @click.stop
        >
          <!-- Approve Option (Neutral Slate / Subtle hover) -->
          <button
            v-if="activeMenu.verification.status === 'pending'"
            type="button"
            @click="handleMenuAction('approve')"
            :disabled="actionLoading === activeMenu.verification.id"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer disabled:opacity-50"
          >
            <CheckCircle class="w-3.5 h-3.5 text-slate-600 dark:text-slate-400" />
            <span>{{ t('approve', 'Approve Verification') }}</span>
          </button>

          <!-- Reject Option (Neutral Slate / Subtle hover, same clean styling) -->
          <button
            v-if="activeMenu.verification.status === 'pending'"
            type="button"
            @click="handleMenuAction('reject')"
            :disabled="actionLoading === activeMenu.verification.id"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer disabled:opacity-50"
          >
            <XCircle class="w-3.5 h-3.5 text-slate-600 dark:text-slate-400" />
            <span>{{ t('reject', 'Reject Verification') }}</span>
          </button>

          <!-- Inspect Document Option -->
          <button
            v-if="getDocuments(activeMenu.verification).length > 0"
            type="button"
            @click="handleMenuAction('inspect')"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer border-t border-slate-100 dark:border-slate-800 mt-0.5 pt-1.5"
          >
            <ExternalLink class="w-3.5 h-3.5 text-slate-500" />
            <span>Inspect Document</span>
          </button>

          <!-- Re-Approve Option (If previously rejected) -->
          <button
            v-if="activeMenu.verification.status === 'rejected'"
            type="button"
            @click="handleMenuAction('approve')"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <RotateCcw class="w-3.5 h-3.5 text-slate-500" />
            <span>Reconsider & Approve</span>
          </button>

          <!-- Read Notes Option -->
          <button
            v-if="activeMenu.verification.notes"
            type="button"
            @click="handleMenuAction('notes')"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <Eye class="w-3.5 h-3.5 text-slate-400" />
            <span>View Notes</span>
          </button>
        </div>
      </div>
    </Teleport>

    <!-- Reject Verification Modal (Slate Design System) -->
    <Teleport to="body">
      <div v-if="verificationToReject" class="fixed inset-0 z-[100000] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 animate-in fade-in zoom-in-95">
          <div class="flex items-center justify-between pb-1 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">
              {{ t('reject_verif_title', 'Reject Verification Request') }}
            </h3>
            <button
              @click="verificationToReject = null"
              class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer rounded-lg"
            >
              <X class="w-4 h-4" />
            </button>
          </div>

          <p class="text-xs text-slate-500 dark:text-slate-400">
            {{ t('reject_verif_prompt', 'Please specify the rejection reason (e.g. illegible scan, expired document, mismatching name) so the user can re-upload correctly.') }}
          </p>

          <textarea
            v-model="rejectionNotes"
            rows="3"
            :placeholder="t('notes_placeholder', 'Enter reason for rejection...')"
            class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-400"
          ></textarea>

          <div class="flex justify-end gap-2.5 pt-2">
            <button
              type="button"
              @click="verificationToReject = null"
              class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-colors cursor-pointer"
            >
              {{ t('cancel', 'Cancel') }}
            </button>
            <button
              type="button"
              @click="confirmRejectVerification"
              :disabled="actionLoading === verificationToReject.id"
              class="px-4 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl text-xs font-bold transition-colors shadow-xs cursor-pointer disabled:opacity-50"
            >
              {{ actionLoading === verificationToReject.id ? 'Rejecting...' : t('reject', 'Reject') }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Notes Inspection Modal -->
    <Teleport to="body">
      <div v-if="verificationNotesModal" class="fixed inset-0 z-[100000] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 animate-in fade-in zoom-in-95">
          <div class="flex items-center justify-between pb-1 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Verification Notes</h3>
            <button
              @click="verificationNotesModal = null"
              class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer rounded-lg"
            >
              <X class="w-4 h-4" />
            </button>
          </div>
          <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">
            {{ verificationNotesModal.notes }}
          </div>
          <div class="flex justify-end">
            <button
              @click="verificationNotesModal = null"
              class="px-4 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl text-xs font-bold cursor-pointer"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import {
  Search,
  X,
  RotateCcw,
  Clock,
  ShieldCheck,
  FileText,
  ExternalLink,
  Calendar,
  MoreVertical,
  CheckCircle,
  XCircle,
  Eye,
  Home,
  UserCheck,
  Briefcase
} from 'lucide-vue-next'
import { adminService } from '../../services/adminService'
import { useLanguage } from '../../composables/useLanguage'
import { useToast } from '../../composables/useToast'
import StatusBadge from '../../components/dashboard/StatusBadge.vue'
import EmptyState from '../../components/dashboard/EmptyState.vue'
import BasePagination from '../../components/common/BasePagination.vue'

const { t } = useLanguage()
const { success, error } = useToast()

const verifications = ref([])
const loading = ref(true)
const apiError = ref(null)
const actionLoading = ref(null)

const searchQuery = ref('')
let searchTimeout = null

const selectedStatus = ref('')
const selectedType = ref('')

const activeMenu = ref(null)
const verificationToReject = ref(null)
const rejectionNotes = ref('')
const verificationNotesModal = ref(null)

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
})

const pendingCount = computed(() => {
  return verifications.value.filter(v => (v.status || '').toLowerCase() === 'pending').length
})

const loadVerifications = async () => {
  loading.value = true
  apiError.value = null
  activeMenu.value = null

  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
    }
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim()
    if (selectedStatus.value) params.status = selectedStatus.value
    if (selectedType.value) params.type = selectedType.value

    const res = await adminService.getVerifications(params)
    
    if (res && res.data) {
      if (Array.isArray(res.data)) {
        verifications.value = res.data
      } else if (Array.isArray(res.data.data)) {
        verifications.value = res.data.data
      } else {
        verifications.value = []
      }
    } else if (Array.isArray(res)) {
      verifications.value = res
    } else {
      verifications.value = []
    }

    const meta = res?.meta || res?.data?.meta
    if (meta) {
      pagination.current_page = meta.current_page || 1
      pagination.last_page = meta.last_page || 1
      pagination.total = meta.total || verifications.value.length
      pagination.per_page = meta.per_page || pagination.per_page
    } else {
      pagination.total = verifications.value.length
      pagination.last_page = 1
    }
  } catch (err) {
    console.error('Failed to load verifications:', err)
    apiError.value = err.message || 'Failed to load verification requests.'
  } finally {
    loading.value = false
  }
}

const handleSearchInput = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1
    loadVerifications()
  }, 350)
}

const clearSearch = () => {
  searchQuery.value = ''
  pagination.current_page = 1
  loadVerifications()
}

const handleFilterChange = () => {
  pagination.current_page = 1
  loadVerifications()
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedStatus.value = ''
  selectedType.value = ''
  pagination.current_page = 1
  loadVerifications()
}

const handlePageChange = (page) => {
  pagination.current_page = page
  loadVerifications()
}

// 3-Dot Menu Dropdown Logic
const toggleActionMenu = (v, event) => {
  if (activeMenu.value && activeMenu.value.id === v.id) {
    activeMenu.value = null
    return
  }

  const rect = event.currentTarget.getBoundingClientRect()
  const menuWidth = 208
  const menuHeight = 160
  let top = rect.bottom + 6
  let left = rect.right - menuWidth

  if (top + menuHeight > window.innerHeight) {
    top = rect.top - menuHeight - 6
  }

  activeMenu.value = {
    id: v.id,
    verification: v,
    top: `${Math.max(10, top)}px`,
    left: `${Math.max(10, left)}px`
  }
}

const handleMenuAction = async (action) => {
  if (!activeMenu.value?.verification) return
  const v = activeMenu.value.verification
  activeMenu.value = null

  if (action === 'approve') {
    await approveVerification(v)
  } else if (action === 'reject') {
    openRejectModal(v)
  } else if (action === 'inspect') {
    const docs = getDocuments(v)
    if (docs[0]?.url) {
      window.open(docs[0].url, '_blank')
    }
  } else if (action === 'notes') {
    verificationNotesModal.value = v
  }
}

const approveVerification = async (v) => {
  actionLoading.value = v.id
  try {
    await adminService.approveVerification(v.id)
    success(t('approved', 'Approved'), 'Verification request approved successfully.')
    await loadVerifications()
  } catch (err) {
    console.error('Failed to approve verification:', err)
    error(t('error', 'Error'), err.message || 'Could not approve verification.')
  } finally {
    actionLoading.value = null
  }
}

const openRejectModal = (v) => {
  verificationToReject.value = v
  rejectionNotes.value = ''
}

const confirmRejectVerification = async () => {
  if (!verificationToReject.value || !rejectionNotes.value.trim()) {
    error(t('error', 'Error'), 'Please provide a reason for rejecting this verification.')
    return
  }
  actionLoading.value = verificationToReject.value.id
  try {
    await adminService.rejectVerification(verificationToReject.value.id, rejectionNotes.value.trim())
    success(t('rejected', 'Rejected'), 'Verification request rejected.')
    verificationToReject.value = null
    await loadVerifications()
  } catch (err) {
    console.error('Failed to reject verification:', err)
    error(t('error', 'Error'), err.message || 'Could not reject verification.')
  } finally {
    actionLoading.value = null
  }
}

const formatDate = (dateStr) => {
  if (!dateStr) return '—'
  try {
    return new Date(dateStr).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    })
  } catch {
    return dateStr
  }
}

const getUserInitials = (name) => {
  if (!name) return 'U'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const formatTypeName = (type) => {
  if (!type) return 'Document'
  const t = String(type).toLowerCase()
  if (t === 'property') return 'Property Deed'
  if (t === 'identity') return 'National ID / Fayda'
  if (t === 'agent') return 'Agent License'
  return t.replace(/_/g, ' ').toUpperCase()
}

const getTypeIcon = (type) => {
  const t = String(type || '').toLowerCase()
  if (t === 'property') return Home
  if (t === 'identity') return UserCheck
  if (t === 'agent') return Briefcase
  return FileText
}

const getTypeBadgeClass = (type) => {
  const t = String(type || '').toLowerCase()
  if (t === 'property') {
    return 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border-slate-300 dark:border-slate-700'
  }
  if (t === 'identity') {
    return 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border-slate-300 dark:border-slate-700'
  }
  return 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border-slate-300 dark:border-slate-700'
}

const getDocuments = (v) => {
  if (!v) return []
  if (v.document_url || v.file_path) {
    return [{ name: 'Inspect Document', url: v.document_url || v.file_path }]
  }
  if (!v.documents) return []
  if (Array.isArray(v.documents)) {
    return v.documents.map((d, idx) => ({
      name: typeof d === 'string' ? (d.split('/').pop() || `Document #${idx + 1}`) : (d.file_name || d.name || `Document #${idx + 1}`),
      url: typeof d === 'string' ? d : (d.url || '#')
    }))
  }
  if (typeof v.documents === 'object') {
    return Object.entries(v.documents).map(([key, d]) => {
      if (typeof d === 'string') {
        return { name: key.replace(/_/g, ' ').toUpperCase(), url: d }
      }
      return {
        name: d.file_name || d.document_type?.replace(/_/g, ' ').toUpperCase() || key.replace(/_/g, ' ').toUpperCase(),
        url: d.url || '#'
      }
    })
  }
  return []
}

onMounted(() => {
  loadVerifications()
})
</script>
