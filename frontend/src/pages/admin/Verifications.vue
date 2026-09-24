<template>
  <div class="space-y-5">
    <!-- Filter Tabs -->
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

      <!-- Verifications List -->
      <div class="divide-y divide-gray-100">
        <div v-for="verification in filteredVerifications" :key="verification.id" class="p-5 hover:bg-gray-50 transition-colors">
          <div class="flex gap-4 items-start mb-4">
            <!-- User Avatar -->
            <img
              :src="verification.avatar"
              :alt="verification.userName"
              class="w-12 h-12 rounded-full object-cover flex-shrink-0"
            />

            <!-- Verification Details -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-2 mb-1">
                <div>
                  <h3 class="font-bold text-gray-900">{{ verification.userName }}</h3>
                  <p class="text-xs text-gray-500">{{ verification.userRole }} • Submitted {{ verification.submittedDate }}</p>
                </div>
                <span :class="verification.statusBadge" class="px-3 py-1 text-xs font-bold rounded-full flex-shrink-0">
                  {{ verification.status }}
                </span>
              </div>

              <!-- Documents List -->
              <div class="mt-3 space-y-2">
                <template v-for="doc in verification.documents" :key="doc.id">
                  <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                    <div class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                      <span class="text-sm text-gray-700">{{ doc.name }}</span>
                    </div>
                    <button
                      @click="viewDocument(doc)"
                      class="px-2.5 py-1 border border-blue-300 text-blue-600 text-xs font-semibold rounded hover:bg-blue-50 transition-colors"
                    >
                      View
                    </button>
                  </div>
                </template>
              </div>

              <!-- Notes -->
              <div v-if="verification.notes" class="mt-3 p-3 bg-yellow-50 rounded border border-yellow-200">
                <p class="text-xs font-semibold text-yellow-700 mb-1">Admin Notes:</p>
                <p class="text-xs text-yellow-700">{{ verification.notes }}</p>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-2 flex-wrap">
            <button
              @click="approveVerification(verification.id)"
              class="px-4 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors text-sm flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
              </svg>
              Approve
            </button>
            <button
              @click="requestMoreInfo(verification.id)"
              class="px-4 py-2 border border-yellow-600 text-yellow-600 font-semibold rounded-lg hover:bg-yellow-50 transition-colors text-sm"
            >
              Request More Info
            </button>
            <button
              @click="rejectVerification(verification.id)"
              class="px-4 py-2 border border-red-300 text-red-600 font-semibold rounded-lg hover:bg-red-50 transition-colors text-sm"
            >
              Reject
            </button>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredVerifications.length === 0" class="px-5 py-12 text-center">
          <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <p class="text-gray-500 font-medium">No verifications pending</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const activeTab = ref('pending')

const tabs = [
  { id: 'pending', label: 'Pending', count: 8 },
  { id: 'approved', label: 'Approved', count: 124 },
  { id: 'rejected', label: 'Rejected', count: 12 },
]

const verifications = ref([
  {
    id: 1,
    userName: 'Getachew Assefa',
    userRole: 'Property Owner',
    avatar: 'https://ui-avatars.com/api/?name=Getachew+Assefa&background=1e293b&color=fff&size=64',
    submittedDate: '3 hours ago',
    status: 'Pending',
    statusBadge: 'bg-yellow-100 text-yellow-700',
    documents: [
      { id: 1, name: 'National_ID.pdf' },
      { id: 2, name: 'Proof_of_Residence.pdf' },
      { id: 3, name: 'Property_Deed.pdf' },
    ],
    notes: '',
  },
  {
    id: 2,
    userName: 'Abebe Kebede',
    userRole: 'Real Estate Agent',
    avatar: 'https://ui-avatars.com/api/?name=Abebe+Kebede&background=3b82f6&color=fff&size=64',
    submittedDate: '1 day ago',
    status: 'Pending',
    statusBadge: 'bg-yellow-100 text-yellow-700',
    documents: [
      { id: 1, name: 'Professional_License.pdf' },
      { id: 2, name: 'Government_ID.pdf' },
      { id: 3, name: 'References.pdf' },
    ],
    notes: 'Professional license appears expired. Requesting updated documentation.',
  },
  {
    id: 3,
    userName: 'Sara Mohammed',
    userRole: 'Property Owner',
    avatar: 'https://ui-avatars.com/api/?name=Sara+Mohammed&background=f59e0b&color=fff&size=64',
    submittedDate: '5 days ago',
    status: 'Approved',
    statusBadge: 'bg-emerald-100 text-emerald-700',
    documents: [
      { id: 1, name: 'National_ID.pdf' },
      { id: 2, name: 'Proof_of_Residence.pdf' },
      { id: 3, name: 'Property_Deed.pdf' },
    ],
    notes: '',
  },
])

const filteredVerifications = computed(() => {
  if (activeTab.value === 'pending') {
    return verifications.value.filter(v => v.status === 'Pending')
  } else if (activeTab.value === 'approved') {
    return verifications.value.filter(v => v.status === 'Approved')
  } else if (activeTab.value === 'rejected') {
    return verifications.value.filter(v => v.status === 'Rejected')
  }
  return verifications.value
})

const viewDocument = (doc) => {
  alert(`View document: ${doc.name}`)
}

const approveVerification = (id) => {
  const verification = verifications.value.find(v => v.id === id)
  if (verification) {
    verification.status = 'Approved'
    verification.statusBadge = 'bg-emerald-100 text-emerald-700'
    alert('Verification approved!')
  }
}

const requestMoreInfo = (id) => {
  const verification = verifications.value.find(v => v.id === id)
  if (verification) {
    alert(`Send request for more information to ${verification.userName}`)
  }
}

const rejectVerification = (id) => {
  if (confirm('Reject this verification request?')) {
    const verification = verifications.value.find(v => v.id === id)
    if (verification) {
      verification.status = 'Rejected'
      verification.statusBadge = 'bg-red-100 text-red-700'
      alert('Verification rejected!')
    }
  }
}
</script>
