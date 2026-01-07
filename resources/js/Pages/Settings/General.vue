<template>
  <SettingsLayout>
    <div class="divide-y divide-grey-200">
      <!-- Section 1: Email Update -->
      <div class="py-10">
        <div class="space-y-1">
          <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">{{ $t('settings.general.updateEmail') }}</h3>
          <p class="text-base text-grey-700 dark:text-grey-200">
            {{ $t('settings.general.updateEmailDesc') }}
          </p>
        </div>
        <div class="mt-4">
          <form
            @submit.prevent="
              emailForm.post(route('settings.edit_default_recipient'), {
                preserveScroll: true,
                onSuccess: () => emailForm.reset(),
              })
            "
          >
            <div class="grid grid-cols-1 mb-6">
              <div>
                <div class="mb-4">
                  <label for="current_email" class="block text-sm font-medium leading-6 text-grey-600 dark:text-white">{{ $t('settings.general.currentEmail') }}</label>
                  <div class="relative mt-2">
                    <input type="email" name="current_email" id="current_email" :value="$page.props.user.email" disabled class="block w-full rounded-md border-0 py-1.5 text-grey-900 shadow-sm ring-1 ring-grey-300 placeholder:text-grey-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 disabled:cursor-not-allowed disabled:bg-grey-50 disabled:dark:bg-grey-900 disabled:text-grey-500 disabled:ring-grey-200 sm:text-sm sm:leading-6" />
                  </div>
                </div>
                <div class="mb-4">
                  <label for="email" class="block text-sm font-medium leading-6 text-grey-600 dark:text-white">{{ $t('settings.general.newEmail') }}</label>
                  <div class="relative mt-2">
                    <input v-model="emailForm.email" type="email" name="email" id="email" required autocomplete="email" class="block w-full rounded-md border-0 py-2 pr-10 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 dark:bg-white/5 text-grey-900 dark:text-white ring-grey-300 placeholder:text-grey-400 focus:ring-indigo-600" placeholder="johndoe@example.com" />
                  </div>
                </div>
                <div>
                  <label for="current" class="block text-sm font-medium leading-6 text-grey-600 dark:text-white">{{ $t('settings.general.currentPassword') }}</label>
                  <div class="relative mt-2">
                    <input v-model="emailForm.current" type="password" name="current" id="current" required class="block w-full rounded-md border-0 py-2 pr-10 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 dark:bg-white/5 text-grey-900 dark:text-white ring-grey-300 placeholder:text-grey-400 focus:ring-indigo-600" placeholder="********" />
                  </div>
                </div>
              </div>
            </div>
            <button type="submit" :disabled="emailForm.processing" class="bg-cyan-400 w-full hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed">
              {{ $t('settings.general.updateEmail') }}
              <loader v-if="emailForm.processing" />
            </button>
          </form>
        </div>
      </div>

      <!-- Section 2: Dark Mode -->
      <div class="py-10">
        <div class="space-y-1">
          <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">{{ $t('settings.general.darkModeTheme') }}</h3>
          <p class="text-base text-grey-700 dark:text-grey-200">{{ $t('settings.general.darkModeThemeDesc') }}</p>
        </div>
        <div class="mt-4">
          <form @submit.prevent="darkModeForm.post(route('settings.dark_mode'), { preserveScroll: true, onSuccess: () => reloadPage() })">
            <div class="grid grid-cols-1 mb-6">
              <div>
                <label for="dark-mode" class="block text-sm font-medium leading-6 text-grey-600 dark:text-white">{{ $t('settings.general.darkMode') }}</label>
                <div class="block relative w-full mt-2">
                  <select id="dark-mode" v-model="darkModeForm.dark_mode" name="format" required class="relative block w-full rounded border-0 bg-transparent py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset focus:z-10 focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 ring-grey-300 focus:ring-indigo-600">
                    <option :value="false" class="dark:bg-grey-900">{{ $t('common.disabled') }}</option>
                    <option :value="true" class="dark:bg-grey-900">{{ $t('common.enabled') }}</option>
                  </select>
                </div>
              </div>
            </div>
            <button type="submit" :disabled="darkModeForm.processing" class="bg-cyan-400 w-full hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed">
              {{ $page.props.darkMode ? $t('settings.general.disableDarkMode') : $t('settings.general.enableDarkMode') }}
              <loader v-if="darkModeForm.processing" />
            </button>
          </form>
        </div>
      </div>

      <!-- Section 3: Default Alias Domain -->
      <div class="py-10">
        <div class="space-y-1">
          <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">{{ $t('settings.general.updateDefaultAliasDomain') }}</h3>
          <p class="text-base text-grey-700 dark:text-grey-200">{{ $t('settings.general.updateDefaultAliasDomainDesc') }}</p>
        </div>
        <div class="mt-4">
          <form @submit.prevent="defaultAliasDomainForm.post(route('settings.default_alias_domain'), { preserveScroll: true })">
            <div class="grid grid-cols-1 mb-6">
              <div>
                <label for="default-alias-domain" class="block text-sm font-medium leading-6 text-grey-600 dark:text-white">{{ $t('settings.general.selectDefaultDomain') }}</label>
                <div class="block relative w-full mt-2">
                  <select id="default-alias-domain" v-model="defaultAliasDomainForm.domain" name="domain" required class="relative block w-full rounded border-0 bg-transparent py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset focus:z-10 focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 ring-grey-300 focus:ring-indigo-600">
                    <option v-for="domain in domainOptions" :key="domain" class="dark:bg-grey-900">{{ domain }}</option>
                  </select>
                </div>
              </div>
            </div>
            <button type="submit" :disabled="defaultAliasDomainForm.processing" class="bg-cyan-400 w-full hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed">
              {{ $t('settings.general.updateDefaultAliasDomain') }}
              <loader v-if="defaultAliasDomainForm.processing" />
            </button>
          </form>
        </div>
      </div>

      <!-- Section 4: Default Alias Format -->
      <div class="py-10">
        <div class="space-y-1">
          <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">{{ $t('settings.general.updateDefaultAliasFormat') }}</h3>
          <p class="text-base text-grey-700 dark:text-grey-200">{{ $t('settings.general.updateDefaultAliasFormatDesc') }}</p>
        </div>
        <div class="mt-4">
          <form @submit.prevent="defaultAliasFormatForm.post(route('settings.default_alias_format'), { preserveScroll: true })">
            <div class="grid grid-cols-1 mb-6">
              <div>
                <label for="default-alias-format" class="block text-sm font-medium leading-6 text-grey-600 dark:text-white">{{ $t('settings.general.selectDefaultFormat') }}</label>
                <div class="block relative w-full mt-2">
                  <select id="default-alias-format" v-model="defaultAliasFormatForm.format" name="format" required class="relative block w-full rounded border-0 bg-transparent py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset focus:z-10 focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 ring-grey-300 focus:ring-indigo-600">
                    <option value="random_characters" class="dark:bg-grey-900">{{ $t('aliases.formatRandomChars') }}</option>
                    <option value="uuid" class="dark:bg-grey-900">UUID</option>
                    <option value="random_words" class="dark:bg-grey-900">{{ $t('aliases.formatRandomWords') }}</option>
                    <option value="custom" class="dark:bg-grey-900">{{ $t('settings.general.custom') }}</option>
                  </select>
                </div>
              </div>
            </div>
            <button type="submit" :disabled="defaultAliasFormatForm.processing" class="bg-cyan-400 w-full hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed">
              {{ $t('settings.general.updateDefaultAliasFormat') }}
              <loader v-if="defaultAliasFormatForm.processing" />
            </button>
          </form>
        </div>
      </div>

      <!-- Section 5: Login Redirect -->
      <div class="py-10">
        <div class="space-y-1">
          <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">{{ $t('settings.general.updateLoginRedirect') }}</h3>
          <p class="text-base text-grey-700 dark:text-grey-200">{{ $t('settings.general.updateLoginRedirectDesc') }}</p>
        </div>
        <div class="mt-4">
          <form @submit.prevent="loginRedirectForm.post(route('settings.login_redirect'), { preserveScroll: true })">
            <div class="grid grid-cols-1 mb-6">
              <div>
                <label for="login-redirect" class="block text-sm font-medium leading-6 text-grey-600 dark:text-white">{{ $t('settings.general.selectLoginRedirect') }}</label>
                <div class="block relative w-full mt-2">
                  <select id="login-redirect" v-model="loginRedirectForm.redirect" name="redirect" required class="relative block w-full rounded border-0 bg-transparent py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset focus:z-10 focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 ring-grey-300 focus:ring-indigo-600">
                    <option v-for="redirect in loginRedirectOptions" :key="redirect.value" :value="redirect.value" class="dark:bg-grey-900">{{ redirect.label }}</option>
                  </select>
                </div>
              </div>
            </div>
            <button type="submit" :disabled="loginRedirectForm.processing" class="bg-cyan-400 w-full hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed">
              {{ $t('settings.general.updateLoginRedirect') }}
              <loader v-if="loginRedirectForm.processing" />
            </button>
          </form>
        </div>
      </div>

      <!-- Section 6: Display From Format -->
      <div class="py-10">
        <div class="space-y-1">
          <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">{{ $t('settings.general.updateDisplayFromFormat') }}</h3>
          <p class="text-base text-grey-700 dark:text-grey-200">
            <span v-html="$t('settings.general.updateDisplayFromFormatDesc')"></span>
          </p>
        </div>
        <div class="mt-4">
          <form @submit.prevent="displayFromFormatForm.post(route('settings.display_from_format'), { preserveScroll: true })">
            <div class="grid grid-cols-1 mb-6">
              <div>
                <label for="display-from-format" class="block text-sm font-medium leading-6 text-grey-600 dark:text-white">{{ $t('settings.general.selectDisplayFromFormat') }}</label>
                <div class="block relative w-full mt-2">
                  <select id="display-from-format" v-model="displayFromFormatForm.format" name="format" required class="relative block w-full rounded border-0 bg-transparent py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset focus:z-10 focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 ring-grey-300 focus:ring-indigo-600">
                    <option v-for="format in displayFromFormatOptions" :key="format.value" :value="format.value" class="dark:bg-grey-900">{{ format.label }}</option>
                  </select>
                </div>
              </div>
            </div>
            <button type="submit" :disabled="displayFromFormatForm.processing" class="bg-cyan-400 w-full hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed">
              {{ $t('settings.general.updateDisplayFromFormat') }}
              <loader v-if="displayFromFormatForm.processing" />
            </button>
          </form>
        </div>
      </div>

      <!-- Section 7: Use Reply-To Header -->
      <div class="py-10">
        <div class="space-y-1">
          <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">{{ $t('settings.general.useReplyToTitle') }}</h3>
          <p class="text-base text-grey-700 dark:text-grey-200">{{ $t('settings.general.useReplyToDesc1') }}</p>
          <p class="text-base text-grey-700 dark:text-grey-200">
            <span v-html="$t('settings.general.useReplyToDesc2', { username: $page.props.user.username })"></span>
          </p>
        </div>
        <div class="mt-4">
          <form @submit.prevent="useReplyToForm.post(route('settings.use_reply_to'), { preserveScroll: true })">
            <div class="grid grid-cols-1 mb-6">
              <div>
                <label for="use-reply-to" class="block text-sm font-medium leading-6 text-grey-600 dark:text-white">{{ $t('settings.general.useReplyTo') }}</label>
                <div class="block relative w-full mt-2">
                  <select id="use-reply-to" v-model="useReplyToForm.use_reply_to" name="format" required class="relative block w-full rounded border-0 bg-transparent py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset focus:z-10 focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 ring-grey-300 focus:ring-indigo-600">
                    <option :value="true" class="dark:bg-grey-900">{{ $t('common.enabled') }}</option>
                    <option :value="false" class="dark:bg-grey-900">{{ $t('common.disabled') }}</option>
                  </select>
                </div>
              </div>
            </div>
            <button type="submit" :disabled="useReplyToForm.processing" class="bg-cyan-400 w-full hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed">
              {{ $t('settings.general.updateUseReplyTo') }}
              <loader v-if="useReplyToForm.processing" />
            </button>
          </form>
        </div>
      </div>

      <!-- Section 8: Store Failed Deliveries -->
      <div class="py-10">
        <div class="space-y-1">
          <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">{{ $t('settings.general.storeFailedDeliveries') }}</h3>
          <p class="text-base text-grey-700 dark:text-grey-200">
            <span v-html="$t('settings.general.storeFailedDeliveriesDesc')"></span>
          </p>
        </div>
        <div class="mt-4">
          <form @submit.prevent="storeFailedDeliveriesForm.post(route('settings.store_failed_deliveries'), { preserveScroll: true })">
            <div class="grid grid-cols-1 mb-6">
              <div>
                <label for="store-failed-deliveries" class="block text-sm font-medium leading-6 text-grey-600 dark:text-white">{{ $t('settings.general.storeFailedDeliveries') }}</label>
                <div class="block relative w-full mt-2">
                  <select id="store-failed-deliveries" v-model="storeFailedDeliveriesForm.store_failed_deliveries" name="format" required class="relative block w-full rounded border-0 bg-transparent py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset focus:z-10 focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 ring-grey-300 focus:ring-indigo-600">
                    <option :value="true" class="dark:bg-grey-900">{{ $t('common.enabled') }}</option>
                    <option :value="false" class="dark:bg-grey-900">{{ $t('common.disabled') }}</option>
                  </select>
                </div>
              </div>
            </div>
            <button type="submit" :disabled="storeFailedDeliveriesForm.processing" class="bg-cyan-400 w-full hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed">
              {{ $t('settings.general.updateStoreFailedDeliveries') }}
              <loader v-if="storeFailedDeliveriesForm.processing" />
            </button>
          </form>
        </div>
      </div>

      <!-- Section 9: Save Alias Last Used -->
      <div class="py-10">
        <div class="space-y-1">
          <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">{{ $t('settings.general.saveAliasLastUsedTitle') }}</h3>
          <p class="text-base text-grey-700 dark:text-grey-200">
            <span v-html="$t('settings.general.saveAliasLastUsedDesc')"></span>
            <Link :href="route('aliases.index')" class="text-indigo-500 hover:text-indigo-800 dark:text-indigo-200 dark:hover:text-indigo-300 font-medium">{{ $t('nav.aliases') }}</Link>.
          </p>
        </div>
        <div class="mt-4">
          <form @submit.prevent="saveAliasLastUsedForm.post(route('settings.save_alias_last_used'), { preserveScroll: true })">
            <div class="grid grid-cols-1 mb-6">
              <div>
                <label for="save-alias-last-used" class="block text-sm font-medium leading-6 text-grey-600 dark:text-white">{{ $t('settings.general.saveAliasLastUsed') }}</label>
                <div class="block relative w-full mt-2">
                  <select id="save-alias-last-used" v-model="saveAliasLastUsedForm.save_alias_last_used" name="format" required class="relative block w-full rounded border-0 bg-transparent py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset focus:z-10 focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 ring-grey-300 focus:ring-indigo-600">
                    <option :value="true" class="dark:bg-grey-900">{{ $t('common.enabled') }}</option>
                    <option :value="false" class="dark:bg-grey-900">{{ $t('common.disabled') }}</option>
                  </select>
                </div>
              </div>
            </div>
            <button type="submit" :disabled="saveAliasLastUsedForm.processing" class="bg-cyan-400 w-full hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed">
              {{ $t('settings.general.updateSaveAliasLastUsed') }}
              <loader v-if="saveAliasLastUsedForm.processing" />
            </button>
          </form>
        </div>
      </div>

      <!-- Section 10: From Name -->
      <div class="py-10">
        <div class="space-y-1">
          <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">{{ $t('settings.general.updateGlobalFromName') }}</h3>
          <div>
            <p class="text-base text-grey-700 dark:text-grey-200">
              {{ $t('settings.general.fromNameDesc', { username: $page.props.user.username }) }}
            </p>
            <div class="text-base text-grey-700 dark:text-grey-200 my-3">
              <span v-html="$t('settings.general.fromNamePriorityTitle')"></span>
              <ul class="list-decimal list-inside text-grey-700 dark:text-grey-200 text-base mt-2">
                <li>{{ $t('settings.general.fromNamePriority1') }}</li>
                <li>{{ $t('settings.general.fromNamePriority2') }}</li>
                <li><b>{{ $t('settings.general.fromNamePriority3') }}</b></li>
              </ul>
            </div>
            <p class="text-base text-grey-700 dark:text-grey-200">
              {{ $t('settings.general.fromNameOverride') }}
            </p>
          </div>
        </div>
        <div class="mt-4">
          <form @submit.prevent="fromNameForm.post(route('settings.from_name'), { preserveScroll: true })">
            <div class="grid grid-cols-1 mb-6">
              <div>
                <label for="from-name" class="block text-sm font-medium leading-6 text-grey-600 dark:text-white">{{ $t('settings.general.globalFromName') }}</label>
                <div class="relative mt-2">
                  <input v-model="fromNameForm.from_name" type="text" name="from_name" id="from-name" class="block w-full rounded-md border-0 py-2 pr-10 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 dark:bg-white/5 text-grey-900 dark:text-white ring-grey-300 placeholder:text-grey-400 focus:ring-indigo-600" placeholder="John Doe" />
                </div>
              </div>
            </div>
            <button type="submit" :disabled="fromNameForm.processing" class="bg-cyan-400 w-full hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed">
              {{ $t('settings.general.updateGlobalFromName') }}
              <loader v-if="fromNameForm.processing" />
            </button>
          </form>
        </div>
      </div>

      <!-- Section 11: Banner Location -->
      <div class="py-10">
        <div class="space-y-1">
          <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">{{ $t('settings.general.updateBannerLocation') }}</h3>
          <p class="text-base text-grey-700 dark:text-grey-200">{{ $t('settings.general.updateBannerLocationDesc') }}</p>
        </div>
        <div class="mt-4">
          <form @submit.prevent="bannerLocationForm.post(route('settings.banner_location'), { preserveScroll: true })">
            <div class="grid grid-cols-1 mb-6">
              <div>
                <label for="banner-location" class="block text-sm font-medium leading-6 text-grey-600 dark:text-white">{{ $t('settings.general.selectBannerLocation') }}</label>
                <div class="block relative w-full mt-2">
                  <select id="banner-location" v-model="bannerLocationForm.banner_location" name="banner_location" required class="relative block w-full rounded border-0 bg-transparent py-2 text-grey-900 dark:text-white dark:bg-white/5 ring-1 ring-inset focus:z-10 focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 ring-grey-300 focus:ring-indigo-600">
                    <option v-for="location in bannerLocationOptions" :key="location.value" :value="location.value" class="dark:bg-grey-900">{{ location.label }}</option>
                  </select>
                </div>
              </div>
            </div>
            <button type="submit" :disabled="bannerLocationForm.processing" class="bg-cyan-400 w-full hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed">
              {{ $t('settings.general.updateBannerLocation') }}
              <loader v-if="bannerLocationForm.processing" />
            </button>
          </form>
        </div>
      </div>

      <!-- Section 12: Email Subject -->
      <div class="py-10">
        <div class="space-y-1">
          <h3 class="text-lg font-medium leading-6 text-grey-900 dark:text-white">{{ $t('settings.general.replaceEmailSubject') }}</h3>
          <p class="text-base text-grey-700 dark:text-grey-200">
            <span v-html="$t('settings.general.replaceEmailSubjectDesc')"></span>
          </p>
          <p class="text-base text-grey-700 dark:text-grey-200">{{ $t('settings.general.replaceEmailSubjectEmpty') }}</p>
        </div>
        <div class="mt-4">
          <form @submit.prevent="emailSubjectForm.post(route('settings.email_subject'), { preserveScroll: true })">
            <div class="grid grid-cols-1 mb-6">
              <div>
                <label for="email-subject" class="block text-sm font-medium leading-6 text-grey-600 dark:text-white">{{ $t('settings.general.emailSubject') }}</label>
                <div class="relative mt-2">
                  <input v-model="emailSubjectForm.email_subject" type="text" name="email_subject" id="email-subject" class="block w-full rounded-md border-0 py-2 pr-10 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-base sm:leading-6 dark:bg-white/5 text-grey-900 dark:text-white ring-grey-300 placeholder:text-grey-400 focus:ring-indigo-600" placeholder="The subject" />
                </div>
              </div>
            </div>
            <button type="submit" :disabled="emailSubjectForm.processing" class="bg-cyan-400 w-full hover:bg-cyan-300 text-cyan-900 font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed">
              {{ $t('settings.general.updateEmailSubject') }}
              <loader v-if="emailSubjectForm.processing" />
            </button>
          </form>
        </div>
      </div>

    </div>
  </SettingsLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import SettingsLayout from './../../Layouts/SettingsLayout.vue'

