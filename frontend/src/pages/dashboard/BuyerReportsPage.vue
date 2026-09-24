<template>
  <div class="space-y-6 pt-1 sm:pt-2">
    <!-- Header with Title & Export Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
          {{ t('buyer_reports_title', 'Activity Reports & Records') }}
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
          Track and export your scheduled viewings, saved properties, and landlord inquiries.
        </p>
      </div>

      <!-- Toolbar: Time Filter & Export Buttons -->
      <div class="flex flex-wrap items-center gap-2.5 shrink-0">
        <!-- Time Filter Dropdown -->
        <select
          v-model="selectedTimeRange"
          @change="loadReportData"
          class="px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold focus:outline-none cursor-pointer shadow-xs"
        >
          <option value="today">Today</option>
          <option value="week">This Week</option>
          <option value="month">This Month</option>
          <option value="year">This Year</option>
          <option value="all">All Time</option>
        </select>

        <button
          @click="openExportModal('excel')"
          :disabled="isExporting || currentRecordCount === 0"
          :class="[
            'flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all shadow-xs',
            currentRecordCount === 0
              ? 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 border border-slate-200/60 dark:border-slate-800 opacity-60 cursor-not-allowed'
              : 'bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 cursor-pointer'
          ]"
          :title="currentRecordCount === 0 ? 'No records to export' : 'Export Excel'"
        >
          <FileSpreadsheet class="w-4 h-4 text-slate-700 dark:text-slate-300" />
          <span>Export Excel</span>
        </button>

        <button
          @click="openExportModal('pdf')"
          :disabled="isExporting || currentRecordCount === 0"
          :class="[
            'flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all shadow-xs',
            currentRecordCount === 0
              ? 'bg-slate-900/40 dark:bg-white/40 text-white/50 dark:text-slate-900/50 opacity-60 cursor-not-allowed'
              : 'bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 cursor-pointer'
          ]"
          :title="currentRecordCount === 0 ? 'No records to export' : 'Export PDF'"
        >
          <FileText class="w-4 h-4" />
          <span>Export PDF</span>
        </button>
      </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
      <!-- 1. Scheduled Tours -->
      <div
        @click="activeTab = 'tours'"
        :class="[
          'p-4 rounded-2xl border shadow-xs cursor-pointer transition-all bg-white dark:bg-slate-900',
          activeTab === 'tours'
            ? 'border-slate-900 dark:border-slate-100 shadow-sm ring-1 ring-slate-900/10 dark:ring-slate-100/20'
            : 'border-slate-200/80 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'
        ]"
      >
        <div class="flex items-center justify-between mb-2">
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
            Scheduled Tours
          </span>
          <Calendar class="w-4 h-4 text-slate-400 dark:text-slate-500" />
        </div>
        <div class="text-xl font-black text-slate-900 dark:text-white">
          {{ summaryStats.totalAppointments }}
        </div>
        <p class="text-[10px] mt-1 font-medium text-slate-500 dark:text-slate-400">
          {{ summaryStats.confirmedAppointments }} confirmed
        </p>
      </div>

      <!-- 2. Saved Favorites -->
      <div
        @click="activeTab = 'favorites'"
        :class="[
          'p-4 rounded-2xl border shadow-xs cursor-pointer transition-all bg-white dark:bg-slate-900',
          activeTab === 'favorites'
            ? 'border-slate-900 dark:border-slate-100 shadow-sm ring-1 ring-slate-900/10 dark:ring-slate-100/20'
            : 'border-slate-200/80 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'
        ]"
      >
        <div class="flex items-center justify-between mb-2">
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
            Saved Properties
          </span>
          <Heart class="w-4 h-4 text-slate-400 dark:text-slate-500" />
        </div>
        <div class="text-xl font-black text-slate-900 dark:text-white">
          {{ summaryStats.savedFavorites }}
        </div>
        <p class="text-[10px] mt-1 font-medium text-slate-500 dark:text-slate-400">
          Favorite shortlisted units
        </p>
      </div>

      <!-- 3. Active Landlord Chats -->
      <div
        @click="activeTab = 'inquiries'"
        :class="[
          'p-4 rounded-2xl border shadow-xs cursor-pointer transition-all bg-white dark:bg-slate-900',
          activeTab === 'inquiries'
            ? 'border-slate-900 dark:border-slate-100 shadow-sm ring-1 ring-slate-900/10 dark:ring-slate-100/20'
            : 'border-slate-200/80 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'
        ]"
      >
        <div class="flex items-center justify-between mb-2">
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
            Active Inquiries
          </span>
          <MessageSquare class="w-4 h-4 text-slate-400 dark:text-slate-500" />
        </div>
        <div class="text-xl font-black text-slate-900 dark:text-white">
          {{ summaryStats.activeConversations }}
        </div>
        <p class="text-[10px] mt-1 font-medium text-slate-500 dark:text-slate-400">
          Landlord conversations
        </p>
      </div>

      <!-- 4. Saved Searches / Alerts -->
      <div
        @click="activeTab = 'alerts'"
        :class="[
          'p-4 rounded-2xl border shadow-xs cursor-pointer transition-all bg-white dark:bg-slate-900',
          activeTab === 'alerts'
            ? 'border-slate-900 dark:border-slate-100 shadow-sm ring-1 ring-slate-900/10 dark:ring-slate-100/20'
            : 'border-slate-200/80 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'
        ]"
      >
        <div class="flex items-center justify-between mb-2">
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
            Saved Alerts
          </span>
          <Search class="w-4 h-4 text-slate-400 dark:text-slate-500" />
        </div>
        <div class="text-xl font-black text-slate-900 dark:text-white">
          {{ summaryStats.savedSearches }}
        </div>
        <p class="text-[10px] mt-1 font-medium text-slate-500 dark:text-slate-400">
          Custom filter presets
        </p>
      </div>
    </div>

    <!-- Search, Filter & View Mode Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
      
      <!-- Active Section Title Indicator -->
      <div class="flex items-center gap-2">
        <h3 class="font-black text-sm text-slate-900 dark:text-white flex items-center gap-2">
          <span>{{ activeSectionTitle }}</span>
          <span class="px-2 py-0.5 text-[11px] rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold">
            {{ currentRecordCount }} Records
          </span>
        </h3>
      </div>

      <!-- Right Controls: Search, Status Filter, and View Mode (Table / Grid) -->
      <div class="flex flex-wrap items-center gap-2.5">
        <!-- Search Input -->
        <div class="relative min-w-[200px] sm:min-w-[240px]">
          <Search class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search records..."
            class="w-full pl-8 pr-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400"
          />
        </div>

        <!-- Status Filter (When in Tours tab) -->
        <select
          v-if="activeTab === 'tours'"
          v-model="statusFilter"
          class="px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:outline-none cursor-pointer"
        >
          <option value="">All Statuses</option>
          <option value="Confirmed">Confirmed</option>
          <option value="Pending">Pending</option>
          <option value="Completed">Completed</option>
          <option value="Cancelled">Cancelled</option>
        </select>

        <!-- Sort Filter Dropdown -->
        <select
          v-model="sortBy"
          class="px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:outline-none cursor-pointer"
        >
          <option value="newest">Newest</option>
          <option value="oldest">Oldest</option>
          <option value="price_low">Lowest</option>
          <option value="price_high">Highest</option>
          <option value="name_asc">Ascending</option>
          <option value="name_desc">Descending</option>
        </select>

        <!-- View Toggle Buttons (Table View vs Grid View) -->
        <div class="flex items-center p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200/60 dark:border-slate-700">
          <button
            @click="viewMode = 'table'"
            :class="[
              'px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer',
              viewMode === 'table'
                ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs'
                : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'
            ]"
            title="Table View"
          >
            <Table class="w-3.5 h-3.5" />
            <span class="hidden sm:inline">Table</span>
          </button>

          <button
            @click="viewMode = 'grid'"
            :class="[
              'px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer',
              viewMode === 'grid'
                ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs'
                : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'
            ]"
            title="Grid View"
          >
            <LayoutGrid class="w-3.5 h-3.5" />
            <span class="hidden sm:inline">Grid</span>
          </button>
        </div>
      </div>

    </div>

    <!-- ── 1. TOURS & APPOINTMENTS SECTION ── -->
    <div v-if="activeTab === 'tours'">
      <!-- Empty State: No Tours Scheduled in Database -->
      <div v-if="liveTours.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-12 text-center shadow-xs">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mb-4 shadow-xs">
          <Calendar class="w-7 h-7" />
        </div>
        <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1.5">No Scheduled Tours Yet</h4>
        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-5 leading-relaxed">
          You haven't scheduled any property tours yet. Browse available listings and book an in-person or virtual viewing with property owners.
        </p>
        <RouterLink
          to="/buyer/properties"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition-all shadow-xs"
        >
          <span>Browse Properties</span>
          <ArrowRight class="w-3.5 h-3.5" />
        </RouterLink>
      </div>

      <!-- Empty State: Search Filters Returned 0 Results -->
      <div v-else-if="filteredTours.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-12 text-center shadow-xs">
        <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-3">
          <Search class="w-6 h-6" />
        </div>
        <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">No Matching Records Found</h4>
        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-4">
          No tours matched your current search filters. Try clearing your search query or status filter.
        </p>
        <button
          @click="searchQuery = ''; statusFilter = ''; sortBy = 'newest'"
          class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition-colors cursor-pointer"
        >
          Clear Filters
        </button>
      </div>

      <!-- Table View -->
      <div v-else-if="viewMode === 'table'" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Property</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Location</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Host / Landlord</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Date & Time</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Format</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs sm:text-sm">
              <tr v-for="t in filteredTours" :key="t.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <td class="px-6 py-3.5 font-bold text-slate-900 dark:text-white">
                  {{ t.property }}
                </td>
                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                  {{ t.location || 'Addis Ababa' }}
                </td>
                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                  {{ t.host }}
                </td>
                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                  {{ t.scheduled_at }}
                </td>
                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                  {{ t.type }}
                </td>
                <td class="px-6 py-3.5">
                  <span
                    :class="[
                      'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide',
                      String(t.status).toLowerCase() === 'confirmed' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' :
                      String(t.status).toLowerCase() === 'pending' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' :
                      String(t.status).toLowerCase() === 'completed' ? 'bg-slate-200 text-slate-800 dark:bg-slate-800 dark:text-slate-200' :
                      'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300'
                    ]"
                  >
                    {{ t.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grid / Card View -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="t in filteredTours"
          :key="t.id"
          class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col justify-between"
        >
          <div>
            <div class="relative aspect-video bg-slate-100 dark:bg-slate-800">
              <img :src="t.property_image" :alt="t.property" class="w-full h-full object-cover" />
              <span
                :class="[
                  'absolute top-3 right-3 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide shadow-xs',
                  String(t.status).toLowerCase() === 'confirmed' ? 'bg-slate-900 text-white' :
                  String(t.status).toLowerCase() === 'pending' ? 'bg-amber-500 text-white' :
                  'bg-slate-700 text-white'
                ]"
              >
                {{ t.status }}
              </span>
              <span class="absolute bottom-3 left-3 px-2 py-0.5 rounded-md bg-black/70 text-white text-[10px] font-bold">
                {{ t.type }}
              </span>
            </div>

            <div class="p-4 space-y-2">
              <h4 class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ t.property }}</h4>
              <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1 truncate">
                <MapPin class="w-3.5 h-3.5 text-slate-400" />
                <span>{{ t.location || 'Addis Ababa' }}</span>
              </p>
              <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Host:</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">{{ t.host }}</span>
              </div>
              <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Time:</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">{{ t.scheduled_at }}</span>
              </div>
            </div>
          </div>

          <div class="p-4 pt-0">
            <RouterLink
              v-if="t.property_id"
              :to="`/buyer/properties/${t.property_id}`"
              class="w-full py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 transition-colors"
            >
              <span>View Property</span>
              <ExternalLink class="w-3 h-3" />
            </RouterLink>
          </div>
        </div>
      </div>
    </div>

    <!-- ── 2. SAVED FAVORITES SECTION ── -->
    <div v-else-if="activeTab === 'favorites'">
      <!-- Empty State: No Saved Properties in Database -->
      <div v-if="liveFavorites.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-12 text-center shadow-xs">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mb-4 shadow-xs">
          <Heart class="w-7 h-7" />
        </div>
        <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1.5">No Saved Properties Yet</h4>
        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-5 leading-relaxed">
          You haven't saved any properties to your favorites. Tap the heart icon on any property card to shortlist and compare them here.
        </p>
        <RouterLink
          to="/buyer/properties"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition-all shadow-xs"
        >
          <span>Explore Properties</span>
          <ArrowRight class="w-3.5 h-3.5" />
        </RouterLink>
      </div>

      <!-- Empty State: Search Filters Returned 0 Results -->
      <div v-else-if="filteredFavorites.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-12 text-center shadow-xs">
        <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-3">
          <Search class="w-6 h-6" />
        </div>
        <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">No Matching Properties Found</h4>
        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-4">
          No saved properties matched your current search filters. Try clearing your search keyword.
        </p>
        <button
          @click="searchQuery = ''; sortBy = 'newest'"
          class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition-colors cursor-pointer"
        >
          Clear Filters
        </button>
      </div>

      <!-- Table View -->
      <div v-else-if="viewMode === 'table'" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Property</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Location</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Price (ETB)</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Beds / Baths</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs sm:text-sm">
              <tr v-for="f in filteredFavorites" :key="f.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <td class="px-6 py-3.5 font-bold text-slate-900 dark:text-white">
                  {{ f.title }}
                </td>
                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                  {{ f.location || 'Addis Ababa' }}
                </td>
                <td class="px-6 py-3.5 font-bold text-emerald-600 dark:text-emerald-400">
                  ETB {{ Number(f.price || 0).toLocaleString() }}
                </td>
                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                  {{ f.listing_type }}
                </td>
                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                  {{ f.bedrooms || '—' }} bd / {{ f.bathrooms || '—' }} ba
                </td>
                <td class="px-6 py-3.5">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-slate-900 text-white dark:bg-white dark:text-slate-900">
                    {{ f.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grid / Card View -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="f in filteredFavorites"
          :key="f.id"
          class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col justify-between"
        >
          <div>
            <div class="relative aspect-video bg-slate-100 dark:bg-slate-800">
              <img :src="f.image" :alt="f.title" class="w-full h-full object-cover" />
              <span class="absolute top-3 left-3 px-2 py-0.5 rounded-md bg-black/70 text-white text-[10px] font-bold">
                {{ f.listing_type }}
              </span>
            </div>

            <div class="p-4 space-y-2">
              <h4 class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ f.title }}</h4>
              <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1 truncate">
                <MapPin class="w-3.5 h-3.5 text-slate-400" />
                <span>{{ f.location || 'Addis Ababa' }}</span>
              </p>
              <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="text-xs font-black text-emerald-600 dark:text-emerald-400">
                  ETB {{ Number(f.price || 0).toLocaleString() }}
                </span>
                <span class="text-[11px] text-slate-500 font-semibold">
                  {{ f.bedrooms || '—' }} Beds • {{ f.bathrooms || '—' }} Baths
                </span>
              </div>
            </div>
          </div>

          <div class="p-4 pt-0">
            <RouterLink
              :to="`/buyer/properties/${f.id}`"
              class="w-full py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 transition-colors"
            >
              <span>View Property</span>
              <ExternalLink class="w-3 h-3" />
            </RouterLink>
          </div>
        </div>
      </div>
    </div>

    <!-- ── 3. INQUIRIES & CHATS SECTION ── -->
    <div v-else-if="activeTab === 'inquiries'">
      <!-- Empty State: No Inquiries in Database -->
      <div v-if="liveConversations.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-12 text-center shadow-xs">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mb-4 shadow-xs">
          <MessageSquare class="w-7 h-7" />
        </div>
        <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1.5">No Active Inquiries Yet</h4>
        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-5 leading-relaxed">
          Direct messages and tour questions you send to property owners will appear here for easy reference and tracking.
        </p>
        <RouterLink
          to="/buyer/properties"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition-all shadow-xs"
        >
          <span>Browse Properties</span>
          <ArrowRight class="w-3.5 h-3.5" />
        </RouterLink>
      </div>

      <!-- Empty State: Search Filters Returned 0 Results -->
      <div v-else-if="filteredConversations.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-12 text-center shadow-xs">
        <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-3">
          <Search class="w-6 h-6" />
        </div>
        <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">No Matching Inquiries Found</h4>
        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-4">
          No message records matched your current search filters. Try clearing your search keyword.
        </p>
        <button
          @click="searchQuery = ''; sortBy = 'newest'"
          class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition-colors cursor-pointer"
        >
          Clear Filters
        </button>
      </div>

      <!-- Table View -->
      <div v-else-if="viewMode === 'table'" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Property / Subject</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Host / Landlord</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Host Email</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Last Message</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Updated</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs sm:text-sm">
              <tr v-for="c in filteredConversations" :key="c.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <td class="px-6 py-3.5 font-bold text-slate-900 dark:text-white">
                  {{ c.property }}
                </td>
                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                  {{ c.contact_name }}
                </td>
                <td class="px-6 py-3.5 text-slate-500 dark:text-slate-400 font-medium">
                  {{ c.contact_email || '—' }}
                </td>
                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-300 font-medium max-w-xs truncate">
                  {{ c.last_message }}
                </td>
                <td class="px-6 py-3.5 text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
                  {{ c.updated_at }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grid / Card View -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="c in filteredConversations"
          :key="c.id"
          class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs hover:shadow-md transition-all flex flex-col justify-between"
        >
          <div class="space-y-3">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-white font-bold flex items-center justify-center text-sm uppercase">
                {{ c.contact_name ? c.contact_name.charAt(0) : 'H' }}
              </div>
              <div class="min-w-0 flex-1">
                <h4 class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ c.contact_name }}</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ c.property }}</p>
              </div>
            </div>

            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl text-xs text-slate-600 dark:text-slate-300 leading-relaxed line-clamp-2">
              "{{ c.last_message }}"
            </div>

            <div class="text-[11px] text-slate-400 flex items-center gap-1">
              <Clock class="w-3 h-3" />
              <span>Updated: {{ c.updated_at }}</span>
            </div>
          </div>

          <div class="pt-4 mt-2">
            <RouterLink
              :to="`/buyer/messages`"
              class="w-full py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 transition-colors"
            >
              <span>Open Conversation</span>
              <MessageSquare class="w-3 h-3" />
            </RouterLink>
          </div>
        </div>
      </div>
    </div>

    <!-- ── 4. SAVED ALERTS & CUSTOM SEARCHES SECTION ── -->
    <div v-else-if="activeTab === 'alerts'">
      <!-- Empty State: No Saved Searches in Database -->
      <div v-if="liveSearches.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-12 text-center shadow-xs">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mb-4 shadow-xs">
          <Search class="w-7 h-7" />
        </div>
        <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1.5">No Saved Alerts Yet</h4>
        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-5 leading-relaxed">
          Save tailored property searches with price, type, and location criteria to receive automated updates on new listings.
        </p>
        <RouterLink
          to="/buyer/properties"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition-all shadow-xs"
        >
          <span>Find Properties</span>
          <ArrowRight class="w-3.5 h-3.5" />
        </RouterLink>
      </div>

      <!-- Empty State: Search Filters Returned 0 Results -->
      <div v-else-if="filteredSearches.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-12 text-center shadow-xs">
        <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-3">
          <Search class="w-6 h-6" />
        </div>
        <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">No Matching Alerts Found</h4>
        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-4">
          No saved alerts matched your current search filters. Try clearing your search keyword.
        </p>
        <button
          @click="searchQuery = ''; sortBy = 'newest'"
          class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition-colors cursor-pointer"
        >
          Clear Filters
        </button>
      </div>

      <!-- Table View -->
      <div v-else-if="viewMode === 'table'" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Alert / Search Name</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Saved Criteria</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Notifications</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Saved On</th>
                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs sm:text-sm">
              <tr v-for="s in filteredSearches" :key="s.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <td class="px-6 py-3.5 font-bold text-slate-900 dark:text-white">
                  {{ s.name }}
                </td>
                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                  <div class="flex flex-wrap gap-1">
                    <span v-for="(val, key) in (s.filters || {})" :key="key" class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-[10px] font-semibold text-slate-700 dark:text-slate-300">
                      {{ key }}: {{ val }}
                    </span>
                    <span v-if="!s.filters || Object.keys(s.filters).length === 0" class="text-slate-400 text-xs">—</span>
                  </div>
                </td>
                <td class="px-6 py-3.5">
                  <span
                    :class="[
                      'inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold rounded-lg',
                      s.alert_enabled
                        ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800'
                        : 'bg-slate-100 dark:bg-slate-800 text-slate-500'
                    ]"
                  >
                    <span>{{ s.alert_enabled ? 'Active' : 'Muted' }}</span>
                  </span>
                </td>
                <td class="px-6 py-3.5 text-slate-500 text-xs font-medium">
                  {{ s.created_at }}
                </td>
                <td class="px-6 py-3.5 text-right">
                  <RouterLink
                    to="/buyer/properties"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-lg text-xs font-bold transition-colors"
                  >
                    <span>Search</span>
                    <ExternalLink class="w-3 h-3" />
                  </RouterLink>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grid View -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="s in filteredSearches"
          :key="s.id"
          class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow"
        >
          <div class="space-y-3">
            <div class="flex items-start justify-between gap-2">
              <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ s.name }}</h4>
              <span
                :class="[
                  'px-2 py-0.5 text-[10px] font-bold rounded-md',
                  s.alert_enabled
                    ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400'
                    : 'bg-slate-100 dark:bg-slate-800 text-slate-500'
                ]"
              >
                {{ s.alert_enabled ? 'Alerts ON' : 'Muted' }}
              </span>
            </div>

            <div class="flex flex-wrap gap-1.5 pt-1">
              <span v-for="(val, key) in (s.filters || {})" :key="key" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-xs font-medium text-slate-700 dark:text-slate-300">
                {{ key }}: {{ val }}
              </span>
            </div>

            <p class="text-[11px] text-slate-400">Created: {{ s.created_at }}</p>
          </div>

          <div class="pt-4 border-t border-slate-100 dark:border-slate-800 mt-3">
            <RouterLink
              to="/buyer/properties"
              class="w-full py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 transition-colors"
            >
              <span>Explore Matching Properties</span>
              <ExternalLink class="w-3 h-3" />
            </RouterLink>
          </div>
        </div>
      </div>
    </div>

    <!-- Export Confirmation Modal -->
    <ConfirmModal
      :isOpen="showExportModal"
      :title="`Export ${exportTitle} (${exportType.toUpperCase()})`"
      :message="`Are you sure you want to export your personal ${exportTitle.toLowerCase()} as ${exportType === 'excel' ? 'an Excel Spreadsheet (.csv)' : 'a PDF Document'}?`"
      :confirmLabel="exportType === 'excel' ? 'Download Excel' : 'Download PDF'"
      cancelLabel="Cancel"
      @confirm="confirmExport"
      @cancel="showExportModal = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import {
  FileSpreadsheet,
  FileText,
  Heart,
  Calendar,
  MessageSquare,
  Search,
  LayoutGrid,
  Table,
  MapPin,
  ExternalLink,
  Clock,
  ArrowRight
} from 'lucide-vue-next'
import { reportService } from '@/services/reportService'
import { favoriteService } from '@/services/favoriteService'
import { appointmentService } from '@/services/appointmentService'
import { useLanguage } from '@/composables/useLanguage'
import { useToastStore } from '@/stores/toast'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'

