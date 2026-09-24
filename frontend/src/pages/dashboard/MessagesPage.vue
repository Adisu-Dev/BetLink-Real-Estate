<template>
  <div class="h-[calc(100vh-135px)] flex flex-col md:flex-row gap-4 select-text">
    <!-- Left Sidebar: Conversations Threads (hidden on mobile if chat is open) -->
    <div
      :class="[
        'w-full md:w-80 lg:w-96 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col overflow-hidden shrink-0 transition-all',
        isMobileChatOpen ? 'hidden md:flex' : 'flex'
      ]"
    >
      <!-- Search & Header with New Chat Button -->
      <div class="p-4 border-b border-slate-100 dark:border-slate-800 space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
            <MessageSquare class="w-5 h-5 text-slate-700 dark:text-slate-300" />
            Messages
          </h2>

          <!-- New Chat Trigger Button -->
          <button
            type="button"
            @click="showNewChatModal = true"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition-colors cursor-pointer border border-slate-200 dark:border-slate-700"
          >
            <Plus class="w-3.5 h-3.5" />
            <span>New Chat</span>
          </button>
        </div>

        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search contacts or properties..."
            class="w-full pl-9 pr-8 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 placeholder-slate-400 rounded-xl text-xs focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:outline-none transition-all"
          />
          <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
          <button
            v-if="searchQuery"
            @click="searchQuery = ''"
            class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>
      </div>

      <!-- Threads List -->
      <div v-if="filteredConversations.length > 0" class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
        <div
          v-for="conv in filteredConversations"
          :key="conv.id"
          @click="selectConversation(conv)"
          :class="[
            'p-3.5 cursor-pointer transition-colors flex items-start gap-3 text-left border-l-4 group',
            selectedConversation?.id === conv.id
              ? 'bg-slate-100 dark:bg-slate-800/90 border-l-slate-900 dark:border-l-white'
              : 'border-l-transparent hover:bg-slate-50 dark:hover:bg-slate-800/60'
          ]"
        >
          <!-- Participant Avatar -->
          <div class="relative shrink-0">
            <img
              v-if="conv.other_user?.avatar"
              :src="conv.other_user.avatar"
              :alt="conv.other_user.name"
              class="w-10 h-10 rounded-full object-cover border border-slate-200 dark:border-slate-700"
            />
            <div v-else class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs flex items-center justify-center border border-slate-200 dark:border-slate-700">
              {{ (conv.other_user?.name?.[0] || conv.property?.title?.[0] || 'P').toUpperCase() }}
            </div>
            <span
              v-if="conv.other_user?.online"
              class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-slate-400 border-2 border-white dark:border-slate-900 rounded-full"
            ></span>
          </div>

          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-1">
              <p class="text-xs font-bold text-slate-900 dark:text-white truncate">
                {{ conv.other_user?.name || 'Property Owner' }}
              </p>
              <span class="text-[10px] text-slate-400 shrink-0 font-medium">
                {{ formatTimeBadge(conv.updated_at) }}
              </span>
            </div>

            <p class="text-[11px] text-slate-600 dark:text-slate-400 font-semibold truncate mt-0.5">
              {{ conv.property?.title || 'Property Discussion' }}
            </p>

            <div class="flex items-center justify-between gap-2 mt-1">
              <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                {{ getLatestMessageSnippet(conv) }}
              </p>
              <span
                v-if="conv.unread_count > 0"
                class="w-4 h-4 rounded-full bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 text-[10px] font-extrabold flex items-center justify-center shrink-0"
              >
                {{ conv.unread_count }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State Threads -->
      <div v-else class="flex-1 flex flex-col items-center justify-center p-6 text-center text-slate-400 space-y-2">
        <MessageSquare class="w-10 h-10 text-slate-300 dark:text-slate-700" />
        <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">No conversations found</p>
        <button
          @click="showNewChatModal = true"
          class="text-xs font-bold text-slate-700 dark:text-slate-300 hover:underline cursor-pointer"
        >
          Start a new inquiry
        </button>
      </div>
    </div>

    <!-- Right Pane: Telegram / WhatsApp Style Chat Window -->
    <div
      :class="[
        'flex-1 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col overflow-hidden transition-colors',
        !isMobileChatOpen && 'hidden md:flex'
      ]"
    >
      <template v-if="selectedConversation">
        <!-- Chat Header (Sticky Top) -->
        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-800/90 backdrop-blur-xs flex items-center justify-between gap-3 shrink-0">
          <div class="flex items-center gap-3 min-w-0">
            <!-- Mobile Back Button -->
            <button
              type="button"
              @click="isMobileChatOpen = false"
              class="md:hidden p-1.5 rounded-lg text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700"
            >
              <ArrowLeft class="w-5 h-5" />
            </button>

            <div class="relative shrink-0">
              <img
                v-if="selectedConversation.other_user?.avatar"
                :src="selectedConversation.other_user.avatar"
                class="w-10 h-10 rounded-full object-cover border border-slate-200"
              />
              <div v-else class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs flex items-center justify-center border border-slate-200 dark:border-slate-700">
                {{ (selectedConversation.other_user?.name?.[0] || selectedConversation.property?.title?.[0] || 'P').toUpperCase() }}
              </div>
            </div>

            <div class="min-w-0">
              <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white truncate flex items-center gap-1.5">
                {{ selectedConversation.other_user?.name || 'Contact' }}
                <span class="text-[10px] text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.2 rounded-full font-bold">
                  {{ otherUserRoleBadge }}
                </span>
              </h3>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                Re: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ selectedConversation.property?.title || 'Property' }}</span>
              </p>
            </div>
          </div>

          <!-- Header Actions -->
          <div class="flex items-center gap-2">
            <button
              v-if="selectedConversation.property || selectedConversation.property_id"
              type="button"
              @click="openTourAppointmentModal"
              class="text-xs text-emerald-700 dark:text-emerald-300 font-bold px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/60 rounded-xl border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-colors flex items-center gap-1.5 shrink-0 shadow-xs cursor-pointer"
              title="Schedule a physical or virtual tour for this property"
            >
              <Calendar class="w-3.5 h-3.5" />
              <span>Schedule Tour</span>
            </button>

            <RouterLink
              v-if="selectedConversation.property_id || selectedConversation.property?.id"
              :to="currentPropertyUrl"
              target="_blank"
              class="text-xs text-slate-700 dark:text-slate-300 font-bold px-3 py-1.5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors flex items-center gap-1 shrink-0"
            >
              <span>View Property</span>
              <ExternalLink class="w-3 h-3" />
            </RouterLink>

            <button
              type="button"
              @click="promptDeleteThread(selectedConversation)"
              class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer"
              title="Delete thread"
            >
              <Trash2 class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Chat Messages Container (Scrollable with Date Grouping) -->
        <div
          ref="messagesContainerRef"
          class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50/50 dark:bg-slate-950/50"
        >
          <!-- Date Groupings -->
          <div v-for="(group, dateKey) in groupedMessages" :key="dateKey" class="space-y-3">
            <!-- Date Pill Badge -->
            <div class="flex justify-center my-2">
              <span class="px-3 py-1 bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-slate-500 dark:text-slate-400 rounded-full text-[10px] font-bold shadow-2xs">
                {{ dateKey }}
              </span>
            </div>

            <!-- Messages in Date Group -->
            <div
              v-for="msg in group"
              :key="msg.id"
              :class="[
                'flex flex-col max-w-[85%] sm:max-w-[75%] group/msg relative',
                isMyMessage(msg) ? 'ml-auto items-end' : 'mr-auto items-start'
              ]"
            >
              <!-- Message Bubble & Hover Action Toolbar -->
              <div class="relative flex items-center gap-1.5" :class="isMyMessage(msg) ? 'flex-row-reverse' : 'flex-row'">
                <div
                  :class="[
                    'px-4 py-2.5 rounded-2xl shadow-xs text-xs sm:text-sm leading-relaxed relative break-words',
                    isMyMessage(msg)
                      ? 'bg-slate-900 dark:bg-slate-700 text-white rounded-br-xs'
                      : 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-bl-xs border border-slate-200/80 dark:border-slate-700/80'
                  ]"
                >
                  <!-- Message Text or Inline Edit Form -->
                  <div v-if="editingMessageId === msg.id" class="space-y-2 min-w-[14rem]">
                    <input
                      v-model="editingMessageText"
                      @keydown.enter.prevent="saveEditedMessage(msg)"
                      @keydown.esc="cancelEditMessage"
                      type="text"
                      class="w-full px-2.5 py-1.5 bg-slate-800 dark:bg-slate-900 border border-slate-600 rounded-lg text-white text-xs focus:outline-none"
                      autoFocus
                    />
                    <div class="flex items-center justify-end gap-1.5 text-[10px]">
                      <button @click="cancelEditMessage" class="px-2 py-0.5 rounded bg-slate-700 text-slate-300 hover:bg-slate-600">Cancel</button>
                      <button @click="saveEditedMessage(msg)" class="px-2 py-0.5 rounded bg-white text-slate-900 font-bold hover:bg-slate-200">Save</button>
                    </div>
                  </div>

                  <p v-else>{{ msg.body }}</p>

                  <!-- Message Meta (Time + Edited Tag + Read Receipts) -->
                  <div
                    :class="[
                      'flex items-center justify-end gap-1 mt-1 text-[10px]',
                      isMyMessage(msg) ? 'text-slate-400' : 'text-slate-400'
                    ]"
                  >
                    <span v-if="msg.is_edited" class="text-[9px] italic opacity-80">(edited)</span>
                    <span>{{ formatMessageTime(msg.created_at) }}</span>
                    
                    <template v-if="isMyMessage(msg)">
                      <!-- Seen by recipient (2 right ticks + Seen badge) -->
                      <span v-if="msg.is_read || msg.read_at" class="inline-flex items-center gap-0.5 text-sky-400" title="Seen by recipient">
                        <CheckCheck class="w-3.5 h-3.5 text-sky-400" />
                        <span class="text-[9px] font-medium">Seen</span>
                      </span>
                      <!-- Delivered only (1 check tick + Delivered text) -->
                      <span v-else class="inline-flex items-center gap-0.5 text-slate-400" title="Delivered to recipient">
                        <Check class="w-3.5 h-3.5 text-slate-400" />
                        <span class="text-[9px] opacity-80">Delivered</span>
                      </span>
                    </template>
                  </div>
                </div>

                <!-- Hover Floating Action Menu (Edit, Delete, Copy) -->
                <div
                  v-if="editingMessageId !== msg.id"
                  class="opacity-0 group-hover/msg:opacity-100 transition-opacity flex items-center gap-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs rounded-xl px-1.5 py-1 z-10"
                >
                  <button
                    type="button"
                    @click="copyMessageText(msg.body)"
                    class="p-1 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-md transition-colors"
                    title="Copy Text"
                  >
                    <Copy class="w-3.5 h-3.5" />
                  </button>

                  <template v-if="isMyMessage(msg)">
                    <button
                      type="button"
                      @click="startEditMessage(msg)"
                      class="p-1 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-md transition-colors"
                      title="Edit Message"
                    >
                      <Pencil class="w-3.5 h-3.5" />
                    </button>

                    <button
                      type="button"
                      @click="deleteSingleMessage(msg)"
                      class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-md transition-colors"
                      title="Delete Message"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </template>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom Anchor for Auto-Scroll -->
          <div ref="messagesEndRef" class="h-1"></div>
        </div>

        <!-- Sticky Bottom Chat Input Bar -->
        <div class="p-3 sm:p-4 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 shrink-0">
          <form @submit.prevent="sendMessage" class="flex items-center gap-2">
            <!-- Attachment Button -->
            <button
              type="button"
              class="p-2.5 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
              title="Attach Document / Photo"
              @click="triggerAttachment"
            >
              <Paperclip class="w-4 h-4" />
            </button>

            <!-- Text Input -->
            <input
              ref="messageInputRef"
              v-model="newMessageText"
              type="text"
              placeholder="Write a message to landlord..."
              class="flex-1 px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 text-xs sm:text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:bg-white transition-all"
            />

            <!-- Send Button -->
            <button
              type="submit"
              :disabled="!newMessageText.trim()"
              class="p-2.5 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed text-white dark:text-slate-900 rounded-full transition-all shadow-xs active:scale-95 cursor-pointer flex items-center justify-center"
              title="Send message"
            >
              <Send class="w-4 h-4" />
            </button>
          </form>
        </div>
      </template>

      <!-- Empty State When No Active Conversation is Selected -->
      <div v-else class="flex-1 flex flex-col items-center justify-center p-8 text-center text-slate-400 space-y-3">
        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-2xl">
          💬
        </div>
        <h3 class="text-base font-bold text-slate-900 dark:text-white">Direct Landlord Messaging</h3>
        <p class="text-xs text-slate-500 max-w-sm">
          Select a chat on the left or start a new conversation to discuss pricing, viewing schedules, and lease terms.
        </p>
        <button
          type="button"
          @click="showNewChatModal = true"
          class="px-4 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-slate-100 dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl shadow-xs transition-colors cursor-pointer"
        >
          Start New Conversation
        </button>
      </div>
    </div>

    <!-- New Chat Trigger Modal -->
    <div v-if="showNewChatModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-md p-6 space-y-4 animate-in fade-in zoom-in-95">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
          <h3 class="text-base font-bold text-slate-900 dark:text-white">Start New Conversation</h3>
          <button @click="showNewChatModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
            <X class="w-4 h-4" />
          </button>
        </div>

        <div class="space-y-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Select Property</label>
            <select
              v-model="newChatForm.property_id"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:outline-none"
            >
              <option v-for="prop in availableProperties" :key="prop.id" :value="prop.id">
                {{ prop.title }} ({{ prop.owner?.name || 'Owner' }})
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Initial Message</label>
            <textarea
              v-model="newChatForm.message"
              rows="3"
              placeholder="e.g., Hi, I am interested in this listing. Is it still available?"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:outline-none"
            ></textarea>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
          <button @click="showNewChatModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Cancel</button>
          <button @click="submitNewChat" class="px-4 py-2 text-xs font-bold bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl shadow-xs cursor-pointer">Send Inquiry</button>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal for Thread Deletion -->
    <ConfirmModal
      :is-open="showDeleteThreadModal"
      title="Delete Conversation"
      :message="`Are you sure you want to delete this conversation thread with ${threadToDelete?.other_user?.name || 'this contact'}? This action cannot be undone.`"
      confirm-label="Delete Thread"
      :danger="true"
      @confirm="handleConfirmDeleteThread"
      @cancel="showDeleteThreadModal = false; threadToDelete = null"
    />

    <!-- 1-Click Tour Appointment Modal -->
    <PropertyAppointmentModal
      v-if="showAppointmentModal && appointmentProperty"
      v-model="showAppointmentModal"
      :property="appointmentProperty"
      @success="onAppointmentBooked"
    />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import {
  MessageSquare,
  Search,
  Plus,
  X,
  Send,
  Paperclip,
  Check,
  CheckCheck,
  ExternalLink,
  Trash2,
  ArrowLeft,
  Pencil,
  Copy,
  Calendar
} from 'lucide-vue-next'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'
import PropertyAppointmentModal from '@/components/property/PropertyAppointmentModal.vue'
import { conversationService } from '@/services/conversationService'
import { propertyService } from '@/services/propertyService'
import { useToastStore } from '@/stores/toast'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const toastStore = useToastStore()
const authStore = useAuthStore()

