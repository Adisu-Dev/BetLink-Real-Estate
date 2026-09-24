<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
          {{ t('appointments', 'Property Viewings & Appointments') }}
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">
          Track, schedule, and moderate verified property tours between buyers, owners, and agents.
        </p>
      </div>

      <!-- Book New Viewing Action (For Buyers / Clients only - Hidden for Admin) -->
      <button
        v-if="currentUserRole !== 'admin'"
        type="button"
        @click="openBookModal(route.query.property_id)"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 dark:text-slate-900 text-white rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-all active:scale-95 cursor-pointer self-start sm:self-auto"
      >
        <CalendarPlus class="w-4 h-4" />
        <span>Book New Viewing</span>
      </button>
    </div>

    <!-- Status Filter & Search Toolbar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 transition-colors">
      <!-- Search Input (Filter by Property, Client name, Host name, Location) -->
      <div class="relative flex-1 max-w-md">
        <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by property, client, host, or location..."
          class="w-full pl-9 pr-9 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 transition-colors"
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

      <!-- Controls: Status Dropdown, Sort Dropdown, View Toggle & Refresh -->
      <div class="flex flex-wrap items-center gap-2">
        <!-- Status Filter Dropdown -->
        <select
          v-model="selectedStatus"
          class="px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 cursor-pointer shadow-xs"
        >
          <option v-for="tab in statusTabs" :key="tab.value" :value="tab.value">
            {{ tab.label }} ({{ getTabCount(tab.value) }})
          </option>
        </select>

        <!-- Sort Dropdown -->
        <select
          v-model="sortBy"
          class="px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 cursor-pointer shadow-xs"
        >
          <option value="latest">Latest</option>
          <option value="oldest">Oldest</option>
          <option value="newest">Newest</option>
        </select>

        <!-- View Mode Switcher (Table vs Grid) -->
        <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-0.5 rounded-xl border border-slate-200 dark:border-slate-700">
          <button
            type="button"
            @click="viewMode = 'table'"
            :class="[
              'p-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1',
              viewMode === 'table'
                ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs'
                : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'
            ]"
            title="Table View (Data-Dense)"
          >
            <List class="w-4 h-4" />
            <span class="hidden md:inline pr-1">Table</span>
          </button>
          <button
            type="button"
            @click="viewMode = 'grid'"
            :class="[
              'p-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1',
              viewMode === 'grid'
                ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs'
                : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'
            ]"
            title="Card Grid View"
          >
            <LayoutGrid class="w-4 h-4" />
            <span class="hidden md:inline pr-1">Cards</span>
          </button>
        </div>

        <!-- Refresh Action Button -->
        <button
          @click="loadAppointments"
          class="p-2 text-slate-500 hover:text-slate-900 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          title="Refresh List"
        >
          <RotateCcw class="w-4 h-4" />
        </button>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="space-y-4">
      <div v-if="viewMode === 'table'" class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4 animate-pulse">
        <div class="h-8 bg-slate-200 dark:bg-slate-800 rounded w-full"></div>
        <div class="h-12 bg-slate-100 dark:bg-slate-800/60 rounded w-full"></div>
        <div class="h-12 bg-slate-100 dark:bg-slate-800/60 rounded w-full"></div>
        <div class="h-12 bg-slate-100 dark:bg-slate-800/60 rounded w-full"></div>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="n in 3" :key="n" class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs animate-pulse space-y-4">
          <div class="h-48 bg-slate-200 dark:bg-slate-800 rounded-xl"></div>
          <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-3/4"></div>
          <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-1/2"></div>
        </div>
      </div>
    </div>

    <!-- 1. Appointments Table View (Comprehensive, Data-Dense for Admin & Oversight) -->
    <div
      v-else-if="filteredAppointments.length > 0 && viewMode === 'table'"
      class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 overflow-hidden"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
          <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200/80 dark:border-slate-800">
            <tr>
              <th class="px-3.5 py-3 text-center text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-12">No</th>
              <th class="px-4 py-3 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Property</th>
              <th class="px-3.5 py-3 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Client (Visitor)</th>
              <th class="px-3.5 py-3 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Host (Owner / Agent)</th>
              <th class="px-3.5 py-3 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Scheduled Tour</th>
              <th class="px-3 py-3 text-center text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Format</th>
              <th class="px-3 py-3 text-center text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
              <th class="px-4 py-3 text-right text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr
              v-for="(appt, index) in filteredAppointments"
              :key="appt.id"
              class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors"
            >
              <!-- Index / Record # (Sequential numbering 1, 2, 3...) -->
              <td class="px-3.5 py-3 whitespace-nowrap text-center text-xs font-mono font-bold text-slate-400 dark:text-slate-500">
                {{ index + 1 }}
              </td>

              <!-- Property Column -->
              <td class="px-4 py-3">
                <div class="flex items-center gap-2.5">
                  <div
                    class="relative w-12 h-10 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 shrink-0 border border-slate-200 dark:border-slate-700 cursor-pointer group"
                    @click="openDetailModal(appt)"
                    title="Click to view appointment details"
                  >
                    <img
                      :src="getAppointmentImage(appt, index)"
                      :alt="getApptPropertyTitle(appt)"
                      class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                    />
                  </div>
                  <div class="max-w-[190px] min-w-0">
                    <p
                      class="font-bold text-slate-900 dark:text-white text-xs truncate hover:underline cursor-pointer"
                      :title="getApptPropertyTitle(appt)"
                      @click="openDetailModal(appt)"
                    >
                      {{ getApptPropertyTitle(appt) }}
                    </p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium truncate flex items-center gap-1 mt-0.5">
                      <MapPin class="w-3 h-3 text-slate-400 shrink-0" />
                      <span class="truncate">{{ getApptLocation(appt) }}</span>
                    </p>
                  </div>
                </div>
              </td>

              <!-- Client (Visitor) Column -->
              <td class="px-3.5 py-3 whitespace-nowrap">
                <div class="space-y-0.5">
                  <p class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                    <User class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                    <span>{{ getApptVisitorName(appt) }}</span>
                  </p>
                  <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">
                    {{ getApptVisitorPhone(appt) }}
                  </p>
                  <span class="inline-block text-[9px] font-semibold px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                    {{ getApptVisitorRole(appt) }}
                  </span>
                </div>
              </td>

              <!-- Host (Owner / Agent) Column -->
              <td class="px-3.5 py-3 whitespace-nowrap">
                <div class="space-y-0.5">
                  <p class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                    <Key class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                    <span>{{ getApptOwnerName(appt) }}</span>
                  </p>
                  <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">
                    {{ getApptOwnerPhone(appt) }}
                  </p>
                  <span class="inline-block text-[9px] font-semibold px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                    Host
                  </span>
                </div>
              </td>

              <!-- Scheduled Tour Date & Time -->
              <td class="px-3.5 py-3 whitespace-nowrap">
                <div class="space-y-0.5">
                  <p class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                    <Clock class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                    <span>{{ formatDateTime(appt.scheduled_at) }}</span>
                  </p>
                  <span
                    :class="[
                      'inline-flex items-center text-[9px] font-bold px-1.5 py-0.2 rounded-full',
                      isTourPast(appt.scheduled_at)
                        ? 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'
                        : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400'
                    ]"
                  >
                    {{ isTourPast(appt.scheduled_at) ? 'Passed' : 'Upcoming' }}
                  </span>
                </div>
              </td>

              <!-- Format (In-Person vs Virtual) -->
              <td class="px-3 py-3 whitespace-nowrap text-center">
                <span
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-medium border bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700"
                >
                  <MapPin class="w-3 h-3 text-slate-400" />
                  <span>{{ appt.type === 'virtual' ? 'Virtual' : 'In-Person' }}</span>
                </span>
              </td>

              <!-- Status Badge (Unified with AdminPropertiesPage) -->
              <td class="px-3 py-3 whitespace-nowrap text-center">
                <StatusBadge :status="appt.status ? appt.status.toLowerCase() : 'confirmed'" />
              </td>

              <!-- Actions 3-Dot Dropdown (Details is accessed inside the 3-dots menu) -->
              <td class="px-4 py-3 whitespace-nowrap text-right text-xs">
                <button
                  type="button"
                  @click.stop="toggleActionMenu(appt, $event)"
                  class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer border border-transparent hover:border-slate-200 dark:hover:border-slate-700"
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

    <!-- 2. Appointments Cards Grid (Available when toggled to 'grid' view) -->
    <div v-else-if="filteredAppointments.length > 0 && viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="(appt, index) in filteredAppointments"
        :key="appt.id"
        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-xl hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300 overflow-hidden flex flex-col justify-between group"
      >
        <!-- Card Top: High Quality Property Thumbnail & Badges -->
        <div class="relative h-48 sm:h-52 overflow-hidden bg-slate-100 dark:bg-slate-800">
          <img
            :src="getAppointmentImage(appt, index)"
            :alt="getApptPropertyTitle(appt)"
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 cursor-pointer"
            @click="openDetailModal(appt)"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/20 to-black/30 pointer-events-none"></div>

          <!-- Top-Left Status & Format Pills -->
          <div class="absolute top-3 left-3 flex items-center gap-1.5 z-10">
            <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-lg shadow-xs backdrop-blur-md bg-slate-900/80 text-slate-200 border border-slate-700/60">
              {{ appt.status }}
            </span>

            <span class="px-2 py-1 text-[10px] font-bold rounded-lg bg-slate-900/80 text-slate-200 border border-slate-700/60 backdrop-blur-md">
              {{ appt.type === 'virtual' ? 'Virtual' : 'In-Person' }}
            </span>
          </div>

          <!-- Top-Right Three-Dot Menu Button -->
          <div class="absolute top-3 right-3 z-10">
            <button
              type="button"
              @click.stop="toggleActionMenu(appt, $event)"
              class="w-8 h-8 rounded-full bg-slate-900/70 hover:bg-slate-900/90 text-white backdrop-blur-md flex items-center justify-center transition-all shadow-md cursor-pointer border border-white/20"
              title="More Actions"
            >
              <MoreVertical class="w-4 h-4" />
            </button>
          </div>

          <!-- Bottom Schedule Info Overlay on Image -->
          <div class="absolute bottom-3 left-3 right-3 text-white flex items-end justify-between pointer-events-none">
            <div class="min-w-0 pr-2">
              <p class="text-[11px] font-bold text-slate-300 flex items-center gap-1">
                <Clock class="w-3.5 h-3.5" />
                {{ formatDateTime(appt.scheduled_at) }}
              </p>
              <p class="text-sm font-black text-white truncate drop-shadow-sm mt-0.5">
                {{ getApptPropertyTitle(appt) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Clean Card Body (Minimal text, only Location & Quick Host line) -->
        <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between space-y-4">
          <div class="space-y-2">
            <!-- Location Line -->
            <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5 truncate font-medium">
              <MapPin class="w-3.5 h-3.5 text-slate-400 shrink-0" />
              <span class="truncate">{{ getApptLocation(appt) }}</span>
            </p>

            <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-300 pt-1">
              <span class="truncate font-semibold">Host: {{ getApptOwnerName(appt) }}</span>
              <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Client: {{ getApptVisitorName(appt) }}</span>
            </div>
          </div>

          <!-- Actions: Details + Role-aware Actions (Equal 2-column Grid) -->
          <div class="pt-3 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2">
            <button
              type="button"
              @click="openDetailModal(appt)"
              class="w-full h-9 px-3 rounded-xl bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition-all active:scale-98 cursor-pointer flex items-center justify-center gap-1.5 border border-slate-200 dark:border-slate-700 shadow-2xs"
            >
              <Eye class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" />
              <span>Details</span>
            </button>

            <!-- Manager/Host Direct Approve Button -->
            <template v-if="canManageAppt(appt) && appt.status === 'pending'">
              <button
                type="button"
                @click="approveAppt(appt)"
                class="w-full h-9 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition-all active:scale-98 cursor-pointer flex items-center justify-center gap-1.5 shadow-xs"
                title="Confirm and approve client tour"
              >
                <CheckCircle class="w-3.5 h-3.5" />
                <span>Approve</span>
              </button>
            </template>

            <!-- Manager/Host Direct Complete Button -->
            <template v-else-if="canManageAppt(appt) && appt.status === 'confirmed'">
              <button
                v-if="isTourPast(appt.scheduled_at) || currentUserRole === 'admin'"
                type="button"
                @click="markApptCompleted(appt)"
                class="w-full h-9 px-3 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-900 dark:text-white border border-slate-300 dark:border-slate-600 text-xs font-bold transition-all active:scale-98 cursor-pointer flex items-center justify-center gap-1.5 shadow-xs"
                title="Tour date has arrived or Admin completion. Click to mark as completed."
              >
                <CheckCircle class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                <span>Complete</span>
              </button>
              <button
                v-else
                type="button"
                disabled
                class="w-full h-9 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 text-xs font-medium cursor-not-allowed flex items-center justify-center gap-1.5 border border-slate-200/60 dark:border-slate-700/60 opacity-70"
                title="The appointment date has not arrived yet. You can complete it once the viewing time arrives."
              >
                <Clock class="w-3.5 h-3.5 text-slate-400" />
                <span>Upcoming</span>
              </button>
            </template>

            <!-- Manager/Host when Tour is Completed -->
            <template v-else-if="canManageAppt(appt) && appt.status === 'completed'">
              <div
                class="w-full h-9 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-semibold flex items-center justify-center gap-1.5 border border-slate-200/80 dark:border-slate-700/80"
              >
                <CheckCircle class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" />
                <span>Completed</span>
              </div>
            </template>

            <!-- Manager/Host when Tour is Cancelled -->
            <template v-else-if="canManageAppt(appt) && appt.status === 'cancelled'">
              <div
                class="w-full h-9 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-semibold flex items-center justify-center gap-1.5 border border-slate-200/80 dark:border-slate-700/80"
              >
                <XCircle class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" />
                <span>Cancelled</span>
              </div>
            </template>

            <!-- General Client / Reschedule Buttons -->
            <template v-else>
              <button
                v-if="appt.status === 'pending' || appt.status === 'confirmed'"
                type="button"
                @click="openRescheduleModal(appt)"
                class="w-full h-9 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition-all active:scale-98 cursor-pointer flex items-center justify-center gap-1.5 border border-slate-200 dark:border-slate-700 shadow-2xs"
              >
                <Calendar class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" />
                <span>Reschedule</span>
              </button>
              <button
                v-else
                type="button"
                @click="openBookModal(appt.property_id || appt.property?.id)"
                class="w-full h-9 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition-all active:scale-98 cursor-pointer flex items-center justify-center gap-1.5 border border-slate-200 dark:border-slate-700 shadow-2xs"
              >
                <RotateCcw class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" />
                <span>Rebook</span>
              </button>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-300 dark:border-slate-800 p-8 sm:p-12 text-center">
      <EmptyState
        :title="searchQuery ? `No viewing appointments matching &quot;${searchQuery}&quot;` : 'No viewing appointments found'"
        :description="searchQuery ? 'No appointments match your search criteria. Please verify spelling or try another keyword.' : 'There are currently no scheduled appointments matching this filter.'"
        :actionLabel="searchQuery ? 'Clear Search' : (currentUserRole !== 'admin' ? 'Book a Viewing' : '')"
        @action="searchQuery ? (searchQuery = '') : openBookModal(route.query.property_id)"
      />
    </div>

    <!-- Floating Teleported Three-Dot Action Dropdown Menu -->
    <Teleport to="body">
      <div v-if="activeMenu" class="fixed inset-0 z-[9998]" @click="activeMenu = null">
        <div
          :style="{ top: activeMenu.top, left: activeMenu.left }"
          class="fixed w-48 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 py-1.5 z-[9999] animate-in fade-in zoom-in-95 text-left"
          @click.stop
        >
          <!-- View Details Option -->
          <button
            type="button"
            @click="handleMenuAction('details')"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <Eye class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" />
            <span>View Details</span>
          </button>

          <!-- Approve Option (for Owner / Agent / Admin on Pending) -->
          <button
            v-if="canManageAppt(activeMenu.appt) && activeMenu.appt.status === 'pending'"
            type="button"
            @click="handleMenuAction('approve')"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <CheckCircle class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" />
            <span>Approve</span>
          </button>

          <!-- Complete Option (for Owner / Agent / Admin on Confirmed) -->
          <button
            v-if="canManageAppt(activeMenu.appt) && activeMenu.appt.status === 'confirmed' && (isTourPast(activeMenu.appt.scheduled_at) || currentUserRole === 'admin')"
            type="button"
            @click="handleMenuAction('complete')"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <CheckCircle class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
            <span>Complete</span>
          </button>

          <!-- Reschedule Option -->
          <button
            v-if="activeMenu.appt.status === 'pending' || activeMenu.appt.status === 'confirmed'"
            type="button"
            @click="handleMenuAction('reschedule')"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <Calendar class="w-3.5 h-3.5 text-slate-500" />
            <span>Reschedule</span>
          </button>

          <!-- Message Option -->
          <button
            type="button"
            @click="handleMenuAction('message')"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <MessageSquare class="w-3.5 h-3.5 text-slate-500" />
            <span>Message</span>
          </button>

          <!-- Inspect Property Option -->
          <button
            v-if="activeMenu.appt.property_id || activeMenu.appt.property?.id"
            type="button"
            @click="handleMenuAction('property')"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <ExternalLink class="w-3.5 h-3.5 text-slate-500" />
            <span>Property</span>
          </button>

          <!-- Cancel Option (For Active Bookings) -->
          <button
            v-if="activeMenu.appt.status === 'pending' || activeMenu.appt.status === 'confirmed'"
            type="button"
            @click="handleMenuAction('cancel')"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer border-t border-slate-100 dark:border-slate-800 mt-1 pt-1.5"
          >
            <XCircle class="w-3.5 h-3.5" />
            <span>Cancel</span>
          </button>

          <!-- Delete Record Option -->
          <button
            type="button"
            @click="handleMenuAction('delete')"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer border-t border-slate-100 dark:border-slate-800 mt-1 pt-1.5"
          >
            <Trash2 class="w-3.5 h-3.5" />
            <span>Delete</span>
          </button>
        </div>
      </div>
    </Teleport>

    <!-- 1. Full Viewing Details Modal -->
    <Teleport to="body">
      <div v-if="showDetailModal && selectedAppt" class="fixed inset-0 z-[100000] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-150 space-y-4 p-6">
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
              <span class="text-[10px] font-black uppercase text-slate-400">Appointment Record #{{ selectedAppt.id }}</span>
              <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Tour Appointment Details</h3>
            </div>
            <button @click="showDetailModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
              <X class="w-5 h-5" />
            </button>
          </div>

          <div class="space-y-3.5 max-h-[70vh] overflow-y-auto pr-1">
            <!-- Property Box -->
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200/60 dark:border-slate-700/60 flex items-center gap-3">
              <img :src="getAppointmentImage(selectedAppt)" class="w-14 h-14 rounded-xl object-cover" />
              <div class="min-w-0 flex-1">
                <p class="text-xs font-black text-slate-900 dark:text-white truncate">{{ getApptPropertyTitle(selectedAppt) }}</p>
                <p class="text-[11px] text-slate-500 truncate">{{ getApptLocation(selectedAppt) }}</p>
                <RouterLink
                  v-if="selectedAppt.property_id || selectedAppt.property?.id"
                  :to="`/properties/${selectedAppt.property_id || selectedAppt.property?.id}`"
                  target="_blank"
                  class="text-[11px] font-bold text-slate-900 dark:text-white hover:underline mt-0.5 inline-block"
                >
                  Inspect Property Listing →
                </RouterLink>
              </div>
            </div>

            <!-- Schedule & Format Box -->
            <div class="grid grid-cols-2 gap-3">
              <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Date & Time</span>
                <p class="text-xs font-bold text-slate-900 dark:text-white mt-0.5">{{ formatDateTime(selectedAppt.scheduled_at) }}</p>
              </div>
              <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Tour Format</span>
                <p class="text-xs font-bold text-slate-900 dark:text-white mt-0.5">{{ selectedAppt.type === 'virtual' ? 'Virtual' : 'In-Person' }}</p>
              </div>
            </div>

            <!-- Visitor (Buyer) Information -->
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-800 space-y-2">
              <span class="text-[10px] font-black uppercase text-slate-400 block">👤 Client / Visitor Information</span>
              <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                  <span class="text-slate-400 text-[11px]">Full Name:</span>
                  <p class="font-bold text-slate-900 dark:text-white">{{ getApptVisitorName(selectedAppt) }}</p>
                </div>
                <div>
                  <span class="text-slate-400 text-[11px]">Phone / Contact:</span>
                  <p class="font-bold text-slate-900 dark:text-white">{{ getApptVisitorPhone(selectedAppt) }}</p>
                </div>
                <div v-if="selectedAppt.visitor?.email" class="col-span-2">
                  <span class="text-slate-400 text-[11px]">Email Address:</span>
                  <p class="font-bold text-slate-900 dark:text-white">{{ selectedAppt.visitor?.email }}</p>
                </div>
              </div>
            </div>

            <!-- Host (Owner / Agent) Information -->
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-800 space-y-2">
              <span class="text-[10px] font-black uppercase text-slate-400 block">🔑 Host / Landlord Information</span>
              <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                  <span class="text-slate-400 text-[11px]">Host Name:</span>
                  <p class="font-bold text-slate-900 dark:text-white">{{ getApptOwnerName(selectedAppt) }}</p>
                </div>
                <div>
                  <span class="text-slate-400 text-[11px]">Phone:</span>
                  <p class="font-bold text-slate-900 dark:text-white">{{ getApptOwnerPhone(selectedAppt) }}</p>
                </div>
              </div>
            </div>

            <!-- Special Note / Client Request -->
            <div v-if="selectedAppt.message || selectedAppt.note" class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
              <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Special Client Instructions</span>
              <p class="text-xs text-slate-700 dark:text-slate-300 italic">"{{ selectedAppt.message || selectedAppt.note }}"</p>
            </div>
          </div>

          <div class="flex items-center justify-between gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
            <!-- Left side: Status Indicator / Cancel Tour -->
            <div class="flex items-center gap-2">
              <button
                v-if="selectedAppt.status === 'pending' || selectedAppt.status === 'confirmed'"
                @click="promptCancelModal(selectedAppt); showDetailModal = false"
                class="px-4 py-2 text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80 rounded-xl transition-colors cursor-pointer"
              >
                {{ selectedAppt.status === 'pending' ? 'Decline' : 'Cancel Tour' }}
              </button>
            </div>

            <!-- Right side: Approve / Complete / Close actions with uniform sizes -->
            <div class="flex items-center gap-2">
              <!-- Manager Approve Action -->
              <button
                v-if="canManageAppt(selectedAppt) && selectedAppt.status === 'pending'"
                @click="approveAppt(selectedAppt); showDetailModal = false"
                class="px-4 py-2 text-xs font-bold bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl cursor-pointer flex items-center gap-1.5 shadow-xs transition-colors"
              >
                <CheckCircle class="w-3.5 h-3.5" />
                <span>Approve</span>
              </button>

              <!-- Manager Mark Completed Action: Shown when confirmed and tour date has arrived/passed -->
              <button
                v-else-if="canManageAppt(selectedAppt) && selectedAppt.status === 'confirmed' && isTourPast(selectedAppt.scheduled_at)"
                @click="markApptCompleted(selectedAppt); showDetailModal = false"
                class="px-4 py-2 text-xs font-bold bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-900 dark:text-white border border-slate-300 dark:border-slate-600 rounded-xl cursor-pointer flex items-center gap-1.5 shadow-xs transition-colors"
              >
                <CheckCircle class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                <span>Complete</span>
              </button>

              <!-- When confirmed but tour is in the future -->
              <span
                v-else-if="selectedAppt.status === 'confirmed'"
                class="px-3 py-2 text-xs font-bold rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80 uppercase tracking-wider"
              >
                Confirmed
              </span>

              <!-- Close Modal -->
              <button
                @click="showDetailModal = false"
                class="px-4 py-2 text-xs font-bold bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-900 dark:text-white border border-slate-300 dark:border-slate-600 rounded-xl transition-colors cursor-pointer shadow-xs"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- 2. Book New Viewing Modal -->
    <Teleport to="body">
      <div v-if="showBookModal" class="fixed inset-0 z-[100000] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-150">
          <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <CalendarPlus class="w-5 h-5 text-slate-700 dark:text-slate-300" />
              Book Property Tour
            </h3>
            <button @click="showBookModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
              <X class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="submitBookAppointment" class="p-6 space-y-4">
            <!-- Property Selector -->
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                Select Property ({{ availableProperties.length }} available) *
              </label>
              <select
                v-model="bookingForm.property_id"
                required
                :class="[
                  'w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border text-slate-900 dark:text-white text-xs sm:text-sm rounded-xl focus:outline-none transition-colors cursor-pointer',
                  bookErrors.property_id 
                    ? 'border-red-500 focus:ring-2 focus:ring-red-500' 
                    : 'border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400'
                ]"
              >
                <option value="" disabled>Choose a property...</option>
                <option v-for="prop in availableProperties" :key="prop.id" :value="prop.id">
                  {{ prop.title }} — {{ prop.location }} (ETB {{ Number(prop.price || 0).toLocaleString() }})
                </option>
              </select>
              <p v-if="bookErrors.property_id" class="mt-1 text-xs text-red-500 font-medium">{{ bookErrors.property_id }}</p>
            </div>

            <!-- Date and Time with Validation -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                  Preferred Date *
                </label>
                <input
                  type="date"
                  v-model="bookingForm.date"
                  :min="minBookingDate"
                  required
                  :class="[
                    'w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border text-slate-900 dark:text-white text-xs rounded-xl focus:outline-none transition-colors',
                    bookErrors.date 
                      ? 'border-red-500 focus:ring-2 focus:ring-red-500' 
                      : 'border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400'
                  ]"
                />
                <p v-if="bookErrors.date" class="mt-1 text-xs text-red-500 font-medium">{{ bookErrors.date }}</p>
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                  Preferred Time *
                </label>
                <input
                  type="time"
                  v-model="bookingForm.time"
                  min="08:00"
                  max="18:00"
                  required
                  :class="[
                    'w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border text-slate-900 dark:text-white text-xs rounded-xl focus:outline-none transition-colors',
                    bookErrors.time 
                      ? 'border-red-500 focus:ring-2 focus:ring-red-500' 
                      : 'border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400'
                  ]"
                />
                <p v-if="bookErrors.time" class="mt-1 text-xs text-red-500 font-medium">{{ bookErrors.time }}</p>
              </div>
            </div>

            <!-- Viewing Type -->
            <!-- Viewing Type -->
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                Tour Format *
              </label>
              <div class="grid grid-cols-2 gap-3">
                <label
                  :class="[
                    'p-3 rounded-xl border flex items-center justify-center gap-2 cursor-pointer transition-all text-xs font-bold shadow-xs',
                    bookingForm.viewing_type === 'in_person'
                      ? 'bg-slate-300 text-slate-900 border-slate-400 ring-2 ring-slate-400/50 dark:bg-slate-700 dark:text-white dark:border-slate-500'
                      : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 dark:border-slate-700'
                  ]"
                >
                  <input type="radio" v-model="bookingForm.viewing_type" value="in_person" class="sr-only" />
                  <span>In-Person Tour</span>
                </label>

                <label
                  :class="[
                    'p-3 rounded-xl border flex items-center justify-center gap-2 cursor-pointer transition-all text-xs font-bold shadow-xs',
                    bookingForm.viewing_type === 'virtual'
                      ? 'bg-slate-300 text-slate-900 border-slate-400 ring-2 ring-slate-400/50 dark:bg-slate-700 dark:text-white dark:border-slate-500'
                      : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 dark:border-slate-700'
                  ]"
                >
                  <input type="radio" v-model="bookingForm.viewing_type" value="virtual" class="sr-only" />
                  <span>Virtual Video Tour</span>
                </label>
              </div>
              <p v-if="bookErrors.viewing_type" class="mt-1 text-xs text-red-500 font-medium">{{ bookErrors.viewing_type }}</p>
            </div>

            <!-- Notes -->
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">
                  Message or Special Request
                </label>
                <span class="text-[11px] text-slate-400">{{ (bookingForm.note || '').length }}/500</span>
              </div>
              <textarea
                v-model="bookingForm.note"
                maxlength="500"
                rows="2"
                placeholder="e.g. Please bring keys to rooftop and parking area."
                :class="[
                  'w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border text-slate-900 dark:text-white text-xs rounded-xl focus:outline-none transition-colors',
                  bookErrors.note
                    ? 'border-red-500 focus:ring-2 focus:ring-red-500'
                    : 'border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400'
                ]"
              ></textarea>
              <p v-if="bookErrors.note" class="mt-1 text-xs text-red-500 font-medium">{{ bookErrors.note }}</p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
              <button
                type="button"
                @click="showBookModal = false"
                class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 rounded-xl cursor-pointer transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="px-5 py-2 text-xs font-bold bg-slate-300 hover:bg-slate-400 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-900 dark:text-white border border-slate-400 dark:border-slate-500 rounded-xl shadow-xs transition-all cursor-pointer active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ isSubmitting ? 'Booking...' : 'Book' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- 3. Reschedule Modal -->
    <Teleport to="body">
      <div v-if="showRescheduleModal" class="fixed inset-0 z-[100000] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xl w-full max-w-md p-6 space-y-4 animate-in fade-in zoom-in-95">
          <div class="flex items-start justify-between">
            <div>
              <h3 class="text-base font-bold text-slate-900 dark:text-white">Reschedule Viewing</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pick a new date and time for "{{ getApptPropertyTitle(selectedAppt) }}":</p>
            </div>
            <button
              type="button"
              @click="showRescheduleModal = false"
              class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            >
              <X class="w-4 h-4" />
            </button>
          </div>

          <!-- Current Schedule Display -->
          <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200/70 dark:border-slate-700/70 flex items-center justify-between text-xs">
            <span class="text-slate-500 dark:text-slate-400 font-medium">Current Schedule</span>
            <span class="font-bold text-slate-800 dark:text-slate-200">{{ formatApptDate(selectedAppt?.scheduled_at) }}</span>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">New Date</label>
              <input
                type="date"
                v-model="rescheduleForm.date"
                :min="minBookingDate"
                required
                class="w-full px-3 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium text-xs rounded-xl focus:ring-2 focus:ring-slate-900 dark:focus:ring-white focus:outline-none transition-all"
              />
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">New Time</label>
              <input
                type="time"
                v-model="rescheduleForm.time"
                min="08:00"
                max="18:00"
                required
                class="w-full px-3 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium text-xs rounded-xl focus:ring-2 focus:ring-slate-900 dark:focus:ring-white focus:outline-none transition-all"
              />
            </div>
          </div>

          <!-- Reschedule Reason Input Field -->
          <div class="space-y-1.5">
            <div class="flex items-center justify-between">
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                Reschedule Reason <span class="text-rose-500">*</span>
              </label>
              <span class="text-[10px] text-slate-400 font-medium">{{ (rescheduleForm.reason || '').length }}/300</span>
            </div>
            <textarea
              v-model="rescheduleForm.reason"
              maxlength="300"
              rows="3"
              placeholder="e.g. Landlord is unavailable, moving viewing to next Monday at 10:00 AM."
              :class="[
                'w-full px-3 py-2 bg-white dark:bg-slate-800 border text-slate-900 dark:text-white font-medium text-xs rounded-xl focus:outline-none transition-all placeholder:text-slate-400',
                rescheduleError && (!rescheduleForm.reason || rescheduleForm.reason.trim().length < 5)
                  ? 'border-rose-500 focus:ring-2 focus:ring-rose-500'
                  : 'border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-slate-900 dark:focus:ring-white'
              ]"
            ></textarea>
            <p class="text-[10px] text-slate-400 dark:text-slate-500">Please provide a clear written explanation (minimum 5 characters, with words).</p>
          </div>

          <!-- Unchanged Notice -->
          <div v-if="isRescheduleUnchanged" class="text-[11px] text-amber-600 dark:text-amber-400 font-medium flex items-center gap-1.5">
            <span>&bull;</span>
            <span>Please select a new date or time different from the current schedule.</span>
          </div>

          <div v-if="rescheduleError" class="text-[11px] text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-1.5">
            <span>&bull;</span>
            <span>{{ rescheduleError }}</span>
          </div>

          <div class="flex justify-end gap-2.5 pt-2 border-t border-slate-100 dark:border-slate-800">
            <button
              type="button"
              @click="showRescheduleModal = false"
              class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer"
            >
              Cancel
            </button>
            <button
              type="button"
              @click="submitReschedule"
              :disabled="isRescheduleUnchanged || isRescheduleInPast || isRescheduling"
              class="px-5 py-2 text-xs font-bold bg-slate-300 hover:bg-slate-400 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-900 dark:text-white border border-slate-400 dark:border-slate-500 rounded-xl shadow-xs transition-all cursor-pointer active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed disabled:active:scale-100"
            >
              {{ isRescheduling ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- 4. Cancel Modal Confirmation -->
    <Teleport to="body">
      <div v-if="showCancelModal" class="fixed inset-0 z-[100000] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-md p-6 space-y-4">
          <h3 class="text-base font-bold text-rose-600">Cancel Appointment Request</h3>
          <p class="text-xs text-slate-600 dark:text-slate-300">Are you sure you want to cancel the viewing for "{{ getApptPropertyTitle(selectedAppt) }}"?</p>
          <textarea v-model="cancelReason" rows="2" placeholder="Reason for cancellation (optional)" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-xl"></textarea>

          <div class="flex justify-end gap-2 pt-2">
            <button @click="showCancelModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl">Back</button>
            <button @click="confirmCancelAppointment" class="px-4 py-2 text-xs font-bold bg-rose-600 text-white rounded-xl hover:bg-rose-700 cursor-pointer">Cancel</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- 5. Delete Confirmation Modal -->
    <ConfirmModal
      :is-open="showDeleteModal"
      :title="'Delete Appointment Record'"
      :message="`Are you sure you want to permanently delete the viewing record for '${getApptPropertyTitle(apptToDelete)}'? This record will disappear completely.`"
      :confirm-label="'Delete'"
      :danger="true"
      @confirm="confirmDeleteAppointment"
      @cancel="showDeleteModal = false; apptToDelete = null"
    />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import {
  CalendarPlus,
  Calendar,
  Clock,
  MapPin,
  MessageSquare,
  RotateCcw,
  Trash2,
  X,
  Eye,
  MoreVertical,
  User,
  Key,
  ExternalLink,
  XCircle,
  CheckCircle,
  Search,
  List,
  LayoutGrid
} from 'lucide-vue-next'
import { appointmentService } from '@/services/appointmentService'
import { propertyService } from '@/services/propertyService'
import { clearApiCache } from '@/services/api'
import { useLanguage } from '@/composables/useLanguage'
import { useToastStore } from '@/stores/toast'
import { useAuthStore } from '@/stores/auth'
import EmptyState from '@/components/dashboard/EmptyState.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'
import StatusBadge from '@/components/dashboard/StatusBadge.vue'

const router = useRouter()
const route = useRoute()
const { t } = useLanguage()
const toastStore = useToastStore()
const authStore = useAuthStore()

const currentUserId = computed(() => authStore.user?.id)
const currentUserRole = computed(() => (authStore.user?.role || authStore.user?.roles?.[0]?.name || '').toLowerCase())
const viewMode = ref('table')

function canManageAppt(appt) {
  if (!appt) return false
  if (['admin', 'agent'].includes(currentUserRole.value)) return true
  const ownerId = Number(appt.owner_id || appt.owner?.id || 0)
  return (ownerId > 0 && ownerId === Number(currentUserId.value)) || currentUserRole.value === 'owner'
}

async function approveAppt(appt) {
  if (!appt?.id) return
  try {
    await appointmentService.confirmAppointment(appt.id)
    appt.status = 'confirmed'
    toastStore.success('Tour appointment confirmed successfully!')
    await loadAppointments()
  } catch (err) {
    console.error('Failed to confirm appointment:', err)
    toastStore.error(err.response?.data?.message || 'Failed to confirm appointment.')
  }
}

async function markApptCompleted(appt) {
  if (!appt?.id) return
  try {
    await appointmentService.completeAppointment(appt.id)
    appt.status = 'completed'
    toastStore.success('Tour appointment marked as completed!')
    await loadAppointments()
  } catch (err) {
    console.error('Failed to complete appointment:', err)
    toastStore.error(err.response?.data?.message || 'Failed to complete appointment.')
  }
}

const isLoading = ref(true)
const isSubmitting = ref(false)
const appointments = ref([])
const selectedStatus = ref('all')
const searchQuery = ref('')
const sortBy = ref('latest')
const liveProperties = ref([])
const isLoadingProperties = ref(false)

const bookErrors = reactive({
  property_id: '',
  date: '',
  time: '',
  viewing_type: '',
  note: ''
})

const activeMenu = ref(null)
const showDetailModal = ref(false)
const showBookModal = ref(false)
const showRescheduleModal = ref(false)
const showCancelModal = ref(false)
const showDeleteModal = ref(false)

const selectedAppt = ref(null)
const apptToDelete = ref(null)
const cancelReason = ref('')

const statusTabs = [
  { label: 'All', value: 'all' },
  { label: 'Pending', value: 'pending' },
  { label: 'Confirmed', value: 'confirmed' },
  { label: 'Completed', value: 'completed' },
  { label: 'Cancelled', value: 'cancelled' },
]

async function loadAvailableProperties() {
  isLoadingProperties.value = true
  try {
    const res = await propertyService.getProperties({ per_page: 100 })
    const payload = res?.data || {}
    const items = Array.isArray(payload.data) ? payload.data : (Array.isArray(payload) ? payload : [])
    if (items.length > 0) {
      const currentUserId = authStore.user?.id
      liveProperties.value = items
        .filter(p => !currentUserId || Number(p.user_id || p.owner_id || p.user?.id) !== Number(currentUserId))
        .map(p => {
          const extractName = (val) => {
            if (!val) return ''
            if (typeof val === 'string') return val.trim()
            if (typeof val === 'object') return val.name || val.title || val.slug || ''
            return String(val)
          }
          const subCity = extractName(p.address?.subCity) || extractName(p.address?.sub_city) || extractName(p.subcity) || extractName(p.sub_city)
          const city = extractName(p.address?.city) || extractName(p.city) || 'Addis Ababa'
          const safeSubCity = subCity && !subCity.includes('[object Object]') ? subCity : ''
          const safeCity = city && !city.includes('[object Object]') ? city : 'Addis Ababa'
          const locationStr = safeSubCity ? `${safeSubCity}, ${safeCity}` : safeCity
          return {
            id: p.id,
            user_id: p.user_id || p.owner_id || p.user?.id,
            title: p.title || `Property #${p.id}`,
            location: locationStr,
            price: p.price || 0,
            listing_type: p.listing_type || 'sale'
          }
        })
    }
    // Ensure property from route query exists in available list
    const queryPropId = route.query.property_id
    if (queryPropId && !liveProperties.value.some(p => Number(p.id) === Number(queryPropId))) {
      try {
        const singleRes = await propertyService.getPropertyBySlug(queryPropId)
        const p = singleRes?.data?.data || singleRes?.data || singleRes
        if (p && p.id) {
          liveProperties.value.unshift({
            id: p.id,
            user_id: p.user_id || p.owner_id || p.user?.id,
            title: p.title || `Property #${p.id}`,
            location: p.address?.city?.name || p.city?.name || 'Addis Ababa',
            price: p.price || 0,
            listing_type: p.listing_type || 'sale'
          })
        }
      } catch (e) {
        console.warn('Could not prefetch query property for booking modal:', e)
      }
    }

    if (queryPropId && (!bookingForm.property_id || bookingForm.property_id === '')) {
      bookingForm.property_id = Number(queryPropId)
    }
  } catch (err) {
    console.error('Failed to load real properties for booking modal:', err)
  } finally {
    isLoadingProperties.value = false
  }
}

const availableProperties = computed(() => {
  return liveProperties.value
})

const minBookingDate = computed(() => {
  const d = new Date()
  d.setDate(d.getDate() + 1)
  return d.toISOString().split('T')[0]
})

const bookingForm = reactive({
  property_id: '',
  date: '',
  time: '10:00',
  viewing_type: 'in_person',
  note: ''
})

const rescheduleForm = reactive({
  date: '',
  time: '10:00',
  reason: ''
})

const initialRescheduleDate = ref('')
const initialRescheduleTime = ref('')
const isRescheduling = ref(false)
const rescheduleError = ref('')

const isRescheduleUnchanged = computed(() => {
  return rescheduleForm.date === initialRescheduleDate.value && rescheduleForm.time === initialRescheduleTime.value
})

const isRescheduleInPast = computed(() => {
  if (!rescheduleForm.date || !rescheduleForm.time) return true
  const selected = new Date(`${rescheduleForm.date}T${rescheduleForm.time}:00`)
  return isNaN(selected.getTime()) || selected <= new Date()
})

const filteredAppointments = computed(() => {
  let list = appointments.value

  // Discard any invalid self-booked records (where owner_id === visitor_id)
  list = list.filter(a => {
    const ownerId = Number(a.owner_id || a.owner?.id || 0)
    const visitorId = Number(a.visitor_id || a.visitor?.id || 0)
    if (ownerId > 0 && visitorId > 0 && ownerId === visitorId) {
      return false
    }
    return true
  })

  // Status Filter
  if (selectedStatus.value !== 'all') {
    list = list.filter(a => (a.status || '').toLowerCase() === selectedStatus.value.toLowerCase())
  }

  // Text Search Filter (Property title, Sub-city/location, Visitor name, Host name)
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    const isShort = q.length <= 2
    list = list.filter(a => {
      const propTitle = getApptPropertyTitle(a).toLowerCase()
      const location = getApptLocation(a).toLowerCase()
      const visitor = getApptVisitorName(a).toLowerCase()
      const owner = getApptOwnerName(a).toLowerCase()
      if (isShort) {
        const boundaryRegex = new RegExp('(^|\\s|[,/-])' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'i')
        return boundaryRegex.test(propTitle) || boundaryRegex.test(location) || boundaryRegex.test(visitor) || boundaryRegex.test(owner)
      }
      return propTitle.includes(q) || location.includes(q) || visitor.includes(q) || owner.includes(q)
    })
  }

  // Sorting
  return [...list].sort((a, b) => {
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase().trim()
      const getRelevance = (item) => {
        const title = getApptPropertyTitle(item).toLowerCase()
        if (title.startsWith(q)) return 1
        if (title.includes(' ' + q)) return 2
        if (title.includes(q)) return 3
        return 4
      }
      const relA = getRelevance(a)
      const relB = getRelevance(b)
      if (relA !== relB) return relA - relB
    }
    const dateA = new Date(a.scheduled_at || a.created_at || 0).getTime()
    const dateB = new Date(b.scheduled_at || b.created_at || 0).getTime()
    if (sortBy.value === 'latest') return dateB - dateA
    if (sortBy.value === 'oldest') return dateA - dateB
    if (sortBy.value === 'newest') {
      const createA = new Date(a.created_at || 0).getTime()
      const createB = new Date(b.created_at || 0).getTime()
      return createB - createA
    }
    return 0
  })
})

