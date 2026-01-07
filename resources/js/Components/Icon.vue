<template>
  <component
    :is="iconComponent"
    v-if="iconComponent"
    :size="size"
    :stroke-width="strokeWidth"
    aria-hidden="true"
  />
  <!-- Fallback for custom icons not in Lucide -->
  <svg
    v-else-if="name === 'apple'"
    xmlns="http://www.w3.org/2000/svg"
    :width="size"
    :height="size"
    viewBox="0 0 100 100"
  >
    <g fill="currentColor" fill-rule="nonzero">
      <path d="M46.173 19.967C49.927-1.838 19.797-.233 14.538.21c-.429.035-.648.4-.483.8 2.004 4.825 14.168 31.66 32.118 18.957zm13.18 1.636c1.269-.891 1.35-1.614.047-2.453l-2.657-1.71c-.94-.607-1.685-.606-2.532.129-5.094 4.42-7.336 9.18-8.211 15.24 1.597.682 3.55.79 5.265.328 1.298-4.283 3.64-8.412 8.088-11.534z" />
      <path d="M88.588 67.75c9.65-27.532-13.697-45.537-35.453-32.322-1.84 1.118-4.601 1.118-6.441 0-21.757-13.215-45.105 4.79-35.454 32.321 5.302 15.123 17.06 39.95 37.295 29.995.772-.38 1.986-.38 2.758 0 20.235 9.955 31.991-14.872 37.295-29.995z" />
    </g>
  </svg>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import {
  Trash2,
  X,
  ChevronUp,
  ChevronDown,
  ChevronRight,
  Pencil,
  FileText,
  Check,
  Mail,
  Search,
  Info,
  XCircle,
  Plus,
  Save,
  Fingerprint,
  Undo2,
  Eraser,
  Ban,
  Send,
  CheckCircle,
  CornerUpLeft,
  ArrowRight,
  Inbox,
  Move,
  Menu,
  MoreVertical,
} from 'lucide-vue-next'

const props = withDefaults(defineProps<{
  name: string
  size?: number | string
  strokeWidth?: number
}>(), {
  size: 24,
  strokeWidth: 2,
})

// 기존 아이콘 이름을 Lucide 컴포넌트로 매핑
const iconMap: Record<string, any> = {
  'trash': Trash2,
  'delete': X,
  'chevron-up': ChevronUp,
  'chevron-down': ChevronDown,
  'chevron-right': ChevronRight,
  'edit': Pencil,
  'desc': FileText,
  'check': Check,
  'envelope': Mail,
  'search': Search,
  'info': Info,
  'close': X,
  'close-circle': XCircle,
  'plus': Plus,
  'save': Save,
  'fingerprint': Fingerprint,
  'undo': Undo2,
  'rubber': Eraser,
  'blocked': Ban,
  'send': Send,
  'check-circle': CheckCircle,
  'cross-circle': XCircle,
  'corner-up-left': CornerUpLeft,
  'arrow-right': ArrowRight,
  'mail': Mail,
  'inbox': Inbox,
  'move': Move,
  'menu': Menu,
  'more': MoreVertical,
}

const iconComponent = computed(() => iconMap[props.name] || null)
</script>