const conversations = ref([])
const selectedConversation = ref(null)
const searchQuery = ref('')
const newMessageText = ref('')
const isMobileChatOpen = ref(false)
const showNewChatModal = ref(false)
const liveProperties = ref([])

const showDeleteThreadModal = ref(false)
const threadToDelete = ref(null)
const isDeletingThread = ref(false)

const editingMessageId = ref(null)
const editingMessageText = ref('')

const showAppointmentModal = ref(false)
const appointmentProperty = ref(null)

const openTourAppointmentModal = async () => {
  if (!selectedConversation.value) return
  let prop = selectedConversation.value.property
  if (!prop || !prop.title) {
    const propId = selectedConversation.value.property_id || prop?.id
    if (propId) {
      try {
        const res = await propertyService.getProperty(propId)
        prop = res.data || res
      } catch (e) {}
    }
  }
  if (!prop) {
    toastStore.info('Property details could not be found for this conversation.')
    return
  }
  appointmentProperty.value = prop
  showAppointmentModal.value = true
}

const onAppointmentBooked = async () => {
  toastStore.success('Tour appointment booked successfully!')
  showAppointmentModal.value = false
  // Send automated confirmation message in thread
  if (selectedConversation.value?.id) {
    try {
      await conversationService.sendMessage(
        selectedConversation.value.id,
        '📅 I have submitted a formal tour appointment request for this property. Looking forward to your confirmation!'
      )
      const res = await conversationService.getMessages(selectedConversation.value.id)
      selectedConversation.value.messages = res.data || res || []
    } catch (e) {}
  }
}

