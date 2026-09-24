<template>
  <div class="space-y-6 w-full">
    <!-- Header  -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
          {{ t('admin_reports_title', 'System Reports & Analytics') }}
        </h1>
        <p class="text-xs text-slate-500 mt-0.5 font-medium">
          Comprehensive inventory audit, user account registry, and safety moderation reports.
        </p>
      </div>

      <!-- Report Section & Time Range Dropdowns + Direct Export Buttons -->
      <div class="flex flex-wrap items-center gap-2.5 shrink-0">
        <select
          v-model="activeTab"
          class="px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold focus:outline-none cursor-pointer shadow-xs"
        >
          <option value="properties">Properties Audit ({{ filteredProperties.length }})</option>
          <option value="users">Users Directory ({{ filteredUsers.length }})</option>
          <option value="abuse">Fraud &amp; Safety Reports ({{ filteredAbuseReports.length }})</option>
        </select>

        <select
          v-if="activeTab !== 'abuse'"
          v-model="selectedTimeRange"
          @change="loadReport"
          class="px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold focus:outline-none cursor-pointer shadow-xs"
        >
          <option value="all">All Time</option>
          <option value="today">Today</option>
          <option value="week">This Week</option>
          <option value="month">This Month</option>
          <option value="year">This Year</option>
        </select>

        <!-- Direct Instant Export Buttons (No Confusing Modal) -->
        <button
          @click="exportExcelDirect"
          :disabled="isExporting"
          class="flex items-center gap-1.5 px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition-all shadow-xs disabled:opacity-60 cursor-pointer"
          title="Download Excel Report"
        >
          <Loader2 v-if="isExportingExcel" class="w-3.5 h-3.5 animate-spin text-slate-700 dark:text-slate-300" />
          <FileSpreadsheet v-else class="w-3.5 h-3.5 text-slate-700 dark:text-slate-300" />
          <span>{{ isExportingExcel ? 'Downloading...' : 'Export Excel' }}</span>
        </button>

        <button
          @click="exportPdfDirect"
          :disabled="isExporting"
          class="flex items-center gap-1.5 px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition-all shadow-xs disabled:opacity-60 cursor-pointer"
          title="Download PDF Report"
        >
          <Loader2 v-if="isExportingPdf" class="w-3.5 h-3.5 animate-spin text-slate-700 dark:text-slate-300" />
          <FileText v-else class="w-3.5 h-3.5" />
          <span>{{ isExportingPdf ? 'Downloading...' : 'Export PDF' }}</span>
        </button>
      </div>
    </div>

    <!-- 1. PROPERTIES AUDIT TABLE  -->
    <div v-if="activeTab === 'properties'" class="space-y-4">
      <!-- Search & Filters -->
      <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
          <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search property title, type, owner..."
            class="w-full pl-10 pr-9 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl text-xs sm:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400"
          />
          <button
            v-if="searchQuery"
            type="button"
            @click="searchQuery = ''"
            class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors cursor-pointer rounded-lg"
            title="Clear search"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>

        <div class="flex gap-2">
          <select
            v-model="typeFilter"
            class="px-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none cursor-pointer"
          >
            <option value="">All Types</option>
            <option value="Residential">Residential</option>
            <option value="Commercial">Commercial</option>
            <option value="Apartment">Apartment</option>
            <option value="Villa">Villa</option>
            <option value="Land">Land</option>
          </select>

          <select
            v-model="statusFilter"
            class="px-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none cursor-pointer"
          >
            <option value="">All Statuses</option>
            <option value="Active">Active</option>
            <option value="Pending">Pending</option>
            <option value="Draft">Draft</option>
            <option value="Sold">Sold</option>
            <option value="Rented">Rented</option>
          </select>
        </div>
      </div>

      <!-- Properties Table -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
          <h2 class="text-sm font-bold text-slate-900 dark:text-white">Properties Audit Directory</h2>
          <span class="text-xs text-slate-500 font-medium">{{ filteredProperties.length }} Records</span>
        </div>

        <div class="overflow-x-auto w-full">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
              <tr>
                <th class="px-4 py-3.5 w-12 text-center">No</th>
                <th class="px-6 py-3.5">Property Title</th>
                <th class="px-6 py-3.5">Category</th>
                <th class="px-6 py-3.5">Listing</th>
                <th class="px-6 py-3.5">Price</th>
                <th class="px-6 py-3.5">Owner / Landlord</th>
                <th class="px-6 py-3.5 text-center">Views</th>
                <th class="px-6 py-3.5">Status</th>
                <th class="px-6 py-3.5">Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
              <tr
                v-for="(prop, index) in filteredProperties"
                :key="prop.id"
                class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors"
              >
                <td class="px-4 py-3.5 text-center font-mono font-bold text-slate-400">{{ index + 1 }}</td>
                <td class="px-6 py-3.5 font-bold text-slate-900 dark:text-white max-w-[240px] truncate">
                  {{ prop.title }}
                </td>
                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-300">{{ prop.type }}</td>
                <td class="px-6 py-3.5">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    {{ prop.listing_type }}
                  </span>
                </td>
                <td class="px-6 py-3.5 font-mono font-bold text-slate-900 dark:text-white">
                  ETB {{ Number(prop.price).toLocaleString() }}
                </td>
                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-300">{{ prop.owner }}</td>
                <td class="px-6 py-3.5 text-center font-bold text-slate-700 dark:text-slate-300">{{ prop.views }}</td>
                <td class="px-6 py-3.5">
                  <StatusBadge :status="prop.status ? prop.status.toLowerCase() : 'active'" />
                </td>
                <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">{{ formatDate(prop.created_at) }}</td>
              </tr>
              <tr v-if="filteredProperties.length === 0">
                <td colspan="9" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center justify-center space-y-2">
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">
                      {{ searchQuery ? `No properties matching "${searchQuery}"` : 'No properties found matching the selected filter.' }}
                    </p>
                    <p v-if="searchQuery" class="text-xs text-slate-400">
                      Check your spelling or try searching by a different term.
                    </p>
                    <button
                      v-if="searchQuery || typeFilter || statusFilter"
                      type="button"
                      @click="searchQuery = ''; typeFilter = ''; statusFilter = ''"
                      class="mt-2 inline-flex items-center px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-bold transition-colors cursor-pointer"
                    >
                      Clear Search &amp; Filters
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 2. USERS DIRECTORY TAB -->
    <div v-else-if="activeTab === 'users'" class="space-y-4">
      <!-- Search & Filters -->
      <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
          <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
          <input
            v-model="userSearchQuery"
            type="text"
            placeholder="Search users by name, email, or role..."
            class="w-full pl-10 pr-9 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl text-xs sm:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400"
          />
          <button
            v-if="userSearchQuery"
            type="button"
            @click="userSearchQuery = ''"
            class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors cursor-pointer rounded-lg"
            title="Clear search"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>

        <select
          v-model="userRoleFilter"
          class="px-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none cursor-pointer"
        >
          <option value="">All Roles</option>
          <option value="Buyer">Buyer</option>
          <option value="Owner">Owner</option>
          <option value="Agent">Agent</option>
        </select>
      </div>

      <!-- Users Table -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
          <h2 class="text-sm font-bold text-slate-900 dark:text-white">Registered Users Directory</h2>
          <span class="text-xs text-slate-500 font-medium">{{ filteredUsers.length }} Accounts</span>
        </div>

        <div class="overflow-x-auto w-full">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
              <tr>
                <th class="px-4 py-3.5 w-12 text-center">No</th>
                <th class="px-6 py-3.5">Full Name</th>
                <th class="px-6 py-3.5">Email Address</th>
                <th class="px-6 py-3.5">Phone</th>
                <th class="px-6 py-3.5">Role</th>
                <th class="px-6 py-3.5">Status</th>
                <th class="px-6 py-3.5">Joined Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
              <tr
                v-for="(u, index) in filteredUsers"
                :key="u.id"
                class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors"
              >
                <td class="px-4 py-3.5 text-center font-mono font-bold text-slate-400">{{ index + 1 }}</td>
                <td class="px-6 py-3.5">
                  <div class="flex items-center gap-2.5">
                    <img
                      v-if="resolveAvatar(u)"
                      :src="resolveAvatar(u)"
                      :alt="u.name"
                      class="w-7 h-7 rounded-full object-cover border border-slate-200 dark:border-slate-700 shrink-0"
                    />
                    <div v-else class="w-7 h-7 rounded-full bg-slate-900 dark:bg-slate-800 text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                      {{ (u.name || 'U').charAt(0).toUpperCase() }}
                    </div>
                    <span class="font-bold text-slate-900 dark:text-white">{{ u.name }}</span>
                  </div>
                </td>
                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-300">{{ u.email }}</td>
                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-300 font-mono">{{ u.phone || '—' }}</td>
                <td class="px-6 py-3.5">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    {{ u.role }}
                  </span>
                </td>
                <td class="px-6 py-3.5">
                  <StatusBadge :status="u.status ? u.status.toLowerCase() : 'active'" />
                </td>
                <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">{{ formatDate(u.created_at) }}</td>
              </tr>
              <tr v-if="filteredUsers.length === 0">
                <td colspan="7" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center justify-center space-y-2">
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">
                      {{ userSearchQuery ? `No users matching "${userSearchQuery}"` : 'No users found matching the selected filter.' }}
                    </p>
                    <p v-if="userSearchQuery" class="text-xs text-slate-400">
                      Check your spelling or try searching by another name or email.
                    </p>
                    <button
                      v-if="userSearchQuery || userRoleFilter"
                      type="button"
                      @click="userSearchQuery = ''; userRoleFilter = ''"
                      class="mt-2 inline-flex items-center px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-bold transition-colors cursor-pointer"
                    >
                      Clear Search &amp; Filters
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 3. FRAUD & SAFETY ABUSE REPORTS TAB -->
    <div v-else-if="activeTab === 'abuse'" class="space-y-4">
      <!-- Search & Filters -->
      <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
          <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
          <input
            v-model="abuseSearchQuery"
            type="text"
            placeholder="Search complaints by reason, reporter, or subject..."
            class="w-full pl-10 pr-9 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl text-xs sm:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400"
          />
          <button
            v-if="abuseSearchQuery"
            type="button"
            @click="abuseSearchQuery = ''"
            class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors cursor-pointer rounded-lg"
            title="Clear search"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>

        <div class="flex gap-2">
          <select
            v-model="abuseReasonFilter"
            class="px-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none cursor-pointer"
          >
            <option value="">All Reasons</option>
            <option value="fraud">Fraud</option>
            <option value="duplicate">Duplicate</option>
            <option value="inappropriate">Inappropriate</option>
            <option value="spam">Spam</option>
            <option value="other">Other</option>
          </select>

          <select
            v-model="abuseStatusFilter"
            class="px-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none cursor-pointer"
          >
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="reviewed">Reviewed</option>
            <option value="resolved">Resolved</option>
            <option value="dismissed">Dismissed</option>
          </select>
        </div>
      </div>

      <!-- Abuse Reports Table -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
          <div>
            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Fraud &amp; Safety Complaints Directory</h2>
            <p class="text-xs text-slate-400">User-reported listings, spam, misleading pricing, or policy violations.</p>
          </div>
          <span class="text-xs text-slate-500 font-medium">{{ filteredAbuseReports.length }} Reports</span>
        </div>

        <div class="overflow-x-auto w-full">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
              <tr>
                <th class="px-4 py-3.5 w-12 text-center">No</th>
                <th class="px-6 py-3.5">Reported Target</th>
                <th class="px-6 py-3.5">Reported By</th>
                <th class="px-6 py-3.5">Reason</th>
                <th class="px-6 py-3.5">Description</th>
                <th class="px-6 py-3.5">Status</th>
                <th class="px-6 py-3.5">Date Filed</th>
                <th class="px-4 py-3.5 text-center">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
              <tr
                v-for="(r, index) in filteredAbuseReports"
                :key="r.id"
                class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors"
              >
                <td class="px-4 py-3.5 text-center font-mono font-bold text-slate-400">{{ index + 1 }}</td>
                <td class="px-6 py-3.5">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                      {{ getReportableType(r) }}
                    </span>
                    <span class="font-bold text-slate-900 dark:text-white truncate max-w-[180px]" :title="getReportableTitle(r)">
                      {{ getReportableTitle(r) }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-3.5">
                  <div class="text-slate-900 dark:text-white font-bold">{{ r.reporter?.name || 'Anonymous' }}</div>
                  <div class="text-slate-400 text-[11px]">{{ r.reporter?.email || '—' }}</div>
                </td>
                <td class="px-6 py-3.5">
                  <span
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                    :class="getReasonClass(r.reason)"
                  >
                    {{ r.reason }}
                  </span>
                </td>
                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-300 max-w-[260px] truncate" :title="r.description">
                  {{ r.description || '—' }}
                </td>
                <td class="px-6 py-3.5">
                  <StatusBadge :status="r.status ? r.status.toLowerCase() : 'pending'" />
                </td>
                <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">{{ formatDate(r.created_at) }}</td>
                <td class="px-4 py-3.5 text-center">
                  <button
                    @click="openInspectModal(r)"
                    class="px-3 py-1 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-lg transition-colors cursor-pointer shadow-xs"
                  >
                    Inspect
                  </button>
                </td>
              </tr>
              <tr v-if="filteredAbuseReports.length === 0">
                <td colspan="8" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center justify-center space-y-2">
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">
                      {{ abuseSearchQuery ? `No complaints matching "${abuseSearchQuery}"` : 'No fraud or abuse complaints found.' }}
                    </p>
                    <p v-if="abuseSearchQuery" class="text-xs text-slate-400">
                      Try adjusting your keyword or clearing the filter.
                    </p>
                    <button
                      v-if="abuseSearchQuery || abuseReasonFilter || abuseStatusFilter"
                      type="button"
                      @click="abuseSearchQuery = ''; abuseReasonFilter = ''; abuseStatusFilter = ''"
                      class="mt-2 inline-flex items-center px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-bold transition-colors cursor-pointer"
                    >
                      Clear Filters
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Review / Inspect Complaint Modal -->
    <BaseModal v-model="showInspectModal" title="Inspect Fraud / Safety Report" max-width="lg">
      <div v-if="selectedComplaint" class="space-y-4">
        <!-- Target & Reporter Info Card -->
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 space-y-2.5">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Reported Target</span>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase" :class="getReasonClass(selectedComplaint.reason)">
              {{ selectedComplaint.reason }}
            </span>
          </div>
          <div class="text-sm font-bold text-slate-900 dark:text-white">
            {{ getReportableType(selectedComplaint) }}: {{ getReportableTitle(selectedComplaint) }}
          </div>
          <div class="text-xs text-slate-500 pt-1 border-t border-slate-200/60 dark:border-slate-700 flex justify-between">
            <span>Reported by: <strong class="text-slate-700 dark:text-slate-200">{{ selectedComplaint.reporter?.name || 'Anonymous' }}</strong> ({{ selectedComplaint.reporter?.email || '—' }})</span>
            <span>Date: {{ formatDate(selectedComplaint.created_at) }}</span>
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Complaint Details</label>
          <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">
            {{ selectedComplaint.description || 'No detailed description provided.' }}
          </div>
        </div>

        <!-- Admin Resolution Form -->
        <div class="space-y-3 pt-2 border-t border-slate-100 dark:border-slate-800">
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Update Status</label>
            <select
              v-model="editStatus"
              class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none"
            >
              <option value="reviewed">Mark as Reviewed</option>
              <option value="resolved">Mark as Resolved (Action Taken)</option>
              <option value="dismissed">Mark as Dismissed (Invalid Complaint)</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Admin Notes / Moderation Action</label>
            <textarea
              v-model="editAdminNotes"
              rows="3"
              placeholder="Record any actions taken (e.g. warned the seller, confirmed pricing, adjusted status)..."
              class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400"
            ></textarea>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex justify-end gap-2 pt-3">
          <button
            type="button"
            @click="showInspectModal = false"
            class="px-4 py-2 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
          >
            Cancel
          </button>
          <button
            type="button"
            @click="saveComplaintResolution"
            :disabled="isSavingComplaint"
            class="px-5 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold rounded-xl hover:bg-slate-800 dark:hover:bg-slate-100 cursor-pointer transition-colors disabled:opacity-60 flex items-center gap-1.5"
          >
            <Loader2 v-if="isSavingComplaint" class="w-3.5 h-3.5 animate-spin" />
            <span>{{ isSavingComplaint ? 'Saving...' : 'Save Resolution' }}</span>
          </button>
        </div>
      </div>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  FileSpreadsheet,
  FileText,
  Search,
  X,
  Loader2
} from 'lucide-vue-next'
import { useLanguage } from '../../composables/useLanguage'
import { useToast } from '../../composables/useToast'
import { reportService } from '../../services/reportService'
import StatusBadge from '../../components/dashboard/StatusBadge.vue'
import BaseModal from '../../components/common/BaseModal.vue'

