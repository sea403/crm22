<template>
  <div class="base-select" :class="[{ 'base-select--error': error }, sizeClass]">
    <select
      v-bind="$attrs"
      :value="modelValue"
      @change="$emit('update:modelValue', $event.target.value)"
      class="base-select__field"
    >
      <slot />
    </select>
    <p v-if="error" class="base-select__error-message">{{ error }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: [String, Number],
  error: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'md', // options: 'md' or 'sm'
    validator: val => ['sm', 'md'].includes(val)
  }
})

defineEmits(['update:modelValue'])

const sizeClass = computed(() => {
  return props.size === 'sm' ? 'base-select--sm' : ''
})
</script>

<style lang="less" scoped>
@select-border-color: #ccc;
@select-focus-border: #4a90e2;
@select-error-border: #e04f5f;
@select-padding: 10px 12px;
@select-border-radius: 4px;
@error-text-color: #e04f5f;
@select-font-size: 1rem;

.base-select {
  display: flex;
  flex-direction: column;
  width: 100%;

  &__field {
    padding: @select-padding;
    border: 1px solid @select-border-color;
    border-radius: @select-border-radius;
    font-size: @select-font-size;
    outline: none;
    transition: border-color 0.3s ease;

    &:focus {
      border-color: @select-focus-border;
      box-shadow: 0 0 4px fade(@select-focus-border, 40%);
    }
  }

  &--sm {
    .base-select__field {
      padding: 4px;
      font-size: 0.85rem;
    }
  }

  &--error {
    .base-select__field {
      border-color: @select-error-border;
    }
  }

  &__error-message {
    margin-top: 4px;
    font-size: 0.85rem;
    color: @error-text-color;
    font-weight: 600;
  }
}
</style>
