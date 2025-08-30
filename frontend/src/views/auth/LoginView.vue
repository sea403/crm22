<template>
    <div class="login-view">
        <div class="login-box">
            <h2>Login to MyCRM</h2>
            <form @submit.prevent="handleLogin">
                <BaseInput :hide_lebel="true" v-model="email" type="email" placeholder="Email" required autocomplete="username" />
                <BaseInput :hide_lebel="true" v-model="password" type="password" placeholder="Password" required
                    autocomplete="current-password" />
                <BaseButton :loading="loading" type="submit" variant="primary">
                    Login
                </BaseButton>
            </form>
            <p v-if="error" class="error">{{ error }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import { login as apiLogin } from '@/services/auth'
import { useAuth } from '@/composables/useAuth'

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')
const router = useRouter()
const { login } = useAuth();

async function handleLogin() {
    error.value = ''
    loading.value = true

    const token = await apiLogin(email.value, password.value)

    loading.value = false
    
    if (token) {
        login(token);
        router.push('/home')
    } else {
        error.value = 'Invalid email or password.'
    }
}
</script>

<style lang="less" scoped>
.login-view {
    display: flex;
    height: 100vh;
    justify-content: center;
    align-items: center;
    background: #f5f7fa;

    .login-box {
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
    }
}
</style>