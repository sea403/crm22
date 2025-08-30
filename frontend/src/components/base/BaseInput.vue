<template>
    <div class="base-input" :class="{ 'base-input--error': error }">
        <label v-if="!hide_lebel">
            {{ label }}
            <span v-if="isRequired">*</span>
        </label>

        <input v-bind="$attrs" :type="type" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"
            class="base-input__field" />

        <p v-if="error" class="base-input__error-message">{{ error }}</p>
    </div>
</template>

<script setup>
import { useAttrs, computed } from 'vue';

const props = defineProps({
    modelValue: [String, Number],
    hide_lebel: {
        type: Boolean,
        default: false,
    }, 
    type: {
        type: String,
        default: 'text'
    },
    error: {
        type: String,
        default: ''
    }
})

const $attrs = useAttrs()

const isRequired = computed(() => 'required' in $attrs)

const label = computed(() => {
  if (props.hide_lebel) return null;

  const name = $attrs.name || ''
  return name
    .split('_')                            
    .map(part => part.charAt(0).toUpperCase() + part.slice(1))
    .join(' ')                            
})


defineEmits(['update:modelValue'])
</script>

<style lang="less" scoped>
@input-border-color: #ccc;
@input-focus-border: #4a90e2;
@input-error-border: #e04f5f;
@input-padding: 6px 12px;
@input-border-radius: 4px;
@error-text-color: #e04f5f;

.base-input {
    display: flex;
    flex-direction: column;
    width: 100%;

    label {
        margin-bottom: 5px;
        display: block;
    }

    &__field {
        padding: @input-padding;
        border: 1px solid @input-border-color;
        border-radius: @input-border-radius;
        font-size: 0.9rem;
        outline: none;
        transition: border-color 0.3s ease;

        &:focus {
            border-color: @input-focus-border;
            box-shadow: 0 0 4px fade(@input-focus-border, 40%);
        }
    }

    &--error {
        .base-input__field {
            border-color: @input-error-border;
        }
    }

    &__error-message {
        margin-top: 0px;
        font-size: 0.8rem;
        color: @error-text-color;
        font-weight: 500;
    }
}
</style>