function getTabCount(status) {
  if (status === 'all') return appointments.value.length
  return appointments.value.filter(a => (a.status || '').toLowerCase() === status.toLowerCase()).length
}

async function loadAppointments(force = false) {
  if (force) {
    clearApiCache('/appointments')
  }
  isLoading.value = true
  try {
    const res = await appointmentService.getAppointments({ per_page: 50 })
    const payload = res?.data || {}
    appointments.value = payload.data || payload || []
  } catch (err) {
    console.error('Failed to load appointments:', err)
    appointments.value = []
  } finally {
    isLoading.value = false
  }
}

function toggleActionMenu(appt, event) {
  if (activeMenu.value && activeMenu.value.id === appt.id) {
    activeMenu.value = null
    return
  }

  const rect = event.currentTarget.getBoundingClientRect()
  const menuWidth = 192
  const menuHeight = 180
  let top = rect.bottom + 6
  let left = rect.right - menuWidth

  if (top + menuHeight > window.innerHeight) {
    top = rect.top - menuHeight - 6
  }

  activeMenu.value = {
    id: appt.id,
    appt,
    top: `${Math.max(10, top)}px`,
    left: `${Math.max(10, left)}px`
  }
}

function handleMenuAction(action) {
  if (!activeMenu.value?.appt) return
  const appt = activeMenu.value.appt
  activeMenu.value = null

  if (action === 'details') {
    openDetailModal(appt)
  } else if (action === 'approve') {
    approveAppt(appt)
  } else if (action === 'complete') {
    markApptCompleted(appt)
  } else if (action === 'reschedule') {
    openRescheduleModal(appt)
  } else if (action === 'message') {
    sendMessageToHost(appt)
  } else if (action === 'property') {
    const propId = appt.property_id || appt.property?.id
    if (propId) {
      window.open(`/properties/${propId}`, '_blank')
    }
  } else if (action === 'cancel') {
    promptCancelModal(appt)
  } else if (action === 'delete') {
    promptDeleteModal(appt)
  }
}

