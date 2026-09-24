<template>
  <div class="flex h-[calc(100vh-12rem)] gap-5 rounded-lg overflow-hidden">
    <!-- Conversations List (Left Panel) -->
    <div class="w-80 bg-white border border-gray-200 rounded-lg flex flex-col">
      <!-- Header -->
      <div class="p-4 border-b border-gray-200">
        <h2 class="font-bold text-gray-900 mb-3">Messages</h2>
        <!-- Search -->
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search conversations..."
          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
        />
      </div>

      <!-- Conversations List -->
      <div class="flex-1 overflow-y-auto">
        <button
          v-for="conv in filteredConversations"
          :key="conv.id"
          @click="selectConversation(conv)"
          :class="[
            'w-full px-4 py-3 border-b border-gray-100 text-left transition-colors hover:bg-gray-50',
            selectedConversation?.id === conv.id ? 'bg-emerald-50 border-r-4 border-emerald-600' : ''
          ]"
        >
          <div class="flex gap-3 items-start">
            <img
              :src="conv.avatar"
              :alt="conv.name"
              class="w-10 h-10 rounded-full object-cover flex-shrink-0"
            />
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-2 mb-0.5">
                <p class="font-semibold text-gray-900 text-sm truncate">{{ conv.name }}</p>
                <span class="text-xs text-gray-400 flex-shrink-0">{{ conv.time }}</span>
              </div>
              <p class="text-xs text-gray-600 truncate line-clamp-1">{{ conv.lastMessage }}</p>
            </div>
            <span v-if="conv.unread > 0" class="flex-shrink-0 w-5 h-5 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xs font-bold">
              {{ conv.unread }}
            </span>
          </div>
        </button>

        <!-- Empty State -->
        <div v-if="filteredConversations.length === 0" class="px-4 py-8 text-center text-gray-500">
          <p class="text-sm">No conversations found</p>
        </div>
      </div>
    </div>

    <!-- Chat Panel (Right) -->
    <div v-if="selectedConversation" class="flex-1 bg-white border border-gray-200 rounded-lg flex flex-col">
      <!-- Header -->
      <div class="p-4 border-b border-gray-200 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <img
            :src="selectedConversation.avatar"
            :alt="selectedConversation.name"
            class="w-10 h-10 rounded-full object-cover"
          />
          <div>
            <h3 class="font-semibold text-gray-900">{{ selectedConversation.name }}</h3>
            <p class="text-xs text-gray-500">Active now</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
          </button>
          <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Messages -->
      <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
        <div v-for="msg in messages" :key="msg.id" :class="['flex', msg.sent ? 'justify-end' : 'justify-start']">
          <div
            :class="[
              'max-w-xs px-4 py-2 rounded-lg',
              msg.sent
                ? 'bg-emerald-600 text-white rounded-br-none'
                : 'bg-white border border-gray-200 text-gray-900 rounded-bl-none'
            ]"
          >
            <p class="text-sm">{{ msg.text }}</p>
            <p :class="['text-xs mt-1', msg.sent ? 'text-emerald-100' : 'text-gray-500']">
              {{ msg.time }}
            </p>
          </div>
        </div>
      </div>

      <!-- Input -->
      <div class="p-4 border-t border-gray-200 flex gap-3">
        <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors flex-shrink-0">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
        </button>
        <input
          v-model="newMessage"
          type="text"
          placeholder="Type your message..."
          @keyup.enter="sendMessage"
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
        />
        <button
          @click="sendMessage"
          class="px-4 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex-shrink-0"
        >
          Send
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="flex-1 bg-white border border-gray-200 rounded-lg flex items-center justify-center">
      <div class="text-center text-gray-500">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <p class="font-medium">Select a conversation to start messaging</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const searchQuery = ref('')
const selectedConversation = ref(null)
const newMessage = ref('')

const conversations = ref([
  {
    id: 1,
    name: 'Hiwot Alemu',
    lastMessage: 'Thanks for the property details!',
    avatar: 'https://ui-avatars.com/api/?name=Hiwot+Alemu&background=f59e0b&color=fff&size=64',
    time: '2m ago',
    unread: 2,
  },
  {
    id: 2,
    name: 'Yohannes Bekele',
    lastMessage: 'When is the next viewing available?',
    avatar: 'https://ui-avatars.com/api/?name=Yohannes+Bekele&background=3b82f6&color=fff&size=64',
    time: '1h ago',
    unread: 0,
  },
  {
    id: 3,
    name: 'Sara Mohammed',
    lastMessage: 'I am interested in the apartment',
    avatar: 'https://ui-avatars.com/api/?name=Sara+Mohammed&background=1e293b&color=fff&size=64',
    time: '3h ago',
    unread: 1,
  },
])

const messages = ref([
  { id: 1, text: 'Hi, I am interested in the apartment you listed', sent: false, time: '10:30 AM' },
  { id: 2, text: 'Thanks for reaching out! Let me know if you have any questions', sent: true, time: '10:31 AM' },
  { id: 3, text: 'What is the price per month?', sent: false, time: '10:32 AM' },
  { id: 4, text: 'The rental price is 15,000 ETB per month', sent: true, time: '10:33 AM' },
])

const filteredConversations = computed(() => {
  if (!searchQuery.value) return conversations.value
  return conversations.value.filter(conv =>
    conv.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const selectConversation = (conv) => {
  selectedConversation.value = conv
  conv.unread = 0
}

const sendMessage = () => {
  if (!newMessage.value.trim()) return
  messages.value.push({
    id: messages.value.length + 1,
    text: newMessage.value,
    sent: true,
    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
  })
  newMessage.value = ''
}
</script>
