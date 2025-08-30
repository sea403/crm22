import axios from './api'

export const login = async (email, password) => {
  try {
    const res = await axios.post('/login', { email, password })
    localStorage.setItem('token', res.data.token)
    return res.data.token;
  } catch (e) {
    console.error('Login failed:', e)
    return false
  }
}


export const register = async (email, password) => {
  try {
    const res = await axios.post('/login', { email, password })
    localStorage.setItem('token', res.data.token)
    return true
  } catch (e) {
    console.error('Login failed:', e)
    return false
  }
}

export const logout = () => {
  localStorage.removeItem('token')
}

export const getToken = () => localStorage.getItem('token')
