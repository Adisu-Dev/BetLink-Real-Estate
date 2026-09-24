<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Broker Credentials</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Submit your official license to get verified and build trust with clients.</p>
      </div>

      <!-- Current Status Badge -->
      <div :class="[
        'px-3 py-1.5 rounded-xl border text-xs font-bold flex items-center gap-2 shadow-xs',
        isVerified 
          ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300'
          : 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300'
      ]">
        <span class="w-2 h-2 rounded-full" :class="isVerified ? 'bg-emerald-500' : 'bg-amber-500'"></span>
        <span>{{ isVerified ? 'Verified Broker ✓' : 'Verification Pending' }}</span>
      </div>
    </div>

    <!-- Error State with Retry -->
    <div v-if="apiError" class="p-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-center space-y-3">
      <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ apiError }}</p>
      <button 
        @click="fetchVerifications" 
        class="px-3.5 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold rounded-xl hover:bg-slate-800 transition-colors shadow-xs cursor-pointer"
      >
        Retry Loading
      </button>
    </div>

    <!-- Grid: License Upload Form & Credentials List -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Submission Form (1 Col) -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-300 dark:border-slate-800">
        <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Submit Document</h2>
        <p class="text-xs text-slate-400 mb-4">Upload official licenses or certifications.</p>

        <form @submit.prevent="handleUpload" class="space-y-4 text-xs">
          <!-- Document Type Dropdown -->
          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Document Type *</label>
            <select
              v-model="form.document_type"
              required
              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:outline-none cursor-pointer"
            >
              <option value="broker_license">Broker License</option>
              <option value="commercial_reg">Commercial Registration</option>
              <option value="tax_tin">TIN Certificate</option>
              <option value="national_id">National ID</option>
            </select>
          </div>

          <!-- License Number -->
          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">License / Reg Number *</label>
            <input
              v-model="form.license_number"
              type="text"
              required
              placeholder="e.g. RE-2026-0045"
              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400"
            />
          </div>

          <!-- Document File Upload (Browse / Drag & Drop) -->
          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
              Document File (Image / PDF) *
            </label>
            
            <input
              ref="fileInput"
              type="file"
              accept="image/*,.pdf"
              @change="onFileSelected"
              class="hidden"
            />

            <!-- Drop Zone when no file is selected -->
            <div
              v-if="!selectedFile"
              @click="$refs.fileInput.click()"
              @dragover.prevent
              @drop.prevent="onDrop"
              class="border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-500 rounded-xl p-4 text-center cursor-pointer transition-colors bg-slate-50 dark:bg-slate-800/60"
            >
              <UploadCloud class="w-7 h-7 text-slate-400 mx-auto mb-1.5" />
              <p class="text-xs font-bold text-slate-700 dark:text-slate-200">
                Click to browse or drag & drop file
              </p>
              <p class="text-[10px] text-slate-400 mt-0.5">
                Supports JPG, PNG, PDF (Up to 10MB)
              </p>
            </div>

            <!-- Selected File Preview Badge -->
            <div
              v-else
              class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl"
            >
              <div class="flex items-center gap-2.5 min-w-0">
                <FileText class="w-5 h-5 text-slate-700 dark:text-slate-300 shrink-0" />
                <div class="min-w-0">
                  <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ selectedFile.name }}</p>
                  <p class="text-[10px] text-slate-400">{{ (selectedFile.size / 1024).toFixed(0) }} KB</p>
                </div>
              </div>
              <button
                type="button"
                @click="removeFile"
                class="p-1 text-slate-400 hover:text-rose-500 rounded-lg transition-colors cursor-pointer"
                title="Remove file"
              >
                <X class="w-4 h-4" />
              </button>
            </div>
          </div>

          <!-- Notes -->
          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Notes</label>
            <textarea
              v-model="form.notes"
              rows="2"
              placeholder="Additional license details or issuing agency..."
              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400"
            ></textarea>
          </div>

          <button
            type="submit"
            :disabled="isSubmitting"
            class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl transition-all shadow-xs disabled:opacity-60 cursor-pointer active:scale-95"
          >
            {{ isSubmitting ? 'Uploading...' : 'Submit Credential' }}
          </button>
        </form>
      </div>

      <!-- Submitted Documents Table (2 Cols) -->
      <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-300 dark:border-slate-800 overflow-hidden flex flex-col justify-between">
        <div>
          <div class="px-5 sm:px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Credentials History</h2>
            <span class="text-[11px] text-slate-400 font-semibold">{{ requests.length }} Records</span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left">
              <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-300 dark:border-slate-800">
                <tr>
                  <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Credential</th>
                  <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Details</th>
                  <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Submitted</th>
                  <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                <tr v-for="req in requests" :key="req.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                  <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white capitalize">
                    {{ (req.type || 'License').replace('_', ' ') }}
                  </td>
                  <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400">
                    {{ req.notes || 'Real Estate Credentials' }}
                  </td>
                  <td class="px-5 py-3.5 text-slate-400">
                    {{ req.created_at ? new Date(req.created_at).toLocaleDateString() : 'Recent' }}
                  </td>
                  <td class="px-5 py-3.5 text-right">
                    <span :class="[
                      'px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize',
                      req.status === 'approved' ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400' :
                      req.status === 'rejected' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400' :
                      'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400'
                    ]">
                      {{ req.status || 'Pending' }}
                    </span>
                  </td>
                </tr>

                <tr v-if="requests.length === 0">
                  <td colspan="4" class="px-5 py-8 text-center text-slate-400 text-xs font-medium">
                    No documents submitted yet. Use the form to submit your real estate license.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="p-4 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500">
          💡 Once verified by an administrator, your listings will feature the verified broker badge automatically.
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { UploadCloud, FileText, X } from 'lucide-vue-next'
import { agentService } from '@/services/agentService'
import { useToastStore } from '@/stores/toast'

