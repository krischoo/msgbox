<template>
  <SettingsLayout>
    <div class="divide-y divide-grey-200">
      <div class="py-10">
        <div>
          <div class="mb-6 text-base text-grey-700 dark:text-grey-200">
            <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">
              {{ $t('settings.data.importAliases') }}
            </h3>

            <div class="mt-4 w-24 border-b-2 border-grey-200"></div>

            <p class="mt-6" v-html="$t('settings.data.importDesc1')"></p>

            <p class="mt-4" v-html="$t('settings.data.importDesc2')"></p>
            <p class="mt-4" v-html="$t('settings.data.importDesc3')"></p>
            <p class="mt-4">
              {{ $t('settings.data.importDesc4') }}
            </p>
            <p class="mt-4" v-html="$t('settings.data.importDesc5')"></p>
            <p class="mt-4">
              <a
                href="/import-aliases-template.csv"
                rel="nofollow noopener noreferrer"
                class="text-indigo-700 cursor-pointer dark:text-indigo-400"
                >{{ $t('settings.data.downloadTemplate') }}</a
              >
            </p>
          </div>

          <form
            @submit.prevent="
              importAliasesForm.post(route('aliases.import'), {
                preserveScroll: true,
                onSuccess: () => clearForm(),
              })
            "
          >
            <div class="row">
              <input
                type="file"
                id="aliases-import"
                @input="importAliasesForm.aliases_import = $event.target.files[0]"
                required
                :disabled="!domainsCount ? 'disabled' : undefined"
              />

              <p v-if="importAliasesForm.errors.aliases_import" class="mt-2 text-sm text-red-600">
                {{ importAliasesForm.errors.aliases_import }}
              </p>

              <div class="mt-4">
                <div
                  v-if="!domainsCount"
                  class="bg-cyan-400 block w-full text-center hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 cursor-not-allowed"
                >
                  {{ $t('settings.data.noCustomDomains') }}
                </div>
                <button
                  v-else
                  type="submit"
                  class="bg-cyan-400 block w-full text-center hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >
                  {{ $t('settings.data.importAliasData') }}
                </button>
              </div>
            </div>
          </form>

          <div class="my-6">
            <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">
              {{ $t('settings.data.exportAliases') }}
            </h3>

            <div class="mt-4 w-24 border-b-2 border-grey-200"></div>

            <p v-if="totalAliasesCount" class="mt-6 text-base text-grey-700 dark:text-grey-200" v-html="$t('settings.data.exportDesc', { count: totalAliasesCount })">
            </p>
            <p v-else class="mt-6 text-base text-grey-700 dark:text-grey-200">
              {{ $t('settings.data.noAliasesToExport') }}
            </p>
          </div>

          <p
            v-if="$page.props.errors.aliases_export"
            class="mt-2 text-sm text-red-600"
            id="enable-two-factor-error"
          >
            {{ $page.props.errors.aliases_export }}
          </p>
          <a
            v-if="totalAliasesCount"
            :href="route('aliases.export')"
            :class="!totalAliasesCount ? 'cursor-not-allowed' : ''"
            :disabled="!totalAliasesCount"
            class="bg-cyan-400 block w-full text-center hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            {{ $t('settings.data.exportAliasData') }}
          </a>
          <div
            v-else
            class="bg-cyan-400 block w-full text-center hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 cursor-not-allowed"
          >
            {{ $t('settings.data.exportAliasData') }}
          </div>
        </div>
      </div>
    </div>
  </SettingsLayout>
</template>

<script setup lang="ts">
import SettingsLayout from './../../Layouts/SettingsLayout.vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  totalAliasesCount: {
    type: Number,
    required: true,
  },
  domainsCount: {
    type: Number,
    required: true,
  },
})

const importAliasesForm = useForm({
  aliases_import: '',
})

const clearForm = () => {
  importAliasesForm.reset()
  document.getElementById('aliases-import').value = ''
}
</script>