const { t } = useLanguage()
const toast = useToast()

const activeTab = ref('properties')
const isExportingExcel = ref(false)
const isExportingPdf = ref(false)
const isExporting = computed(() => isExportingExcel.value || isExportingPdf.value)

const selectedTimeRange = ref('all')

// Properties filters
const searchQuery = ref('')
const typeFilter = ref('')
const statusFilter = ref('')

// Users filters
const userSearchQuery = ref('')
const userRoleFilter = ref('')

// Abuse/Fraud Complaints filters & state
const abuseReports = ref([])
const abuseSearchQuery = ref('')
const abuseReasonFilter = ref('')
const abuseStatusFilter = ref('')

const showInspectModal = ref(false)
const selectedComplaint = ref(null)
const editStatus = ref('reviewed')
const editAdminNotes = ref('')
const isSavingComplaint = ref(false)

const reportData = ref({})

function resolveAvatar(user) {
  if (!user) return null
  const av = user.avatar_url || user.avatar || user.profile?.avatar
  if (!av || typeof av !== 'string') return null
  if (av.startsWith('data:') || av.startsWith('http://') || av.startsWith('https://')) return av
  if (av.startsWith('/')) return `http://127.0.0.1:8000${av}`
  return `http://127.0.0.1:8000/storage/${av.replace(/^storage\//, '')}`
}

