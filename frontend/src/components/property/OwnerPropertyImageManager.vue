<template>
  <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-6">
    <!-- Header  -->
    <div class="border-b border-gray-100 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
      <div>
        <h2 class="text-base font-bold text-gray-900"> Photos </h2>
        <p class="text-xs text-gray-500">
          Upload up to {{ maxImages }} high-resolution photos. The primary photo is displayed on search results and property cards.
        </p>
      </div>
      <span class="text-xs font-semibold px-3 py-1 bg-gray-100 text-gray-700 rounded-full self-start sm:self-auto">
        {{ images.length }} / {{ maxImages }} photos
      </span>
    </div>

    <!-- Error Banner -->
    <div v-if="uploadError" class="p-3.5 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs flex items-center justify-between">
      <span class="font-medium">{{ uploadError }}</span>
      <button @click="uploadError = null" class="text-red-500 hover:text-red-700 font-bold ml-2">✕</button>
    </div>

    <!-- Upload Dropzone -->
    <div v-if="images.length < maxImages">
      <label
        for="propertyImageFileInput"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleFileDrop"
        :class="[
          'border-2 border-dashed rounded-2xl p-6 flex flex-col items-center justify-center cursor-pointer transition-colors text-center',
          isDragging ? 'border-emerald-500 bg-emerald-50/30' : 'border-gray-300 hover:border-emerald-400 bg-gray-50/50 hover:bg-emerald-50/10',
          uploading && 'opacity-60 pointer-events-none'
        ]"
      >
        <div v-if="uploading" class="flex flex-col items-center py-2 space-y-2">
          <svg class="animate-spin w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p class="text-xs font-bold text-gray-700">Uploading ...</p>
        </div>

        <template v-else>
          <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <p class="text-sm font-semibold text-gray-700">
            Drag & drop photos here, or <span class="text-emerald-600 underline">browse files</span>
          </p>
          <p class="text-xs text-gray-400 mt-1">
            Supports JPG, JPEG, PNG, WEBP (Max 5MB each, up to {{ maxImages - images.length }} more photos)
          </p>
        </template>

        <input
          id="propertyImageFileInput"
          type="file"
          multiple
          accept="image/jpeg,image/png,image/jpg,image/webp"
          class="hidden"
          :disabled="uploading"
          @change="handleFileInputChange"
        />
      </label>
    </div>

    <!-- Reorder & Management Info -->
    <div v-if="images.length > 1" class="flex items-center justify-between text-xs text-gray-500 bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
      <span class="flex items-center gap-1.5 font-medium">
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
        </svg>
        Drag photos or use arrows to rearrange sort order
      </span>
      <span v-if="isReordering" class="text-emerald-600 font-bold animate-pulse">Saving order...</span>
    </div>

    <!-- Empty State -->
    <div v-if="images.length === 0" class="text-center py-8 text-gray-400">
      <p class="text-xs italic">No photos uploaded yet. High quality photos significantly increase buyer & tenant inquiries.</p>
    </div>

    <!-- Existing Images -->
    <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
      <div
        v-for="(img, index) in images"
        :key="img.id"
        draggable="true"
        @dragstart="onDragStart(index, $event)"
        @dragover.prevent="onDragOver(index)"
        @drop.prevent="onDrop(index)"
        @dragend="onDragEnd"
        :class="[
          'relative group rounded-2xl overflow-hidden border-2 bg-gray-100 transition-all aspect-square flex flex-col',
          img.is_primary ? 'border-emerald-500 shadow-md ring-2 ring-emerald-500/20' : 'border-gray-200 hover:border-gray-300',
          draggedIndex === index && 'opacity-30 scale-95 border-dashed border-emerald-500'
        ]"
      >
        <!-- Photo Image -->
        <img
          :src="img.url"
          :alt="`Property Photo ${index + 1}`"
          class="w-full h-full object-cover select-none pointer-events-none"
          @error="onImageError"
        />

        <!-- Top Badges Overlay -->
        <div class="absolute top-2 left-2 right-2 flex items-center justify-between pointer-events-none">
          <span
            v-if="img.is_primary"
            class="px-2 py-0.5 bg-emerald-600 text-white text-[10px] font-extrabold rounded-md shadow-sm uppercase tracking-wider"
          >
            ★ Primary
          </span>
          <span
            v-else
            class="px-2 py-0.5 bg-black/60 backdrop-blur-sm text-white text-[10px] font-bold rounded-md"
          >
            #{{ index + 1 }}
          </span>

          <!-- Delete Button -->
          <button
            type="button"
            @click.stop="promptDelete(img)"
            class="pointer-events-auto bg-red-600/90 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow-md transition-transform hover:scale-110"
            title="Delete Photo"
          >
            ✕
          </button>
        </div>

        <!-- Bottom Action Toolbar  -->
        <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent p-2.5 flex items-center justify-between gap-1 opacity-90 sm:opacity-0 group-hover:opacity-100 transition-opacity">
          <div class="flex items-center gap-1">
            <button
              type="button"
              :disabled="index === 0 || isReordering"
              @click.stop="moveImage(index, index - 1)"
              class="p-1 bg-white/20 hover:bg-white/40 text-white rounded-lg disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
              title="Move Left"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <button
              type="button"
              :disabled="index === images.length - 1 || isReordering"
              @click.stop="moveImage(index, index + 1)"
              class="p-1 bg-white/20 hover:bg-white/40 text-white rounded-lg disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
              title="Move Right"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>

          <button
            v-if="!img.is_primary"
            type="button"
            :disabled="isSettingPrimary === img.id"
            @click.stop="setPrimary(img)"
            class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold rounded-lg transition-colors shadow-sm disabled:opacity-50"
          >
            {{ isSettingPrimary === img.id ? 'Setting...' : 'Set Primary' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      :is-open="showDeleteConfirm"
      title="Delete Property Photo"
      message="Are you sure you want to delete this photo?"
      confirm-label="Yes, Delete"
      :danger="true"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { propertyService } from '@/services/propertyService'
import { useToast } from '@/composables/useToast'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'

const props = defineProps({
  propertyId: {
    type: [Number, String],
    required: true,
  },
  initialImages: {
    type: Array,
    default: () => [],
  },
  maxImages: {
    type: Number,
    default: 20,
  },
})

const emit = defineEmits(['update:images', 'change'])

const { success, error } = useToast()

const images = ref([...props.initialImages])
const isDragging = ref(false)
const uploading = ref(false)
const uploadError = ref(null)

const showDeleteConfirm = ref(false)
const imageToDelete = ref(null)
const isDeleting = ref(false)
const isSettingPrimary = ref(null)
const isReordering = ref(false)
const draggedIndex = ref(null)

const fallbackImage = 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=300&q=80'

watch(
  () => props.initialImages,
  (newVal) => {
    if (Array.isArray(newVal)) {
      images.value = [...newVal]
    }
  },
  { deep: true }
)

// File Selection & Upload
const handleFileInputChange = (event) => {
  const files = Array.from(event.target.files || [])
  processFiles(files)
  event.target.value = ''
}

const handleFileDrop = (event) => {
  isDragging.value = false
  const files = Array.from(event.dataTransfer.files || [])
  processFiles(files)
}

const processFiles = async (files) => {
  uploadError.value = null
  if (!files.length) return

  const allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']
  const maxSizeBytes = 5 * 1024 * 1024 // 5MB

  const validFiles = []
  for (const file of files) {
    if (!allowedMimes.includes(file.type.toLowerCase())) {
      uploadError.value = `File '${file.name}' is not supported. Use JPG, PNG, or WEBP.`
      error('Invalid File Type', uploadError.value)
      return
    }
    if (file.size > maxSizeBytes) {
      uploadError.value = `File '${file.name}' exceeds the 5MB size limit.`
      error('File Too Large', uploadError.value)
      return
    }
    validFiles.push(file)
  }

  if (images.value.length + validFiles.length > props.maxImages) {
    const remaining = props.maxImages - images.value.length
    uploadError.value = `Maximum ${props.maxImages} photos allowed. You can only add ${remaining} more photo(s).`
    error('Limit Exceeded', uploadError.value)
    return
  }

  uploading.value = true
  try {
    const formData = new FormData()
    validFiles.forEach((file) => {
      formData.append('images[]', file)
    })

    const res = await propertyService.uploadImages(props.propertyId, formData)
    const newUploaded = res.data?.data || res.data || []

    if (Array.isArray(newUploaded)) {
      images.value = [...images.value, ...newUploaded]
    } else if (newUploaded && typeof newUploaded === 'object') {
      images.value.push(newUploaded)
    }

    emit('update:images', images.value)
    emit('change', images.value)
    success('Upload Complete', `${validFiles.length} photo(s) uploaded successfully.`)
  } catch (err) {
    console.error('Failed to upload photos:', err)
    uploadError.value = err.response?.data?.message || 'Failed to upload photos. Please try again.'
    error('Upload Failed', uploadError.value)
  } finally {
    uploading.value = false
  }
}

const setPrimary = async (image) => {
  isSettingPrimary.value = image.id
  try {
    await propertyService.setPrimaryImage(props.propertyId, image.id)
    images.value.forEach((img) => {
      img.is_primary = img.id === image.id
    })
    emit('update:images', images.value)
    emit('change', images.value)
    success('Primary Photo Updated', 'This photo is now set as the primary listing photo.')
  } catch (err) {
    console.error('Failed to set primary photo:', err)
    const msg = err.response?.data?.message || 'Failed to set primary photo.'
    error('Update Failed', msg)
  } finally {
    isSettingPrimary.value = null
  }
}

// Delete Image
const promptDelete = (image) => {
  imageToDelete.value = image
  showDeleteConfirm.value = true
}

const confirmDelete = async () => {
  if (!imageToDelete.value) return
  isDeleting.value = true

  const imageId = imageToDelete.value.id
  try {
    await propertyService.deleteImage(props.propertyId, imageId)
    images.value = images.value.filter((img) => img.id !== imageId)

    // If deleted image was primary and others exist, set first as primary
    if (imageToDelete.value.is_primary && images.value.length > 0) {
      images.value[0].is_primary = true
    }

    emit('update:images', images.value)
    emit('change', images.value)
    success('Photo Deleted', 'The photo was removed from your property.')
    showDeleteConfirm.value = false
    imageToDelete.value = null
  } catch (err) {
    console.error('Failed to delete photo:', err)
    const msg = err.response?.data?.message || 'Failed to delete photo.'
    error('Delete Failed', msg)
  } finally {
    isDeleting.value = false
  }
}

const cancelDelete = () => {
  showDeleteConfirm.value = false
  imageToDelete.value = null
}

// Drag and Drop & Manual Reorder
const onDragStart = (index, event) => {
  draggedIndex.value = index
  event.dataTransfer.effectAllowed = 'move'
}

const onDragOver = (index) => {
}

const onDrop = (targetIndex) => {
  if (draggedIndex.value === null || draggedIndex.value === targetIndex) {
    draggedIndex.value = null
    return
  }
  const item = images.value.splice(draggedIndex.value, 1)[0]
  images.value.splice(targetIndex, 0, item)
  draggedIndex.value = null
  persistReorder()
}

const onDragEnd = () => {
  draggedIndex.value = null
}

const moveImage = (fromIndex, toIndex) => {
  if (toIndex < 0 || toIndex >= images.value.length) return
  const item = images.value.splice(fromIndex, 1)[0]
  images.value.splice(toIndex, 0, item)
  persistReorder()
}

const persistReorder = async () => {
  isReordering.value = true
  const orderIds = images.value.map((img) => img.id)

  try {
    await propertyService.reorderImages(props.propertyId, orderIds)
    emit('update:images', images.value)
    emit('change', images.value)
  } catch (err) {
    console.error('Failed to persist photo order:', err)
    error('Reorder Failed', 'Could not save the new photo order on server.')
  } finally {
    isReordering.value = false
  }
}

const onImageError = (event) => {
  event.target.src = fallbackImage
}
</script>
