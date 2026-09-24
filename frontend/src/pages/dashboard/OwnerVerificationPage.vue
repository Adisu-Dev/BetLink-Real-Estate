<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Property & Landlord Verification</h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Submit official ownership documents to verify your landlord profile.</p>
      </div>
      <div v-if="isProfileVerified" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 text-xs font-bold shrink-0">
        <CheckCircle2 class="w-4 h-4" />
        <span>Verified Landlord</span>
      </div>
    </div>

    <!-- Verification Documents  -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 space-y-5">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
          <FileText class="w-4 h-4 text-slate-900 dark:text-white" />
          <span>Required Ownership Documents</span>
        </h2>
        <span class="text-xs text-slate-500 font-medium">Fast 24-Hour Review</span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="doc in documentTypes"
          :key="doc.id"
          class="p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs hover:border-slate-300 dark:hover:border-slate-700 transition-all flex flex-col justify-between space-y-4"
        >
          <!-- Top Section -->
          <div class="space-y-2">
            <div class="flex items-start justify-between gap-2">
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ doc.title }}</h3>
              <span
                :class="[
                  'px-2.5 py-0.5 text-[10px] font-extrabold rounded-md uppercase tracking-wider shrink-0 border',
                  getStatusBadgeClass(doc.status)
                ]"
              >
                {{ formatStatus(doc.status) }}
              </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{{ doc.description }}</p>

            <!--Submitted Document Box  -->
            <div
              v-if="doc.fileName"
              class="mt-3 p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/70 dark:border-slate-700/70 flex items-center justify-between gap-3"
            >
              <div class="flex items-center gap-2.5 min-w-0 flex-1">
                <div class="w-8 h-8 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center shrink-0 text-slate-700 dark:text-slate-300">
                  <FileText class="w-4 h-4" />
                </div>
                <div class="min-w-0">
                  <p class="text-xs font-bold text-slate-900 dark:text-white truncate" :title="doc.fileName">{{ doc.fileName }}</p>
                  <p class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center gap-1.5 mt-0.5">
                    <span v-if="doc.submittedAt">Uploaded {{ formatShortDate(doc.submittedAt) }}</span>
                    <span v-if="doc.submittedAt">•</span>
                    <span :class="getStatusTextColor(doc.status)">
                      {{ getStatusLabel(doc.status) }}
                    </span>
                  </p>
                  <p v-if="doc.adminNotes && isRejected(doc.status)" class="text-[11px] text-rose-600 dark:text-rose-400 font-medium mt-1">
                    Feedback: {{ doc.adminNotes }}
                  </p>
                </div>
              </div>

              <button
                type="button"
                @click="openPreviewModal(doc)"
                class="px-3 py-1.5 bg-white hover:bg-slate-100 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-600 rounded-lg text-xs font-bold transition-all shadow-2xs cursor-pointer active:scale-95 flex items-center gap-1.5 shrink-0"
                title="Preview submitted document"
              >
                <Eye class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" />
                <span>View</span>
              </button>
            </div>

            <div
              v-else
              class="mt-3 p-3.5 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/20 flex items-center gap-3 text-slate-400"
            >
              <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                <UploadCloud class="w-4 h-4 text-slate-400" />
              </div>
              <div class="min-w-0">
                <p class="text-xs font-medium text-slate-600 dark:text-slate-400">No document submitted yet</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Supports PDF, PNG, JPG (Max 10MB)</p>
              </div>
            </div>
          </div>

          <!-- Bottom Footer: Information & Action -->
          <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
            <span class="text-[11px] text-slate-400 font-medium">
              {{ doc.fileName ? (isApproved(doc.status) ? 'Verified by Administration' : 'Verification Document') : 'Required for verified status' }}
            </span>

            <button
              type="button"
              @click="openUploadModal(doc)"
              class="px-3.5 py-1.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-900 dark:text-white border border-slate-300 dark:border-slate-600 rounded-xl text-xs font-bold transition-all shadow-2xs cursor-pointer active:scale-95 flex items-center gap-1.5"
            >
              <Upload class="w-3.5 h-3.5" />
              <span>{{ doc.fileName ? 'Replace Document' : 'Upload Document' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Upload Document Modal (Device File Picker) -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-md p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
          <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ selectedDoc?.fileName ? 'Replace' : 'Upload' }} {{ selectedDoc?.title }}</h3>
          <button @click="showModal = false" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-white cursor-pointer">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitDoc" class="space-y-4">
          <!-- Hidden Native File Input -->
          <input
            ref="fileInputRef"
            type="file"
            accept=".pdf,.png,.jpg,.jpeg,.doc,.docx"
            class="hidden"
            @change="handleFileChange"
          />

          <!-- Device File Selector Dropzone -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
              Select Document File <span class="text-rose-500">*</span>
            </label>

            <!-- If File Selected -->
            <div
              v-if="selectedFile"
              class="p-4 bg-slate-50 dark:bg-slate-800/60 border-2 border-slate-300 dark:border-slate-700 rounded-2xl flex items-center justify-between gap-3"
            >
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-900 flex items-center justify-center shrink-0 shadow-xs">
                  <FileText class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                  <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ selectedFile.name }}</p>
                  <p class="text-[10px] text-slate-400 font-mono">{{ formatFileSize(selectedFile.size) }}</p>
                </div>
              </div>

              <div class="flex items-center gap-1.5 shrink-0">
                <button
                  type="button"
                  @click="triggerFileInput"
                  class="px-2.5 py-1 text-[11px] font-bold text-slate-700 dark:text-slate-300 bg-slate-200 dark:bg-slate-700 rounded-lg hover:bg-slate-300 transition-colors cursor-pointer"
                >
                  Change
                </button>
                <button
                  type="button"
                  @click="removeSelectedFile"
                  class="p-1 text-slate-400 hover:text-rose-500 transition-colors cursor-pointer"
                >
                  <X class="w-4 h-4" />
                </button>
              </div>
            </div>

            <!-- Empty Dropzone Box -->
            <div
              v-else
              @click="triggerFileInput"
              class="p-6 border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-slate-900 dark:hover:border-slate-400 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 text-center cursor-pointer transition-all flex flex-col items-center justify-center space-y-2 group"
            >
              <div class="w-12 h-12 rounded-2xl bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-700 dark:text-slate-300 group-hover:scale-105 transition-transform">
                <UploadCloud class="w-6 h-6" />
              </div>
              <div>
                <p class="text-xs font-bold text-slate-900 dark:text-white">Click to browse file from your device</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Supports PDF, JPG, PNG, DOCX (Max 10MB)</p>
              </div>
            </div>
          </div>

          <!-- Optional Notes / Reference Number -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
              Registry Reference / Notes (Optional)
            </label>
            <textarea
              v-model="uploadForm.notes"
              rows="2"
              placeholder="Official registry reference number or notes..."
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:outline-none transition-colors"
            ></textarea>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
            <button
              type="button"
              @click="showModal = false"
              class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl cursor-pointer transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isSubmitting || !selectedFile"
              class="px-5 py-2 text-xs font-bold bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl cursor-pointer shadow-xs transition-colors disabled:opacity-50 flex items-center gap-1.5"
            >
              <Upload class="w-3.5 h-3.5" v-if="!isSubmitting" />
              <span>{{ isSubmitting ? 'Uploading...' : 'Submit Document' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Document Preview Modal -->
    <div v-if="showPreviewModal" class="fixed inset-0 z-[100000] flex items-center justify-center p-4 bg-black/70 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-lg p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150 text-left">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
          <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
              <FileText class="w-5 h-5 text-slate-700 dark:text-slate-300" />
            </div>
            <div>
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ previewDoc?.title }}</h3>
              <p class="text-[11px] text-slate-400 truncate max-w-xs">{{ previewDoc?.fileName }}</p>
            </div>
          </div>
          <button @click="showPreviewModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-white cursor-pointer rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Document Preview Canvas / Details Card -->
        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-3">
          <div class="flex items-center justify-between text-xs">
            <span class="text-slate-500">Verification Status:</span>
            <span
              :class="[
                'px-2.5 py-0.5 text-[10px] font-extrabold rounded-md uppercase tracking-wider border',
                getStatusBadgeClass(previewDoc?.status)
              ]"
            >
              {{ formatStatus(previewDoc?.status) }}
            </span>
          </div>
          <div class="flex items-center justify-between text-xs" v-if="previewDoc?.submittedAt">
            <span class="text-slate-500">Submitted Date:</span>
            <span class="font-bold text-slate-800 dark:text-slate-200">{{ formatFullDate(previewDoc?.submittedAt) }}</span>
          </div>
          <div v-if="previewDoc?.adminNotes" class="text-xs pt-2 border-t border-slate-200/60 dark:border-slate-700">
            <span class="text-slate-500 block mb-1 font-semibold">Admin Notes:</span>
            <p class="text-slate-800 dark:text-slate-200 bg-white dark:bg-slate-900 p-2.5 rounded-lg border border-slate-200/60 dark:border-slate-700 text-xs">{{ previewDoc.adminNotes }}</p>
          </div>
          <div v-if="previewDoc?.notes" class="text-xs pt-2 border-t border-slate-200/60 dark:border-slate-700">
            <span class="text-slate-500 block mb-1">Registry Reference:</span>
            <p class="font-mono text-slate-800 dark:text-slate-200 bg-white dark:bg-slate-900 p-2 rounded-lg border border-slate-200/60 dark:border-slate-700">{{ previewDoc.notes }}</p>
          </div>

          <!-- Document Preview Visual Box -->
          <div class="mt-3 p-6 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 text-center space-y-2">
            <img
              v-if="previewDoc?.fileUrl && isImageFile(previewDoc?.fileName)"
              :src="previewDoc.fileUrl"
              class="max-h-56 mx-auto rounded-lg object-contain shadow-xs"
            />
            <div v-else class="py-6 flex flex-col items-center justify-center space-y-2 text-slate-400">
              <FileText class="w-12 h-12 text-slate-400" />
              <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ previewDoc?.fileName }}</p>
              <p class="text-[10px] text-slate-400">Official KYC document submitted for Admin approval</p>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between pt-2">
          <button
            type="button"
            @click="openUploadModal(previewDoc); showPreviewModal = false"
            class="text-xs font-bold text-slate-600 dark:text-slate-400 hover:underline cursor-pointer"
          >
            Upload New Version
          </button>
          <button
            type="button"
            @click="showPreviewModal = false"
            class="px-5 py-2 text-xs font-bold bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl cursor-pointer"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { FileText, X, Upload, UploadCloud, Eye, CheckCircle2 } from 'lucide-vue-next'