const { t } = useLanguage()
const toast = useToastStore()

const activeTab = ref('tours') // 'tours' | 'favorites' | 'inquiries' | 'alerts'
const viewMode = ref('table') // 'table' | 'grid'
const selectedTimeRange = ref('all')

const isExporting = ref(false)
const showExportModal = ref(false)
const exportType = ref('excel')

const searchQuery = ref('')
const statusFilter = ref('')
const sortBy = ref('newest')

const reportData = ref({
  summary: {
    savedFavorites: 0,
    totalAppointments: 0,
    confirmedAppointments: 0,
    completedAppointments: 0,
    activeConversations: 0,
    savedSearches: 0
  },
  toursHistory: [],
  favorites: [],
  conversations: [],
  saved_searches: []
})

const summaryStats = computed(() => reportData.value?.summary || {
  savedFavorites: liveFavorites.value.length,
  totalAppointments: liveTours.value.length,
  confirmedAppointments: liveTours.value.filter(t => String(t.status).toLowerCase() === 'confirmed').length,
  completedAppointments: liveTours.value.filter(t => String(t.status).toLowerCase() === 'completed').length,
  activeConversations: liveConversations.value.length,
  savedSearches: liveSearches.value.length
})

const liveTours = computed(() => reportData.value?.toursHistory || [])
const liveFavorites = computed(() => reportData.value?.favorites || [])
const liveConversations = computed(() => reportData.value?.conversations || [])
const liveSearches = computed(() => reportData.value?.saved_searches || [])

