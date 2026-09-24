<template>
  <div class="space-y-6" @click="closeAllDropdowns">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
          {{ t('user_management_title', 'User Management') }}
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">
          Manage registered marketplace members (Property Owners, Agents, and Buyers).
        </p>
      </div>
      <button 
        @click="loadUsers"
        class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl font-bold text-xs sm:text-sm shadow-xs transition-colors self-start sm:self-auto cursor-pointer"
      >
        <span>🔄</span> {{ t('refresh', 'Refresh') }}
      </button>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 transition-colors">
      <div class="flex flex-wrap items-center gap-3 flex-1">
        <div class="relative flex-1 min-w-[200px] max-w-md">
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="t('search_users_placeholder', 'Search by name or email...')"
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

        <!-- Role Filter -->
        <select
          v-model="selectedRole"
          @change="handleFilterChange"
          class="px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 transition-colors cursor-pointer"
        >
          <option value="">{{ t('all_roles', 'All Roles') }}</option>
          <option value="owner">{{ t('dashboard.property_owner', 'Owner') }}</option>
          <option value="agent">{{ t('dashboard.real_estate_agent', 'Agent') }}</option>
          <option value="buyer">{{ t('dashboard.buyer_renter', 'Buyer') }}</option>
        </select>

        <!-- Status Filter -->
        <select
          v-model="selectedStatus"
          @change="handleFilterChange"
          class="px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 transition-colors cursor-pointer"
        >
          <option value="">{{ t('all_status', 'All Status') }}</option>
          <option value="active">{{ t('active', 'Active') }}</option>
          <option value="suspended">{{ t('suspended', 'Suspended') }}</option>
          <option value="banned">{{ t('banned', 'Banned') }}</option>
        </select>

        <!-- Sort Filter (Single word) -->
        <select
          v-model="selectedSort"
          @change="handleFilterChange"
          class="px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 transition-colors cursor-pointer"
        >
          <option value="">{{ t('sort', 'Sort') }}</option>
          <option value="latest">{{ t('latest', 'Latest') }}</option>
          <option value="oldest">{{ t('oldest', 'Oldest') }}</option>
          <option value="alphabetical">{{ t('alphabetical', 'Alphabetical') }}</option>
          <option value="reverse">{{ t('reverse', 'Reverse') }}</option>
        </select>
      </div>
    </div>

    <!-- Error State with Retry -->
    <div v-if="apiError" class="p-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-center space-y-3">
      <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ apiError }}</p>
      <button
        @click="loadUsers"
        class="px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-xl hover:bg-rose-700 transition-colors shadow-xs cursor-pointer"
      >
        {{ t('retry_loading', 'Retry Loading') }}
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-else-if="loading" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 p-6 space-y-4">
      <div v-for="n in 5" :key="n" class="animate-pulse flex items-center gap-4 py-3 border-b border-slate-100 dark:border-slate-800">
        <div class="w-10 h-10 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
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
      v-else-if="users.length === 0"
      :title="searchQuery ? `No users matching &quot;${searchQuery}&quot;` : t('no_users_found', 'No users found')"
      :description="searchQuery ? 'We could not find any accounts matching your search keyword. Please verify spelling or try another term.' : t('no_users_description', 'No users matched your search and filter criteria.')"
      :action-label="searchQuery || selectedRole || selectedStatus ? 'Clear Search &amp; Filters' : ''"
      @action="clearSearchAndFilters"
    />

    <!-- Users Table -->
    <div v-else class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 transition-colors">
      <div class="overflow-x-auto min-h-[300px]">
        <table class="w-full text-left">
          <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800">
            <tr>
              <th class="px-4 py-3.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-12 text-center">No</th>
              <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ t('name', 'Name') }}</th>
              <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ t('email', 'Email') }}</th>
              <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ t('role', 'Role') }}</th>
              <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ t('account_status', 'Account') }}</th>
              <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ t('kyc_verification', 'KYC Status') }}</th>
              <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ t('joined', 'Joined') }}</th>
              <th class="px-6 py-3.5 text-right text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ t('actions', 'Actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr v-for="(user, index) in displayedUsers" :key="user.id" class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
              <td class="px-4 py-4 whitespace-nowrap text-center text-xs font-mono font-bold text-slate-400 dark:text-slate-500">
                {{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center gap-3">
                  <img
                    v-if="resolveAvatar(user) && !user.avatar_error"
                    :src="resolveAvatar(user)"
                    :alt="user.name"
                    @error="user.avatar_error = true"
                    class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-700"
                  />
                  <div v-else class="w-9 h-9 rounded-full bg-slate-900 dark:bg-slate-800 text-white border border-slate-200 dark:border-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                    {{ (user.name || 'U').charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <p class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">{{ user.name }}</p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">{{ user.phone || '—' }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-slate-600 dark:text-slate-300 font-medium">{{ user.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-xs sm:text-sm">
                <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-full text-[11px] font-bold capitalize">
                  {{ getUserRole(user) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <StatusBadge :status="user.status ? user.status.toLowerCase() : 'active'" />
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <router-link 
                  v-if="getUserKycStatus(user) === 'pending'"
                  to="/admin/verifications"
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:hover:bg-amber-900/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800 transition-colors shadow-xs"
                  title="Click to review pending verification"
                >
                  <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                  <span>KYC Pending ⏳</span>
                </router-link>
                <span 
                  v-else-if="getUserKycStatus(user) === 'verified'"
                  class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800"
                >
                  <CheckCircle2 class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                  <span>Verified ✓</span>
                </span>
                <span 
                  v-else-if="getUserKycStatus(user) === 'rejected'"
                  class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200 dark:border-rose-800"
                >
                  <span>Rejected</span>
                </span>
                <span 
                  v-else
                  class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700"
                >
                  <span>Unverified</span>
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-slate-600 dark:text-slate-300 font-medium">{{ formatDate(user.created_at) }}</td>
              
              <!-- Actions 3-Dot Dropdown Menu -->
              <td class="px-6 py-4 whitespace-nowrap text-right text-xs sm:text-sm">
                <button
                  @click.stop="toggleDropdown(user, $event)"
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

    <!-- User Details Modal -->
    <div v-if="selectedUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
          <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ t('user_details', 'User Details') }}</h3>
          <button @click="selectedUserModal = null" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold">✕</button>
        </div>

        <div class="space-y-3 text-xs sm:text-sm">
          <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
            <span class="text-slate-500">{{ t('name', 'Name') }}:</span>
            <span class="font-bold text-slate-900 dark:text-white">{{ selectedUserModal.name }}</span>
          </div>
          <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
            <span class="text-slate-500">{{ t('email', 'Email') }}:</span>
            <span class="font-semibold text-slate-900 dark:text-white">{{ selectedUserModal.email }}</span>
          </div>
          <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
            <span class="text-slate-500">{{ t('phone_number', 'Phone') }}:</span>
            <span class="font-semibold text-slate-900 dark:text-white">{{ selectedUserModal.phone || 'N/A' }}</span>
          </div>
          <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
            <span class="text-slate-500">{{ t('role', 'Role') }}:</span>
            <span class="font-bold text-slate-900 dark:text-white capitalize">{{ getUserRole(selectedUserModal) }}</span>
          </div>
          <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
            <span class="text-slate-500">Properties Listed:</span>
            <span class="font-bold text-slate-900 dark:text-white">
              {{ selectedUserModal.properties_count ?? selectedUserModal.properties?.length ?? 0 }}
            </span>
          </div>
          <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
            <span class="text-slate-500">Booked Tours:</span>
            <span class="font-bold text-slate-900 dark:text-white">
              {{ selectedUserModal.appointments_as_visitor_count ?? selectedUserModal.appointments?.length ?? 0 }}
            </span>
          </div>
          <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
            <span class="text-slate-500">{{ t('status', 'Status') }}:</span>
            <StatusBadge :status="selectedUserModal.status || 'active'" />
          </div>
          <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
            <span class="text-slate-500">KYC Status:</span>
            <span 
              class="px-2.5 py-0.5 rounded-full text-xs font-bold capitalize"
              :class="{
                'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300': getUserKycStatus(selectedUserModal) === 'verified',
                'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300': getUserKycStatus(selectedUserModal) === 'pending',
                'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300': getUserKycStatus(selectedUserModal) === 'rejected',
                'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300': getUserKycStatus(selectedUserModal) === 'unverified'
              }"
            >
              {{ getUserKycStatus(selectedUserModal) === 'pending' ? 'KYC Pending ⏳' : getUserKycStatus(selectedUserModal) }}
            </span>
          </div>
          <div class="flex justify-between py-1.5">
            <span class="text-slate-500">{{ t('joined', 'Joined') }}:</span>
            <span class="text-slate-700 dark:text-slate-300 font-medium">{{ formatDate(selectedUserModal.created_at) }}</span>
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
          <button
            @click="selectedUserModal = null"
            class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 rounded-xl font-bold text-xs cursor-pointer"
          >
            {{ t('close', 'Close') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Suspend Confirmation Modal -->
    <ConfirmModal
      :is-open="showSuspendModal"
      :title="'Suspend User Account'"
      :message="`Are you sure you want to suspend ${userToSuspend?.name}? Their account access will be temporarily disabled until reactivated.`"
      :confirm-label="'Suspend User'"
      :danger="true"
      @confirm="confirmSuspendUser"
      @cancel="showSuspendModal = false; userToSuspend = null"
    />

    <!-- Ban Confirmation Modal -->
    <ConfirmModal
      :is-open="showBanModal"
      :title="t('ban_user_title', 'Ban User Account')"
      :message="t('ban_user_desc', `Are you sure you want to ban ${userToBan?.name}? They will immediately lose access to the platform.`)"
      :confirm-label="t('ban', 'Ban User')"
      :danger="true"
      @confirm="confirmBanUser"
      @cancel="showBanModal = false; userToBan = null"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      :is-open="showDeleteModal"
      :title="'Delete User Account'"
      :message="`Are you sure you want to permanently delete user ${userToDelete?.name}? This action cannot be undone.`"
      :confirm-label="'Delete User'"
      :danger="true"
      @confirm="confirmDeleteUser"
      @cancel="showDeleteModal = false; userToDelete = null"
    />
    <!-- Add User Modal -->
    <div v-if="showAddUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-slate-900 dark:bg-white text-white dark:text-slate-900 flex items-center justify-center font-black text-sm">
              +
            </div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Create Account</h3>
          </div>
          <button @click="closeAddUserModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold text-lg cursor-pointer">✕</button>
        </div>

        <form @submit.prevent="submitCreateUser" autocomplete="off" class="space-y-3.5">
          <input type="text" name="fakeusernameremembered" class="hidden" tabindex="-1" autocomplete="username" />
          <input type="password" name="fakepasswordremembered" class="hidden" tabindex="-1" autocomplete="current-password" />
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Full Name <span class="text-rose-500">*</span></label>
            <input
              v-model="newUserForm.name"
              @input="onNameInput"
              @keypress="onNameKeyPress"
              type="text"
              placeholder="e.g. Sara Mohammed"
              autocomplete="off"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border rounded-xl text-xs sm:text-sm font-medium focus:outline-none transition-colors"
              :class="userFormErrors.name ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700'"
            />
            <p v-if="userFormErrors.name" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ userFormErrors.name }}</p>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email Address <span class="text-rose-500">*</span></label>
            <input
              v-model="newUserForm.email"
              type="email"
              placeholder="e.g. sara@betlink.et"
              autocomplete="new-email"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border rounded-xl text-xs sm:text-sm font-medium focus:outline-none transition-colors"
              :class="userFormErrors.email ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700'"
            />
            <p v-if="userFormErrors.email" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ userFormErrors.email }}</p>
          </div>

          <!-- Phone -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Phone Number</label>
            <input
              v-model="newUserForm.phone"
              type="text"
              placeholder="e.g. +251911000000"
              autocomplete="off"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-medium focus:outline-none transition-colors"
            />
          </div>

          <!-- Role & Status  -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Account Role <span class="text-rose-500">*</span></label>
              <select
                v-model="newUserForm.role"
                class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-bold focus:outline-none transition-colors cursor-pointer"
              >
                <option value="buyer">Buyer / Renter</option>
                <option value="owner">Property Owner</option>
                <option value="agent">Real Estate Agent</option>
                <option value="admin">Administrator</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status</label>
              <select
                v-model="newUserForm.status"
                class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-bold focus:outline-none transition-colors cursor-pointer"
              >
                <option value="active">Active</option>
                <option value="suspended">Suspended</option>
              </select>
            </div>
          </div>

          <!-- Password -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Password <span class="text-rose-500">*</span></label>
            <input
              v-model="newUserForm.password"
              type="password"
              placeholder="Minimum 6 characters"
              autocomplete="new-password"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border rounded-xl text-xs sm:text-sm font-medium focus:outline-none transition-colors"
              :class="userFormErrors.password ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700'"
            />
            <p v-if="userFormErrors.password" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ userFormErrors.password }}</p>
          </div>

          <!-- Confirm Password -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Confirm Password <span class="text-rose-500">*</span></label>
            <input
              v-model="newUserForm.confirmPassword"
              type="password"
              placeholder="Confirm password"
              autocomplete="new-password"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border rounded-xl text-xs sm:text-sm font-medium focus:outline-none transition-colors"
              :class="userFormErrors.confirmPassword ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700'"
            />
            <p v-if="userFormErrors.confirmPassword" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ userFormErrors.confirmPassword }}</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
            <button
              type="button"
              @click="closeAddUserModal"
              class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl font-bold text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isCreatingUser"
              class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700 rounded-xl font-bold text-xs shadow-xs transition-colors disabled:opacity-50 flex items-center gap-2 cursor-pointer"
            >
              <span v-if="isCreatingUser" class="animate-spin">⌛</span>
              <span>Create Account</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Floating Teleported Dropdown -->
    <Teleport to="body">
      <div v-if="activeDropdown" class="fixed inset-0 z-[99998]" @click="activeDropdown = null">
        <div
          :style="{ top: activeDropdown.top, left: activeDropdown.left }"
          class="fixed w-40 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 py-1.5 z-[99999] animate-in fade-in zoom-in-95 text-left"
          @click.stop
        >
          <button
            @click="viewUserDetails(activeDropdown.user); activeDropdown = null"
            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <Eye class="w-3.5 h-3.5 text-slate-500" />
            <span>View Details</span>
          </button>
          <button
            v-if="activeDropdown.user.status !== 'active'"
            @click="activateUser(activeDropdown.user); activeDropdown = null"
            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <CheckCircle2 class="w-3.5 h-3.5" />
            <span>Set Active</span>
          </button>
          <button
            v-if="activeDropdown.user.status === 'active'"
            @click="promptSuspendUser(activeDropdown.user); activeDropdown = null"
            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-amber-600 dark:text-amber-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <ShieldAlert class="w-3.5 h-3.5" />
            <span>Suspend</span>
          </button>
          <button
            v-if="activeDropdown.user.status !== 'banned'"
            @click="promptBanUser(activeDropdown.user); activeDropdown = null"
            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-orange-600 dark:text-orange-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <Ban class="w-3.5 h-3.5" />
            <span>Ban User</span>
          </button>
          <button
            @click="promptDeleteUser(activeDropdown.user); activeDropdown = null"
            class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors border-t border-slate-100 dark:border-slate-800 mt-1 pt-1.5 cursor-pointer"
          >
            <Trash2 class="w-3.5 h-3.5" />
            <span>Delete User</span>
          </button>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import {
  MoreVertical,
  Eye,
  Ban,
  CheckCircle2,
  Trash2,
  ShieldAlert,
  X
} from 'lucide-vue-next'
import { adminService } from '../../services/adminService'
import { useLanguage } from '../../composables/useLanguage'
import { useToast } from '../../composables/useToast'
import StatusBadge from '../../components/dashboard/StatusBadge.vue'
import EmptyState from '../../components/dashboard/EmptyState.vue'
import BasePagination from '../../components/common/BasePagination.vue'
import ConfirmModal from '../../components/dashboard/ConfirmModal.vue'

