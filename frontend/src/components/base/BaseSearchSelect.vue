<template>
  <div class="search-select" @keydown.stop>
    <div class="control" @click="toggle">
      <span class="value" v-if="selectedLabel">{{ selectedLabel }}</span>
      <span class="placeholder" v-else>{{ placeholder }}</span>
      <span class="arrow">▾</span>
    </div>
    <div v-if="open" class="menu">
      <input class="search" type="text" v-model="query" :placeholder="searchPlaceholder" />
      <ul class="options">
        <li v-for="opt in filtered" :key="getVal(opt)" @click="choose(opt)" :class="{active: getVal(opt) === modelValue}">
          {{ getLabel(opt) }}
        </li>
        <li v-if="!filtered.length" class="empty">No results</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  modelValue: [String, Number, null],
  options: { type: Array, default: () => [] },
  placeholder: { type: String, default: 'Select…' },
  labelKey: { type: String, default: 'name' },
  valueKey: { type: String, default: 'id' },
  searchPlaceholder: { type: String, default: 'Search…' },
})
const emit = defineEmits(['update:modelValue'])

const open = ref(false)
const query = ref('')

const getLabel = (o) => (typeof o === 'object' ? o[props.labelKey] : String(o))
const getVal = (o) => (typeof o === 'object' ? o[props.valueKey] : o)

const filtered = computed(() => {
  const q = query.value.trim().toLowerCase()
  if (!q) return props.options
  return props.options.filter((o) => getLabel(o)?.toLowerCase().includes(q))
})

const selectedLabel = computed(() => {
  const v = props.modelValue
  const f = props.options.find((o) => getVal(o) == v)
  return f ? getLabel(f) : ''
})

const choose = (o) => {
  emit('update:modelValue', getVal(o))
  open.value = false
}

const toggle = () => {
  open.value = !open.value
  if (open.value) setTimeout(() => (query.value = ''), 0)
}

const onDoc = (e) => {
  if (!e.target.closest('.search-select')) open.value = false
}

onMounted(() => document.addEventListener('click', onDoc))
onBeforeUnmount(() => document.removeEventListener('click', onDoc))
</script>

<style scoped>
.search-select { position: relative; width: 100%; }
.control { display: flex; justify-content: space-between; align-items: center; border: 1px solid #e5e5e5; padding: 6px 10px; border-radius: 6px; cursor: pointer; background: #fff; }
.value { color: #222; }
.placeholder { color: #999; }
.arrow { color: #666; font-size: 0.8rem; }
.menu { position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #fff; border: 1px solid #e5e5e5; border-radius: 6px; box-shadow: rgba(0,0,0,0.06) 0 4px 16px; z-index: 50; }
.search { width: 100%; border: none; border-bottom: 1px solid #eee; padding: 8px 10px; outline: none; }
.options { list-style: none; margin: 0; padding: 4px 0; max-height: 240px; overflow-y: auto; }
.options li { padding: 8px 10px; cursor: pointer; }
.options li:hover, .options li.active { background: #f5f7fb; }
.empty { color: #999; cursor: default; }
</style>

