<template>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    
    <!-- Left Panel: Registration Form (Master) -->
    <div class="lg:col-span-1 bg-white rounded-lg border border-gray-200 p-5 h-fit sticky top-20">
      <h2 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
        </svg>
        Register New User
      </h2>
      <form @submit.prevent="submitRegistration" class="space-y-3">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Full Name *</label>
          <input
            v-model="form.name"
            type="text"
            placeholder="John Doe"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-emerald-500"
            required
          />
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Email *</label>
          <input
            v-model="form.email"
            type="email"
            placeholder="john@example.com"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-emerald-500"
            required
          />
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Phone *</label>
          <input
            v-model="form.phone"
            type="tel"
            placeholder="+251 911 123456"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-emerald-500"
            required
          />
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Role *</label>
          <select v-model="form.role" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-emerald-500" required>
            <option value="">Select Role</option>
            <option value="buyer">Buyer</option>
            <option value="owner">Property Owner</option>
            <option value="agent">Real Estate Agent</option>
            <option value="admin">Administrator</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Password *</label>
          <input
            v-model="form.password"
            type="password"
            placeholder="••••••••"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-emerald-500"
            required
          />
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Confirm Password *</label>
          <input
            v-model="form.confirm_password"
            type="password"
            placeholder="••••••••"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-emerald-500"
            required
          />
        </div>
        <button
          type="submit"
          class="w-full px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-colors"
        >
          Register User
        </button>
      </form>
    </div>

    <!-- Right Panel: Member Directory (Detail) -->
    <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 p-5">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
          <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
          Member Directory
        </h2>
        <span class="text-xs text-gray-500">{{ filteredUsers.length }} users</span>
      </div>

      <!-- Search & Filter -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by name, email..."
          class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-emerald-500"
        />
        <select v-model="filterRole" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-emerald-500">
          <option value="">All Roles</option>
          <option value="buyer">Buyer</option>
          <option value="owner">Owner</option>
          <option value="agent">Agent</option>
          <option value="admin">Admin</option>
        </select>
        <select v-model="filterStatus" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-emerald-500">
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="blocked">Blocked</option>
        </select>
      </div>

      <!-- User Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">User</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Email</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Role</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Status</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Joined</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="user in paginatedUsers" :key="user.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <img :src="user.avatar" :alt="user.name" class="w-8 h-8 rounded-full object-cover" />
                  <div>
                    <p class="text-sm font-semibold text-gray-900">{{ user.name }}</p>
                    <p class="text-xs text-gray-500">+251 {{ user.phone }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 text-sm text-gray-700">{{ user.email }}</td>
              <td class="px-4 py-3">
                <span :class="getRoleBadge(user.role)" class="px-2 py-0.5 rounded text-xs font-bold">
                  {{ getRoleLabel(user.role) }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span :class="user.is_blocked ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'" class="px-2 py-0.5 rounded text-xs font-bold">
                  {{ user.is_blocked ? 'Blocked' : 'Active' }}
                </span>
              </td>
              <td class="px-4 py-3 text-xs text-gray-600">{{ user.created_at }}</td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <button
                    @click="viewUserDetails(user)"
                    class="px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded font-medium"
                  >
                    View
                  </button>
                  <button
                    @click="toggleBlock(user.id)"
                    :class="user.is_blocked ? 'text-emerald-600 hover:bg-emerald-50' : 'text-red-600 hover:bg-red-50'"
                    class="px-2 py-1 text-xs rounded font-medium"
                  >
                    {{ user.is_blocked ? 'Unblock' : 'Block' }}
                  </button>
                  <button
                    @click="promptDeleteUser(user)"
                    class="px-2 py-1 text-xs text-red-600 hover:bg-red-50 rounded font-medium"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
        <span class="text-xs text-gray-600">{{ filteredUsers.length }} results</span>
        <div class="flex items-center gap-2">
          <button
            @click="currentPage = Math.max(1, currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-2 py-1 border border-gray-300 rounded text-xs hover:bg-gray-100 disabled:opacity-50"
          >
            ← Prev
          </button>
          <span class="text-xs text-gray-600 px-2">Page {{ currentPage }} of {{ totalPages }}</span>
          <button
            @click="currentPage = Math.min(totalPages, currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-2 py-1 border border-gray-300 rounded text-xs hover:bg-gray-100 disabled:opacity-50"
          >
            Next →
          </button>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <ConfirmModal
        :isOpen="showDeleteModal"
        title="Delete User Account"
        :message="`Are you sure you want to delete '${userToDelete?.name || 'this user'}'?`"
        confirmLabel="Delete User"
        cancelLabel="Cancel"
        :danger="true"
        @confirm="confirmDeleteUser"
        @cancel="showDeleteModal = false; userToDelete = null"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'
import { useToastStore } from '@/stores/toast'

const toastStore = useToastStore()
const showDeleteModal = ref(false)
const userToDelete = ref(null)

// Registration form
const form = ref({
  name: '',
  email: '',
  phone: '',
  role: '',
  password: '',
  confirm_password: '',
})

// Sample users
const users = ref([
  { id: 1, name: 'Abebe Kebede', email: 'abebe@example.com', phone: '911 123 456', role: 'buyer', is_blocked: false, avatar: 'https://ui-avatars.com/api/?name=Abebe+Kebede&background=3b82f6&color=fff&size=64', created_at: '2024-01-15' },
  { id: 2, name: 'Sara Mohammed', email: 'sara@example.com', phone: '911 234 567', role: 'owner', is_blocked: false, avatar: 'https://ui-avatars.com/api/?name=Sara+Mohammed&background=1e293b&color=fff&size=64', created_at: '2024-01-20' },
  { id: 3, name: 'Daniel Tesfaye', email: 'daniel@example.com', phone: '911 345 678', role: 'agent', is_blocked: false, avatar: 'https://ui-avatars.com/api/?name=Daniel+Tesfaye&background=8b5cf6&color=fff&size=64', created_at: '2024-02-01' },
  { id: 4, name: 'Hiwot Alemu', email: 'hiwot@example.com', phone: '911 456 789', role: 'buyer', is_blocked: true, avatar: 'https://ui-avatars.com/api/?name=Hiwot+Alemu&background=ec4899&color=fff&size=64', created_at: '2024-02-05' },
  { id: 5, name: 'Yohannes Bekele', email: 'yohannes@example.com', phone: '911 567 890', role: 'owner', is_blocked: false, avatar: 'https://ui-avatars.com/api/?name=Yohannes+Bekele&background=f59e0b&color=fff&size=64', created_at: '2024-02-10' },
  { id: 6, name: 'Meron Tadesse', email: 'meron@example.com', phone: '911 678 901', role: 'agent', is_blocked: false, avatar: 'https://ui-avatars.com/api/?name=Meron+Tadesse&background=06b6d4&color=fff&size=64', created_at: '2024-02-15' },
])

// Filters
const searchQuery = ref('')
const filterRole = ref('')
const filterStatus = ref('')
const currentPage = ref(1)
const pageSize = 5

const filteredUsers = computed(() => {
  let result = users.value
  
  if (searchQuery.value) {
    result = result.filter(u => u.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || u.email.toLowerCase().includes(searchQuery.value.toLowerCase()))
  }
  if (filterRole.value) {
    result = result.filter(u => u.role === filterRole.value)
  }
  if (filterStatus.value) {
    result = result.filter(u => (filterStatus.value === 'active' && !u.is_blocked) || (filterStatus.value === 'blocked' && u.is_blocked))
  }
  
  return result
})

const totalPages = computed(() => Math.ceil(filteredUsers.value.length / pageSize))

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * pageSize
  return filteredUsers.value.slice(start, start + pageSize)
})