function getReportableType(r) {
  if (!r || !r.reportable_type) return 'Item'
  const parts = r.reportable_type.split('\\')
  return parts[parts.length - 1] || 'Item'
}

function getReportableTitle(r) {
  if (!r) return '—'
  if (r.reportable?.title) return r.reportable.title
  if (r.reportable?.name) return r.reportable.name
  return `Item #${r.reportable_id}`
}

function getReasonClass(reason) {
  const r = (reason || '').toLowerCase()
  if (r === 'fraud') return 'bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-300'
  if (r === 'duplicate') return 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300'
  if (r === 'inappropriate') return 'bg-purple-100 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300'
  return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
}

const filteredProperties = computed(() => {
  let list = reportData.value.recentProperties || []
  if (typeFilter.value) {
    list = list.filter(p => p.type?.toLowerCase() === typeFilter.value.toLowerCase())
  }
  if (statusFilter.value) {
    list = list.filter(p => p.status?.toLowerCase() === statusFilter.value.toLowerCase())
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    const isShort = q.length <= 2
    list = list.filter(p => {
      const title = (p.title || '').toLowerCase()
      const owner = (p.owner || '').toLowerCase()
      const type = (p.type || '').toLowerCase()
      if (isShort) {
        const boundaryRegex = new RegExp('(^|\\s|[,/-])' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'i')
        return boundaryRegex.test(title) || boundaryRegex.test(owner) || boundaryRegex.test(type)
      }
      return title.includes(q) || owner.includes(q) || type.includes(q)
    })
  }
  return list
})