const { t } = useLanguage()
const { success, error } = useToast()

const users = ref([])
const loading = ref(true)
const apiError = ref(null)

const searchQuery = ref('')
const selectedRole = ref('')
const selectedStatus = ref('')
const selectedSort = ref('')

const activeDropdown = ref(null)
const selectedUserModal = ref(null)
const showSuspendModal = ref(false)
const userToSuspend = ref(null)
const showBanModal = ref(false)
const userToBan = ref(null)
const showDeleteModal = ref(false)
const userToDelete = ref(null)

// Add User
const showAddUserModal = ref(false)
const isCreatingUser = ref(false)
const userFormErrors = reactive({
  name: '',
  email: '',
  password: '',
  confirmPassword: '',
})
const newUserForm = reactive({
  name: '',
  email: '',
  phone: '',
  role: 'buyer',
  status: 'active',
  password: '',
  confirmPassword: '',
})

const onNameKeyPress = (e) => {
  if (/[0-9]/.test(e.key)) {
    e.preventDefault()
  }
}

const onNameInput = (e) => {
  newUserForm.name = (e.target.value || '').replace(/[0-9]/g, '')
  if (userFormErrors.name) {
    if (!newUserForm.name.trim()) {
      userFormErrors.name = 'Full Name is required.'
    } else if (newUserForm.name.trim().length < 2) {
      userFormErrors.name = 'Name must be at least 2 characters.'
    } else {
      userFormErrors.name = ''
    }
  }
}