function openDetailModal(appt) {
  selectedAppt.value = appt
  showDetailModal.value = true
}

function openBookModal(prefillPropertyId = '') {
  bookErrors.property_id = ''
  bookErrors.date = ''
  bookErrors.time = ''
  bookErrors.viewing_type = ''
  bookErrors.note = ''
  const targetId = prefillPropertyId || route.query.property_id
  bookingForm.property_id = targetId ? Number(targetId) : (availableProperties.value[0]?.id || '')
  bookingForm.date = minBookingDate.value
  bookingForm.time = '10:00'
  bookingForm.viewing_type = 'in_person'
  bookingForm.note = ''
  showBookModal.value = true
  if (liveProperties.value.length === 0) {
    loadAvailableProperties()
  }
}

async function submitBookAppointment() {
  bookErrors.property_id = ''
  bookErrors.date = ''
  bookErrors.time = ''
  bookErrors.viewing_type = ''
  bookErrors.note = ''

  let hasError = false

  if (!bookingForm.property_id) {
    bookErrors.property_id = 'Please select a property.'
    hasError = true
  } else {
    // Check if user is booking their own property
    const chosenProp = liveProperties.value.find(p => Number(p.id) === Number(bookingForm.property_id))
    if (authStore.user?.id && chosenProp?.user_id && Number(authStore.user.id) === Number(chosenProp.user_id)) {
      bookErrors.property_id = 'You cannot book a tour for your own property listing.'
      toastStore.info('This is your own listing. You cannot book a tour with yourself.')
      hasError = true
    } else {
      // Check if active (pending or confirmed) appointment already exists for this property
      const activeAppt = appointments.value.find(a => 
        String(a.property_id || a.property?.id) === String(bookingForm.property_id) &&
        ['pending', 'confirmed'].includes(String(a.status).toLowerCase())
      )

      if (activeAppt) {
        const statusText = String(activeAppt.status).toLowerCase() === 'confirmed' ? 'confirmed' : 'pending'
        bookErrors.property_id = `You already have an active (${statusText}) tour for this property.`
        toastStore.error(`You already have an active (${statusText}) appointment for this property.`)
        hasError = true
      }
    }
  }

  if (!bookingForm.date) {
    bookErrors.date = 'Please select an appointment date.'
    hasError = true
  } else {
    const selectedDate = new Date(`${bookingForm.date}T${bookingForm.time || '00:00'}`)
    if (isNaN(selectedDate.getTime()) || selectedDate <= new Date()) {
      bookErrors.date = 'Date must be scheduled in the future.'
      hasError = true
    } else {
      const maxDate = new Date()
      maxDate.setDate(maxDate.getDate() + 180)
      if (selectedDate > maxDate) {
        bookErrors.date = 'Viewing cannot be scheduled more than 6 months in advance.'
        hasError = true
      }
    }
  }

  if (!bookingForm.time) {
    bookErrors.time = 'Please select an appointment time.'
    hasError = true
  } else {
    const [hour, minute] = bookingForm.time.split(':').map(Number)
    if (isNaN(hour) || hour < 8 || hour > 18 || (hour === 18 && (minute || 0) > 0)) {
      bookErrors.time = 'Viewing time must be between 08:00 AM and 06:00 PM.'
      hasError = true
    }
  }

  if (!['in_person', 'virtual'].includes(bookingForm.viewing_type)) {
    bookErrors.viewing_type = 'Please select a valid tour format (In-Person or Virtual).'
    hasError = true
  }

  if (bookingForm.note && bookingForm.note.length > 500) {
    bookErrors.note = 'Special request message cannot exceed 500 characters.'
    hasError = true
  }

  if (hasError) return

  isSubmitting.value = true
  try {
    const selectedDate = new Date(`${bookingForm.date}T${bookingForm.time}`)
    const scheduledAt = selectedDate.toISOString()
    await appointmentService.bookAppointment({
      property_id: bookingForm.property_id,
      scheduled_at: scheduledAt,
      note: bookingForm.note,
      viewing_type: bookingForm.viewing_type
    })
    toastStore.success('Tour appointment booked successfully!')
    showBookModal.value = false
    await loadAppointments()
  } catch (err) {
    console.error('Booking failed:', err)
    toastStore.error(err.response?.data?.message || err.message || 'Failed to book appointment.')
  } finally {
    isSubmitting.value = false
  }
}

