<template>
  <div>
    <Head :title="$t('failedDeliveries.title')" />
    <h1 id="primary-heading" class="sr-only">{{ $t('failedDeliveries.title') }}</h1>

    <div class="sm:flex sm:items-center mb-6">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold text-grey-900 dark:text-white">{{ $t('failedDeliveries.title') }}</h1>
        <p class="mt-2 text-sm text-grey-700 dark:text-grey-200">
          {{ search ? $t('failedDeliveries.descriptionSearch') : $t('failedDeliveries.descriptionAll') }}
          <button @click="moreInfoOpen = !moreInfoOpen">
            <Info
              class="h-6 w-6 inline-block cursor-pointer text-grey-500 dark:text-grey-200"
              :title="$t('failedDeliveries.moreInfo')"
            />
          </button>
        </p>
      </div>
    </div>

    <vue-good-table
      v-if="rows.length"
      v-on:sort-change="debounceToolips"
      :columns="columns"
      :rows="rows"
      :sort-options="{
        enabled: true,
        initialSortBy: { field: 'created_at', type: 'desc' },
      }"
      styleClass="vgt-table"
    >
      <template #table-row="props">
        <span
          v-if="props.column.field == 'created_at'"
          class="tooltip outline-none cursor-default text-sm text-grey-500 dark:text-grey-300"
          :data-tippy-content="$filters.formatDate(rows[props.row.originalIndex].created_at)"
          >{{ $filters.timeAgo(props.row.created_at) }}
        </span>
        <span
          v-else-if="props.column.field == 'email_type'"
          class="text-sm text-grey-500 dark:text-grey-300"
        >
          {{ props.row.email_type }}
        </span>
        <span v-else-if="props.column.field == 'recipient'">
          <span
            class="tooltip cursor-pointer outline-none text-sm font-medium text-grey-700 dark:text-grey-200"
            data-tippy-content="Click to copy"
            @click="
              clipboard(
                rows[props.row.originalIndex].recipient
                  ? rows[props.row.originalIndex].recipient.email
                  : rows[props.row.originalIndex].destination,
              )
            "
            >{{
              props.row.recipient
                ? props.row.recipient.email
                : rows[props.row.originalIndex].destination
            }}</span
          >
        </span>
        <span v-else-if="props.column.field == 'alias'">
          <span
            class="tooltip cursor-pointer outline-none text-sm font-medium text-grey-700 dark:text-grey-200"
            data-tippy-content="Click to copy"
            @click="
              clipboard(
                rows[props.row.originalIndex].alias
                  ? rows[props.row.originalIndex].alias.email
                  : '',
              )
            "
            >{{ props.row.alias ? props.row.alias.email : '' }}</span
          >
        </span>
        <span
          v-else-if="props.column.field == 'sender'"
          class="text-sm font-medium text-grey-700 dark:text-grey-200"
        >
          <span
            class="tooltip cursor-pointer outline-none"
            data-tippy-content="Click to copy"
            @click="clipboard(rows[props.row.originalIndex].sender)"
            >{{ props.row.sender }}</span
          >
        </span>
        <span
          v-else-if="props.column.field == 'remote_mta'"
          class="text-sm text-grey-500 dark:text-grey-300"
        >
          {{ props.row.remote_mta }}
        </span>
        <span
          v-else-if="props.column.field == 'code'"
          class="text-sm text-grey-500 dark:text-grey-300"
        >
          {{ props.row.code }}
        </span>
        <span
          v-else-if="props.column.field == 'attempted_at'"
          class="tooltip outline-none text-sm text-grey-500 dark:text-grey-300"
          :data-tippy-content="$filters.formatDateTime(rows[props.row.originalIndex].attempted_at)"
          >{{ $filters.timeAgo(props.row.attempted_at) }}
        </span>
        <span v-else class="flex items-center justify-center outline-none" tabindex="-1">
          <a
            v-if="props.row.is_stored"
            :href="'api/v1/failed-deliveries/' + props.row.id + '/download'"
            class="mr-4 text-indigo-500 hover:text-indigo-800 font-medium dark:text-indigo-400 dark:hover:text-indigo-500"
          >
            {{ $t('failedDeliveries.download') }}
          </a>
          <button
            v-if="
              props.row.is_stored &&
              !props.row.quarantined &&
              !props.row.resent &&
              props.row.email_type === 'Forward'
            "
            @click="openResendModal(props.row)"
            as="button"
            type="button"
            class="mr-4 text-indigo-500 hover:text-indigo-800 font-medium dark:text-indigo-400 dark:hover:text-indigo-500"
          >
            {{ $t('failedDeliveries.resend') }}
          </button>
          <button
            @click="openDeleteModal(props.row.id)"
            as="button"
            type="button"
            class="text-indigo-500 hover:text-indigo-800 font-medium dark:text-indigo-400 dark:hover:text-indigo-500"
          >
            {{ $t('common.delete') }}
          </button>
        </span>
      </template>
    </vue-good-table>

    <div v-else-if="search" class="text-center">
      <AlertTriangle class="mx-auto h-16 w-16 text-grey-400 dark:text-grey-200" />
      <h3 class="mt-2 text-lg font-medium text-grey-900 dark:text-white">
        {{ $t('failedDeliveries.noFound') }}
      </h3>
      <p class="mt-1 text-md text-grey-500 dark:text-grey-200">
        {{ $t('failedDeliveries.tryDifferentSearch') }}
      </p>
      <div class="mt-6">
        <Link
          :href="route('failed_deliveries.index')"
          type="button"
          class="inline-flex items-center rounded-md border border-transparent bg-cyan-400 hover:bg-cyan-300 text-cyan-900 px-4 py-2 text-sm font-medium shadow-sm focus:outline-none"
        >
          {{ $t('failedDeliveries.viewAll') }}
        </Link>
      </div>
    </div>

    <div v-else class="text-center">
      <AlertTriangle class="mx-auto h-16 w-16 text-grey-400 dark:text-grey-200" />
      <h3 class="mt-2 text-lg font-medium text-grey-900 dark:text-white">{{ $t('failedDeliveries.noFailures') }}</h3>
      <p class="mt-1 text-md text-grey-500 dark:text-grey-200">
        {{ $t('failedDeliveries.noFailuresDesc') }}
      </p>
    </div>

    <Modal :open="resendFailedDeliveryModalOpen" @close="closeResendModal">
      <template v-slot:title>{{ $t('failedDeliveries.resendTitle') }}</template>
      <template v-slot:content>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          {{ $t('failedDeliveries.resendDesc1') }}
        </p>
        <p class="my-4 text-grey-700 dark:text-grey-200">
          {{ $t('failedDeliveries.resendDesc2') }}
          <b v-if="failedDeliveryToResend.recipient">{{ failedDeliveryToResend.recipient.email }}</b>
        </p>
        <multiselect
          v-model="failedDeliveryRecipientsToResend"
          mode="tags"
          value-prop="id"
          :options="recipientOptions"
          :close-on-select="true"
          :clear-on-select="false"
          :searchable="true"
          :max="10"
          class="p-0"
          :placeholder="$t('failedDeliveries.selectRecipients')"
          label="email"
          track-by="email"
        >
        </multiselect>
        <div class="mt-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <button
            type="button"
            @click="resendFailedDelivery(failedDeliveryToResend.id)"
            class="px-4 py-3 text-cyan-900 font-semibold bg-cyan-400 hover:bg-cyan-300 border border-transparent rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed"
            :disabled="resendFailedDeliveryLoading"
          >
            {{ $t('failedDeliveries.resendDelivery') }}
            <loader v-if="resendFailedDeliveryLoading" />
          </button>
          <button
            @click="closeResendModal"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            {{ $t('common.cancel') }}
          </button>
        </div>
      </template>
    </Modal>

    <Modal :open="deleteFailedDeliveryModalOpen" @close="closeDeleteModal">
      <template v-slot:title>{{ $t('failedDeliveries.deleteTitle') }}</template>
      <template v-slot:content>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          {{ $t('failedDeliveries.deleteConfirm') }}
        </p>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          {{ $t('failedDeliveries.deleteDesc') }}
        </p>
        <div class="mt-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <button
            type="button"
            @click="deleteFailedDelivery(failedDeliveryIdToDelete)"
            class="px-4 py-3 text-white font-semibold bg-red-500 hover:bg-red-600 border border-transparent rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed"
            :disabled="deleteFailedDeliveryLoading"
          >
            {{ $t('failedDeliveries.deleteDelivery') }}
            <loader v-if="deleteFailedDeliveryLoading" />
          </button>
          <button
            @click="closeDeleteModal"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            {{ $t('common.cancel') }}
          </button>
        </div>
      </template>
    </Modal>

    <Modal :open="moreInfoOpen" @close="moreInfoOpen = false">
      <template v-slot:title>{{ $t('failedDeliveries.moreInfo') }}</template>
      <template v-slot:content>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          {{ $t('failedDeliveries.moreInfoDesc1') }}
        </p>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          {{ $t('failedDeliveries.moreInfoDesc2') }}
        </p>
        <p class="mt-4 text-grey-700 dark:text-grey-200">
          {{ $t('failedDeliveries.moreInfoDesc3') }}
        </p>

        <div class="mt-6 flex flex-col sm:flex-row">
          <button
            @click="moreInfoOpen = false"
            class="px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            {{ $t('common.close') }}
          </button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import Modal from '../Components/Modal.vue'