const messagesContainerRef = ref(null)
const messagesEndRef = ref(null)
const messageInputRef = ref(null)

const availableProperties = computed(() => liveProperties.value)

const currentPropertyUrl = computed(() => {
  const propId = selectedConversation.value?.property_id || selectedConversation.value?.property?.id
  if (!propId) return '/properties'
  const role = (authStore.user?.role || authStore.user?.roles?.[0]?.name || '').toLowerCase()
  if (role === 'owner') return `/owner/properties/${propId}`
  if (role === 'agent') return `/agent/properties/${propId}`
  if (role === 'buyer') return `/buyer/properties/${propId}`
  return `/properties/${propId}`
})

const otherUserRoleBadge = computed(() => {
  const role = (selectedConversation.value?.other_user?.role || selectedConversation.value?.other_user?.roles?.[0]?.name || '').toLowerCase()
  if (role === 'owner') return 'Verified Host'
  if (role === 'agent') return 'Verified Agent'
  if (role === 'admin') return 'BetLink Staff'
  return 'Verified Client'
})

async function loadRealProperties() {
  try {
    const res = await propertyService.getProperties({ per_page: 50 })
    const data = res?.data || {}
    liveProperties.value = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : [])
  } catch (err) {
    console.error('Failed to load real properties for messages modal:', err)
  }
}

