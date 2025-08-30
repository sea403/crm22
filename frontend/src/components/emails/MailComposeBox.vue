<script setup>
import { ref, watch } from 'vue'
import BaseButton from '../base/BaseButton.vue'
import BaseInput from '../base/BaseInput.vue'
import RichEditor from '../base/RichEditor.vue'
import { useToast } from 'vue-toastification'
import { createRecord } from '@/services/jsonrpc'
const toast = useToast();

const form = ref({
  to: '',
  subject: '',
  body: ''
})

const emit = defineEmits(['close'])
const sending = ref(false);

const sendMail = async () => {
  sending.value = true;

  // do something with this reponse later 
  const response = await createRecord('Email', form.value);

  toast.success('Email sent successfully');

  closeCompose();
  
  sending.value = false;
}

const saveDraft = () => {
  console.log('Draft saved:', form.value)
}

const closeCompose = () => {
  emit('close')
}
</script>

<template>
  <div class="overlay" @click.self="closeCompose">
    <div class="mail-compose-box animate-in">
      <div class="mail-compose-box-header">
        <h3>Compose New Mail</h3>
        <BaseButton class="close-btn" variant="ghost" @click="closeCompose">✕</BaseButton>
      </div>

      <div class="mail-compose-box-body">
        <form @submit.prevent="sendMail">
          <BaseInput class="mb-3" v-model="form.to" name="to" placeholder="Recipient Email" />
          <BaseInput class="mb-3" v-model="form.subject" name="subject" placeholder="Mail Subject" />
          <label class="mb-2 d-block font-semibold">Message Body</label>
          <RichEditor v-model="form.body" />

          <div class="mb-4"></div>

          <div class="actions ">
            <BaseButton type="button" size="sm" variant="secondary" @click="saveDraft">Save Draft</BaseButton>
            <BaseButton :loading="sending" size="sm" type="submit">Send</BaseButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style lang="less" scoped>
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.4);
  z-index: 9998;
  display: flex;
  justify-content: flex-end;
}

.mail-compose-box {
  width: 100%;
  max-width: 500px;
  height: 100%;
  background: #fff;
  box-shadow: -4px 0 15px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  animation: slideIn 0.3s ease-out;
  z-index: 9999;

  &-header {
    padding: 1rem;
    background: #f5f5f5;
    display: flex;
    justify-content: space-between;
    align-items: center;

    h3 {
      margin: 0;
      font-size: 1.2rem;
    }
  }

  &-body {
    padding: 1rem;
    flex: 1;
    overflow-y: auto;
  }

  .close-btn {
    font-size: 1.2rem;
    line-height: 1;
  }

  .actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
  }
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
  }

  to {
    transform: translateX(0%);
  }
}
</style>
