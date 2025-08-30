import axios from 'axios'
import { getToken, logout } from './auth'

const instance = axios.create({
  baseURL: 'http://localhost:8000/api',
})

instance.interceptors.request.use(config => {
  const token = getToken()
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
});

instance.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 401) {
      logout()

      window.location.href = '/login'
    }

    return Promise.reject(error)
  }
)

export default instance