const getRoleBadge = (role) => {
  const badges = {
    'buyer': 'bg-blue-100 text-blue-700',
    'owner': 'bg-emerald-100 text-emerald-700',
    'agent': 'bg-purple-100 text-purple-700',
    'admin': 'bg-red-100 text-red-700',
  }
  return badges[role] || 'bg-gray-100 text-gray-700'
}

const getRoleLabel = (role) => {
  const labels = {
    'buyer': 'Buyer',
    'owner': 'Owner',
    'agent': 'Agent',
    'admin': 'Admin',
  }
  return labels[role] || role
}

const submitRegistration = () => {
  if (form.value.password !== form.value.confirm_password) {
    toastStore.error('Passwords do not match!')
    return
  }
  
  const newUser = {
    id: Math.max(...users.value.map(u => u.id), 0) + 1,
    name: form.value.name,
    email: form.value.email,
    phone: form.value.phone.replace(/\D/g, '').slice(-9),
    role: form.value.role,
    is_blocked: false,
    avatar: `https://ui-avatars.com/api/?name=${encodeURIComponent(form.value.name)}&background=random&color=fff&size=64`,
    created_at: new Date().toISOString().split('T')[0],
  }
  
  users.value.unshift(newUser)
  toastStore.success('User created!')
  
  form.value = { name: '', email: '', phone: '', role: '', password: '', confirm_password: '' }
}

const viewUserDetails = (user) => {
  toastStore.info(`Viewing ${user.name}`)
}

const toggleBlock = (userId) => {
  const user = users.value.find(u => u.id === userId)
  if (user) {
    user.is_blocked = !user.is_blocked
    toastStore.success(user.is_blocked ? 'User blocked' : 'User unblocked')
  }
}

const promptDeleteUser = (user) => {
  userToDelete.value = user
  showDeleteModal.value = true
}

const confirmDeleteUser = () => {
  if (userToDelete.value) {
    users.value = users.value.filter(u => u.id !== userToDelete.value.id)
    toastStore.success('Deleted successfully!')
  }
  showDeleteModal.value = false
  userToDelete.value = null
}
</script>
