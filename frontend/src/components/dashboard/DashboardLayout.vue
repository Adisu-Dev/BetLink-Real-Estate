<template>
  <div class="min-h-screen bg-gray-100 flex">
    <!-- Sidebar - Fixed with internal scrolling -->
    <DashboardSidebar 
      :role="user.role"
      :isOpen="isSidebarOpen"
      @toggle="toggleSidebar"
      @logout="showLogoutModal = true"
    />

    <!-- Main Content Area - Offset by sidebar width on desktop -->
    <div class="flex-1 flex flex-col min-w-0 lg:ml-64">
      <!-- Header - Sticky/Fixed at top with search -->
      <DashboardHeader 
        :title="title"
        :subtitle="subtitle"
        :user="user"
        :searchQuery="searchQuery"
        @toggle-sidebar="toggleSidebar"
        @search="handleSearch"
        @logout="showLogoutModal = true"
      />

      <!-- Page Content - Independent scrolling -->
      <main class="flex-1 overflow-x-hidden overflow-y-auto">
        <div class="p-4 lg:p-6">
          <slot />
        </div>
      </main>

      <!-- Footer - Fixed at bottom of main content -->
      <DashboardFooter />
    </div>

    <!-- Logout Confirmation Modal -->
    <LogoutModal
      :show="showLogoutModal"
      @confirm="confirmLogout"
      @cancel="showLogoutModal = false"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import DashboardSidebar from './DashboardSidebar.vue'
import DashboardHeader from './DashboardHeader.vue'
import DashboardFooter from './DashboardFooter.vue'
import LogoutModal from './LogoutModal.vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  user: {
    type: Object,
    required: true
  }
})

const router = useRouter()
const isSidebarOpen = ref(false)
const showLogoutModal = ref(false)
const searchQuery = ref('')

function toggleSidebar() {
  isSidebarOpen.value = !isSidebarOpen.value
}

function handleSearch(query) {
  searchQuery.value = query
  // Emit search event or handle search logic
  console.log('Search query:', query)
}

async function confirmLogout() {
  try {
    // Clear authentication data
    localStorage.removeItem('betlink_auth_token')
    localStorage.removeItem('betlink_user')
    
    // Close modal
    showLogoutModal.value = false
    
    // Navigate to login
    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
  }
}
</script>