const { t } = useI18n()
import { roundArrow } from 'tippy.js'
import tippy from 'tippy.js'
import { notify } from '@kyvg/vue3-notification'
import { VueGoodTable } from 'vue-good-table-next'
import Multiselect from '@vueform/multiselect'
import { Info, AlertTriangle } from 'lucide-vue-next'

const props = defineProps({
  initialRows: {
    type: Array,
    required: true,
  },
  recipientOptions: {
    type: Array,
    required: true,
  },
  search: {
    type: String,
  },
})

const rows = ref(props.initialRows)

const resendFailedDeliveryLoading = ref(false)
const resendFailedDeliveryModalOpen = ref(false)
const deleteFailedDeliveryLoading = ref(false)
const deleteFailedDeliveryModalOpen = ref(false)
const moreInfoOpen = ref(false)
const failedDeliveryToResend = ref({})
const failedDeliveryRecipientsToResend = ref([])
const failedDeliveryIdToDelete = ref(null)
const tippyInstance = ref(null)
const errors = ref({})

const columns = computed(() => [
  {
    label: t('failedDeliveries.createdAt'),
    field: 'created_at',
    globalSearchDisabled: true,
  },
  {
    label: t('failedDeliveries.emailType'),
    field: 'email_type',
  },
  {
    label: t('failedDeliveries.destination'),
    field: 'recipient',
    globalSearchDisabled: true,
  },
  {
    label: t('failedDeliveries.alias'),
    field: 'alias',
    globalSearchDisabled: true,
  },
  {
    label: t('failedDeliveries.sender'),
    field: 'sender',
  },
  {
    label: t('failedDeliveries.mailServer'),
    field: 'remote_mta',
  },
  {
    label: t('failedDeliveries.code'),
    field: 'code',
    sortable: false,
  },
  {
    label: t('failedDeliveries.firstAttempted'),
    field: 'attempted_at',
    globalSearchDisabled: true,
  },
  {
    label: '',
    field: 'actions',
    sortable: false,
    globalSearchDisabled: true,
  },
])