const filteredTours = computed(() => {
  let list = [...liveTours.value]
  if (statusFilter.value) {
    list = list.filter(t => String(t.status).toLowerCase() === statusFilter.value.toLowerCase())
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(t =>
      t.property?.toLowerCase().includes(q) ||
      t.location?.toLowerCase().includes(q) ||
      t.host?.toLowerCase().includes(q) ||
      t.scheduled_at?.toLowerCase().includes(q)
    )
  }
  if (sortBy.value === 'newest') {
    list.sort((a, b) => new Date(b.scheduled_at || 0) - new Date(a.scheduled_at || 0) || (b.id || 0) - (a.id || 0))
  } else if (sortBy.value === 'oldest') {
    list.sort((a, b) => new Date(a.scheduled_at || 0) - new Date(b.scheduled_at || 0) || (a.id || 0) - (b.id || 0))
  } else if (sortBy.value === 'name_asc') {
    list.sort((a, b) => (a.property || '').localeCompare(b.property || ''))
  } else if (sortBy.value === 'name_desc') {
    list.sort((a, b) => (b.property || '').localeCompare(a.property || ''))
  }
  return list
})

const filteredFavorites = computed(() => {
  let list = [...liveFavorites.value]
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(f =>
      f.title?.toLowerCase().includes(q) ||
      f.location?.toLowerCase().includes(q) ||
      f.listing_type?.toLowerCase().includes(q)
    )
  }
  if (sortBy.value === 'newest') {
    list.sort((a, b) => new Date(b.created_at || b.saved_at || 0) - new Date(a.created_at || a.saved_at || 0) || (b.id || 0) - (a.id || 0))
  } else if (sortBy.value === 'oldest') {
    list.sort((a, b) => new Date(a.created_at || a.saved_at || 0) - new Date(b.created_at || b.saved_at || 0) || (a.id || 0) - (b.id || 0))
  } else if (sortBy.value === 'price_low') {
    list.sort((a, b) => (Number(a.price) || 0) - (Number(b.price) || 0))
  } else if (sortBy.value === 'price_high') {
    list.sort((a, b) => (Number(b.price) || 0) - (Number(a.price) || 0))
  } else if (sortBy.value === 'name_asc') {
    list.sort((a, b) => (a.title || '').localeCompare(b.title || ''))
  } else if (sortBy.value === 'name_desc') {
    list.sort((a, b) => (b.title || '').localeCompare(a.title || ''))
  }
  return list
})

