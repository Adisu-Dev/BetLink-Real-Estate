<template>
  <div class="space-y-5">
    <!-- Tabs -->
    <div class="bg-white rounded-lg border border-gray-200">
      <div class="flex border-b border-gray-200">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'px-6 py-3 font-semibold text-sm transition-colors border-b-2 -mb-px',
            activeTab === tab.id
              ? 'border-emerald-600 text-emerald-600'
              : 'border-transparent text-gray-500 hover:text-gray-700'
          ]"
        >
          {{ tab.label }}
          <span class="ml-2 px-2 py-0.5 bg-gray-100 rounded-full text-xs">{{ tab.count }}</span>
        </button>
      </div>

      <!-- Inquiries List -->
      <div class="divide-y divide-gray-100">
        <div v-for="inquiry in filteredInquiries" :key="inquiry.id" class="p-5 hover:bg-gray-50 transition-colors">
          <div class="flex gap-4 items-start mb-3">
            <!-- Buyer Avatar -->
            <img
              :src="inquiry.buyerAvatar"
              :alt="inquiry.buyerName"
              class="w-12 h-12 rounded-full object-cover flex-shrink-0"
            />

            <!-- Inquiry Details -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-2 mb-1">
                <h3 class="font-bold text-gray-900">{{ inquiry.buyerName }}</h3>
                <span :class="inquiry.statusBadge" class="px-2 py-0.5 text-xs font-bold rounded-full flex-shrink-0">
                  {{ inquiry.status }}
                </span>
              </div>
              <p class="text-sm text-gray-600 mb-2">Re: {{ inquiry.propertyName }}</p>
              <p class="text-sm text-gray-700 mb-3">{{ inquiry.question }}</p>

              <!-- Meta Info -->
              <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
                <span class="flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  {{ inquiry.time }}
                </span>
                <span>•</span>
                <span>{{ inquiry.buyerPhone }}</span>
              </div>
            </div>
          </div>

          <!-- Reply Section (if not replied) -->
          <div v-if="inquiry.status === 'New'" class="bg-gray-50 rounded-lg p-4 mb-3">
            <textarea
              v-model="inquiry.reply"
              rows="3"
              placeholder="Type your reply here..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-3"
            ></textarea>
            <div class="flex gap-2">
              <button
                @click="sendReply(inquiry)"
                class="px-4 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors text-sm"
              >
                Send Reply
              </button>
              <button
                @click="inquiry.reply = ''"
                class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition-colors text-sm"
              >
                Clear
              </button>
            </div>
          </div>

          <!-- Previous Reply Display -->
          <div v-else-if="inquiry.reply" class="bg-emerald-50 rounded-lg p-4 mb-3 border-l-4 border-emerald-600">
            <p class="text-xs font-semibold text-emerald-700 mb-2">Your reply:</p>
            <p class="text-sm text-gray-700">{{ inquiry.reply }}</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-2 flex-wrap">
            <button
              @click="markAsResolved(inquiry.id)"
              class="px-4 py-2 border border-emerald-600 text-emerald-600 font-semibold rounded-lg hover:bg-emerald-50 transition-colors text-sm"
            >
              Mark as Resolved
            </button>
            <button
              @click="contactBuyer(inquiry)"
              class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition-colors text-sm"
            >
              Contact Directly
            </button>
            <button
              @click="deleteInquiry(inquiry.id)"
              class="px-4 py-2 border border-red-300 text-red-600 font-semibold rounded-lg hover:bg-red-50 transition-colors text-sm"
            >
              Delete
            </button>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredInquiries.length === 0" class="px-5 py-12 text-center">
          <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
          </svg>
          <p class="text-gray-500 font-medium">No inquiries {{ activeTab }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const activeTab = ref('all')

const tabs = [
  { id: 'all', label: 'All', count: 5 },
  { id: 'new', label: 'New', count: 2 },
  { id: 'resolved', label: 'Resolved', count: 3 },
]

const inquiries = ref([
  {
    id: 1,
    buyerName: 'Hiwot Alemu',
    buyerAvatar: 'https://ui-avatars.com/api/?name=Hiwot+Alemu&background=f59e0b&color=fff&size=64',
    buyerPhone: '+251-911-234-567',
    propertyName: 'Bole Luxury Apartment',
    question: 'Is the apartment available for immediate occupancy? Also, do you offer flexible payment terms?',
    status: 'New',
    statusBadge: 'bg-yellow-100 text-yellow-700',
    time: '2 hours ago',
    reply: '',
  },
  {
    id: 2,
    buyerName: 'Yohannes Bekele',
    buyerAvatar: 'https://ui-avatars.com/api/?name=Yohannes+Bekele&background=3b82f6&color=fff&size=64',
    buyerPhone: '+251-922-345-678',
    propertyName: 'CMC Villa',
    question: 'What utilities are included in the rent? Is there parking available?',
    status: 'Resolved',
    statusBadge: 'bg-emerald-100 text-emerald-700',
    time: '1 day ago',
    reply: 'Thank you for your interest! Yes, water and electricity are included. There is dedicated parking for 2 cars. Looking forward to hearing from you.',
  },
  {
    id: 3,
    buyerName: 'Sara Mohammed',
    buyerAvatar: 'https://ui-avatars.com/api/?name=Sara+Mohammed&background=1e293b&color=fff&size=64',
    buyerPhone: '+251-933-456-789',
    propertyName: 'Nifas Silk Office Space',
    question: 'Can I schedule a viewing this week?',
    status: 'New',
    statusBadge: 'bg-yellow-100 text-yellow-700',
    time: '3 hours ago',
    reply: '',
  },
  {
    id: 4,
    buyerName: 'Daniel Tesfaye',
    buyerAvatar: 'https://ui-avatars.com/api/?name=Daniel+Tesfaye&background=8b5cf6&color=fff&size=64',
    buyerPhone: '+251-944-567-890',
    propertyName: 'Piazza Contemporary',
    question: 'Is the property pet-friendly?',
    status: 'Resolved',
    statusBadge: 'bg-emerald-100 text-emerald-700',
    time: '5 days ago',
    reply: 'Yes, we allow small pets with a small additional monthly fee of 500 ETB.',
  },
  {
    id: 5,
    buyerName: 'Meron Tadesse',
    buyerAvatar: 'https://ui-avatars.com/api/?name=Meron+Tadesse&background=ec4899&color=fff&size=64',
    buyerPhone: '+251-955-678-901',
    propertyName: 'Bole Luxury Apartment',
    question: 'What is the lease period? Can I rent for 3 months?',
    status: 'Resolved',
    statusBadge: 'bg-emerald-100 text-emerald-700',
    time: '1 week ago',
    reply: 'Standard lease is 1 year, but we can negotiate shorter terms. Let me know your preferred dates.',
  },
])

const filteredInquiries = computed(() => {
  if (activeTab.value === 'all') return inquiries.value
  if (activeTab.value === 'new') return inquiries.value.filter(i => i.status === 'New')
  if (activeTab.value === 'resolved') return inquiries.value.filter(i => i.status === 'Resolved')
  return inquiries.value
})

const sendReply = (inquiry) => {
  if (!inquiry.reply.trim()) {
    alert('Please type a reply')
    return
  }
  inquiry.status = 'Resolved'
  inquiry.statusBadge = 'bg-emerald-100 text-emerald-700'
  alert('Reply sent successfully!')
}

const markAsResolved = (id) => {
  const inquiry = inquiries.value.find(i => i.id === id)
  if (inquiry && inquiry.status === 'New') {
    inquiry.status = 'Resolved'
    inquiry.statusBadge = 'bg-emerald-100 text-emerald-700'
    alert('Marked as resolved!')
  }
}

const contactBuyer = (inquiry) => {
  alert(`Call ${inquiry.buyerName} at ${inquiry.buyerPhone}`)
}

const deleteInquiry = (id) => {
  if (confirm('Delete this inquiry?')) {
    const idx = inquiries.value.findIndex(i => i.id === id)
    if (idx !== -1) {
      inquiries.value.splice(idx, 1)
    }
  }
}
</script>