const filteredUsers = computed(() => {
  let list = reportData.value.usersList || []
  if (userRoleFilter.value) {
    list = list.filter(u => u.role?.toLowerCase() === userRoleFilter.value.toLowerCase())
  }
  if (userSearchQuery.value.trim()) {
    const q = userSearchQuery.value.toLowerCase().trim()
    const isShort = q.length <= 2
    list = list.filter(u => {
      const name = (u.name || '').toLowerCase()
      const email = (u.email || '').toLowerCase()
      const phone = (u.phone || '').toLowerCase()
      const role = (u.role || '').toLowerCase()
      if (isShort) {
        const boundaryRegex = new RegExp('(^|\\s|[,/-])' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'i')
        return boundaryRegex.test(name) || email.startsWith(q) || role.startsWith(q) || phone.startsWith(q)
      }
      return name.includes(q) || email.includes(q) || phone.includes(q) || role.includes(q)
    })
  }
  return list
})

const filteredAbuseReports = computed(() => {
  let list = abuseReports.value || []
  if (abuseReasonFilter.value) {
    list = list.filter(r => r.reason?.toLowerCase() === abuseReasonFilter.value.toLowerCase())
  }
  if (abuseStatusFilter.value) {
    list = list.filter(r => r.status?.toLowerCase() === abuseStatusFilter.value.toLowerCase())
  }
  if (abuseSearchQuery.value.trim()) {
    const q = abuseSearchQuery.value.toLowerCase().trim()
    list = list.filter(r => {
      const reason = (r.reason || '').toLowerCase()
      const desc = (r.description || '').toLowerCase()
      const reporter = (r.reporter?.name || '').toLowerCase()
      const target = getReportableTitle(r).toLowerCase()
      return reason.includes(q) || desc.includes(q) || reporter.includes(q) || target.includes(q)
    })
  }
  return list
})

