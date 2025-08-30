<template>
    <div class="register-view">
      <div class="register-box">
        <h2>Create an Account</h2>
        <form @submit.prevent="handleRegister">
          <BaseInput
            v-model="name"
            type="text"
            placeholder="Full Name"
            required
            autocomplete="name"
          />
          <BaseInput
            v-model="email"
            type="email"
            placeholder="Email"
            required
            autocomplete="email"
          />
          <BaseInput
            v-model="password"
            type="password"
            placeholder="Password"
            required
            autocomplete="new-password"
            minlength="6"
          />
          <BaseInput
            v-model="passwordConfirm"
            type="password"
            placeholder="Confirm Password"
            required
            autocomplete="new-password"
            minlength="6"
          />
          <BaseButton :loading="loading" type="submit" variant="primary">
            Register
          </BaseButton>
        </form>
        <p v-if="error" class="error">{{ error }}</p>
        <p class="redirect">
          Already have an account?
          <router-link to="/login">Login here</router-link>
        </p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import { useRouter } from 'vue-router'
  import BaseInput from '@/components/base/BaseInput.vue'
  import BaseButton from '@/components/base/BaseButton.vue'
  import { register } from '@/services/auth'
  
  const name = ref('')
  const email = ref('')
  const password = ref('')
  const passwordConfirm = ref('')
  const loading = ref(false)
  const error = ref('')
  const router = useRouter()
  
  async function handleRegister() {
    error.value = ''
  
    if (password.value !== passwordConfirm.value) {
      error.value = 'Passwords do not match.'
      return
    }
  
    loading.value = true
  
    const success = await register({
      name: name.value,
      email: email.value,
      password: password.value
    })
  
    loading.value = false
  
    if (success) {
      router.push('/login')
    } else {
      error.value = 'Registration failed. Please try again.'
    }
  }
  </script>
  
  <style lang="less" scoped>
  .register-view {
    display: flex;
    height: 100vh;
    justify-content: center;
    align-items: center;
    background: #f5f7fa;
  
    .register-box {
      background: white;
      padding: 32px 40px;
      border-radius: 8px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
      width: 320px;
      text-align: center;
  
      h2 {
        margin-bottom: 24px;
        color: #4a90e2;
      }
  
      form {
        display: flex;
        flex-direction: column;
        gap: 16px;
      }
  
      .error {
        margin-top: 12px;
        color: #e04f5f;
        font-weight: 600;
      }
  
      .redirect {
        margin-top: 16px;
        font-size: 0.9rem;
  
        a {
          color: #4a90e2;
          text-decoration: none;
          font-weight: 600;
  
          &:hover {
            text-decoration: underline;
          }
        }
      }
    }
  }
  </style>
  