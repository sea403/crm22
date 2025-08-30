<!-- GmailCallBack.vue -->
<template>
  <div class="flex items-center justify-center h-screen">
    <div>
      <p class="text-lg">Connecting your Gmail account...</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from '@/services/api';

const router = useRouter();

onMounted(async () => {
  const urlParams = new URLSearchParams(window.location.search);
  const code = urlParams.get('code');
  // const state = urlParams.get('state');

  if (code) {
    try {
      await axios.post('/gmail/callback', { code });
      alert('✅ Gmail connected!');
      router.push('/home'); 
    } catch (err) {
      console.error('OAuth error:', err.response?.data || err.message);
      alert('❌ Failed to connect Gmail.');
      router.push('/settings'); 
    }
  } else {
    alert('❌ Missing authorization code.');
    router.push('/');
  }
});
</script>
