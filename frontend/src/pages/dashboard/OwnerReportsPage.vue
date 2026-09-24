<template>
  <div class="space-y-6">
    <!-- Header with Title, Time Filter & Export Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
          Portfolio Reports & Analytics
        </h1>
      </div>

      <!-- Toolbar: Time Filter Dropdown & Export Buttons -->
      <div class="flex flex-wrap items-center gap-2.5 shrink-0">
        <!-- Time Filter Dropdown -->
        <select
          v-model="selectedTimeRange"
          @change="loadReport"
          class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 cursor-pointer shadow-xs"
        >
          <option value="today">Today</option>
          <option value="week">Week</option>
          <option value="month">Month</option>
          <option value="year">Year</option>
          <option value="all">All</option>
        </select>

        <!-- Export Excel Button -->
        <button
          type="button"
          @click="promptExport('excel')"
          :disabled="isExporting"
          class="flex items-center gap-1.5 px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition-all shadow-xs disabled:opacity-60 cursor-pointer active:scale-95"
        >
          <FileSpreadsheet class="w-4 h-4 text-slate-700 dark:text-slate-300" />
          <span>Excel</span>
        </button>

        <!-- Export PDF Button -->
        <button
          type="button"
          @click="promptExport('pdf')"
          :disabled="isExporting"
          class="flex items-center gap-1.5 px-3.5 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl text-xs font-bold transition-all shadow-xs disabled:opacity-60 cursor-pointer active:scale-95"
        >
          <FileText class="w-4 h-4" />
          <span>PDF</span>
        </button>
      </div>
    </div>

    <!-- KPI Metric Cards Grid (Interactive Category Switchers) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
      <!-- 1. Total Views -->
      <button
        type="button"
        @click="activeTab = 'properties'"
        :class="[
          'p-4 rounded-2xl shadow-xs transition-all text-left cursor-pointer active:scale-[0.99]',
          activeTab === 'properties'
            ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 ring-2 ring-slate-900 dark:ring-white border-transparent'
            : 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600'
        ]"
      >
        <div class="flex items-center justify-between mb-2" :class="activeTab === 'properties' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-400'">
          <span class="text-[11px] font-bold uppercase tracking-wider">Total Views</span>
          <Eye class="w-4 h-4" />
        </div>
        <div class="text-xl font-black" :class="activeTab === 'properties' ? 'text-white dark:text-slate-900' : 'text-slate-900 dark:text-white'">
          {{ reportData.summary?.totalViews?.toLocaleString() || 0 }}
        </div>
        <p class="text-[10px] mt-1 font-medium" :class="activeTab === 'properties' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-500'">
          Cumulative listing views
        </p>
      </button>

      <!-- 2. Tour Bookings -->
      <button
        type="button"
        @click="activeTab = 'appointments'"
        :class="[
          'p-4 rounded-2xl shadow-xs transition-all text-left cursor-pointer active:scale-[0.99]',
          activeTab === 'appointments'
            ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 ring-2 ring-slate-900 dark:ring-white border-transparent'
            : 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600'
        ]"
      >
        <div class="flex items-center justify-between mb-2" :class="activeTab === 'appointments' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-400'">
          <span class="text-[11px] font-bold uppercase tracking-wider">Tour Bookings</span>
          <Calendar class="w-4 h-4" />
        </div>
        <div class="text-xl font-black" :class="activeTab === 'appointments' ? 'text-white dark:text-slate-900' : 'text-slate-900 dark:text-white'">
          {{ reportData.summary?.totalAppointments || 0 }}
        </div>
        <p class="text-[10px] mt-1 font-medium" :class="activeTab === 'appointments' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-500'">
          Scheduled property tours
        </p>
      </button>

      <!-- 3. Active Listings -->
      <button
        type="button"
        @click="activeTab = 'properties'"
        :class="[
          'p-4 rounded-2xl shadow-xs transition-all text-left cursor-pointer active:scale-[0.99]',
          activeTab === 'properties'
            ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 ring-2 ring-slate-900 dark:ring-white border-transparent'
            : 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600'
        ]"
      >
        <div class="flex items-center justify-between mb-2" :class="activeTab === 'properties' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-400'">
          <span class="text-[11px] font-bold uppercase tracking-wider">Active Listings</span>
          <Building2 class="w-4 h-4" />
        </div>
        <div class="text-xl font-black" :class="activeTab === 'properties' ? 'text-white dark:text-slate-900' : 'text-slate-900 dark:text-white'">
          {{ reportData.summary?.activeListings || 0 }} / {{ reportData.summary?.totalProperties || 0 }}
        </div>
        <p class="text-[10px] mt-1 font-medium" :class="activeTab === 'properties' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-500'">
          Published on marketplace
        </p>
      </button>

      <!-- 4. Client Inquiries -->
      <button
        type="button"
        @click="activeTab = 'inquiries'"
        :class="[
          'p-4 rounded-2xl shadow-xs transition-all text-left cursor-pointer active:scale-[0.99]',
          activeTab === 'inquiries'
            ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 ring-2 ring-slate-900 dark:ring-white border-transparent'
            : 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600'
        ]"
      >
        <div class="flex items-center justify-between mb-2" :class="activeTab === 'inquiries' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-400'">
          <span class="text-[11px] font-bold uppercase tracking-wider">Client Inquiries</span>
          <MessageSquare class="w-4 h-4" />
        </div>
        <div class="text-xl font-black" :class="activeTab === 'inquiries' ? 'text-white dark:text-slate-900' : 'text-slate-900 dark:text-white'">
          {{ reportData.summary?.totalInquiries || 0 }}
        </div>
        <p class="text-[10px] mt-1 font-medium" :class="activeTab === 'inquiries' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-500'">
          {{ reportData.summary?.inquiryRate || 0 }}% conversion rate
        </p>
      </button>
    </div>

    <!-- Filters, Category Tabs & View Mode Switcher -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
      <!-- Search Input -->
      <div class="relative flex-1 max-w-sm">
        <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search records..."
          class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 transition-colors"
        />
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <!-- Category Selector -->
        <select
          v-model="activeTab"
          class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:outline-none cursor-pointer"
        >
          <option value="properties">All ({{ reportData.propertyBreakdown?.length || 0 }})</option>
          <option value="appointments">Bookings ({{ reportData.appointmentsList?.length || 0 }})</option>
          <option value="inquiries">Inquiries ({{ reportData.inquiriesList?.length || 0 }})</option>
          <option value="favorites">Favorites ({{ reportData.favoritesList?.length || 0 }})</option>
        </select>

        <!-- Property Type Filter (Apartment, Condominium, Villa, Office, etc.) -->
        <select
          v-if="activeTab === 'properties'"
          v-model="selectedPropertyType"
          class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:outline-none cursor-pointer animate-in fade-in duration-100"
        >
          <option value="all">All</option>
          <option v-for="t in availablePropertyTypes" :key="t" :value="t">
            {{ t }}
          </option>
        </select>

        <!-- View Mode: Table vs Grid Toggle -->
        <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700">
          <button
            type="button"
            @click="viewMode = 'table'"
            :class="[
              'px-2.5 py-1 text-xs font-bold rounded-lg transition-all flex items-center gap-1 cursor-pointer',
              viewMode === 'table'
                ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs'
                : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'
            ]"
            title="Table View"
          >
            <List class="w-3.5 h-3.5" />
            <span class="hidden sm:inline">Table</span>
          </button>
          <button
            type="button"
            @click="viewMode = 'grid'"
            :class="[
              'px-2.5 py-1 text-xs font-bold rounded-lg transition-all flex items-center gap-1 cursor-pointer',
              viewMode === 'grid'
                ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs'
                : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'
            ]"
            title="Grid View"
          >
            <LayoutGrid class="w-3.5 h-3.5" />
            <span class="hidden sm:inline">Grid</span>
          </button>
        </div>
      </div>
    </div>

    <!-- 1. PROPERTIES TAB CONTENT -->
    <template v-if="activeTab === 'properties'">
      <!-- Loading Skeleton -->
      <div v-if="isLoading" class="bg-white dark:bg-slate-900 rounded-2xl p-8 text-center animate-pulse">
        <div class="h-6 bg-slate-200 dark:bg-slate-800 rounded w-1/3 mx-auto mb-4"></div>
        <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-1/2 mx-auto"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredProperties.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-12 text-center">
        <Building2 class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
        <h3 class="text-sm font-bold text-slate-900 dark:text-white">No property records found</h3>
        <p class="text-xs text-slate-500 mt-1">Try selecting a different time range or clear search filter.</p>
      </div>

      <!-- Table View -->
      <div v-else-if="viewMode === 'table'" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="py-2.5 px-2.5 w-10 text-center">No</th>
                <th class="py-2.5 px-3">Property</th>
                <th class="py-2.5 px-3 whitespace-nowrap">Type</th>
                <th class="py-2.5 px-3 whitespace-nowrap">Price</th>
                <th class="py-2.5 px-2.5 text-center whitespace-nowrap">Views</th>
                <th class="py-2.5 px-2.5 text-center whitespace-nowrap">Saves</th>
                <th class="py-2.5 px-2.5 text-center whitespace-nowrap">Tours</th>
                <th class="py-2.5 px-2.5 text-center whitespace-nowrap">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
              <tr v-for="(p, index) in filteredProperties" :key="p.id" class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                <td class="py-2.5 px-2.5 text-center font-bold text-slate-400 dark:text-slate-500">{{ index + 1 }}</td>
                <td class="py-2.5 px-3 min-w-[200px]">
                  <div class="flex items-center gap-2.5">
                    <img :src="p.image" class="w-10 h-10 rounded-lg object-cover bg-slate-100 shrink-0" />
                    <div class="min-w-0">
                      <p class="font-bold text-slate-900 dark:text-white truncate max-w-xs">{{ p.title }}</p>
                      <p class="text-[11px] text-slate-400 truncate">{{ p.location }}</p>
                    </div>
                  </div>
                </td>
                <td class="py-2.5 px-3 font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ p.type }}</td>
                <td class="py-2.5 px-3 font-black text-slate-900 dark:text-white whitespace-nowrap">ETB {{ Number(p.price).toLocaleString() }}</td>
                <td class="py-2.5 px-2.5 text-center font-bold text-slate-900 dark:text-white whitespace-nowrap">{{ p.views }}</td>
                <td class="py-2.5 px-2.5 text-center font-bold text-slate-900 dark:text-white whitespace-nowrap">{{ p.favorites }}</td>
                <td class="py-2.5 px-2.5 text-center font-bold text-slate-900 dark:text-white whitespace-nowrap">{{ p.tours_booked }}</td>
                <td class="py-2.5 px-2.5 text-center whitespace-nowrap">
                  <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80">
                    {{ p.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grid View -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="p in filteredProperties"
          :key="p.id"
          class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden flex flex-col justify-between"
        >
          <div class="relative h-44 bg-slate-100 dark:bg-slate-800">
            <img :src="p.image" class="w-full h-full object-cover" />
            <div class="absolute top-3 right-3">
              <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md uppercase tracking-wider shadow-xs bg-slate-100/90 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80 backdrop-blur-xs">
                {{ p.status }}
              </span>
            </div>
            <div class="absolute bottom-3 left-3 right-3 text-white">
              <p class="text-sm font-black truncate drop-shadow-md">{{ p.title }}</p>
              <p class="text-[11px] text-slate-200 truncate">{{ p.location }}</p>
            </div>
          </div>
          <div class="p-4 space-y-3">
            <div class="flex items-center justify-between text-xs">
              <span class="text-slate-500">{{ p.type }}</span>
              <span class="font-black text-slate-900 dark:text-white">ETB {{ Number(p.price).toLocaleString() }}</span>
            </div>
            <div class="grid grid-cols-3 gap-2 text-center text-xs py-2 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800/80">
              <div>
                <span class="text-[10px] text-slate-400 block uppercase font-bold">Views</span>
                <span class="font-black text-slate-900 dark:text-white">{{ p.views }}</span>
              </div>
              <div>
                <span class="text-[10px] text-slate-400 block uppercase font-bold">Saves</span>
                <span class="font-black text-slate-900 dark:text-white">{{ p.favorites }}</span>
              </div>
              <div>
                <span class="text-[10px] text-slate-400 block uppercase font-bold">Tours</span>
                <span class="font-black text-slate-900 dark:text-white">{{ p.tours_booked }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- 2. APPOINTMENTS TAB CONTENT -->
    <template v-else-if="activeTab === 'appointments'">
      <div v-if="filteredAppointments.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-12 text-center">
        <Calendar class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
        <h3 class="text-sm font-bold text-slate-900 dark:text-white">No tour appointment records</h3>
        <p class="text-xs text-slate-500 mt-1">Appointments booked in this timeframe will appear here.</p>
      </div>

      <!-- Table View -->
      <div v-else-if="viewMode === 'table'" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="py-2.5 px-2.5 w-10 text-center">No</th>
                <th class="py-2.5 px-3">Property</th>
                <th class="py-2.5 px-3 whitespace-nowrap">Visitor</th>
                <th class="py-2.5 px-3 whitespace-nowrap">Contact</th>
                <th class="py-2.5 px-3 whitespace-nowrap">Format</th>
                <th class="py-2.5 px-3 whitespace-nowrap">Date & Time</th>
                <th class="py-2.5 px-2.5 text-center whitespace-nowrap">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
              <tr v-for="(a, index) in filteredAppointments" :key="a.id" class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                <td class="py-2.5 px-2.5 text-center font-bold text-slate-400 dark:text-slate-500">{{ index + 1 }}</td>
                <td class="py-2.5 px-3 font-bold text-slate-900 dark:text-white">{{ a.property }}</td>
                <td class="py-2.5 px-3 font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ a.visitor }}</td>
                <td class="py-2.5 px-3 text-slate-500 whitespace-nowrap">{{ a.visitor_phone }}</td>
                <td class="py-2.5 px-3 text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ a.type }}</td>
                <td class="py-2.5 px-3 font-medium text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ a.scheduled_at }}</td>
                <td class="py-2.5 px-2.5 text-center whitespace-nowrap">
                  <span
                    class="px-2 py-0.5 text-[10px] font-extrabold rounded-md uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80"
                  >
                    {{ a.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grid View -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="a in filteredAppointments"
          :key="a.id"
          class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3"
        >
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-900 dark:text-white">{{ a.property }}</span>
            <span
              class="px-2 py-0.5 text-[10px] font-extrabold rounded-md uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80"
            >
              {{ a.status }}
            </span>
          </div>
          <div class="space-y-1 text-xs text-slate-500 dark:text-slate-400 pt-1">
            <p><span class="font-bold text-slate-700 dark:text-slate-300">Client:</span> {{ a.visitor }} ({{ a.visitor_phone }})</p>
            <p><span class="font-bold text-slate-700 dark:text-slate-300">Format:</span> {{ a.type }}</p>
            <p><span class="font-bold text-slate-700 dark:text-slate-300">Time:</span> {{ a.scheduled_at }}</p>
          </div>
        </div>
      </div>
    </template>

    <!-- 3. INQUIRIES TAB CONTENT -->
    <template v-else-if="activeTab === 'inquiries'">
      <div v-if="filteredInquiries.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-12 text-center">
        <MessageSquare class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
        <h3 class="text-sm font-bold text-slate-900 dark:text-white">No inquiry records</h3>
        <p class="text-xs text-slate-500 mt-1">Lead conversations will appear here.</p>
      </div>

      <!-- Table View -->
      <div v-else-if="viewMode === 'table'" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="py-2.5 px-2.5 w-10 text-center">No</th>
                <th class="py-2.5 px-3">Property</th>
                <th class="py-2.5 px-3 whitespace-nowrap">Client</th>
                <th class="py-2.5 px-3 whitespace-nowrap">Last Activity</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
              <tr v-for="(inq, index) in filteredInquiries" :key="inq.id" class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                <td class="py-2.5 px-2.5 text-center font-bold text-slate-400 dark:text-slate-500">{{ index + 1 }}</td>
                <td class="py-2.5 px-3 font-bold text-slate-900 dark:text-white">{{ inq.property }}</td>
                <td class="py-2.5 px-3 font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ inq.buyer }}</td>
                <td class="py-2.5 px-3 text-slate-500 whitespace-nowrap">{{ inq.updated_at }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grid View -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="inq in filteredInquiries"
          :key="inq.id"
          class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-2"
        >
          <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ inq.property }}</h4>
          <p class="text-xs text-slate-600 dark:text-slate-300">From: <span class="font-bold">{{ inq.buyer }}</span></p>
          <p class="text-[11px] text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800">Updated: {{ inq.updated_at }}</p>
        </div>
      </div>
    </template>

    <!-- 4. FAVORITES TAB CONTENT -->
    <template v-else-if="activeTab === 'favorites'">
      <div v-if="filteredFavorites.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-12 text-center">
        <Heart class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
        <h3 class="text-sm font-bold text-slate-900 dark:text-white">No favorites records</h3>
        <p class="text-xs text-slate-500 mt-1">Properties favorited by interested buyers will appear here.</p>
      </div>

      <!-- Table View -->
      <div v-else-if="viewMode === 'table'" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="py-2.5 px-2.5 w-10 text-center">No</th>
                <th class="py-2.5 px-3">Property</th>
                <th class="py-2.5 px-3 whitespace-nowrap">Category</th>
                <th class="py-2.5 px-3 whitespace-nowrap">Price</th>
                <th class="py-2.5 px-3 whitespace-nowrap">Saved By Client</th>
                <th class="py-2.5 px-3 whitespace-nowrap">Contact</th>
                <th class="py-2.5 px-2.5 text-center whitespace-nowrap">Date Saved</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
              <tr v-for="(fav, index) in filteredFavorites" :key="fav.id" class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                <td class="py-2.5 px-2.5 text-center font-bold text-slate-400 dark:text-slate-500">{{ index + 1 }}</td>
                <td class="py-2.5 px-3 min-w-[200px]">
                  <div class="flex items-center gap-2.5">
                    <img :src="fav.property_image" class="w-10 h-10 rounded-lg object-cover bg-slate-100 shrink-0" />
                    <span class="font-bold text-slate-900 dark:text-white truncate max-w-xs">{{ fav.property }}</span>
                  </div>
                </td>
                <td class="py-2.5 px-3 font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ fav.property_type }}</td>
                <td class="py-2.5 px-3 font-black text-slate-900 dark:text-white whitespace-nowrap">ETB {{ Number(fav.property_price || 0).toLocaleString() }}</td>
                <td class="py-2.5 px-3 font-bold text-slate-900 dark:text-white whitespace-nowrap">{{ fav.user_name }}</td>
                <td class="py-2.5 px-3 text-slate-500 whitespace-nowrap">{{ fav.user_phone !== '—' ? fav.user_phone : fav.user_email }}</td>
                <td class="py-2.5 px-2.5 text-center font-medium text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ fav.saved_at }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grid View -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="fav in filteredFavorites"
          :key="fav.id"
          class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden flex flex-col justify-between"
        >
          <div class="relative h-44 bg-slate-100 dark:bg-slate-800">
            <img :src="fav.property_image" class="w-full h-full object-cover" />
            <div class="absolute top-3 right-3 bg-white/90 dark:bg-slate-900/90 p-1.5 rounded-full shadow-xs">
              <Heart class="w-4 h-4 text-rose-500 fill-rose-500" />
            </div>
            <div class="absolute bottom-3 left-3 right-3 text-white">
              <p class="text-sm font-black truncate drop-shadow-md">{{ fav.property }}</p>
              <p class="text-[11px] text-slate-200 truncate">{{ fav.property_type }}</p>
            </div>
          </div>
          <div class="p-4 space-y-2 text-xs">
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Price:</span>
              <span class="font-black text-slate-900 dark:text-white">ETB {{ Number(fav.property_price || 0).toLocaleString() }}</span>
            </div>
            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-1">
              <p class="text-slate-600 dark:text-slate-300"><span class="font-bold">Saved By:</span> {{ fav.user_name }}</p>
              <p class="text-slate-500"><span class="font-bold">Contact:</span> {{ fav.user_phone !== '—' ? fav.user_phone : fav.user_email }}</p>
              <p class="text-[11px] text-slate-400">Date: {{ fav.saved_at }}</p>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Download Confirmation Modal -->
    <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-sm p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
          <h3 class="text-sm font-bold text-slate-900 dark:text-white">Confirm Report Download</h3>
          <button @click="showConfirmModal = false" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-white cursor-pointer">
            <X class="w-5 h-5" />
          </button>
        </div>

        <p class="text-xs text-slate-600 dark:text-slate-300">
          Are you sure you want to download the <strong>{{ exportFormat.toUpperCase() }}</strong> report for <strong>{{ activeTabLabel }}</strong> ({{ timeRangeLabel }})?
        </p>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
          <button
            type="button"
            @click="showConfirmModal = false"
            class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl cursor-pointer transition-colors"
          >
            Cancel
          </button>
          <button
            type="button"
            :disabled="isExporting"
            @click="executeExport"
            class="px-5 py-2 text-xs font-bold bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl cursor-pointer shadow-xs transition-all disabled:opacity-50"
          >
            {{ isExporting ? 'Downloading...' : 'Confirm' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import {
  Search,
  Eye,
  Calendar,
  Building2,
  MessageSquare,
  Heart,
  FileSpreadsheet,
  FileText,
  List,
  LayoutGrid,
  X
} from 'lucide-vue-next'
import { reportService } from '@/services/reportService'
import { useToastStore } from '@/stores/toast'
import { useAuthStore } from '@/stores/auth'

const toastStore = useToastStore()
const authStore = useAuthStore()

const isLoading = ref(true)
const isExporting = ref(false)
const searchQuery = ref('')
const selectedTimeRange = ref('all')
const selectedPropertyType = ref('all')
const activeTab = ref('properties')
const viewMode = ref('table')

const showConfirmModal = ref(false)
const exportFormat = ref('excel')

const reportData = reactive({
  summary: {
    totalProperties: 0,
    activeListings: 0,
    totalViews: 0,
    totalFavorites: 0,
    totalAppointments: 0,
    totalInquiries: 0,
    inquiryRate: 0
  },
  propertyBreakdown: [],
  appointmentsList: [],
  inquiriesList: [],
  favoritesList: []
})

const availablePropertyTypes = computed(() => {
  const types = new Set()
  reportData.propertyBreakdown.forEach(p => {
    if (p.type) types.add(p.type)
  })
  if (types.size === 0) {
    return ['Apartment', 'Commercial', 'Condominium', 'House', 'Office', 'Villa']
  }
  return Array.from(types).sort()
})

const activeTabLabel = computed(() => {
  const map = {
    properties: selectedPropertyType.value !== 'all' ? `${selectedPropertyType.value}` : 'All',
    appointments: 'Bookings',
    inquiries: 'Inquiries',
    favorites: 'Favorites'
  }
  return map[activeTab.value] || 'All'
})

const timeRangeLabel = computed(() => {
  const map = {
    today: 'Today',
    week: 'Week',
    month: 'Month',
    year: 'Year',
    all: 'All'
  }
  return map[selectedTimeRange.value] || 'All'
})

async function loadReport() {
  isLoading.value = true
  try {
    const res = await reportService.getOwnerReport({ time_range: selectedTimeRange.value })
    const payload = res?.data || {}

    if (payload.summary) {
      Object.assign(reportData.summary, payload.summary)
    }
    reportData.propertyBreakdown = payload.propertyBreakdown || []
    reportData.appointmentsList = payload.appointmentsList || []
    reportData.inquiriesList = payload.inquiriesList || []

    const currentUserName = authStore.user?.name?.toLowerCase().trim() || ''
    const currentUserId = authStore.user?.id
    reportData.favoritesList = (payload.favoritesList || []).filter(f => {
      if (currentUserId && f.user_id && String(f.user_id) === String(currentUserId)) return false
      if (currentUserName && f.user_name?.toLowerCase().trim() === currentUserName) return false
      return true
    })
  } catch (err) {
    console.error('Failed to load owner report:', err)
  } finally {
    isLoading.value = false
  }
}

const filteredProperties = computed(() => {
  let list = reportData.propertyBreakdown

  if (selectedPropertyType.value !== 'all') {
    list = list.filter(p => p.type?.toLowerCase() === selectedPropertyType.value.toLowerCase())
  }

  if (!searchQuery.value) return list
  const q = searchQuery.value.toLowerCase()
  return list.filter(p =>
    p.title?.toLowerCase().includes(q) ||
    p.type?.toLowerCase().includes(q) ||
    p.location?.toLowerCase().includes(q)
  )
})

const filteredAppointments = computed(() => {
  if (!searchQuery.value) return reportData.appointmentsList
  const q = searchQuery.value.toLowerCase()
  return reportData.appointmentsList.filter(a =>
    a.property?.toLowerCase().includes(q) ||
    a.visitor?.toLowerCase().includes(q)
  )
})

const filteredInquiries = computed(() => {
  if (!searchQuery.value) return reportData.inquiriesList
  const q = searchQuery.value.toLowerCase()
  return reportData.inquiriesList.filter(i =>
    i.property?.toLowerCase().includes(q) ||
    i.buyer?.toLowerCase().includes(q)
  )
})

const filteredFavorites = computed(() => {
  if (!searchQuery.value) return reportData.favoritesList
  const q = searchQuery.value.toLowerCase()
  return reportData.favoritesList.filter(f =>
    f.property?.toLowerCase().includes(q) ||
    f.user_name?.toLowerCase().includes(q) ||
    f.property_type?.toLowerCase().includes(q) ||
    f.user_email?.toLowerCase().includes(q)
  )
})

function promptExport(format) {
  exportFormat.value = format
  showConfirmModal.value = true
}

async function executeExport() {
  isExporting.value = true
  try {
    if (exportFormat.value === 'excel') {
      await reportService.exportOwnerReportExcel({
        category: activeTab.value,
        property_type: selectedPropertyType.value,
        time_range: selectedTimeRange.value
      })
      toastStore.success(`Excel report downloaded successfully.`)
    } else {
      await reportService.exportOwnerReportPdf({
        category: activeTab.value,
        property_type: selectedPropertyType.value,
        time_range: selectedTimeRange.value
      })
      toastStore.success(`PDF report downloaded successfully.`)
    }
    showConfirmModal.value = false
  } catch (err) {
    console.error('Export failed:', err)
    toastStore.error('Failed to download report. Please try again.')
  } finally {
    isExporting.value = false
  }
}

onMounted(() => {
  loadReport()
})
</script>
