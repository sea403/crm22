import { defineStore } from 'pinia'
import api from '@/services/api';

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    loading: false,
    error: null,
  }),
  getters: {
    isLoggedIn: (state) => !!state.user,
  },
  actions: {
    async fetchUser() {
      this.loading = true
      this.error = null
      try {
        const response = await api.get('/profile')
        this.user = response.data.user
      } catch (err) {
        this.error = err.response?.data?.message || err.message
        this.user = null
      } finally {
        this.loading = false
      }
    },
    setUser(user) {
      this.user = user
    },
    logout() {
      this.user = null
    }
  }
})