const { t } = useI18n()

const props = defineProps({
  defaultAliasDomain: { type: String, required: true },
  defaultAliasFormat: { type: String, required: true },
  loginRedirect: { type: Number, required: true },
  displayFromFormat: { type: Number, required: true },
  domainOptions: { type: Array, required: true },
  useReplyTo: { type: Boolean, required: true },
  storeFailedDeliveries: { type: Boolean, required: true },
  darkMode: { type: Boolean, required: true },
  saveAliasLastUsed: { type: Boolean, required: true },
  fromName: { type: String, required: true },
  bannerLocation: { type: String, required: true },
  emailSubject: { type: String, required: true },
})

const displayFromFormatOptions = computed(() => [
  { value: 0, label: t('settings.general.displayFromFormat0') },
  { value: 7, label: t('settings.general.displayFromFormat7') },
  { value: 1, label: t('settings.general.displayFromFormat1') },
  { value: 2, label: t('settings.general.displayFromFormat2') },
  { value: 3, label: t('settings.general.displayFromFormat3') },
  { value: 4, label: t('settings.general.displayFromFormat4') },
  { value: 6, label: t('settings.general.displayFromFormat6') },
  { value: 5, label: t('settings.general.noNameJustAlias') },
])

const loginRedirectOptions = computed(() => [
  { value: 0, label: t('nav.dashboard') },
  { value: 1, label: t('nav.aliases') },
  { value: 2, label: t('nav.recipients') },
  { value: 3, label: t('nav.usernames') },
  { value: 4, label: t('nav.domains') },
])

const bannerLocationOptions = computed(() => [
  { value: 'top', label: t('settings.general.bannerTop') },
  { value: 'bottom', label: t('settings.general.bannerBottom') },
  { value: 'off', label: t('settings.general.bannerOff') },
])

const emailForm = useForm({ email: '', current: '' })
const darkModeForm = useForm({ dark_mode: props.darkMode })
const defaultAliasDomainForm = useForm({ domain: props.defaultAliasDomain })
const defaultAliasFormatForm = useForm({ format: props.defaultAliasFormat })
const loginRedirectForm = useForm({ redirect: props.loginRedirect })
const displayFromFormatForm = useForm({ format: props.displayFromFormat })
const useReplyToForm = useForm({ use_reply_to: props.useReplyTo })
const storeFailedDeliveriesForm = useForm({ store_failed_deliveries: props.storeFailedDeliveries })
const saveAliasLastUsedForm = useForm({ save_alias_last_used: props.saveAliasLastUsed })
const fromNameForm = useForm({ from_name: props.fromName })
const bannerLocationForm = useForm({ banner_location: props.bannerLocation })
const emailSubjectForm = useForm({ email_subject: props.emailSubject })

const reloadPage = () => { window.location.reload() }
</script>
