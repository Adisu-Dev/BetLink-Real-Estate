import { useToastStore } from '@/stores/toast'

export function useToast() {
  const toastStore = useToastStore()

  const showToast = (title, options = {}) => {
    const type = options.type || 'info'
    const message = options.message || ''
    const duration = options.duration || 4000
    return toastStore.add({ type, title, message, duration })
  }

  const success = (message, title = 'Success') => {
    return toastStore.success(message, title)
  }

  const error = (message, title = 'Error') => {
    return toastStore.error(message, title)
  }

  const info = (message, title = 'Info') => {
    return toastStore.info(message, title)
  }

  const warning = (message, title = 'Warning') => {
    return toastStore.warning(message, title)
  }

  return {
    showToast,
    success,
    showSuccess: success,
    error,
    showError: error,
    info,
    warning,
    toastStore,
    setToastInstance: () => {}
  }
}