import { ownerService } from '@/services/ownerService'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'

const authStore = useAuthStore()
const toastStore = useToastStore()

const showModal = ref(false)
const showPreviewModal = ref(false)
const previewDoc = ref(null)
const selectedDoc = ref(null)
const selectedFile = ref(null)
const fileInputRef = ref(null)
const isSubmitting = ref(false)
const isProfileVerified = ref(false)

const documentTypes = ref([
  {
    id: 'title_deed',
    title: 'Property Title Deed',
    description: 'Official Land Administration property registration document.',
    status: 'not_submitted',
    fileName: '',
    submittedAt: null,
    adminNotes: '',
    fileUrl: ''
  },
  {
    id: 'id_card',
    title: 'Owner Kebele / National ID',
    description: 'Valid Kebele ID card or Passport for landlord identity check.',
    status: 'not_submitted',
    fileName: '',
    submittedAt: null,
    adminNotes: '',
    fileUrl: ''
  },
  {
    id: 'lease_contract',
    title: 'Rental Agreement Template',
    description: 'Standard lease contract template provided to prospective tenants.',
    status: 'not_submitted',
    fileName: '',
    submittedAt: null,
    adminNotes: '',
    fileUrl: ''
  },
  {
    id: 'business_license',
    title: 'Commercial Business License',
    description: 'Required only for commercial real estate properties.',
    status: 'not_submitted',
    fileName: '',
    submittedAt: null,
    adminNotes: '',
    fileUrl: ''
  }
])