function resetNewUserForm() {
  newUserForm.name = ''
  newUserForm.email = ''
  newUserForm.phone = ''
  newUserForm.role = 'buyer'
  newUserForm.status = 'active'
  newUserForm.password = ''
  newUserForm.confirmPassword = ''
  userFormErrors.name = ''
  userFormErrors.email = ''
  userFormErrors.password = ''
  userFormErrors.confirmPassword = ''
}

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
})

const displayedUsers = computed(() => {
  let list = users.value
  if (selectedRole.value !== 'admin') {
    list = list.filter(u => getUserRole(u).toLowerCase() !== 'admin')
  }
  const q = searchQuery.value.trim().toLowerCase()
  if (q) {
    list = [...list].sort((a, b) => {
      const aName = (a.name || '').toLowerCase()
      const bName = (b.name || '').toLowerCase()
      const aStarts = aName.startsWith(q) ? 1 : aName.includes(' ' + q) ? 2 : 3
      const bStarts = bName.startsWith(q) ? 1 : bName.includes(' ' + q) ? 2 : 3
      if (aStarts !== bStarts) return aStarts - bStarts
      return 0
    })
  } else if (selectedSort.value) {
    list = [...list].sort((a, b) => {
      if (selectedSort.value === 'alphabetical') {
        return (a.name || '').localeCompare(b.name || '')
      }
      if (selectedSort.value === 'reverse') {
        return (b.name || '').localeCompare(a.name || '')
      }
      if (selectedSort.value === 'oldest') {
        return new Date(a.created_at || 0) - new Date(b.created_at || 0)
      }
      if (selectedSort.value === 'latest') {
        return new Date(b.created_at || 0) - new Date(a.created_at || 0)
      }
      return 0
    })
  }
  return list
})

