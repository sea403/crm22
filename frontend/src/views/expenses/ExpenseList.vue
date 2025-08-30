<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Expenses</h1>
        <p class="page-header-subtitle">Track your expenses</p>
      </div>

      <div class="d-flex gap-2">
        <BaseInput :hide_lebel="true" type="search" placeholder="Search here..." autocomplete="off" v-model="search" @input="debouncedFetch" />
        <BaseButton @click="openCreate">
          <span>Add Expense</span>
        </BaseButton>
      </div>
    </div>

    <div class="expense-grid">
      <div v-for="exp in expenses" :key="exp.id" class="expense-grid-item" @click="openEdit(exp)">
        <div class="expense-grid-item-header">
          <div>
            <p>{{ exp.title }}</p>
            <small>{{ exp.category || '—' }}</small>
            <small v-if="exp.account">Account: {{ exp.account.name }}</small>
            <small v-if="exp.project">Project: {{ exp.project.name }}</small>
          </div>
          <div class="amount">{{ exp.amount }}</div>
        </div>
        <div class="expense-grid-item-content">
          <p v-if="exp.date">Date: {{ exp.date }}</p>
          <p v-if="exp.notes">{{ exp.notes }}</p>
        </div>
      </div>
    </div>

    <BaseOffCanvas :isOpen="showOffCanvas" :title="offcanvasTitle" @close="showOffCanvas = false">
      <div class="offcanvas-header">
        <BaseButton class="me-2" :disabled="loading" @click="submitForm">Save</BaseButton>
        <BaseButton variant="lightcoral" :disabled="loading" @click="submitForm">Save & Close</BaseButton>
      </div>

      <BaseInput :error="errors.title" v-model="form.title" name="title" required class="mb-2" placeholder="Title" />
      <BaseInput :error="errors.amount" v-model="form.amount" type="number" step="0.01" min="0" name="amount" class="mb-2" placeholder="Amount" />
      <BaseInput v-model="form.category" name="category" class="mb-2" placeholder="Category" />
      <BaseInput v-model="form.date" type="date" name="date" class="mb-2" placeholder="Date" />
      <BaseInput v-model="form.notes" name="notes" class="mb-2" placeholder="Notes" />
      <BaseSearchSelect v-model="form.account_id" :options="accountOptions" placeholder="Select Account (optional)" class="mb-2" />
      <BaseSearchSelect v-model="form.project_id" :options="projectOptions" placeholder="Select Project (optional)" class="mb-2" />

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
import BaseSearchSelect from '@/components/base/BaseSearchSelect.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

const showOffCanvas = ref(false)
const editingId = ref(null)
const expenses = ref([])
const accountOptions = ref([])
const projectOptions = ref([])
const search = ref('')
const loading = ref(false)
const error = ref(null)

const errors = reactive({ title: '', amount: '' })
const form = reactive({ title: '', amount: '', category: '', date: '', notes: '', account_id: '', project_id: '' })

const offcanvasTitle = computed(() => (editingId.value ? 'Edit Expense' : 'Create Expense'))

const resetForm = () => {
  Object.assign(form, { title: '', amount: '', category: '', date: '', notes: '' })
  Object.keys(errors).forEach(k => (errors[k] = ''))
}

const openCreate = () => { editingId.value = null; resetForm(); showOffCanvas.value = true }
const openEdit = (exp) => { editingId.value = exp.id; Object.assign(form, { title: exp.title || '', amount: exp.amount ?? '', category: exp.category || '', date: exp.date || '', notes: exp.notes || '', account_id: exp.account_id || '', project_id: exp.project_id || '' }); showOffCanvas.value = true }

const fetchExpenses = async () => {
  const response = await listRecords({ module: 'Expense', filters: search.value ? { title: search.value } : {}, page: 1, perPage: 20, sortBy: 'date', sortOrder: 'desc' })
  expenses.value = response.data
  try {
    const accRes = await listRecords({ module: 'Account', filters: {}, page: 1, perPage: 100, sortBy: 'name', sortOrder: 'asc' })
    accountOptions.value = accRes.data
    const projRes = await listRecords({ module: 'Project', filters: {}, page: 1, perPage: 100, sortBy: 'name', sortOrder: 'asc' })
    projectOptions.value = projRes.data
  } catch (e) {}
}

let debounceTimer
const debouncedFetch = () => { clearTimeout(debounceTimer); debounceTimer = setTimeout(fetchExpenses, 300) }

onMounted(fetchExpenses)

const submitForm = async () => {
  error.value = null
  loading.value = true
  const payload = {
    ...form,
    account_id: form.account_id ? Number(form.account_id) : null,
    project_id: form.project_id ? Number(form.project_id) : null,
    amount: form.amount !== '' ? Number(form.amount) : null,
  }
  try {
    if (editingId.value) {
      const updated = await updateRecord('Expense', editingId.value, payload)
      const idx = expenses.value.findIndex(p => p.id === editingId.value)
      if (idx !== -1) expenses.value[idx] = updated
      toast.success('Expense updated.')
    } else {
      const created = await createRecord('Expense', payload)
      expenses.value.unshift(created)
      toast.success('Expense created.')
    }
    Object.keys(errors).forEach(k => (errors[k] = ''))
    showOffCanvas.value = false
    editingId.value = null
  } catch (e) {
    const msg = e?.message || 'Error saving expense'
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
.expense-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
.expense-grid-item { background: #fff; padding: 1rem; border-radius: 8px; box-shadow: rgba(0,0,0,0.05) 0 0 0 1px; cursor: pointer; }
.expense-grid-item-header { display: flex; gap: 10px; align-items: center; justify-content: space-between; }
.expense-grid-item-header .amount { font-weight: 600; }
.expense-grid-item-content { margin-top: 10px; border-top: 1px solid #f0f0f0; padding-top: 10px; color: #666; font-size: 0.9rem; }
</style>