const uploadForm = reactive({
  notes: ''
})

function isApproved(status) {
  return ['approved', 'verified'].includes((status || '').toLowerCase())
}

function isRejected(status) {
  return (status || '').toLowerCase() === 'rejected'
}

function isPending(status) {
  return (status || '').toLowerCase() === 'pending'
}

function getStatusBadgeClass(status) {
  if (isApproved(status)) {
    return 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800'
  }
  if (isRejected(status)) {
    return 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800'
  }
  if (isPending(status)) {
    return 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800'
  }
  return 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700'
}

function getStatusTextColor(status) {
  if (isApproved(status)) return 'text-emerald-600 dark:text-emerald-400 font-bold'
  if (isRejected(status)) return 'text-rose-600 dark:text-rose-400 font-bold'
  if (isPending(status)) return 'text-amber-600 dark:text-amber-400 font-medium'
  return 'text-slate-500 font-medium'
}

function getStatusLabel(status) {
  if (isApproved(status)) return 'Approved'
  if (isRejected(status)) return 'Rejected'
  if (isPending(status)) return 'Under Review'
  return 'Not Submitted'
}

function formatStatus(status) {
  if (!status || status === 'not_submitted') return 'NOT SUBMITTED'
  if (isApproved(status)) return 'APPROVED'
  if (isRejected(status)) return 'REJECTED'
  if (isPending(status)) return 'PENDING'
  return String(status).toUpperCase()
}

