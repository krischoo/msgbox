<template>
  <SettingsLayout>
    <div class="divide-y divide-grey-200">
      <div class="pt-10">
        <div class="space-y-1">
          <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">
            {{ $t('settings.api.manageKeys') }}
          </h3>
          <p class="text-base text-grey-700 dark:text-grey-200">
            <span v-html="$t('settings.api.keysDesc')"></span>
            <a href="/docs" class="text-indigo-700 dark:text-indigo-400">{{ $t('settings.api.apiDocumentation') }}</a>.
          </p>
        </div>
        <div class="mt-4">
          <button
            @click="openCreateTokenModal"
            class="bg-cyan-400 w-full hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            {{ $t('settings.api.createNewKey') }}
          </button>

          <div class="mt-6">
            <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">
              {{ $t('settings.api.personalAccessKeys') }}
            </h3>

            <div class="my-4 w-24 border-b-2 border-grey-200"></div>

            <p class="my-6 text-base text-grey-700 dark:text-grey-200">
              {{ $t('settings.api.keysListDesc') }}
            </p>

            <div>
              <p class="mb-0 text-base text-grey-700 dark:text-grey-200" v-if="tokens.length === 0">
                {{ $t('settings.api.noTokens') }}
              </p>

              <div class="table w-full text-sm md:text-base" v-if="tokens.length > 0">
                <div class="table-row">
                  <div class="table-cell p-1 md:p-4 font-semibold">{{ $t('common.name') }}</div>
                  <div class="table-cell p-1 md:p-4 font-semibold">{{ $t('common.created') }}</div>
                  <div class="table-cell p-1 md:p-4 font-semibold">{{ $t('settings.api.lastUsed') }}</div>
                  <div class="table-cell p-1 md:p-4 font-semibold">{{ $t('settings.api.expiresAt') }}</div>
                  <div class="table-cell p-1 md:p-4"></div>
                </div>
                <div
                  v-for="token in tokens"
                  :key="token.id"
                  class="table-row even:bg-grey-50 odd:bg-white dark:even:bg-grey-800 dark:odd:bg-grey-900"
                >
                  <div class="table-cell p-1 md:p-4">{{ token.name }}</div>
                  <div class="table-cell p-1 md:p-4">{{ $filters.timeAgo(token.created_at) }}</div>
                  <div v-if="token.last_used_at" class="table-cell p-1 md:p-4">
                    {{ $filters.timeAgo(token.last_used_at) }}
                  </div>
                  <div v-else class="table-cell p-1 md:p-4">{{ $t('settings.api.notUsedYet') }}</div>
                  <div v-if="token.expires_at" class="table-cell p-1 md:p-4">
                    {{ $filters.formatDate(token.expires_at) }}
                  </div>
                  <div v-else class="table-cell p-1 md:p-4">{{ $t('settings.api.doesNotExpire') }}</div>
                  <div class="table-cell p-1 md:p-4 text-right">
                    <button
                      class="text-red-500 font-bold cursor-pointer rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                      @click="showRevokeModal(token)"
                    >
                      {{ $t('common.delete') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal :open="createTokenModalOpen" @close="closeCreateTokenModal">
      <template v-if="!accessToken" v-slot:title>{{ $t('settings.api.createNewKey') }}</template>
      <template v-else v-slot:title>{{ $t('settings.api.personalAccessKey') }}</template>
      <template v-slot:content>
        <div v-show="!accessToken">
          <p class="mt-4 text-grey-700 dark:text-grey-200">
            {{ $t('settings.api.createKeyDesc') }}
          </p>
          <div class="mt-6">
            <div v-if="isObject(form.errors)" class="mb-3 text-red-500">
              <ul>
                <li v-for="error in form.errors" :key="error[0]">
                  {{ error[0] }}
                </li>
              </ul>
            </div>
            <label
              for="create-token-name"
              class="block text-sm my-2 font-medium leading-6 text-grey-600 dark:text-white"
              >{{ $t('common.name') }}</label
            >
            <input
              v-model="form.name"
              type="text"
              id="create-token-name"
              class="block w-full rounded-md border-0 py-2 pr-10 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 dark:bg-white/5 text-grey-900 dark:text-white"
              :class="form.errors.name ? 'ring-red-500' : ''"
              :placeholder="$t('settings.api.namePlaceholder')"
              required
              autofocus
            />
            <label
              for="create-token-expiration"
              class="block font-medium leading-6 text-grey-600 text-sm my-2 dark:text-white"
            >
              {{ $t('settings.api.expiration') }}
            </label>
            <div class="block relative">
              <select
                v-model="form.expiration"
                id="create-token-expiration"
                class="relative block w-full rounded border-0 bg-transparent py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset focus:z-10 focus:ring-2 focus:ring-inset sm:text-base sm:leading-6"
                :class="form.errors.expiration ? 'ring-red-500' : ''"
              >
                <option class="dark:bg-grey-900" value="day">{{ $t('settings.api.oneDay') }}</option>
                <option class="dark:bg-grey-900" value="week">{{ $t('settings.api.oneWeek') }}</option>
                <option class="dark:bg-grey-900" value="month">{{ $t('settings.api.oneMonth') }}</option>
                <option class="dark:bg-grey-900" value="year">{{ $t('settings.api.oneYear') }}</option>
                <option class="dark:bg-grey-900" :value="null">{{ $t('settings.api.noExpiration') }}</option>
              </select>
            </div>
            <label
              for="create-token-name"
              class="block text-sm my-2 font-medium leading-6 text-grey-600 dark:text-white"
              >{{ $t('settings.api.confirmPassword') }}</label
            >
            <input
              v-model="form.password"
              type="password"
              id="create-token-password"
              class="block w-full rounded-md border-0 py-2 pr-10 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 mb-6 dark:bg-white/5 text-grey-900 dark:text-white"
              :class="form.errors.password ? 'ring-red-500' : ''"
              placeholder="********"
              required
            />
            <button
              @click="store"
              class="bg-cyan-400 hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
              :class="loading ? 'cursor-not-allowed' : ''"
              :disabled="loading"
            >
              {{ $t('settings.api.createKey') }}
              <loader v-if="loading" />
            </button>
            <button
              @click="closeCreateTokenModal"
              class="ml-4 px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >
              {{ $t('common.close') }}
            </button>
          </div>
        </div>
        <div v-show="accessToken">
          <p class="my-4 text-grey-700 dark:text-grey-200" v-html="$t('settings.api.newKeyCreated')">
          </p>
          <textarea
            v-model="accessToken"
            @click="selectTokenTextArea"
            id="token-text-area"
            class="w-full appearance-none bg-grey-100 border border-transparent text-grey-700 focus:outline-none rounded p-3 text-md break-all dark:bg-white/5 dark:text-white"
            rows="2"
            readonly
          >
          </textarea>
          <div class="text-center">
            <img :src="qrCode" class="inline-block" alt="QR Code" />
            <p class="text-left text-sm mt-2 text-grey-700 dark:text-grey-200">
              {{ $t('settings.api.qrCodeDesc') }}
            </p>
          </div>
          <div class="mt-6">
            <button
              class="bg-cyan-400 hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
              @click="clipboard(accessToken)"
            >
              {{ $t('common.copyToClipboard') }}
            </button>
            <button
              @click="closeCreateTokenModal"
              class="ml-4 px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >
              {{ $t('common.close') }}
            </button>
          </div>
        </div>
      </template>
    </Modal>

    <Modal :open="revokeTokenModalOpen" @close="closeRevokeTokenModal">
      <template v-slot:title>{{ $t('settings.api.revokeKey') }}</template>
      <template v-slot:content>
        <p class="my-4 text-grey-700 dark:text-grey-200">
          {{ $t('settings.api.revokeKeyDesc') }}
        </p>
        <div class="mt-6">
          <button
            @click="revoke"
            class="bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            :class="revokeTokenLoading ? 'cursor-not-allowed' : ''"
            :disabled="revokeTokenLoading"
          >
            {{ $t('settings.api.revokeKeyBtn') }}
            <loader v-if="revokeTokenLoading" />
          </button>
          <button
            @click="closeRevokeTokenModal"
            class="ml-4 px-4 py-3 text-grey-800 font-semibold bg-white hover:bg-grey-50 dark:text-grey-100 dark:hover:bg-grey-700 dark:bg-grey-600 dark:border-grey-700 border border-grey-100 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            {{ $t('common.close') }}
          </button>
        </div>
      </template>
    </Modal>
  </SettingsLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import SettingsLayout from './../../Layouts/SettingsLayout.vue'
import { notify } from '@kyvg/vue3-notification'
import Modal from '../../Components/Modal.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  initialTokens: {
    type: Object,
    required: true,
  },
})