function toggleDropdown(user, event) {
  if (activeDropdown.value && activeDropdown.value.id === user.id) {
    activeDropdown.value = null
    return
  }
  const rect = event.currentTarget.getBoundingClientRect()
  const menuWidth = 160
  const menuHeight = 150
  let top = rect.bottom + 4
  let left = rect.right - menuWidth

  if (top + menuHeight > window.innerHeight) {
    top = rect.top - menuHeight - 4
  }

  activeDropdown.value = {
    id: user.id,
    user,
    top: `${Math.max(10, top)}px`,
    left: `${Math.max(10, left)}px`
  }
}

function closeAllDropdowns() {
  activeDropdown.value = null
}

function resolveAvatar(user) {
  if (!user) return null
  const av = user.avatar_url || user.avatar || user.profile?.avatar
  if (!av || typeof av !== 'string') return null
  if (av.startsWith('data:') || av.startsWith('http://') || av.startsWith('https://')) return av
  if (av.startsWith('/')) return `http://127.0.0.1:8000${av}`
  return `http://127.0.0.1:8000/storage/${av.replace(/^storage\//, '')}`
}

const loadUsers = async () => {
  loading.value = true
  apiError.value = null

  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
    }
    if (searchQuery.value) params.q = searchQuery.value
    if (selectedRole.value) params.role = selectedRole.value
    if (selectedStatus.value) params.status = selectedStatus.value
    if (selectedSort.value) params.sort = selectedSort.value

    const res = await adminService.getUsers(params)
    
    if (res && res.data) {
      if (Array.isArray(res.data)) {
        users.value = res.data
      } else if (Array.isArray(res.data.data)) {
        users.value = res.data.data
      } else {
        users.value = []
      }
    } else if (Array.isArray(res)) {
      users.value = res
    } else {
      users.value = []
    }

    const meta = res?.meta || res?.data?.meta
    if (meta) {
      pagination.current_page = meta.current_page || 1
      pagination.last_page = meta.last_page || 1
      pagination.total = meta.total || users.value.length
      pagination.per_page = meta.per_page || pagination.per_page
    } else {
      pagination.total = users.value.length
      pagination.last_page = 1
    }
  } catch (err) {
    console.error('Failed to load users:', err)
    apiError.value = err.message || 'Failed to load user accounts.'
  } finally {
    loading.value = false
  }
}

