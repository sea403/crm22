<template>
  <div class="page-header">
    <div>
      <h2>{{ $t('communicationSetting') }}</h2>
      <p>{{ $t('communicationAndNotification') }}</p>
    </div>

    <BaseButton @click="handleImapSave" :disabled="loading">
      <template v-if="loading">Saving...</template>
      <template v-else>{{ $t('save') }}</template>
    </BaseButton>
  </div>

  <div class="page-content">
    <!-- IMAP Settings -->
    <section>
      <div class="page-content-section-header">
        <p>IMAP Configuration</p>
      </div>
      <div class="page-content-section-content">
        <table>
          <tr>
            <td><label class="text-12">{{ $t('host') }}</label></td>
            <td>
              <BaseInput v-model="form.host" placeholder="Enter your email host" />
            </td>
          </tr>
          <tr>
            <td><label class="text-12">{{ $t('port') }}</label></td>
            <td>
              <BaseInput v-model="form.port" placeholder="Enter your mail server port" />
            </td>
          </tr>
          <tr>
            <td><label class="text-12">{{ $t('username') }}</label></td>
            <td>
              <BaseInput v-model="form.username" placeholder="Enter your mail server username" />
            </td>
          </tr>
          <tr>
            <td><label class="text-12">{{ $t('password') }}</label></td>
            <td>
              <BaseInput type="password" v-model="form.password" placeholder="Enter your mail server password" />
            </td>
          </tr>
          <tr>
            <td><label class="text-12">{{ $t('encryiption') }}</label></td>
            <td>
              <BaseSelect size="sm" v-model="form.encryption">
                <option value="" disabled>Select an option</option>
                <option value="ssl">SSL</option>
                <option value="tls">TLS</option>
              </BaseSelect>
            </td>
          </tr>
          <tr>
            <td></td>
            <td>
              <BaseButton size="sm" @click="connectionTest" :loading="testLoading">Test Connection</BaseButton>
              <BaseButton size="sm" @click="handleImapSave" variant="success" :loading="testLoading">Save</BaseButton>
            </td>
          </tr>
        </table>
      </div>
    </section>

    <section>
      <div class="page-content-section-header">
        <p>SMTP Configuration</p>
      </div>
      <div class="page-content-section-content">
        <table>
          <tr>
            <td><label class="text-12">{{ $t('host') }}</label></td>
            <td>
              <BaseInput v-model="smtpForm.host" placeholder="Enter your email host" />
            </td>
          </tr>
          <tr>
            <td><label class="text-12">{{ $t('port') }}</label></td>
            <td>
              <BaseInput v-model="smtpForm.port" placeholder="Enter your mail server port" />
            </td>
          </tr>
          <tr>
            <td><label class="text-12">{{ $t('username') }}</label></td>
            <td>
              <BaseInput v-model="smtpForm.username" placeholder="Enter your mail server username" />
            </td>
          </tr>
          <tr>
            <td><label class="text-12">{{ $t('password') }}</label></td>
            <td>
              <BaseInput type="password" v-model="smtpForm.password" placeholder="Enter your mail server password" />
            </td>
          </tr>
          <tr>
            <td><label class="text-12">{{ $t('encryiption') }}</label></td>
            <td>
              <BaseSelect size="sm" v-model="smtpForm.encryption">
                <option value="" disabled>Select an option</option>
                <option value="ssl">SSL</option>
                <option value="tls">TLS</option>
              </BaseSelect>
            </td>
          </tr>
          <tr>
            <td></td>
            <td>
              <BaseButton size="sm" @click="handleSmtpSave" :loading="savingSmtp">Save</BaseButton>
            </td>
          </tr>
        </table>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import BaseSelect from '@/components/base/BaseSelect.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import { useToast } from "vue-toastification";
const toast = useToast();
import BaseInput from '@/components/base/BaseInput.vue'
import { imapConnectionTest, updateIMAPConfig, updateSMTPConfig } from '@/services/communication';
import { useAuth } from '@/composables/useAuth';

const { user } = useAuth();

const form = ref({
  host: user?.config?.email?.imap?.host,
  port: user?.config?.email?.imap?.port,
  encryption: user?.config?.email?.imap?.encryption,
  username: user?.config?.email?.imap?.username,
  password: user?.config?.email?.imap?.password,
});


const smtpForm = ref({
  host: user?.config?.email?.smtp?.host,
  port: user?.config?.email?.smtp?.port,
  encryption: user?.config?.email?.smtp?.encryption,
  username: user?.config?.email?.smtp?.username,
  password: user?.config?.email?.smtp?.password,
});


const loading = ref(false)
const testLoading = ref(false);

const saving = ref(false)
const clicking = ref(false);

const handleImapSave = async () => {
  if (saving.value) return // 👈 prevent double execution
  saving.value = true
  loading.value = true
  try {
    const data = await updateIMAPConfig(form.value)
    if (data?.success) {
      toast.success(data.message);
    }
  } catch (e) {
    toast.error('Failed to save data')
  } finally {
    loading.value = false
    saving.value = false
  }
}

const connectionTest = async () => {
  if (clicking.value) return // 👈 prevent double execution
  clicking.value = true
  testLoading.value = true
  try {
    const data = await imapConnectionTest(form.value)
    if (data?.success) {
      toast.success(data.message);
    } else if (!data?.success) {
      toast.error(data.message);
    }
  } catch (e) {
    toast.error('Connection failed')
  } finally {
    testLoading.value = false
    clicking.value = false
  }
}


const savingSmtp = ref(false)
const loadingSmtp = ref(false);

const handleSmtpSave = async () => {
  if (savingSmtp.value) return
  savingSmtp.value = true
  loadingSmtp.value = true
  try {
    
    const data = await updateSMTPConfig(smtpForm.value)

    if (data?.success) {
      toast.success(data.message);
    }
  } catch (e) {
    toast.error('Failed to save data')
  } finally {
    loadingSmtp.value = false
    savingSmtp.value = false
  }
};

</script>

<style lang="less" scoped>
.page-header {
  color: #484848;
  border-bottom: 1px solid #f2f2f2;
  padding-bottom: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 44px;
  background-color: #ffffff;

  h2 {
    margin: 0;
  }

  p {
    margin: 0;
  }
}

.page-content {
  &-section-header {
    background-color: #f0f0f0;
    padding: 5px 10px;

    p {
      margin: 0;
    }
  }

  &-section-content {
    padding: 1rem;
    border-left: 1px solid #f0f0f0;

    table {
      border-spacing: 0 10px;
      border-collapse: separate;
    }

    td {
      padding: 5px 10px;
    }
  }
}
</style>