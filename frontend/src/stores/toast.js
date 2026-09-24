import { defineStore } from 'pinia'

export const useToastStore = defineStore('toast', {
  state: () => ({
    toasts: [],
  }),

  actions: {
    /**
     * Add a toast notification
     * @param {Object} toast { id, type, title, message, duration }
     */
    add({ type = 'info', title = '', message = '', duration = 3500 }) {
      const id = Date.now() + Math.random().toString(36).substring(2, 9)
      const safeDuration = Math.min(Number(duration) || 3500, 4000)
      const toast = { id, type, title, message, duration: safeDuration }
      this.toasts.push(toast)

      if (safeDuration > 0) {
        setTimeout(() => {
          this.remove(id)
        }, safeDuration)
      }

      return id
    },

    success(message, title = 'Success', duration = 3000) {
      return this.add({ type: 'success', title, message, duration })
    },

    error(message, title = 'Error', duration = 4000) {
      return this.add({ type: 'error', title, message, duration })
    },

    warning(message, title = 'Warning', duration = 3500) {
      return this.add({ type: 'warning', title, message, duration })
    },

    info(message, title = 'Info', duration = 3000) {
      return this.add({ type: 'info', title, message, duration })
    },

    remove(idOrToast) {
      const targetId = typeof idOrToast === 'object' ? idOrToast?.id : idOrToast
      this.toasts = this.toasts.filter((t) => t.id !== targetId)
    },

    clearAll() {
      this.toasts = []
    },
  },
})