async function loadReport() {
  try {
    const res = await reportService.getAdminReport({ time_range: selectedTimeRange.value })
    reportData.value = res.data || {}
  } catch (err) {
    console.error('Failed to load admin reports:', err)
  }
}

async function loadAbuseReports() {
  try {
    const res = await reportService.getAbuseReports({ per_page: 50 })
    abuseReports.value = res.data?.data || res.data || []
  } catch (err) {
    console.error('Failed to load abuse reports:', err)
  }
}

function openInspectModal(report) {
  selectedComplaint.value = report
  editStatus.value = report.status === 'pending' ? 'reviewed' : (report.status || 'reviewed')
  editAdminNotes.value = report.admin_notes || ''
  showInspectModal.value = true
}

async function saveComplaintResolution() {
  if (!selectedComplaint.value) return
  isSavingComplaint.value = true
  try {
    await reportService.updateAbuseReport(selectedComplaint.value.id, {
      status: editStatus.value,
      admin_notes: editAdminNotes.value
    })
    toast.success('Report moderation status updated successfully!')
    showInspectModal.value = false
    await loadAbuseReports()
  } catch (err) {
    toast.error('Failed to update report status.')
  } finally {
    isSavingComplaint.value = false
  }
}

// ── Instant Direct Downloads ──
async function exportExcelDirect() {
  isExportingExcel.value = true
  try {
    if (activeTab.value === 'abuse') {
      await reportService.exportAdminAbuseReportsExcel(filteredAbuseReports.value)
      toast.success('Safety & Abuse complaints Excel downloaded!')
    } else if (activeTab.value === 'users') {
      await reportService.exportAdminUsersExcel(filteredUsers.value)
      toast.success('Users Directory Excel downloaded!')
    } else {
      await reportService.exportAdminPropertiesExcel(filteredProperties.value)
      toast.success('Properties Audit Excel downloaded!')
    }
  } catch (err) {
    console.error(err)
    toast.error('Failed to download Excel report.')
  } finally {
    isExportingExcel.value = false
  }
}

async function exportPdfDirect() {
  isExportingPdf.value = true
  try {
    if (activeTab.value === 'abuse') {
      await reportService.exportAdminAbuseReportsPdf(filteredAbuseReports.value)
      toast.success('Safety & Abuse complaints PDF downloaded!')
    } else if (activeTab.value === 'users') {
      await reportService.exportAdminUsersPdf(filteredUsers.value)
      toast.success('Users Directory PDF downloaded!')
    } else {
      await reportService.exportAdminPropertiesPdf(filteredProperties.value)
      toast.success('Properties Audit PDF downloaded!')
    }
  } catch (err) {
    console.error(err)
    toast.error('Failed to generate PDF report.')
  } finally {
    isExportingPdf.value = false
  }
}

function formatDate(dateStr) {
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

onMounted(() => {
  loadReport()
  loadAbuseReports()
})
</script>