const tokens = ref(props.initialTokens.data)

const accessToken = ref(null)
const qrCode = ref(null)
const createTokenModalOpen = ref(false)
const revokeTokenModalOpen = ref(false)
const tokenToRevoke = ref(null)
const loading = ref(false)
const revokeTokenLoading = ref(false)
const form = ref({
  name: '',
  expiration: null,
  password: '',
  errors: {},
})

const store = () => {
  form.value.errors = {}

  if (!form.value.name.length) {
    return (form.value.errors.name = [t('settings.api.nameRequired')])
  }

  if (!['day', 'week', 'month', 'year', null].includes(form.value.expiration)) {
    return (form.value.errors.expiration = [t('settings.api.invalidExpiration')])
  }

  if (!form.value.password.length) {
    return (form.value.errors.password = [t('settings.api.passwordRequired')])
  }

  loading.value = true
  accessToken.value = null
  qrCode.value = null

  axios
    .post('/settings/personal-access-tokens', form.value)
    .then(response => {
      loading.value = false
      form.value.name = ''
      form.value.password = ''
      form.value.expiration = null
      form.value.errors = {}

      tokens.value.push(response.data.token)
      accessToken.value = response.data.accessToken
      qrCode.value = response.data.qrCode
    })
    .catch(error => {
      loading.value = false
      if (isObject(error.response.data)) {
        form.value.errors = error.response.data.errors
      } else {
        errorMessage()
      }
    })
}