function openRescheduleModal(appt) {
  selectedAppt.value = appt
  rescheduleError.value = ''
  let d = new Date(appt?.scheduled_at || Date.now())
  if (isNaN(d.getTime()) && typeof appt?.scheduled_at === 'string') {
    d = new Date(appt.scheduled_at.replace(' ', 'T'))
  }
  if (isNaN(d.getTime())) d = new Date()
  const dateStr = d.toISOString().split('T')[0]
  const timeStr = d.toTimeString().slice(0, 5)
  rescheduleForm.date = dateStr
  rescheduleForm.time = timeStr
  rescheduleForm.reason = ''
  initialRescheduleDate.value = dateStr
  initialRescheduleTime.value = timeStr
  showRescheduleModal.value = true
}

async function submitReschedule() {
  if (!selectedAppt.value || isRescheduling.value) return
  rescheduleError.value = ''

  if (isRescheduleUnchanged.value) {
    rescheduleError.value = 'Please select a new date or time different from the current schedule.'
    return
  }

  if (isRescheduleInPast.value) {
    rescheduleError.value = 'The viewing must be scheduled in the future.'
    return
  }

  const [hour] = (rescheduleForm.time || '').split(':').map(Number)
  if (isNaN(hour) || hour < 8 || hour > 18) {
    rescheduleError.value = 'Viewing hours must be between 08:00 AM and 06:00 PM.'
    return
  }

  const trimmedReason = (rescheduleForm.reason || '').trim()
  if (!trimmedReason || trimmedReason.length < 5) {
    rescheduleError.value = 'Please provide a reschedule reason (minimum 5 characters).'
    return
  }

  // Prevent submitting only numbers, digits, or symbols without meaningful words (e.g. "12345")
  const letterCount = (trimmedReason.match(/[\p{L}]/gu) || []).length
  if (letterCount < 3) {
    rescheduleError.value = 'Please provide a meaningful explanation using words (not just numbers or symbols).'
    return
  }

  isRescheduling.value = true
  const scheduledAt = new Date(`${rescheduleForm.date}T${rescheduleForm.time}:00`).toISOString()

  try {
    await appointmentService.updateAppointment(selectedAppt.value.id, {
      scheduled_at: scheduledAt,
      message: trimmedReason,
      reason: trimmedReason
    })
    toastStore.success('Tour rescheduled successfully!')
    showRescheduleModal.value = false
    selectedAppt.value = null
    await loadAppointments()
  } catch (err) {
    console.error('Reschedule failed:', err)
    rescheduleError.value = err.response?.data?.message || err.message || 'Failed to reschedule tour.'
    toastStore.error(rescheduleError.value)
  } finally {
    isRescheduling.value = false
  }
}

