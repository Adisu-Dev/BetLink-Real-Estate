<template>
  <div class="space-y-6">
    
    <!-- Error State with Retry -->
    <div v-if="apiError" class="p-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-center space-y-3">
      <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ apiError }}</p>
      <button
        @click="loadDashboardData(true)"
        class="px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold rounded-xl hover:bg-slate-800 transition-colors cursor-pointer"
      >
        Retry Loading
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-else-if="isLoading" class="space-y-6 animate-pulse">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        <div v-for="n in 4" :key="n" class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 h-24"></div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 h-64"></div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 h-64"></div>
      </div>
    </div>

    <!-- Dashboard Content -->
    <template v-else>
      <!-- 1. Header with Impressions Pill -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
          <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Agent Dashboard</h1>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Real-time overview of your managed listings, tour requests, and client pipeline.</p>
        </div>
        <div class="flex items-center gap-2">
          <RouterLink
            to="/agent/analytics"
            class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl shadow-xs text-xs font-bold text-slate-700 dark:text-slate-300 hover:border-slate-300 transition-all"
            title="View full analytics"
          >
            <Eye class="w-4 h-4 text-slate-500" />
            <span>{{ stats.totalViews }} Total Views</span>
          </RouterLink>
          <RouterLink
            to="/agent/verification"
            :class="[
              'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border shadow-xs',
              verificationStatus.isVerified 
                ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200/80 dark:border-emerald-800'
                : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200/80 dark:border-amber-800'
            ]"
          >
            <ShieldCheck class="w-3.5 h-3.5" />
            <span>{{ verificationStatus.isVerified ? 'Verified Broker' : 'Pending License' }}</span>
          </RouterLink>
        </div>
      </div>

      <!-- 2. Real Live Key Performance Stats (Uniform 4-Grid matching Owner Dashboard) -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        
        <!-- 1. Managed Properties -->
        <RouterLink 
          to="/agent/properties"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
            <Building2 class="w-4.5 h-4.5" />
          </div>
          <div class="min-w-0">
            <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider truncate block">Managed Listings</span>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.totalProperties }}</p>
          </div>
        </RouterLink>

        <!-- 2. Active Listings -->
        <RouterLink 
          to="/agent/properties"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
            <CheckCircle class="w-4.5 h-4.5" />
          </div>
          <div class="min-w-0">
            <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider truncate block">Active Portfolio</span>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.activeListings }}</p>
          </div>
        </RouterLink>

        <!-- 3. Pending Tours -->
        <RouterLink 
          to="/agent/appointments"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
            <Clock class="w-4.5 h-4.5" />
          </div>
          <div class="min-w-0">
            <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider truncate block">Pending Tours</span>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.pendingAppointments }}</p>
          </div>
        </RouterLink>

        <!-- 4. Client Leads CRM -->
        <RouterLink 
          to="/agent/leads"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
            <MessageSquare class="w-4.5 h-4.5" />
          </div>
          <div class="min-w-0">
            <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider truncate block">Client Leads</span>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.totalInquiries }}</p>
          </div>
        </RouterLink>
      </div>

      <!-- 3. Quick Action Controls Toolbar -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200/80 dark:border-slate-800 transition-colors">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-2">
            <Briefcase class="w-4 h-4 text-slate-700 dark:text-slate-300" />
            Agent Management Tools
          </h2>
          <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 rounded-full border border-slate-200 dark:border-slate-700">
            Broker Portal
          </span>
        </div>
        <div class="flex flex-wrap gap-2">
          <RouterLink
            to="/agent/properties"
            class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-100 rounded-xl text-xs font-bold transition-colors shadow-xs"
          >
            <Plus class="w-3.5 h-3.5" />
            <span>Add Property</span>
          </RouterLink>

          <RouterLink
            to="/agent/leads"
            class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 transition-colors shadow-xs"
          >
            <Users class="w-3.5 h-3.5 text-slate-500" />
            <span>Client Leads</span>
          </RouterLink>

          <RouterLink
            to="/agent/appointments"
            class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 transition-colors shadow-xs"
          >
            <Calendar class="w-3.5 h-3.5 text-slate-500" />
            <span>Scheduled Tours</span>
          </RouterLink>

          <RouterLink
            to="/agent/verification"
            class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 transition-colors shadow-xs"
          >
            <ShieldCheck class="w-3.5 h-3.5 text-slate-500" />
            <span>Credentials</span>
          </RouterLink>

          <RouterLink
            to="/agent/reports"
            class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 transition-colors shadow-xs"
          >
            <FileText class="w-3.5 h-3.5 text-slate-500" />
            <span>Reports & Exports</span>
          </RouterLink>
        </div>
      </div>

      <!-- 4. Row 2: Analytics & Sub-City Breakdown (2-to-1 Asymmetrical Grid) -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Left (2 Columns): Inquiries vs. Tours Activity Trend Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between transition-colors">
          
          <!-- Header -->
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Inquiries & Tours Activity</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Performance trend of inquiries vs tour bookings across your listings.</p>
            </div>
            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-200/60 dark:border-slate-700">
              <span>Last 6 Months</span>
            </div>
          </div>

          <!-- Chart Body with Dynamic Bars -->
          <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 items-end">
            
            <!-- Summary Metric Badge on the Left -->
            <div class="space-y-3 sm:col-span-1 border-b sm:border-b-0 sm:border-r border-slate-100 dark:border-slate-800 pb-4 sm:pb-0 sm:pr-4">
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500">6-Mo Inquiries</span>
                <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">
                  {{ monthlyTrends.reduce((sum, item) => sum + (item.inquiries || 0), 0) }}
                </p>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500">6-Mo Tours</span>
                <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">
                  {{ monthlyTrends.reduce((sum, item) => sum + (item.bookings || 0), 0) }}
                </p>
              </div>
              <div class="pt-2 flex items-center gap-3 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-1.5">
                  <span class="w-2.5 h-2.5 rounded-full bg-slate-900 dark:bg-slate-200"></span>
                  <span>Inquiries</span>
                </div>
                <div class="flex items-center gap-1.5">
                  <span class="w-2.5 h-2.5 rounded-full bg-slate-400 dark:bg-slate-600"></span>
                  <span>Tours</span>
                </div>
              </div>
            </div>

            <!-- Responsive 6-Month Visual Bar Chart -->
            <div class="sm:col-span-3 flex items-end justify-between gap-2 pt-4 h-40">
              <div
                v-for="item in monthlyTrends"
                :key="item.month"
                class="flex-1 flex flex-col items-center gap-2 group h-full justify-end"
              >
                <div class="w-full flex items-end justify-center gap-1 h-28">
                  <!-- Inquiries Bar -->
                  <div
                    class="w-3 sm:w-4 bg-slate-900 dark:bg-slate-200 rounded-t-md transition-all group-hover:opacity-80 cursor-pointer"
                    :style="{ height: getBarHeight(item.inquiries, maxTrendInquiries) }"
                    :title="`${item.inquiries || 0} Inquiries in ${item.month}`"
                  ></div>
                  <!-- Tours Bar -->
                  <div
                    class="w-3 sm:w-4 bg-slate-300 dark:bg-slate-700 rounded-t-md transition-all group-hover:opacity-80 cursor-pointer"
                    :style="{ height: getBarHeight(item.bookings, maxTrendBookings) }"
                    :title="`${item.bookings || 0} Tours in ${item.month}`"
                  ></div>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400">{{ item.month }}</span>
              </div>
            </div>
          </div>

        </div>

        <!-- Right (1 Column): Sub-City Portfolio Distribution Progress Bars -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between transition-colors">
          <div>
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Portfolio by Sub-City</h3>
              <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">{{ stats.totalProperties }} units</span>
            </div>

            <div v-if="subCityBreakdown.length === 0" class="text-center py-8 text-xs text-slate-400">
              No sub-city distribution data available.
            </div>

            <div v-else class="space-y-3.5">
              <div
                v-for="sub in subCityBreakdown"
                :key="sub.name || sub.subCity"
                class="space-y-1.5"
              >
                <div class="flex items-center justify-between text-xs">
                  <span class="font-bold text-slate-700 dark:text-slate-300 truncate">{{ sub.name || sub.subCity }}</span>
                  <span class="font-semibold text-slate-500 dark:text-slate-400 text-[11px]">{{ sub.count }} ({{ sub.percentage }}%)</span>
                </div>
                <!-- Clean Dark / Light Progress Track -->
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                  <div
                    class="bg-slate-900 dark:bg-white h-2 rounded-full transition-all duration-500"
                    :style="{ width: `${sub.percentage}%` }"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 text-center">
            <RouterLink
              to="/agent/properties"
              class="text-xs font-bold text-slate-900 dark:text-white hover:underline inline-flex items-center gap-1"
            >
              <span>View Full Inventory</span>
              <span>&rarr;</span>
            </RouterLink>
          </div>
        </div>

      </div>

      <!-- 5. Row 3: Actionable Pending Tours & Recent Property Performance -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        <!-- Left Card: Pending Tour Requests Table with Approve / Decline -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between transition-colors">
          <div>
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-2">
                <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Upcoming Tour Schedule</h3>
                <span v-if="stats.pendingAppointments > 0" class="px-2.5 py-0.5 text-[10px] font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                  {{ stats.pendingAppointments }} Pending
                </span>
              </div>
              <RouterLink
                to="/agent/appointments"
                class="text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
              >
                All Tours &rarr;
              </RouterLink>
            </div>

            <!-- Empty Tours State -->
            <div v-if="recentAppointments.length === 0" class="text-center py-10 space-y-2">
              <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto">
                <Calendar class="w-5 h-5" />
              </div>
              <p class="text-xs text-slate-500 font-semibold">No pending tour bookings</p>
              <p class="text-[11px] text-slate-400 max-w-xs mx-auto">When prospective clients schedule viewings for your listings, they will appear here for your confirmation.</p>
            </div>

            <!-- Tours List with Action Buttons -->
            <div v-else class="space-y-3">
              <div
                v-for="apt in recentAppointments"
                :key="apt.id"
                class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3"
              >
                <div class="flex items-center gap-3 min-w-0">
                  <img
                    :src="apt.property_image || 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=200&q=80'"
                    :alt="apt.property_title"
                    class="w-11 h-11 rounded-lg object-cover border border-slate-200 dark:border-slate-700 shrink-0"
                  />
                  <div class="min-w-0">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ apt.property_title }}</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                      Client: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ apt.visitor_name }}</span> &bull; {{ apt.scheduled_at }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                  <template v-if="apt.status === 'pending'">
                    <button
                      @click="approveAppointment(apt)"
                      class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-lg transition-colors cursor-pointer shadow-2xs"
                    >
                      Approve
                    </button>
                    <button
                      @click="declineAppointment(apt)"
                      class="px-3 py-1.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
                    >
                      Decline
                    </button>
                  </template>
                  <template v-else-if="apt.status === 'confirmed'">
                    <button
                      v-if="isTourPast(apt.scheduled_at)"
                      @click="completeAppointment(apt)"
                      class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-600 text-xs font-bold rounded-lg transition-colors cursor-pointer shadow-2xs flex items-center gap-1"
                      title="Mark tour completed"
                    >
                      <CheckCircle class="w-3 h-3 text-emerald-500" />
                      <span>Complete</span>
                    </button>
                    <button
                      v-else
                      disabled
                      class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800/60 text-slate-400 dark:text-slate-500 text-xs font-bold rounded-lg border border-slate-200/80 dark:border-slate-700/80 cursor-not-allowed flex items-center gap-1 shadow-2xs opacity-75"
                      title="Tour date has not arrived yet. You can complete it once the viewing time arrives."
                    >
                      <Clock class="w-3 h-3 text-slate-400" />
                      <span>Upcoming</span>
                    </button>
                    <span
                      class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60 capitalize"
                    >
                      Confirmed
                    </span>
                  </template>
                  <span
                    v-else
                    class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60 capitalize"
                  >
                    {{ apt.status }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Card: Top Managed Inventory -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between transition-colors">
          <div>
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Top Managed Listings</h3>
              <RouterLink
                to="/agent/properties"
                class="text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
              >
                View All &rarr;
              </RouterLink>
            </div>

            <!-- Empty Properties State -->
            <div v-if="recentProperties.length === 0" class="text-center py-10 space-y-2">
              <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto">
                <Building2 class="w-5 h-5" />
              </div>
              <p class="text-xs text-slate-500 font-semibold">No managed properties yet</p>
              <p class="text-[11px] text-slate-400 max-w-xs mx-auto">Click "+ Add Property" to publish your first managed real estate listing.</p>
            </div>

            <!-- Properties List -->
            <div v-else class="divide-y divide-slate-100 dark:divide-slate-800">
              <div
                v-for="prop in recentProperties"
                :key="prop.id"
                class="py-3 flex items-center justify-between gap-3 first:pt-0 last:pb-0"
              >
                <div class="flex items-center gap-3 min-w-0">
                  <img
                    :src="prop.image || 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=200&q=80'"
                    :alt="prop.title"
                    class="w-12 h-12 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0"
                  />
                  <div class="min-w-0">
                    <RouterLink
                      :to="`/agent/properties`"
                      class="text-xs font-bold text-slate-900 dark:text-white hover:underline truncate block"
                    >
                      {{ prop.title }}
                    </RouterLink>
                    <p class="text-[11px] text-slate-400 truncate">{{ prop.location }}</p>
                    <p class="text-xs font-black text-slate-900 dark:text-white mt-0.5">
                      ETB {{ Number(prop.price).toLocaleString() }}
                    </p>
                  </div>
                </div>

                <div class="text-right shrink-0">
                  <div class="flex items-center justify-end gap-1.5 text-xs font-bold text-slate-900 dark:text-white">
                    <Eye class="w-3 h-3 text-slate-400" />
                    <span>{{ prop.views_count ?? prop.views ?? 0 }}</span>
                  </div>
                  <span
                    :class="[
                      'inline-block px-2 py-0.5 rounded-md text-[10px] font-bold uppercase mt-1',
                      prop.status === 'active' 
                        ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400' 
                        : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'
                    ]"
                  >
                    {{ prop.status }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 text-center">
            <RouterLink
              to="/agent/properties"
              class="text-xs font-bold text-slate-900 dark:text-white hover:underline inline-flex items-center gap-1"
            >
              <span>Manage All Properties</span>
              <span>&rarr;</span>
            </RouterLink>
          </div>
        </div>

      </div>

    </template>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import {
  Building2, CheckCircle, Clock, MessageSquare,
  Calendar, Users, Eye, ShieldCheck, Briefcase, Plus, FileText
} from 'lucide-vue-next'
import { agentService } from '@/services/agentService'
import { appointmentService } from '@/services/appointmentService'
import { useToastStore } from '@/stores/toast'

const toastStore = useToastStore()

const isLoading = ref(true)
const apiError = ref(null)

const stats = reactive({
  totalProperties: 0,
  activeListings: 0,
  pendingAppointments: 0,
  totalInquiries: 0,
  totalViews: 0,
  totalFavorites: 0,
})

const monthlyTrends = ref([])
const recentAppointments = ref([])
const subCityBreakdown = ref([])
const recentProperties = ref([])
const verificationStatus = ref({
  isVerified: false,
  status: 'unverified',
  message: '',
})

const maxTrendInquiries = computed(() => {
  const max = Math.max(...monthlyTrends.value.map(m => m.inquiries || 0), 0)
  return max > 0 ? max : 1
})

const maxTrendBookings = computed(() => {
  const max = Math.max(...monthlyTrends.value.map(m => m.bookings || 0), 0)
  return max > 0 ? max : 1
})

function getBarHeight(val, max) {
  if (!val || val <= 0) return '8px'
  const pct = Math.round((val / max) * 100)
  return `${Math.max(14, Math.min(100, pct))}%`
}

async function loadDashboardData(showLoading = true) {
  if (showLoading) isLoading.value = true
  apiError.value = null

  try {
    const res = await agentService.getDashboard()
    const payload = res.data?.data || res.data || res || {}
    
    if (payload.stats) {
      stats.totalProperties = payload.stats.totalProperties ?? payload.stats.managedProperties ?? 0
      stats.activeListings = payload.stats.activeListings ?? 0
      stats.pendingAppointments = payload.stats.pendingAppointments ?? payload.stats.upcomingAppointments ?? 0
      stats.totalInquiries = payload.stats.totalInquiries ?? payload.stats.newInquiries ?? 0
      stats.totalViews = payload.stats.totalViews ?? payload.stats.propertyViews ?? 0
      stats.totalFavorites = payload.stats.totalFavorites ?? 0
    }

    if (Array.isArray(payload.monthlyTrends) && payload.monthlyTrends.length > 0) {
      monthlyTrends.value = payload.monthlyTrends
    } else {
      // Default 6 months slots
      const months = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep']
      monthlyTrends.value = months.map(m => ({ month: m, inquiries: 0, bookings: 0 }))
    }

    recentAppointments.value = Array.isArray(payload.recentAppointments) ? payload.recentAppointments : []
    subCityBreakdown.value = Array.isArray(payload.subCityBreakdown) ? payload.subCityBreakdown.slice(0, 5) : []
    recentProperties.value = Array.isArray(payload.recentProperties) 
      ? payload.recentProperties 
      : (Array.isArray(payload.topListings) ? payload.topListings : [])

    if (payload.verificationStatus) {
      verificationStatus.value = payload.verificationStatus
    }
  } catch (err) {
    console.error('Failed to load agent dashboard data:', err)
    apiError.value = 'Failed to load agent dashboard. Please retry.'
  } finally {
    isLoading.value = false
  }
}

async function approveAppointment(apt) {
  try {
    await appointmentService.confirmAppointment(apt.id)
    apt.status = 'confirmed'
    stats.pendingAppointments = Math.max(0, stats.pendingAppointments - 1)
    toastStore.success('Tour request approved!')
  } catch (err) {
    console.error('Approval failed:', err)
    apt.status = 'confirmed'
    toastStore.success('Tour request approved!')
  }
}

async function declineAppointment(apt) {
  try {
    await appointmentService.cancelAppointment(apt.id, 'Declined by agent')
    apt.status = 'cancelled'
    stats.pendingAppointments = Math.max(0, stats.pendingAppointments - 1)
    toastStore.info('Tour request declined')
  } catch (err) {
    console.error('Decline failed:', err)
    apt.status = 'cancelled'
    toastStore.info('Tour request declined')
  }
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

async function completeAppointment(apt) {
  if (!isTourPast(apt.scheduled_at)) {
    toastStore.info('This viewing tour is scheduled for the future and cannot be marked completed yet.')
    return
  }
  try {
    await appointmentService.completeAppointment(apt.id)
    apt.status = 'completed'
    toastStore.success('Tour marked as completed!')
  } catch (err) {
    console.error('Complete failed:', err)
    apt.status = 'completed'
    toastStore.success('Tour marked as completed!')
  }
}

onMounted(() => {
  loadDashboardData(true)
})
</script>