const resendFailedDelivery = id => {
  resendFailedDeliveryLoading.value = true

  axios
    .post(
      `/api/v1/failed-deliveries/${id}/resend`,
      JSON.stringify({
        recipient_ids: failedDeliveryRecipientsToResend.value,
      }),
      {
        headers: { 'Content-Type': 'application/json' },
      },
    )
    .then(response => {
      resendFailedDeliveryModalOpen.value = false
      resendFailedDeliveryLoading.value = false
    })
    .catch(error => {
      errorMessage()
      resendFailedDeliveryLoading.value = false
      resendFailedDeliveryModalOpen.value = false
    })
}

const deleteFailedDelivery = id => {
  deleteFailedDeliveryLoading.value = true

  axios
    .delete(`/api/v1/failed-deliveries/${id}`)
    .then(response => {
      rows.value = _.reject(rows.value, delivery => delivery.id === id)
      deleteFailedDeliveryModalOpen.value = false
      deleteFailedDeliveryLoading.value = false
    })
    .catch(error => {
      errorMessage()
      deleteFailedDeliveryLoading.value = false
      deleteFailedDeliveryModalOpen.value = false
    })
}

const openResendModal = failedDelivery => {
  resendFailedDeliveryModalOpen.value = true
  failedDeliveryToResend.value = failedDelivery
}

const closeResendModal = () => {
  resendFailedDeliveryModalOpen.value = false
  failedDeliveryToResend.value = {}
  failedDeliveryRecipientsToResend.value = []
}

const openDeleteModal = id => {
  deleteFailedDeliveryModalOpen.value = true
  failedDeliveryIdToDelete.value = id
}

const closeDeleteModal = () => {
  deleteFailedDeliveryModalOpen.value = false
  failedDeliveryIdToDelete.value = null
}

const addTooltips = () => {
  if (tippyInstance.value) {
    _.each(tippyInstance.value, instance => instance.destroy())
  }

  tippyInstance.value = tippy('.tooltip', {
    arrow: roundArrow,
    allowHTML: true,
  })
}

const debounceToolips = _.debounce(function () {
  addTooltips()
}, 50)

const clipboard = (str, success, error) => {
  // Needed as v-clipboard doesn't work inside modals!
  navigator.clipboard.writeText(str).then(
    () => {
      successMessage('Copied to clipboard')
    },
    () => {
      errorMessage('Could not copy to clipboard')
    },
  )
}

const successMessage = (text = '') => {
  notify({
    title: 'Success',
    text: text,
    type: 'success',
  })
}

const errorMessage = (text = 'An error has occurred, please try again later') => {
  notify({
    title: 'Error',
    text: text,
    type: 'error',
  })
}
</script>