function promptCancelModal(appt) {
  selectedAppt.value = appt
  cancelReason.value = ''
  showCancelModal.value = true
}

async function confirmCancelAppointment() {
  if (!selectedAppt.value) return

  const targetId = selectedAppt.value.id
  showCancelModal.value = false

  try {
    await appointmentService.cancelAppointment(targetId, cancelReason.value)
    toastStore.info('Appointment cancelled')
    await loadAppointments()
  } catch (err) {
    toastStore.error('Failed to cancel.')
  } finally {
    selectedAppt.value = null
  }
}

function promptDeleteModal(appt) {
  apptToDelete.value = appt
  showDeleteModal.value = true
}

async function confirmDeleteAppointment() {
  if (!apptToDelete.value?.id) return
  
  const id = apptToDelete.value.id
  showDeleteModal.value = false

  // Instant local removal
  appointments.value = appointments.value.filter(a => a.id !== id)
  toastStore.success('Appointment removed from system')

  try {
    await appointmentService.deleteAppointment(id)
    await loadAppointments()
  } catch (err) {
    console.error('Delete failed:', err)
  } finally {
    apptToDelete.value = null
  }
}

function sendMessageToHost(appt) {
  const role = currentUserRole.value
  if (role === 'owner') {
    router.push('/owner/messages')
  } else if (role === 'agent') {
    router.push('/agent/messages')
  } else {
    const ownerId = appt.owner_id || appt.owner?.id || 1
    router.push(`/buyer/messages?owner_id=${ownerId}&property_id=${appt.property_id || appt.property?.id || ''}`)
  }
}

