<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Accounts</h1>
        <p class="page-header-subtitle">Manage your accounts</p>
      </div>

      <div class="d-flex gap-2">
        <BaseInput :hide_lebel="true" type="search" placeholder="Search here..." autocomplete="off" v-model="search" @input="debouncedFetch" />
        <BaseButton @click="openCreate">
          <span>Add Account</span>
        </BaseButton>
      </div>
    </div>

    <div class="account-grid">
      <div v-for="acc in accounts" :key="acc.id" class="account-grid-item" @click="openEdit(acc)">
        <div class="account-grid-item-header">
          <div class="no-image">
            <p>{{ acc.name[0] }}</p>
          </div>
          <div>
            <p>{{ acc.name }}</p>
            <small>{{ acc.website || '—' }}</small>
          </div>
        </div>
        <div class="account-grid-item-content">
          <p v-if="acc.phone">Phone: {{ acc.phone }}</p>
          <p v-if="acc.industry">Industry: {{ acc.industry }}</p>
          <p v-if="acc.city">City: {{ acc.city }}</p>
        </div>
      </div>
    </div>

    <BaseOffCanvas :isOpen="showOffCanvas" :title="offcanvasTitle" @close="showOffCanvas = false">
      <div class="offcanvas-header">
        <BaseButton class="me-2" :disabled="loading" @click="submitForm">Save</BaseButton>
        <BaseButton variant="lightcoral" :disabled="loading" @click="submitForm">Save & Close</BaseButton>
      </div>

      <BaseInput :error="errors.name" v-model="form.name" name="name" required class="mb-2" placeholder="Account Name" />
      <BaseInput v-model="form.website" name="website" class="mb-2" placeholder="Website (https://...)" />
      <BaseInput v-model="form.phone" name="phone" class="mb-2" placeholder="Phone" />
      <BaseInput v-model="form.industry" name="industry" class="mb-2" placeholder="Industry" />
      <BaseInput v-model="form.billing_address" name="billing_address" class="mb-2" placeholder="Billing Address" />
      <BaseInput v-model="form.shipping_address" name="shipping_address" class="mb-2" placeholder="Shipping Address" />
      <BaseInput v-model="form.city" name="city" class="mb-2" placeholder="City" />
      <BaseInput v-model="form.state" name="state" class="mb-2" placeholder="State" />
      <BaseInput v-model="form.country_code" name="country_code" class="mb-2" placeholder="Country Code" />
      <BaseInput v-model="form.zipcode" name="zipcode" class="mb-2" placeholder="Zip/Postal Code" />
      <BaseInput v-model="form.notes" name="notes" class="mb-2" placeholder="Notes" />

      <p v-if="error" style="color: red;">{{ error }}</p>
    </BaseOffCanvas>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseOffCanvas from '@/components/base/BaseOffCanvas.vue'
import { listRecords, createRecord, updateRecord } from '@/services/jsonrpc'
import { useToast } from 'vue-toastification'

const toast = useToast()

const showOffCanvas = ref(false)
const editingId = ref(null)
const accounts = ref([])
const search = ref('')
const loading = ref(false)
const error = ref(null)

const errors = reactive({ name: '' })
const form = reactive({
  name: '',
  website: '',
  phone: '',
  industry: '',
  billing_address: '',
  shipping_address: '',
  city: '',
  state: '',
  country_code: '',
  zipcode: '',
  notes: ''
})

const offcanvasTitle = computed(() => (editingId.value ? 'Edit Account' : 'Create Account'))

const resetForm = () => {
  Object.assign(form, { name: '', website: '', phone: '', industry: '', billing_address: '', shipping_address: '', city: '', state: '', country_code: '', zipcode: '', notes: '' })
  Object.keys(errors).forEach(k => (errors[k] = ''))
}

const openCreate = () => {
  editingId.value = null
  resetForm()
  showOffCanvas.value = true
}

const openEdit = (acc) => {
  editingId.value = acc.id
  Object.assign(form, {
    name: acc.name || '',
    website: acc.website || '',
    phone: acc.phone || '',
    industry: acc.industry || '',
    billing_address: acc.billing_address || '',
    shipping_address: acc.shipping_address || '',
    city: acc.city || '',
    state: acc.state || '',
    country_code: acc.country_code || '',
    zipcode: acc.zipcode || '',
    notes: acc.notes || ''
  })
  showOffCanvas.value = true
}

const fetchAccounts = async () => {
  const response = await listRecords({ module: 'Account', filters: search.value ? { name: search.value } : {}, page: 1, perPage: 20, sortBy: 'name', sortOrder: 'asc' })
  accounts.value = response.data
}

let debounceTimer
const debouncedFetch = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchAccounts, 300)
}

onMounted(fetchAccounts)

const submitForm = async () => {
  error.value = null
  loading.value = true
  const payload = { ...form }
  try {
    if (editingId.value) {
      const updated = await updateRecord('Account', editingId.value, payload)
      const idx = accounts.value.findIndex(p => p.id === editingId.value)
      if (idx !== -1) accounts.value[idx] = updated
      toast.success('Account updated.')
    } else {
      const created = await createRecord('Account', payload)
      accounts.value.unshift(created)
      toast.success('Account created.')
    }
    Object.keys(errors).forEach(k => (errors[k] = ''))
    showOffCanvas.value = false
    editingId.value = null
  } catch (e) {
    const msg = e?.message || 'Error saving account'
    error.value = msg
    toast.error(msg)
  } finally {
    loading.value = false
  }
}
</script>

<style lang="less" scoped>
.page { padding: 1rem; }
.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
.page-header-title { font-size: 2rem; margin: 0; }
.page-header-subtitle { color: #333; font-size: 0.9rem; margin: 0; }
.offcanvas-header { margin-bottom: 10px; top: 0; position: sticky; }
.account-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
.account-grid-item { background: #fff; padding: 1rem; border-radius: 8px; box-shadow: rgba(0,0,0,0.05) 0 0 0 1px; cursor: pointer; }
.account-grid-item-header { display: flex; gap: 10px; align-items: center; }
.account-grid-item-header .no-image { width: 50px; height: 50px; border-radius: 50%; background: var(--clr-primary); color: #f0f0f0; display: grid; place-content: center; font-size: 1.6rem; font-weight: bold; }
.account-grid-item-content { margin-top: 10px; border-top: 1px solid #f0f0f0; padding-top: 10px; color: #666; font-size: 0.9rem; }
</style>