async function loadVerifications() {
  try {
    const res = await ownerService.getVerifications()
    const payload = res?.data || {}
    const docsMap = payload.documents || {}

    if (typeof payload.is_profile_verified === 'boolean') {
      isProfileVerified.value = payload.is_profile_verified
    } else if (authStore.user?.profile?.is_verified) {
      isProfileVerified.value = true
    }

    // Reset default unsubmitted state
    documentTypes.value.forEach(d => {
      d.status = 'not_submitted'
      d.fileName = ''
      d.submittedAt = null
      d.adminNotes = ''
      d.fileUrl = ''
    })

    if (docsMap && typeof docsMap === 'object') {
      Object.entries(docsMap).forEach(([typeKey, docData]) => {
        const found = documentTypes.value.find(d => d.id === typeKey || (typeKey === 'national_id' && d.id === 'id_card'))
        if (found && docData) {
          found.status = (docData.status || 'pending').toLowerCase()
          found.fileName = docData.file_name || docData.fileName || 'Uploaded_Document.pdf'
          found.submittedAt = docData.submitted_at || docData.submittedAt || null
          found.adminNotes = docData.admin_notes || docData.notes || ''
          found.fileUrl = docData.url || docData.document_url || ''
        }
      })
    }

    // Process requests array if available
    if (Array.isArray(payload.requests)) {
      payload.requests.forEach(req => {
        if (req.documents && typeof req.documents === 'object') {
          Object.entries(req.documents).forEach(([tKey, dData]) => {
            const f = documentTypes.value.find(d => d.id === tKey || (tKey === 'national_id' && d.id === 'id_card'))
            if (f && dData) {
              f.status = (req.status || dData.status || 'pending').toLowerCase()
              f.fileName = dData.file_name || f.fileName || 'Uploaded_Document.pdf'
              f.submittedAt = dData.submitted_at || f.submittedAt || req.created_at
              f.adminNotes = req.notes || dData.admin_notes || f.adminNotes
              f.fileUrl = dData.url || dData.document_url || f.fileUrl
            }
          })
        }
      })
    }
  } catch (err) {
    console.error('Failed to load server verifications:', err)
  }
}

function triggerFileInput() {
  fileInputRef.value?.click()
}

function handleFileChange(event) {
  const file = event.target.files?.[0]
  if (!file) return

  // Check file size (10MB limit)
  if (file.size > 10 * 1024 * 1024) {
    toastStore.error('File size exceeds 10MB limit.')
    return
  }

  selectedFile.value = file
}

function removeSelectedFile() {
  selectedFile.value = null
  if (fileInputRef.value) {
    fileInputRef.value.value = ''
  }
}

function openUploadModal(doc) {
  selectedDoc.value = doc
  selectedFile.value = null
  uploadForm.notes = ''
  if (fileInputRef.value) {
    fileInputRef.value.value = ''
  }
  showModal.value = true
}

async function submitDoc() {
  if (!selectedFile.value) {
    toastStore.error('Please select a file from your device.')
    return
  }

  isSubmitting.value = true
  const docId = selectedDoc.value?.id || 'title_deed'

  try {
    const formData = new FormData()
    formData.append('document_type', docId)
    formData.append('file', selectedFile.value)
    if (uploadForm.notes) {
      formData.append('notes', uploadForm.notes)
    }

    await ownerService.submitVerification(formData)

    if (selectedDoc.value) {
      selectedDoc.value.status = 'pending'
      selectedDoc.value.fileName = selectedFile.value.name
      selectedDoc.value.submittedAt = new Date().toISOString()
      selectedDoc.value.adminNotes = ''
    }

    await loadVerifications()

    toastStore.success('Document uploaded and saved successfully.')
    showModal.value = false
  } catch (err) {
    console.error('Submit error:', err)
    toastStore.error('Failed to submit document. Please try again.')
  } finally {
    isSubmitting.value = false
  }
}

function formatFileSize(bytes) {
  if (!bytes) return '0 KB'
  const kb = bytes / 1024
  if (kb < 1024) return `${kb.toFixed(1)} KB`
  return `${(kb / 1024).toFixed(2)} MB`
}

function formatShortDate(dateStr) {
  if (!dateStr) return ''
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
  } catch {
    return ''
  }
}

function openPreviewModal(doc) {
  if (!doc) return
  previewDoc.value = doc
  showPreviewModal.value = true
}

function isImageFile(filename) {
  if (!filename) return false
  const ext = filename.split('.').pop().toLowerCase()
  return ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)
}

function formatFullDate(dateStr) {
  if (!dateStr) return 'Recently submitted'
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
  } catch {
    return dateStr
  }
}

onMounted(() => {
  loadVerifications()
})
</script>
