<template>
  <Switch
    :checked="modelValue"
    @update:checked="toggle"
    class="peer inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=unchecked]:bg-input"
  >
    <span
      :class="[
        modelValue ? 'translate-x-5' : 'translate-x-0',
        'pointer-events-none block h-5 w-5 rounded-full bg-background shadow-lg ring-0 transition-transform',
      ]"
    />
  </Switch>
</template>

<script setup lang="ts">
import { Switch } from '@/components/ui/switch'

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  on: []
  off: []
}>()

const props = defineProps<{
  modelValue: boolean
}>()

const toggle = (value: boolean) => {
  emit('update:modelValue', value)
  value ? emit('on') : emit('off')
}
</script>