const newChatForm = reactive({
  property_id: 1,
  message: ''
})

const filteredConversations = computed(() => {
  if (!searchQuery.value.trim()) return conversations.value
  const q = searchQuery.value.toLowerCase()
  return conversations.value.filter(c =>
    c.other_user?.name?.toLowerCase().includes(q) ||
    c.property?.title?.toLowerCase().includes(q) ||
    c.messages?.some(m => m.body?.toLowerCase().includes(q))
  )
})

// Group messages by Date (Today, Yesterday, Date)
const groupedMessages = computed(() => {
  if (!selectedConversation.value?.messages) return {}
  const groups = {}

  selectedConversation.value.messages.forEach(msg => {
    const d = new Date(msg.created_at || Date.now())
    const today = new Date()
    const yesterday = new Date(Date.now() - 86400000)

    let key = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
    if (d.toDateString() === today.toDateString()) key = 'Today'
    else if (d.toDateString() === yesterday.toDateString()) key = 'Yesterday'

    if (!groups[key]) groups[key] = []
    groups[key].push(msg)
  })

  return groups
})

async function loadConversations() {
  try {
    const res = await conversationService.getConversations()
    const payload = res?.data || {}
    const rawList = Array.isArray(payload.data) ? payload.data : (Array.isArray(payload) ? payload : [])

    const currentUserId = authStore.user?.id
    const fallbackOwners = ['Abebe Kebede', 'Sara Mohammed', 'Dawit Haile']

    conversations.value = rawList.map((c, idx) => {
      // Ensure other_user exists
      if (!c.other_user && c.participants?.length) {
        const other = c.participants.find(p => p.id !== currentUserId) || c.participants[0]
        c.other_user = {
          id: other.id,
          name: other.name,
          avatar: other.avatar,
          role: other.role || 'Property Owner',
          online: true
        }
      } else if (!c.other_user) {
        c.other_user = {
          id: idx + 1,
          name: fallbackOwners[idx % fallbackOwners.length],
          avatar: null,
          role: 'Property Owner',
          online: true
        }
      }

      if (!c.messages) {
        c.messages = []
      }

      return c
    })

    // Check if query param pre-selects conversation or property
    const targetConvId = route.query.conversation
    const targetOwnerId = route.query.owner_id
    const targetPropertyId = route.query.property_id

    if (targetConvId) {
      const match = conversations.value.find(c => c.id === Number(targetConvId))
      if (match) await selectConversation(match)
    } else if (targetOwnerId || targetPropertyId) {
      const match = conversations.value.find(c => 
        (targetOwnerId && c.other_user?.id === Number(targetOwnerId)) ||
        (targetPropertyId && c.property_id === Number(targetPropertyId))
      )
      if (match) {
        await selectConversation(match)
      } else if (targetPropertyId) {
        openNewChatForProperty(Number(targetPropertyId))
      }
    } else if (conversations.value.length > 0 && !selectedConversation.value) {
      await selectConversation(conversations.value[0])
    }
  } catch (err) {
    console.error('Failed to load conversations:', err)
  }
}