const filteredConversations = computed(() => {
  let list = [...liveConversations.value]
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(c =>
      c.property?.toLowerCase().includes(q) ||
      c.contact_name?.toLowerCase().includes(q) ||
      c.last_message?.toLowerCase().includes(q)
    )
  }
  if (sortBy.value === 'newest') {
    list.sort((a, b) => new Date(b.last_activity || b.created_at || 0) - new Date(a.last_activity || a.created_at || 0) || (b.id || 0) - (a.id || 0))
  } else if (sortBy.value === 'oldest') {
    list.sort((a, b) => new Date(a.last_activity || a.created_at || 0) - new Date(b.last_activity || b.created_at || 0) || (a.id || 0) - (b.id || 0))
  } else if (sortBy.value === 'name_asc') {
    list.sort((a, b) => (a.property || a.contact_name || '').localeCompare(b.property || b.contact_name || ''))
  } else if (sortBy.value === 'name_desc') {
    list.sort((a, b) => (b.property || b.contact_name || '').localeCompare(a.property || a.contact_name || ''))
  }
  return list
})

const filteredSearches = computed(() => {
  let list = [...liveSearches.value]
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(s =>
      s.name?.toLowerCase().includes(q)
    )
  }
  if (sortBy.value === 'newest') {
    list.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0) || (b.id || 0) - (a.id || 0))
  } else if (sortBy.value === 'oldest') {
    list.sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0) || (a.id || 0) - (b.id || 0))
  } else if (sortBy.value === 'name_asc') {
    list.sort((a, b) => (a.name || '').localeCompare(b.name || ''))
  } else if (sortBy.value === 'name_desc') {
    list.sort((a, b) => (b.name || '').localeCompare(a.name || ''))
  }
  return list
})

