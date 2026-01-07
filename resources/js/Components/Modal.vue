<template>
  <Dialog :open="open" @update:open="setIsOpen">
    <DialogContent
      class="sm:max-w-lg"
      :class="maxWidth"
    >
      <DialogHeader>
        <DialogTitle class="text-2xl leading-tight font-medium border-b-2 border-border pb-4">
          <slot name="title"></slot>
        </DialogTitle>
      </DialogHeader>
      <div class="mt-2">
        <slot name="content"></slot>
      </div>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'

defineProps<{
  open: boolean
  maxWidth?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
}>()

function setIsOpen(value: boolean) {
  emit('update:modelValue', value)
}
</script>
