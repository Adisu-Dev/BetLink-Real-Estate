<template>
  <div class="space-y-5">
    <!-- System Configuration -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
      <h2 class="text-lg font-bold text-gray-900 mb-4">System Configuration</h2>
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Platform Commission (%)</label>
          <input
            v-model="config.platformCommission"
            type="number"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
            placeholder="e.g., 5"
          />
          <p class="text-xs text-gray-500 mt-1">Percentage commission on each transaction</p>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Max Properties</label>
          <input
            v-model="config.maxPropertiesPerOwner"
            type="number"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
            placeholder="e.g., 50"
          />
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Banned Domains </label>
          <textarea
            v-model="config.bannedDomains"
            rows="3"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
            placeholder="e.g., spam.com, fraud.net"
          ></textarea>
        </div>
      </div>
    </div>

    <!-- Feature Toggles -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
      <h2 class="text-lg font-bold text-gray-900 mb-4">Feature Toggles</h2>
      <div class="space-y-3">
        <div v-for="feature in features" :key="feature.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
          <div>
            <p class="font-semibold text-gray-900">{{ feature.label }}</p>
            <p class="text-xs text-gray-500">{{ feature.description }}</p>
          </div>
          <label class="relative flex items-center cursor-pointer">
            <input v-model="feature.enabled" type="checkbox" class="hidden peer" />
            <div class="w-11 h-6 bg-gray-300 peer-checked:bg-emerald-600 rounded-full transition-colors"></div>
            <span class="absolute left-1 top-0.5 w-5 h-5 bg-white rounded-full transition-all peer-checked:translate-x-5"></span>
          </label>
        </div>
      </div>
    </div>

    <!-- Email Templates -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
      <h2 class="text-lg font-bold text-gray-900 mb-4">Email Templates</h2>
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Select Template</label>
          <select v-model="selectedTemplate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-4">
            <option value="">Select a template </option>
            <option v-for="template in emailTemplates" :key="template.id" :value="template.id">
              {{ template.name }}
            </option>
          </select>

          <!-- Template Preview -->
          <div v-if="selectedTemplate" class="bg-gray-50 rounded-lg p-4 space-y-3">
            <div>
              <p class="text-xs text-gray-500 font-semibold mb-1">Subject</p>
              <p class="text-sm text-gray-900">{{ getTemplate(selectedTemplate)?.subject }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 font-semibold mb-1">Body</p>
              <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ getTemplate(selectedTemplate)?.body }}</p>
            </div>
            <button
              @click="editTemplate(selectedTemplate)"
              class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors text-sm"
            >
              Edit Template
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Moderation Settings -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
      <h2 class="text-lg font-bold text-gray-900 mb-4">Moderation Settings</h2>
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Content Filter Keywords</label>
          <textarea
            v-model="moderation.filterKeywords"
            rows="3"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
            placeholder="Keywords to auto-flag content"
          ></textarea>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Auto-Approve Threshold (% confidence)</label>
          <input
            v-model="moderation.autoApproveThreshold"
            type="number"
            min="0"
            max="100"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
            placeholder="e.g., 95"
          />
          <p class="text-xs text-gray-500 mt-1">Automatically approve listings above this confidence score</p>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Moderation Actions</label>
          <div class="space-y-2">
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="moderation.requireApproval" type="checkbox" class="w-4 h-4" />
              <span class="text-sm text-gray-700">Require manual approval for new listings</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="moderation.autoSuspend" type="checkbox" class="w-4 h-4" />
              <span class="text-sm text-gray-700">Auto-suspend users with 3+ violations</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="moderation.emailAlerts" type="checkbox" class="w-4 h-4" />
              <span class="text-sm text-gray-700">Send email alerts on flagged content</span>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- Save Button -->
    <div class="flex gap-3">
      <button
        @click="saveSettings"
        class="px-6 py-2 bg-slate-300 text-white font-semibold rounded-lg  transition-colors flex items-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        Save
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const config = ref({
  platformCommission: 5,
  maxPropertiesPerOwner: 50,
  bannedDomains: 'spam.com, fraud.net, scam.org',
})

const features = ref([
  {
    id: 1,
    label: 'Allow Guest Listings',
    description: 'Allow unregistered users to browse listings',
    enabled: true,
  },
  {
    id: 2,
    label: 'Require Email Verification',
    description: 'Require users to verify email before listing',
    enabled: true,
  },
  {
    id: 3,
    label: 'Enable Payments',
    description: 'Allow payments through the platform',
    enabled: true,
  },
  {
    id: 4,
    label: 'Enable Messaging',
    description: 'Allow direct messaging between users',
    enabled: true,
  },
])

const emailTemplates = ref([
  {
    id: 1,
    name: 'Welcome Email',
    subject: 'Welcome to BetLink!',
    body: 'Dear {{name}},\n\nWelcome to BetLink! Your account has been created successfully.\n\nBest regards,\nBetLink Team',
  },
  {
    id: 2,
    name: 'Listing Approved',
    subject: 'Your property listing has been approved',
    body: 'Dear {{name}},\n\nGood news! Your property "{{property}}" has been approved and is now live.\n\nBest regards,\nBetLink Team',
  },
  {
    id: 3,
    name: 'Verification Reminder',
    subject: 'Complete your verification',
    body: 'Dear {{name}},\n\nYou have not completed your verification yet. Please upload required documents.\n\nBest regards,\nBetLink Team',
  },
])

const moderation = ref({
  filterKeywords: 'spam, fraud, scam, unauthorized',
  autoApproveThreshold: 95,
  requireApproval: true,
  autoSuspend: true,
  emailAlerts: true,
})

const selectedTemplate = ref('')

const getTemplate = (id) => emailTemplates.value.find(t => t.id === parseInt(id))

const editTemplate = (id) => {
  alert(`Edit template: ${getTemplate(id)?.name}`)
}

const saveSettings = () => {
  alert('Settings saved successfully!')
}
</script>