const currentRecordCount = computed(() => {
  if (activeTab.value === 'favorites') return filteredFavorites.value.length
  if (activeTab.value === 'inquiries') return filteredConversations.value.length
  if (activeTab.value === 'alerts') return filteredSearches.value.length
  return filteredTours.value.length
})

const exportTitle = computed(() => {
  if (activeTab.value === 'favorites') return 'Saved Favorites Report'
  if (activeTab.value === 'inquiries') return 'Inquiries & Conversations Report'
  if (activeTab.value === 'alerts') return 'Saved Alerts Report'
  return 'Tour Appointments Report'
})

const activeSectionTitle = computed(() => {
  if (activeTab.value === 'favorites') return 'Saved Properties'
  if (activeTab.value === 'inquiries') return 'Inquiries & Chats'
  if (activeTab.value === 'alerts') return 'Saved Alerts & Custom Searches'
  return 'Tour Appointments'
})

async function loadData() {
  try {
    const res = await reportService.getBuyerReport()
    const data = res?.data?.data || res?.data || res || {}
    reportData.value = {
      summary: data.summary || {
        savedFavorites: 0,
        totalAppointments: 0,
        confirmedAppointments: 0,
        completedAppointments: 0,
        activeConversations: 0,
        savedSearches: 0
      },
      toursHistory: data.toursHistory || [],
      favorites: data.favorites || [],
      conversations: data.conversations || [],
      saved_searches: data.saved_searches || []
    }

    // If tours or favorites are empty from reports endpoint, hydrate directly from real services
    if (!reportData.value.toursHistory.length) {
      try {
        const apptRes = await appointmentService.getAppointments()
        const appts = apptRes?.data?.data || apptRes?.data || []
        if (Array.isArray(appts) && appts.length > 0) {
          reportData.value.toursHistory = appts.map(a => ({
            id: a.id,
            property_id: a.property_id,
            property: a.property?.title || a.property_title || 'Property Tour',
            property_image: a.property?.primaryImage?.url || a.property_image || 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80',
            location: a.property?.location || (a.property?.address?.city?.name ?? 'Addis Ababa'),
            price: Number(a.property?.price || 0),
            listing_type: a.property?.listing_type || 'sale',
            host: a.owner?.name || a.owner_name || 'Property Host',
            host_phone: a.owner?.phone || a.owner_phone || '+251 91 100 0000',
            scheduled_at: a.scheduled_at ? new Date(a.scheduled_at).toLocaleString() : 'TBD',
            status: a.status ? a.status.charAt(0).toUpperCase() + a.status.slice(1) : 'Pending',
            type: a.type === 'virtual' ? 'Virtual' : 'In-Person'
          }))
          reportData.value.summary.totalAppointments = reportData.value.toursHistory.length
          reportData.value.summary.confirmedAppointments = reportData.value.toursHistory.filter(t => t.status === 'Confirmed').length
        }
      } catch (e) {
        console.warn('Fallback appt load failed:', e)
      }
    }

    if (!reportData.value.favorites.length) {
      try {
        const favRes = await favoriteService.getFavorites()
        const favs = favRes?.data?.data || favRes?.data || []
        if (Array.isArray(favs) && favs.length > 0) {
          reportData.value.favorites = favs.map(f => ({
            id: f.property?.id || f.id,
            title: f.property?.title || f.title || 'Saved Property',
            image: f.property?.primaryImage?.url || f.image || 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80',
            location: f.property?.location || (f.property?.address?.city?.name ?? 'Addis Ababa'),
            price: Number(f.property?.price || f.price || 0),
            listing_type: f.property?.listing_type || f.listing_type || 'Sale',
            bedrooms: f.property?.bedrooms || f.bedrooms || 0,
            bathrooms: f.property?.bathrooms || f.bathrooms || 0,
            status: 'Active',
            saved_at: f.created_at || new Date().toISOString().split('T')[0]
          }))
          reportData.value.summary.savedFavorites = reportData.value.favorites.length
        }
      } catch (e) {
        console.warn('Fallback fav load failed:', e)
      }
    }

    // Real saved_searches come directly from backend reportData
  } catch (err) {
    console.error('Failed to load buyer reports:', err)
  }
}

function openExportModal(type) {
  exportType.value = type
  showExportModal.value = true
}

async function confirmExport() {
  showExportModal.value = false
  isExporting.value = true

  try {
    let records = []
    let section = activeTab.value

    if (section === 'favorites') {
      records = filteredFavorites.value
    } else if (section === 'inquiries') {
      records = filteredConversations.value
    } else if (section === 'alerts') {
      records = filteredSearches.value
    } else {
      records = filteredTours.value
    }

    if (exportType.value === 'excel') {
      await reportService.exportBuyerReportExcel(records, section)
      toast.success(`${exportTitle.value} Excel downloaded successfully!`)
    } else {
      await reportService.exportBuyerReportPdf(records, section)
      toast.success(`${exportTitle.value} PDF generated successfully!`)
    }
  } catch (err) {
    console.error('Export error:', err)
    toast.error('Failed to export report.')
  } finally {
    isExporting.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>