let searchDebounceTimer = null
watch(searchQuery, (newVal) => {
  clearTimeout(searchDebounceTimer)
  if (!newVal) {
    pagination.current_page = 1
    loadUsers()
    return
  }
  searchDebounceTimer = setTimeout(() => {
    pagination.current_page = 1
    loadUsers()
  }, 150)
})

function clearSearchAndFilters() {
  searchQuery.value = ''
  selectedRole.value = ''
  selectedStatus.value = ''
  selectedSort.value = ''
  pagination.current_page = 1
  loadUsers()
}

const handleSearch = () => {
  clearTimeout(searchDebounceTimer)
  pagination.current_page = 1
  loadUsers()
}

const handleFilterChange = () => {
  pagination.current_page = 1
  loadUsers()
}

const handlePageChange = (page) => {
  pagination.current_page = page
  loadUsers()
}

const getUserRole = (user) => {
  if (Array.isArray(user.roles) && user.roles.length > 0) {
    const firstRole = user.roles[0]
    return typeof firstRole === 'string' ? firstRole : (firstRole?.name || 'Buyer')
  }
  return user.role || 'Buyer'
}

const getUserKycStatus = (user) => {
  if (!user) return 'unverified'
  if (user.profile?.is_verified || user.is_verified) {
    return 'verified'
  }
  const requests = user.verification_requests || user.verificationRequests || []
  if (Array.isArray(requests) && requests.length > 0) {
    if (requests.some(r => r.status === 'pending')) {
      return 'pending'
    }
    if (requests.some(r => r.status === 'approved')) {
      return 'verified'
    }
    if (requests.some(r => r.status === 'rejected')) {
      return 'rejected'
    }
  }
  return 'unverified'
}

