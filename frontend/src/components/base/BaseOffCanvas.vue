<template>
    <div class="offcanvas-wrapper">
      <div class="backdrop" v-if="isOpen" @click="closeOffcanvas"></div>
  
      <div class="offcanvas" :class="{ open: isOpen }">
        <div class="offcanvas-header">
          <h3>{{ title }}</h3>
          <button @click="closeOffcanvas">×</button>
        </div>
        <div class="offcanvas-body">
          <slot />
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from 'vue'
  
  const props = defineProps({
    isOpen: Boolean,
    title: String,
  })
  
  const emit = defineEmits(['close'])
  
  const closeOffcanvas = () => {
    emit('close')
  }
  </script>
  
  <style lang="less" scoped>
  .offcanvas-wrapper {
    .backdrop {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.3);
      z-index: 99;
    }
  
    .offcanvas {
      position: fixed;
      top: 0;
      right: -500px;
      width: 500px;
      height: 100vh;
      background: #fff;
      box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
      transition: right 0.3s ease;
      z-index: 100;
      display: flex;
      flex-direction: column;
  
      &.open {
        right: 0;
      }
  
      .offcanvas-header {
        padding: 1rem;
        background: #f5f5f5;
        display: flex;
        justify-content: space-between;
        align-items: center;
  
        h3 {
          margin: 0;
          font-size: 1.2rem;
        }
  
        button {
          border: none;
          background: transparent;
          font-size: 1.5rem;
          cursor: pointer;
        }
      }
  
      .offcanvas-body {
        padding: 1rem;
        overflow-y: auto;
        flex-grow: 1;
      }
    }
  }
  </style>
  