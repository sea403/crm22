<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-header-title">Projects</h1>
        <p class="page-header-subtitle">Manage your projects</p>
      </div>

      <div class="d-flex gap-2">
        <BaseInput :hide_lebel="true" type="search" placeholder="Search here..." autocomplete="off" v-model="search" @input="debouncedFetch" />
        <BaseButton @click="openCreate">
          <PlusIcon class="icon" />
          <span>Add Project</span>
        </BaseButton>
        <BaseButton variant="light" @click="toggleView">{{ viewMode === 'grid' ? 'List View' : 'Grid View' }}</BaseButton>
      </div>
    </div>

    <div v-if="viewMode === 'grid'" class="project-grid">
      <div v-for="project in projects" :key="project.id" class="project-grid-item" @click="openEdit(project)">
        <div class="project-grid-item-header">
          <div class="no-image">
            <p>{{ project.name[0] }}</p>
          </div>
          <div>
            <p>{{ project.name }}</p>
            <small>Status: {{ project.status || 'planned' }}</small>
            <small v-if="project.account">Account: {{ project.account.name }}</small>
          </div>
        </div>
        <div class="project-grid-item-content">
          <p v-if="project.start_date">Start: {{ project.start_date }}</p>
          <p v-if="project.end_date">End: {{ project.end_date }}</p>
          <p v-if="project.budget">Budget: {{ project.budget }}</p>
        </div>
      </div>
    </div>

    <div v-else class="project-table">
      <table>
        <thead>
          <tr><th>Name</th><th>Status</th><th>Account</th><th>Start</th><th>End</th><th>Budget</th></tr>
        </thead>
        <tbody>
          <tr v-for="p in projects" :key="p.id" @click="openEdit(p)">
            <td>{{ p.name }}</td>
            <td>{{ p.status }}</td>
            <td>{{ p.account?.name || '—' }}</td>
            <td>{{ p.start_date || '—' }}</td>
            <td>{{ p.end_date || '—' }}</td>
            <td>{{ p.budget || '—' }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <BaseOffCanvas :isOpen="showOffCanvas" :title="offcanvasTitle" @close="showOffCanvas = false">
      <div class="offcanvas-header">
        <BaseButton class="me-2" :disabled="loading" @click="submitForm">Save</BaseButton>
        <BaseButton variant="lightcoral" :disabled="loading" @click="submitForm">Save & Close</BaseButton>
      </div>

      <BaseInput :error="errors.name" v-model="form.name" name="name" required class="mb-2" placeholder="Project Name" />
      <BaseSelect v-model="form.status" name="status" class="mb-2">
        <option value="planned">Planned</option>
        <option value="in_progress">In Progress</option>
        <option value="completed">Completed</option>
        <option value="on_hold">On Hold</option>
        <option value="canceled">Canceled</option>
      </BaseSelect>
      <BaseInput v-model="form.start_date" type="date" name="start_date" class="mb-2" placeholder="Start Date" />
      <BaseInput v-model="form.end_date" type="date" name="end_date" class="mb-2" placeholder="End Date" />
      <BaseInput v-model="form.budget" type="number" step="0.01" min="0" name="budget" class="mb-2" placeholder="Budget" />
      <BaseInput v-model="form.description" name="description" class="mb-2" placeholder="Description" />
      <BaseSelect v-model="form.account_id" name="account_id" class="mb-2">
        <option value="">Select Account (optional)</option>
        <option v-for="acc in accountOptions" :key="acc.id" :value="acc.id">{{ acc.name }}</option>
      </BaseSelect>

      <p v-if="error" style="color: red;">{{ error }}</p>
    </BaseOffCanvas>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { PlusIcon } from '@heroicons/vue/24/solid'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseOffCanvas from '@/components/base/BaseOffCanvas.vue'
import BaseSelect from '@/components/base/BaseSelect.vue'
import { listRecords, createRecord, updateRecord } from '@/services/jsonrpc'
import { useToast } from 'vue-toastification'

const toast = useToast()

const showOffCanvas = ref(false)
const editingId = ref(null)
const projects = ref([])
const accountOptions = ref([])
const search = ref('')
const loading = ref(false)
const error = ref(null)
const viewMode = ref('grid')

const errors = reactive({ name: '' })
const form = reactive({
  name: '',
  account_id: '',
  description: '',
  status: 'planned',
  start_date: '',
  end_date: '',
  budget: ''
})

const offcanvasTitle = computed(() => (editingId.value ? 'Edit Project' : 'Create Project'))

const resetForm = () => {
  Object.assign(form, { name: '', description: '', status: 'planned', start_date: '', end_date: '', budget: '' })
  Object.keys(errors).forEach(k => (errors[k] = ''))
}

const openCreate = () => {
  editingId.value = null
  resetForm()
  showOffCanvas.value = true
}

const openEdit = (project) => {
  editingId.value = project.id
  Object.assign(form, {
    account_id: project.account_id || '',
    name: project.name || '',
    description: project.description || '',
    status: project.status || 'planned',
    start_date: project.start_date || '',
    end_date: project.end_date || '',
    budget: project.budget ?? ''
  })
  showOffCanvas.value = true
}

const fetchProjects = async () => {
  const response = await listRecords({ module: 'Project', filters: search.value ? { name: search.value } : {}, page: 1, perPage: 20, sortBy: 'name', sortOrder: 'asc' })
  projects.value = response.data
  try {
    const accRes = await listRecords({ module: 'Account', filters: {}, page: 1, perPage: 100, sortBy: 'name', sortOrder: 'asc' })
    accountOptions.value = accRes.data
  } catch (e) {}
}

let debounceTimer
const debouncedFetch = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchProjects, 300)
}