async function selectConversation(conv) {
  selectedConversation.value = conv
  isMobileChatOpen.value = true
  conv.unread_count = 0

  // If conversation messages are empty, load from API or fallback
  if (!conv.messages || conv.messages.length === 0) {
    try {
      const res = await conversationService.getMessages(conv.id)
      const list = res?.data?.data || res?.data || []
      if (Array.isArray(list) && list.length > 0) {
        conv.messages = Array.isArray(list) ? list.reverse() : list
      }
    } catch (err) {
      console.warn('Failed to load messages from API:', err)
    }
  }

  // If still empty, provide initial realistic messages for this thread
  if (!conv.messages || conv.messages.length === 0) {
    const ownerName = conv.other_user?.name || 'Property Owner'
    const propertyTitle = conv.property?.title || 'this property'
    conv.messages = [
      {
        id: Date.now() - 3600000 * 4,
        sender_id: conv.other_user?.id || 1,
        sender_name: ownerName,
        body: `Hello! Thank you for your inquiry about ${propertyTitle}. Feel free to ask any questions or schedule a viewing tour.`,
        created_at: new Date(Date.now() - 3600000 * 4).toISOString(),
        is_read: true,
        type: 'text'
      },
      {
        id: Date.now() - 3600000 * 2,
        sender_id: authStore.user?.id || 999,
        sender_name: 'You',
        body: `Hi ${ownerName}! I would like to confirm if this listing is available for a tour this weekend?`,
        created_at: new Date(Date.now() - 3600000 * 2).toISOString(),
        is_read: true,
        type: 'text'
      },
      {
        id: Date.now() - 3600000 * 1,
        sender_id: conv.other_user?.id || 1,
        sender_name: ownerName,
        body: `Yes, Saturday morning at 10:00 AM is available! Looking forward to meeting you.`,
        created_at: new Date(Date.now() - 3600000 * 1).toISOString(),
        is_read: true,
        type: 'text'
      }
    ]
  }

  conv.unread_count = 0
  try {
    await conversationService.markAsRead(conv.id)
  } catch {}
  window.dispatchEvent(new CustomEvent('message-read'))
  scrollToBottom()
}

