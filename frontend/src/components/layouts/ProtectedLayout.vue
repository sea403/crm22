<template>
    <div v-if="userStore.loading">
        Loading...
    </div>
    <template v-else>
        <AppHeader v-if="isLoggedIn" />
        <RouterView />
    </template>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth';
import AppHeader from '../layout/AppHeader.vue';
import { useUserStore } from '@/src/stores/user';
import { onMounted } from 'vue';

const userStore = useUserStore();
const { isLoggedIn } = useAuth();

onMounted(async () => {
    await userStore.fetchUser();
});
</script>
