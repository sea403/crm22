<template>
  <div class="page">
    <h2 class="mb-3">Welcome back {{ user?.name }}</h2>

    <div class="stats">
      <div class="stat"><p class="label">Contacts</p><p class="value">{{ totals.contacts }}</p></div>
      <div class="stat"><p class="label">Accounts</p><p class="value">{{ totals.accounts }}</p></div>
      <div class="stat"><p class="label">Projects</p><p class="value">{{ totals.projects }}</p></div>
      <div class="stat"><p class="label">Expenses</p><p class="value">{{ totals.expenses }}</p></div>
    </div>

    <div class="lists">
      <div class="list">
        <h3>Recent Projects</h3>
        <ul>
          <li v-for="p in recent.projects" :key="p.id">{{ p.name }} <small v-if="p.account">({{ p.account.name }})</small></li>
        </ul>
      </div>
      <div class="list">
        <h3>Recent Contacts</h3>
        <ul>
          <li v-for="c in recent.contacts" :key="c.id">{{ c.name }} <small v-if="c.email">- {{ c.email }}</small></li>
        </ul>
      </div>
      <div class="list">
        <h3>Recent Expenses</h3>
        <ul>
          <li v-for="e in recent.expenses" :key="e.id">{{ e.title }} - {{ e.amount }}</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive } from 'vue'
import { useAuth } from '@/composables/useAuth'
import { listRecords } from '@/services/jsonrpc'

const { user } = useAuth()

const totals = reactive({ contacts: 0, accounts: 0, projects: 0, expenses: 0 })
const recent = reactive({ contacts: [], projects: [], expenses: [] })

onMounted(async () => {
  try {
    // Fetch totals using pagination.total
    const [c, a, p, e] = await Promise.all([
      listRecords({ module: 'Contact', page: 1, perPage: 1, sortBy: 'id', sortOrder: 'desc', filters: {} }),
      listRecords({ module: 'Account', page: 1, perPage: 1, sortBy: 'id', sortOrder: 'desc', filters: {} }),
      listRecords({ module: 'Project', page: 1, perPage: 1, sortBy: 'id', sortOrder: 'desc', filters: {} }),
      listRecords({ module: 'Expense', page: 1, perPage: 1, sortBy: 'id', sortOrder: 'desc', filters: {} }),
    ])
    totals.contacts = c.pagination?.total || 0
    totals.accounts = a.pagination?.total || 0
    totals.projects = p.pagination?.total || 0
    totals.expenses = e.pagination?.total || 0

    // recents (limit 5)
    const [rp, rc, re] = await Promise.all([
      listRecords({ module: 'Project', page: 1, perPage: 5, sortBy: 'id', sortOrder: 'desc', filters: {} }),
      listRecords({ module: 'Contact', page: 1, perPage: 5, sortBy: 'id', sortOrder: 'desc', filters: {} }),
      listRecords({ module: 'Expense', page: 1, perPage: 5, sortBy: 'date', sortOrder: 'desc', filters: {} })
    ])
    recent.projects = rp.data
    recent.contacts = rc.data
    recent.expenses = re.data
  } catch (err) {
    // Fail silently for dashboard
    console.debug('Dashboard load failed', err)
  }
})
</script>

<style scoped>
.page { padding: 1rem; }
.stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
.stat { background: #fff; border-radius: 8px; padding: 1rem; box-shadow: rgba(0,0,0,0.05) 0 0 0 1px; }
.stat .label { margin: 0; color: #666; }
.stat .value { margin: 0; font-size: 1.6rem; font-weight: 700; }
.lists { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
.list { background: #fff; border-radius: 8px; padding: 1rem; box-shadow: rgba(0,0,0,0.05) 0 0 0 1px; }
.list h3 { margin-top: 0; }
.list ul { margin: 0; padding-left: 1.2rem; }
</style>