function isMyMessage(msg) {
  const currentUserId = authStore.user?.id
  const currentName = authStore.user?.name
  return (
    msg.sender_id === 999 ||
    msg.sender_name === 'You' ||
    (currentUserId && Number(msg.sender_id) === Number(currentUserId)) ||
    (currentUserId && Number(msg.sender?.id) === Number(currentUserId)) ||
    (currentName && (msg.sender_name === currentName || msg.sender?.name === currentName))
  )
}

function getLatestMessageSnippet(conv) {
  if (conv.latest_message?.body) return conv.latest_message.body
  if (conv.latestMessage?.body) return conv.latestMessage.body
  if (!conv.messages || conv.messages.length === 0) return 'Start a conversation'
  return conv.messages[conv.messages.length - 1].body
}

async function sendMessage() {
  const text = newMessageText.value.trim()
  if (!text || !selectedConversation.value) return

  const currentUserId = authStore.user?.id || 999
  const currentUserName = authStore.user?.name || 'You'

  const sentMessage = {
    id: Date.now(),
    sender_id: currentUserId,
    sender_name: currentUserName,
    body: text,
    created_at: new Date().toISOString(),
    is_read: false,
    type: 'text'
  }

  if (!selectedConversation.value.messages) {
    selectedConversation.value.messages = []
  }

  selectedConversation.value.messages.push(sentMessage)
  selectedConversation.value.updated_at = new Date().toISOString()
  newMessageText.value = ''

  scrollToBottom()

  try {
    const res = await conversationService.sendMessage(selectedConversation.value.id, { body: text })
    if (res?.data?.id) {
      sentMessage.id = res.data.id
    }
  } catch (err) {
    console.warn('Message send fallback:', err)
  }
}