onMounted(fetchProjects)

const submitForm = async () => {
  error.value = null
  loading.value = true
  const payload = { ...form }
  try {
    if (editingId.value) {
      const updated = await updateRecord('Project', editingId.value, payload)
      const idx = projects.value.findIndex(p => p.id === editingId.value)
      if (idx !== -1) projects.value[idx] = updated
      toast.success('Project updated.')
    } else {
      const created = await createRecord('Project', payload)
      projects.value.unshift(created)
      toast.success('Project created.')
    }
    Object.keys(errors).forEach(k => (errors[k] = ''))
    showOffCanvas.value = false
    editingId.value = null
  } catch (e) {
    const msg = e?.message || 'Error saving project'
    error.value = msg
    toast.error(msg)
  } finally {
    loading.value = false
  }
}

const toggleView = () => {
  viewMode.value = viewMode.value === 'grid' ? 'list' : 'grid'
}
</script>

<style lang="less" scoped>
.page { padding: 1rem; }
.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
.page-header-title { font-size: 2rem; margin: 0; }
.page-header-subtitle { color: #333; font-size: 0.9rem; margin: 0; }

.offcanvas-header { margin-bottom: 10px; top: 0; position: sticky; }

.project-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
.project-grid-item { background: #fff; padding: 1rem; border-radius: 8px; box-shadow: rgba(0,0,0,0.05) 0 0 0 1px; cursor: pointer; }
.project-grid-item-header { display: flex; gap: 10px; align-items: center; }
.project-grid-item-header .no-image { width: 50px; height: 50px; border-radius: 50%; background: var(--clr-primary); color: #f0f0f0; display: grid; place-content: center; font-size: 1.6rem; font-weight: bold; }
.project-grid-item-content { margin-top: 10px; border-top: 1px solid #f0f0f0; padding-top: 10px; color: #666; font-size: 0.9rem; }
.project-table table { width: 100%; border-collapse: collapse; background: #fff; }
.project-table th, .project-table td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #eee; }
.project-table tr { cursor: pointer; }
</style>