const viewUserDetails = (user) => {
  selectedUserModal.value = user
}

const openAddUserModal = () => {
  resetNewUserForm()
  showAddUserModal.value = true
  setTimeout(() => {
    newUserForm.name = ''
    newUserForm.email = ''
    newUserForm.phone = ''
    newUserForm.password = ''
    newUserForm.confirmPassword = ''
  }, 50)
}

const closeAddUserModal = () => {
  showAddUserModal.value = false
  resetNewUserForm()
}

const submitCreateUser = async () => {
  userFormErrors.name = ''
  userFormErrors.email = ''
  userFormErrors.password = ''
  userFormErrors.confirmPassword = ''

  let hasError = false
  if (!newUserForm.name || !newUserForm.name.trim()) {
    userFormErrors.name = 'Full Name is required.'
    hasError = true
  } else if (/\d/.test(newUserForm.name)) {
    userFormErrors.name = 'Name cannot contain numbers.'
    hasError = true
  } else if (newUserForm.name.trim().length < 2) {
    userFormErrors.name = 'Name must be at least 2 characters.'
    hasError = true
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  const trimmedEmail = (newUserForm.email || '').trim().toLowerCase()
  if (!trimmedEmail) {
    userFormErrors.email = 'Email address is required.'
    hasError = true
  } else if (!emailRegex.test(trimmedEmail)) {
    userFormErrors.email = 'Please enter a valid email address.'
    hasError = true
  } else if (users.value.some(u => (u.email || '').toLowerCase() === trimmedEmail)) {
    userFormErrors.email = 'This email is already registered.'
    hasError = true
  }

  if (!newUserForm.password || newUserForm.password.length < 6) {
    userFormErrors.password = 'Password must be at least 6 characters.'
    hasError = true
  }

  if (!newUserForm.confirmPassword) {
    userFormErrors.confirmPassword = 'Confirm Password is required.'
    hasError = true
  } else if (newUserForm.password !== newUserForm.confirmPassword) {
    userFormErrors.confirmPassword = 'Passwords do not match.'
    hasError = true
  }

  if (hasError) return

  isCreatingUser.value = true
  try {
    await adminService.createUser({
      name: newUserForm.name.trim(),
      email: trimmedEmail,
      phone: newUserForm.phone.trim() || null,
      role: newUserForm.role,
      status: newUserForm.status,
      password: newUserForm.password,
    })

    success('Account Created', `User account for ${newUserForm.name} created successfully!`)
    closeAddUserModal()
    pagination.current_page = 1
    await loadUsers()
  } catch (err) {
    console.error('Failed to create user:', err)
    if (err.errors) {
      if (err.errors.email) {
        userFormErrors.email = Array.isArray(err.errors.email) ? err.errors.email[0] : err.errors.email
      }
      if (err.errors.name) {
        userFormErrors.name = Array.isArray(err.errors.name) ? err.errors.name[0] : err.errors.name
      }
      if (err.errors.password) {
        userFormErrors.password = Array.isArray(err.errors.password) ? err.errors.password[0] : err.errors.password
      }
    }
    error('Creation Failed', err.message || 'Could not create user account.')
  } finally {
    isCreatingUser.value = false
  }
}

const promptSuspendUser = (user) => {
  userToSuspend.value = user
  showSuspendModal.value = true
}

const confirmSuspendUser = async () => {
  if (!userToSuspend.value) return
  try {
    await adminService.suspendUser(userToSuspend.value.id)
    success('Suspended', `User ${userToSuspend.value.name} has been suspended.`)
    showSuspendModal.value = false
    userToSuspend.value = null
    await loadUsers()
  } catch (err) {
    console.error('Failed to suspend user:', err)
    error('Error', err.message || 'Could not suspend user.')
  } finally {
    showSuspendModal.value = false
  }
}

const promptBanUser = (user) => {
  userToBan.value = user
  showBanModal.value = true
}

const confirmBanUser = async () => {
  if (!userToBan.value) return
  try {
    await adminService.banUser(userToBan.value.id)
    success(t('banned', 'Banned'), `User ${userToBan.value.name} has been banned.`)
    showBanModal.value = false
    userToBan.value = null
    await loadUsers()
  } catch (err) {
    console.error('Failed to ban user:', err)
    error(t('error', 'Error'), err.message || 'Could not ban user.')
  } finally {
    showBanModal.value = false
  }
}

const promptDeleteUser = (user) => {
  userToDelete.value = user
  showDeleteModal.value = true
}

const confirmDeleteUser = async () => {
  if (!userToDelete.value) return
  try {
    await adminService.deleteUser(userToDelete.value.id)
    success('Deleted', `User ${userToDelete.value.name} has been deleted.`)
    showDeleteModal.value = false
    userToDelete.value = null
    await loadUsers()
  } catch (err) {
    console.error('Failed to delete user:', err)
    error('Error', err.message || 'Could not delete user.')
  } finally {
    showDeleteModal.value = false
  }
}

const activateUser = async (user) => {
  try {
    await adminService.activateUser(user.id)
    success(t('activated', 'Activated'), `User ${user.name} has been activated.`)
    await loadUsers()
  } catch (err) {
    console.error('Failed to activate user:', err)
    error(t('error', 'Error'), err.message || 'Could not activate user.')
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

onMounted(() => {
  loadUsers()
})
</script>
