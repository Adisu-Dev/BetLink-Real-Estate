<template>
  <div class="space-y-5">
    <!-- Status Overview -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-gray-900">Professional Verification</h2>
        <span :class="verificationStatus.badgeClass" class="px-4 py-2 rounded-full text-sm font-bold">
          {{ verificationStatus.label }}
        </span>
      </div>

      <!-- Progress Bar -->
      <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm text-gray-700 font-semibold">Verification Progress</p>
          <p class="text-sm font-bold text-gray-900">{{ verificationStatus.progress }}%</p>
        </div>
        <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
          <div
            class="h-full bg-emerald-600 transition-all duration-300"
            :style="{ width: verificationStatus.progress + '%' }"
          ></div>
        </div>
      </div>

      <!-- Verification Steps -->
      <div class="space-y-3">
        <template v-for="step in verificationSteps" :key="step.id">
          <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg border" :class="step.status === 'completed' ? 'border-emerald-200' : 'border-gray-200'">
            <!-- Step Icon -->
            <div
              :class="[
                'w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 font-bold',
                step.status === 'completed'
                  ? 'bg-emerald-100 text-emerald-600'
                  : step.status === 'pending'
                  ? 'bg-yellow-100 text-yellow-600'
                  : 'bg-red-100 text-red-600'
              ]"
            >
              <template v-if="step.status === 'completed'">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
              </template>
              <template v-else>
                {{ step.id }}
              </template>
            </div>

            <!-- Step Content -->
            <div class="flex-1">
              <div class="flex items-center justify-between mb-1">
                <h4 class="font-bold text-gray-900">{{ step.title }}</h4>
                <span :class="step.statusColor" class="text-xs font-bold px-2 py-0.5 rounded">
                  {{ step.status }}
                </span>
              </div>
              <p class="text-sm text-gray-600 mb-3">{{ step.description }}</p>

              <!-- Upload Area or Display -->
              <div v-if="step.status === 'pending'" class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                <input type="file" :accept="step.fileType" @change="handleFileUpload(step.id, $event)" class="hidden" :id="`upload-${step.id}`" />
                <label :for="`upload-${step.id}`" class="cursor-pointer block">
                  <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                  </svg>
                  <p class="text-sm font-semibold text-gray-700">Click to upload {{ step.fileType }}</p>
                  <p class="text-xs text-gray-500">or drag and drop</p>
                </label>
              </div>
              <div v-else class="bg-emerald-50 p-3 rounded-lg">
                <p class="text-xs text-emerald-700 font-semibold mb-1">✓ Document verified</p>
                <p class="text-xs text-gray-600">{{ step.fileName }}</p>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>

    <!-- Requirements -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
      <h2 class="text-lg font-bold text-gray-900 mb-4">Professional Documentation Requirements</h2>
      <ul class="space-y-2">
        <li class="flex items-start gap-3">
          <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span class="text-sm text-gray-700">Professional license or certification in real estate</span>
        </li>
        <li class="flex items-start gap-3">
          <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span class="text-sm text-gray-700">Government-issued ID for identity verification</span>
        </li>
        <li class="flex items-start gap-3">
          <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span class="text-sm text-gray-700">Professional references from 2+ clients or organizations</span>
        </li>
      </ul>
    </div>

    <!-- Action Buttons -->
    <div v-if="verificationStatus.label !== 'Verified'" class="flex gap-3">
      <button
        @click="submitVerification"
        class="px-6 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        Submit for Verification
      </button>
    </div>

    <!-- Info -->
    <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
      <p class="text-sm text-blue-700">
        <strong>Note:</strong> Professional verification helps build trust with clients and unlocks premium features.
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const verificationStatus = ref({
  label: 'Pending',
  progress: 33,
  badgeClass: 'bg-yellow-100 text-yellow-700',
})

const verificationSteps = ref([
  {
    id: 1,
    title: 'Professional License',
    description: 'Upload your real estate license or professional certification',
    status: 'completed',
    statusColor: 'bg-emerald-100 text-emerald-700',
    fileType: 'pdf, jpg, png',
    fileName: 'realestate_license.pdf',
  },
  {
    id: 2,
    title: 'Government ID',
    description: 'Upload government-issued ID for verification',
    status: 'pending',
    statusColor: 'bg-yellow-100 text-yellow-700',
    fileType: 'pdf, jpg, png',
    fileName: '',
  },
  {
    id: 3,
    title: 'Professional References',
    description: 'Upload letters of recommendation from clients',
    status: 'pending',
    statusColor: 'bg-yellow-100 text-yellow-700',
    fileType: 'pdf, doc',
    fileName: '',
  },
])

const handleFileUpload = (stepId, event) => {
  const file = event.target.files?.[0]
  if (file) {
    const step = verificationSteps.value.find(s => s.id === stepId)
    if (step) {
      step.status = 'completed'
      step.statusColor = 'bg-emerald-100 text-emerald-700'
      step.fileName = file.name
      alert(`File ${file.name} uploaded successfully!`)
    }
  }
}

const submitVerification = () => {
  const pendingSteps = verificationSteps.value.filter(s => s.status === 'pending')
  if (pendingSteps.length > 0) {
    alert(`Please upload ${pendingSteps.length} remaining document(s)`)
    return
  }
  verificationStatus.value = {
    label: 'Submitted',
    progress: 66,
    badgeClass: 'bg-blue-100 text-blue-700',
  }
  alert('Professional verification submitted! Review will be completed within 24-48 hours.')
}
</script>