const toast = useToastStore()

const isLoading = ref(true)
const isSubmitting = ref(false)
const apiError = ref(null)

const isVerified = ref(false)
const requests = ref([])
const selectedFile = ref(null)
const fileInput = ref(null)

const form = ref({
  document_type: 'broker_license',
  license_number: '',
  notes: '',
})

function onFileSelected(event) {
  const file = event.target.files?.[0]
  if (file) {
    if (file.size > 10 * 1024 * 1024) {
      toast.error('File size exceeds 10MB limit.')
      return
    }
    selectedFile.value = file
  }
}

function onDrop(event) {
  const file = event.dataTransfer?.files?.[0]
  if (file) {
    if (file.size > 10 * 1024 * 1024) {
      toast.error('File size exceeds 10MB limit.')
      return
    }
    selectedFile.value = file
  }
}

function removeFile() {
  selectedFile.value = null
  if (fileInput.value) fileInput.value.value = ''
}

async function fetchVerifications() {
  isLoading.value = true
  apiError.value = null

  try {
    const res = await agentService.getVerifications()
    const data = res.data || {}
    isVerified.value = Boolean(data.isVerified)
    requests.value = data.requests || []
  } catch (err) {
    console.error('Failed to load verification status:', err)
    apiError.value = 'Failed to load verification records.'
  } finally {
    isLoading.value = false
  }
}

async function handleUpload() {
  if (!selectedFile.value && !form.value.license_number) {
    toast.error('Please provide your license number and upload a document.')
    return
  }

  isSubmitting.value = true
  try {
    const formData = new FormData()
    formData.append('document_type', form.value.document_type)
    formData.append('license_number', form.value.license_number)
    if (selectedFile.value) {
      formData.append('file', selectedFile.value)
    }
    if (form.value.notes) {
      formData.append('notes', form.value.notes)
    }

    await agentService.submitVerification(formData)
    toast.success('License document uploaded successfully for admin review!')
    form.value.license_number = ''
    form.value.notes = ''
    selectedFile.value = null
    if (fileInput.value) fileInput.value.value = ''
    await fetchVerifications()
  } catch (err) {
    toast.error('Failed to submit credential document.')
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  fetchVerifications()
})
</script>
