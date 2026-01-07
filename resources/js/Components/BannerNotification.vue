<template>
  <div v-if="props.modelValue" class="fixed inset-x-0 bottom-0 pb-2 sm:pb-5">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
      <div class="rounded-lg p-2 shadow-lg sm:p-3" :class="colours.bgLight">
        <div class="flex flex-wrap items-center justify-between">
          <div class="flex w-0 flex-1 items-center">
            <span class="flex rounded-lg p-2" :class="colours.bgDark">
              <component :is="colours.icon" class="h-6 w-6 text-white" aria-hidden="true" />
            </span>
            <p class="ml-3 truncate font-medium" :class="colours.text">
              <span><slot name="message"></slot></span>
            </p>
          </div>
          <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-2">
            <button
              type="button"
              @click="closeNotification"
              class="-mr-1 flex rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-white"
              :class="colours.bgHover"
            >
              <span class="sr-only">Dismiss</span>
              <X class="h-6 w-6" :class="colours.text" aria-hidden="true" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, type Component } from 'vue'
import { AlertCircle, Info, X, CheckCircle } from 'lucide-vue-next'

const props = defineProps<{
  modelValue: boolean
  status?: 'info' | 'error' | 'success'
}>()

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
}>()

function closeNotification() {
  emit('update:modelValue', false)
}

interface ColourConfig {
  bgLight: string
  bgDark: string
  bgHover: string
  text: string
  icon: Component
}

const colours = computed<ColourConfig>(() => {
  if (props.status === 'info') {
    return {
      bgLight: 'bg-yellow-100',
      bgDark: 'bg-yellow-700',
      bgHover: 'hover:bg-yellow-200',
      text: 'text-yellow-700',
      icon: Info,
    }
  } else if (props.status === 'error') {
    return {
      bgLight: 'bg-red-100',
      bgDark: 'bg-red-700',
      bgHover: 'hover:bg-red-200',
      text: 'text-red-700',
      icon: AlertCircle,
    }
  } else {
    return {
      bgLight: 'bg-green-100',
      bgDark: 'bg-green-800',
      bgHover: 'hover:bg-green-200',
      text: 'text-green-800',
      icon: CheckCircle,
    }
  }
})
</script>
