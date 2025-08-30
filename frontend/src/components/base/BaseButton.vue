<template>
    <button :class="[
        'base-button',
        `base-button--${variant}`,
        `base-button--size-${size}`,
        { 'base-button--disabled': disabled || loading }
    ]" :disabled="disabled || loading">
        <span v-if="loading" class="spinner" />
        <slot />
    </button>
</template>

<script setup>
defineProps({
    variant: {
        type: String,
        default: 'primary'
    },
    size: {
        type: String,
        default: 'md',
        validator: (val) => ['xs', 'sm', 'md', 'lg'].includes(val)
    },
    disabled: Boolean,
    loading: Boolean
})
</script>

<style lang="less" scoped>
@primary-color: #4a90e2;
@secondary-color: #7b8a99;
@danger-color: #e04f5f;
@lightcoral-color: lightcoral;

.base-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    min-width: max-content;

    border: none;
    border-radius: 4px;
    padding: 10px 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;


    &--primary {
        background-color: @primary-color;
        color: white;
    }

    &--secondary {
        background-color: @secondary-color;
        color: white;
    }

    &--danger {
        background-color: @danger-color;
        color: white;
    }

    &--ligtcoral {
        background-color: @lightcoral-color;
        color: white;
    }

    &--disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }

    &:hover:not(.base-button--disabled) {
        filter: brightness(0.9);
    }

    // sizes
    &--size-xs {
        font-size: 0.625rem;
        padding: 4px 8px;
    }

    &--size-sm {
        font-size: 0.75rem;
        padding: 6px 10px;
    }

    &--size-md {
        font-size: 0.875rem;
        padding: 10px 16px;
    }

    &--size-lg {
        font-size: 1rem;
        padding: 12px 20px;
    }

    .spinner {
        width: 12px;
        height: 12px;
        border: 2px solid white;
        border-top: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0);
        }

        100% {
            transform: rotate(360deg);
        }
    }
}
</style>