const showRevokeModal = token => {
  tokenToRevoke.value = token
  revokeTokenModalOpen.value = true
}

const revoke = () => {
  revokeTokenLoading.value = true

  axios
    .delete(`/settings/personal-access-tokens/${tokenToRevoke.value.id}`)
    .then(response => {
      revokeTokenLoading.value = false
      revokeTokenModalOpen.value = false
      tokens.value = _.reject(tokens.value, token => token.id === tokenToRevoke.value.id)
      tokenToRevoke.value = null
    })
    .catch(error => {
      revokeTokenLoading.value = false
      revokeTokenModalOpen.value = false
      errorMessage()
    })
}

const openCreateTokenModal = () => {
  accessToken.value = null
  qrCode.value = null
  createTokenModalOpen.value = true
}

const closeCreateTokenModal = () => {
  createTokenModalOpen.value = false
}

const closeRevokeTokenModal = () => {
  revokeTokenModalOpen.value = false
}

const selectTokenTextArea = () => {
  let textArea = document.getElementById('token-text-area')
  textArea.focus()
  textArea.select()
}

const isObject = val => {
  return _.isObject(val) && !_.isEmpty(val)
}

const clipboard = (str, success, error) => {
  // Needed as v-clipboard doesn't work inside modals!
  navigator.clipboard.writeText(str).then(
    () => {
      successMessage(t('common.copiedToClipboard'))
    },
    () => {
      errorMessage(t('common.clipboardError'))
    },
  )
}

const successMessage = (text = '') => {
  notify({
    title: t('common.success'),
    text: text,
    type: 'success',
  })
}

const errorMessage = (text = '') => {
  notify({
    title: t('common.error'),
    text: text || t('common.errorDefault'),
    type: 'error',
  })
}
</script>
