<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Property Inquiries & Inbox</h1>
      </div>
      <router-link
        to="/owner/messages"
        class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700/80 text-xs font-bold rounded-xl transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer"
      >
        <MessageSquare class="w-3.5 h-3.5 text-slate-500" />
        <span>Messages</span>
      </router-link>
    </div>

    <!-- Error State with Retry -->
    <div v-if="apiError" class="p-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-center space-y-3">
      <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ apiError }}</p>
      <button
        @click="loadInquiries"
        class="px-4 py-2 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-slate-800 transition-colors cursor-pointer"
      >
        Retry Loading
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-else-if="loading" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 space-y-4">
      <div v-for="n in 3" :key="n" class="animate-pulse flex items-center gap-4 py-3 border-b border-slate-100 dark:border-slate-800">
        <div class="w-12 h-12 bg-slate-200 dark:bg-slate-800 rounded-full shrink-0"></div>
        <div class="flex-1 space-y-2">
          <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-1/3"></div>
          <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-2/3"></div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="inquiries.length === 0"
      class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-12 text-center"
    >
      <MessageSquare class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
      <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">No inquiries received yet</h3>
      <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
        When clients message or inquire about your property listings, conversation threads will appear here.
      </p>
    </div>

    <!-- Inquiries / Conversations List -->
    <div v-else class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs divide-y divide-slate-100 dark:divide-slate-800 overflow-hidden">
      <div
        v-for="conv in inquiries"
        :key="conv.id"
        class="p-4 sm:p-5 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4"
      >
        <div class="flex items-start gap-3.5 min-w-0">
          <div class="w-10 h-10 rounded-full bg-slate-900 dark:bg-slate-800 text-white font-extrabold flex items-center justify-center text-xs shrink-0 shadow-xs">
            {{ (getParticipantName(conv)).charAt(0).toUpperCase() }}
          </div>

          <div class="min-w-0">
            <div class="flex items-center gap-2">
              <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white truncate">
                {{ getParticipantName(conv) }}
              </h3>
              <span class="text-[10px] text-slate-400">• {{ formatDate(conv.updated_at) }}</span>
            </div>

            <p class="text-xs text-slate-700 dark:text-slate-300 font-semibold truncate mt-0.5">
              Re: {{ conv.property?.title || 'Property Inquiry' }}
            </p>

            <p class="text-xs sm:text-[13px] text-slate-600 dark:text-slate-300 mt-1 line-clamp-2 leading-relaxed">
              {{ conv.latest_message?.body || conv.messages?.[0]?.body || 'Conversation thread started' }}
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2 sm:self-center shrink-0">
          <router-link
            :to="`/owner/messages?conversation=${conv.id}`"
            class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700/80 text-xs font-bold rounded-xl transition-all shadow-2xs flex items-center gap-1 cursor-pointer"
          >
            <span>Open Chat</span>
            <ArrowRight class="w-3.5 h-3.5 text-slate-500" />
          </router-link>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.total > pagination.per_page" class="px-6 py-3 bg-slate-50/80 dark:bg-slate-800/80 border-t border-slate-100 dark:border-slate-800">
        <BasePagination
          :current-page="pagination.current_page"
          :total-pages="pagination.last_page"
          :total-items="pagination.total"
          :per-page="pagination.per_page"
          @change="handlePageChange"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { MessageSquare, ArrowRight } from 'lucide-vue-next'
import { conversationService } from '@/services/conversationService'
import BasePagination from '@/components/common/BasePagination.vue'

const inquiries = ref([])
const loading = ref(true)
const apiError = ref(null)

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 15,
})

async function loadInquiries(page = 1) {
  loading.value = true
  apiError.value = null

  try {
    const res = await conversationService.getConversations({ page, per_page: pagination.per_page })
    const payload = res?.data || {}
    inquiries.value = payload.data || payload || []
    
    pagination.current_page = payload.current_page || 1
    pagination.last_page = payload.last_page || 1
    pagination.total = payload.total || inquiries.value.length
  } catch (err) {
    console.error('Failed to load inquiries:', err)
    apiError.value = 'Failed to load inquiries. Please check your connection and try again.'
  } finally {
    loading.value = false
  }
}

function handlePageChange(newPage) {
  loadInquiries(newPage)
}

function getParticipantName(conv) {
  if (conv.other_user?.name) return conv.other_user.name
  if (conv.participants && Array.isArray(conv.participants)) {
    const other = conv.participants.find(p => p.role !== 'owner' && p.role !== 'Property Owner') || conv.participants[0]
    if (other?.name) return other.name
  }
  return 'Prospective Buyer'
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
  } catch {
    return dateStr
  }
}

onMounted(() => {
  loadInquiries()
})
</script>