function formatDateTime(dateStr) {
  if (!dateStr) return 'Pending schedule'
  try {
    let d = new Date(dateStr)
    if (isNaN(d.getTime()) && typeof dateStr === 'string') {
      d = new Date(dateStr.replace(' ', 'T'))
    }
    if (isNaN(d.getTime())) return dateStr
    return d.toLocaleDateString('en-US', {
      weekday: 'short',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch {
    return dateStr
  }
}

function formatApptDate(dateStr) {
  return formatDateTime(dateStr)
}

// Distinct Photo Catalog
const PROPERTY_PHOTO_CATALOG = [
  'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&q=80',
  'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800&q=80',
  'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80',
  'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80',
  'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80',
  'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800&q=80',
  'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&q=80',
  'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800&q=80'
]

function getAppointmentImage(appt, idx = 0) {
  if (!appt) return PROPERTY_PHOTO_CATALOG[0]
  if (appt.property?.primary_image?.url) return appt.property.primary_image.url
  if (appt.property?.primary_image?.image_url) return appt.property.primary_image.image_url
  if (appt.property?.images?.[0]?.url) return appt.property.images[0].url
  if (appt.property_image && !appt.property_image.includes('photo-1545324418')) {
    return appt.property_image
  }
  const seed = Number(appt.property_id || appt.id || idx)
  return PROPERTY_PHOTO_CATALOG[seed % PROPERTY_PHOTO_CATALOG.length]
}

function getApptPropertyTitle(appt) {
  if (!appt) return 'Property Viewing'
  return appt.property?.title || appt.property_title || 'Property Viewing'
}

function isTourPast(dateStr) {
  if (!dateStr) return false
  let d = new Date(dateStr)
  if (isNaN(d.getTime()) && typeof dateStr === 'string') {
    d = new Date(dateStr.replace(' ', 'T'))
  }
  if (isNaN(d.getTime())) return false
  return d <= new Date()
}

function getApptLocation(appt) {
  if (!appt) return 'Addis Ababa, Ethiopia'
  const extractName = (val) => {
    if (!val) return ''
    if (typeof val === 'string') return val.trim()
    if (typeof val === 'object') return val.name || val.title || val.slug || ''
    return String(val)
  }
  const prop = appt.property || {}
  const subCity = extractName(prop.address?.subCity) || extractName(prop.address?.sub_city) || extractName(prop.subcity) || extractName(prop.sub_city)
  const city = extractName(prop.address?.city) || extractName(prop.city)
  const safeSubCity = subCity && !subCity.includes('[object Object]') ? subCity : ''
  const safeCity = city && !city.includes('[object Object]') ? city : ''

  if (safeSubCity && safeCity) {
    if (safeSubCity.toLowerCase().includes('adama')) return 'Adama, Oromia'
    return `${safeSubCity}, ${safeCity}`
  }
  if (safeSubCity) {
    if (safeSubCity.toLowerCase().includes('adama')) return 'Adama, Oromia'
    return safeSubCity
  }
  if (safeCity) return safeCity

  if (typeof prop.location === 'string' && prop.location && !prop.location.includes('[object Object]')) {
    if (prop.location.toLowerCase().includes('adama')) return 'Adama, Oromia'
    return prop.location
  }
  if (typeof appt.property_location === 'string' && appt.property_location && !appt.property_location.includes('[object Object]')) {
    if (appt.property_location.toLowerCase().includes('adama')) return 'Adama, Oromia'
    return appt.property_location
  }

  const title = String(prop.title || appt.property_title || '').toLowerCase()
  if (title.includes('adama')) {
    return 'Adama, Oromia'
  }
  return 'Addis Ababa, Ethiopia'
}

function getApptVisitorName(appt) {
  if (!appt) return 'Client'
  return appt.visitor?.name || appt.visitor_name || 'Client'
}

function getApptVisitorPhone(appt) {
  if (!appt) return '+251 91 100 0000'
  return appt.visitor?.phone || appt.visitor_phone || '+251 91 100 0000'
}

function getApptVisitorRole(appt) {
  if (!appt) return 'Buyer'
  return appt.visitor?.roles?.[0]?.name || appt.visitor?.role || 'Buyer'
}

function getApptOwnerName(appt) {
  if (!appt) return 'Host'
  return appt.owner?.name || appt.owner_name || 'Host'
}

function getApptOwnerPhone(appt) {
  if (!appt) return '+251 91 200 0000'
  return appt.owner?.phone || appt.owner_phone || '+251 91 200 0000'
}

let pollTimer = null

function handleVisibilityChange() {
  if (document.visibilityState === 'visible') {
    loadAppointments(true)
  }
}

onMounted(async () => {
  loadAppointments(true)
  await loadAvailableProperties()
  const prefillId = route.query.property_id
  if (prefillId) {
    openBookModal(Number(prefillId))
  }
  document.addEventListener('visibilitychange', handleVisibilityChange)
  pollTimer = setInterval(() => {
    if (document.visibilityState === 'visible') {
      loadAppointments(true)
    }
  }, 30000)
})

onUnmounted(() => {
  document.removeEventListener('visibilitychange', handleVisibilityChange)
  if (pollTimer) {
    clearInterval(pollTimer)
    pollTimer = null
  }
})

watch(
  () => route.query.property_id,
  (newId) => {
    if (newId) {
      openBookModal(Number(newId))
    }
  }
)
</script>