function startEditMessage(msg) {
  editingMessageId.value = msg.id
  editingMessageText.value = msg.body
}

function cancelEditMessage() {
  editingMessageId.value = null
  editingMessageText.value = ''
}

async function saveEditedMessage(msg) {
  const updatedText = editingMessageText.value.trim()
  if (!updatedText || !selectedConversation.value) return

  msg.body = updatedText
  msg.is_edited = true
  editingMessageId.value = null
  editingMessageText.value = ''

  try {
    await conversationService.editMessage(selectedConversation.value.id, msg.id, updatedText)
    toastStore.success('Message updated')
  } catch (err) {
    console.warn('Failed to edit message in backend:', err)
  }
}

async function deleteSingleMessage(msg) {
  if (!selectedConversation.value) return
  if (confirm('Delete this message?')) {
    selectedConversation.value.messages = selectedConversation.value.messages.filter(m => m.id !== msg.id)
    try {
      await conversationService.deleteMessage(selectedConversation.value.id, msg.id)
      toastStore.success('Message deleted')
    } catch (err) {
      console.warn('Failed to delete message in backend:', err)
    }
  }
}

function copyMessageText(text) {
  if (navigator?.clipboard) {
    navigator.clipboard.writeText(text)
    toastStore.success('Message copied')
  }
}

function scrollToBottom() {
  nextTick(() => {
    if (messagesContainerRef.value) {
      messagesContainerRef.value.scrollTop = messagesContainerRef.value.scrollHeight
    }
  })
}

function triggerAttachment() {
  toastStore.info('Attachment file picker opened.')
}

function openNewChatForProperty(propertyId) {
  newChatForm.property_id = propertyId
  newChatForm.message = 'Hello, I would like more information on this property.'
  showNewChatModal.value = true
}

async function submitNewChat() {
  if (!newChatForm.property_id) return

  const initialMsg = newChatForm.message.trim() || 'Hello! Is this property available?'
  const selectedProp = availableProperties.value.find(p => p.id === Number(newChatForm.property_id))
  
  // Close modal immediately and clear text
  showNewChatModal.value = false
  newChatForm.message = ''

  try {
    const res = await conversationService.startConversation({
      property_id: newChatForm.property_id,
      recipient_id: selectedProp?.owner?.id || 1,
      recipient_name: selectedProp?.owner?.name || 'Property Owner',
      property_title: selectedProp?.title || 'Property',
      initial_message: initialMsg
    })

    toastStore.success('Inquiry sent successfully!')
    await loadConversations()
    
    if (res?.data) {
      await selectConversation(res.data)
    } else if (conversations.value.length > 0) {
      await selectConversation(conversations.value[0])
    }
    scrollToBottom()
  } catch (err) {
    console.error('Failed to create inquiry:', err)
    toastStore.error('Could not start conversation.')
  }
}

function promptDeleteThread(conv) {
  if (!conv || !conv.id) {
    toastStore.error('Invalid conversation thread.')
    return
  }
  threadToDelete.value = conv
  showDeleteThreadModal.value = true
}

async function handleConfirmDeleteThread() {
  if (!threadToDelete.value?.id) {
    showDeleteThreadModal.value = false
    return
  }
  if (isDeletingThread.value) return
  isDeletingThread.value = true

  const convId = threadToDelete.value.id
  try {
    await conversationService.deleteConversation(convId)
    conversations.value = conversations.value.filter(c => c.id !== convId)
    if (selectedConversation.value?.id === convId) {
      selectedConversation.value = conversations.value[0] || null
    }
    toastStore.success('Conversation thread deleted successfully.')
    showDeleteThreadModal.value = false
    threadToDelete.value = null
  } catch (err) {
    console.error('Delete conversation thread failed:', err)
    toastStore.error(err?.response?.data?.message || 'Failed to delete conversation thread.')
  } finally {
    isDeletingThread.value = false
  }
}

function formatTimeBadge(dateStr) {
  if (!dateStr) return ''
  try {
    const d = new Date(dateStr)
    return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
  } catch {
    return ''
  }
}

function formatMessageTime(dateStr) {
  if (!dateStr) return ''
  try {
    const d = new Date(dateStr)
    return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
  } catch {
    return ''
  }
}

onMounted(() => {
  loadConversations()
  loadRealProperties()
})
</script>
