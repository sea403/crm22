<template>
  <QuillEditor
    v-model:content="content"
    content-type="html"
    theme="snow"
    :placeholder="placeholder"
  />
</template>

<script setup>
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import { watch, ref } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Type something...'
  }
})

const emit = defineEmits(['update:modelValue'])

const content = ref(props.modelValue)

watch(content, (val) => {
  emit('update:modelValue', val)
})

watch(() => props.modelValue, (val) => {
  if (val !== content.value) {
    content.value = val
  }
})
</script>

<style>
  .ql-editor {
    min-height: 200px;
    font-size: 1rem;
  }
</style>