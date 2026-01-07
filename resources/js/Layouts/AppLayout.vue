<template>
  <div class="antialiased flex bg-grey-50 text-grey-700 dark:bg-grey-800 dark:text-white">
    <!-- Narrow sidebar -->
    <div class="h-screen hidden w-28 bg-indigo-900 overflow-y-auto md:block md:fixed">
      <div class="h-full pb-6 pt-4 flex flex-col items-center">
        <div class="shrink-0 flex items-center">
          <Link :href="route('dashboard.index')">
            <img class="h-10" alt="addy.io Logo" src="/svg/icon-logo.svg" />
          </Link>
        </div>
        <div class="flex-1 grow mt-6 w-full px-2 space-y-1">
          <Link
            v-for="item in sidebarNavigation"
            :key="item.name"
            :href="route(item.route)"
            :class="[
              item.route.startsWith(route().current().split('.')[0])
                ? 'bg-indigo-800 text-white'
                : 'text-indigo-50 hover:bg-indigo-800 hover:text-white',
              'group w-full p-3 rounded-md flex flex-col items-center text-center text-xs font-medium',
            ]"
            :aria-current="
              item.route.startsWith(route().current().split('.')[0]) ? 'page' : undefined
            "
          >
            <component
              :is="item.icon"
              :class="[
                item.route.startsWith(route().current().split('.')[0])
                  ? 'text-white'
                  : 'text-indigo-300 group-hover:text-white',
                'h-6 w-6',
              ]"
              aria-hidden="true"
            />
            <span class="mt-2">{{ item.name }}</span>
          </Link>
        </div>
        <div v-if="$page.props.version" class="text-indigo-200 shrink-0">
          <a
            :href="`https://github.com/anonaddy/anonaddy/releases/tag/v${$page.props.version}`"
            target="_blank"
            rel="nofollow noreferrer noopener"
            class="block sm:inline"
            >v{{ $page.props.version }}</a
          >
        </div>
        <div
          v-if="$page.props.updateAvailable"
          class="text-indigo-50 shrink-0 text-center text-sm font-semibold px-2"
        >
          <a
            href="https://github.com/anonaddy/anonaddy/releases/latest"
            target="_blank"
            rel="nofollow noreferrer noopener"
            class="block sm:inline"
            >{{ t('common.updateAvailable') }}</a
          >
        </div>
      </div>
    </div>

    <!-- Mobile menu (Sheet) -->
    <Sheet v-model:open="mobileMenuOpen">
      <SheetContent side="left" class="w-72 bg-indigo-900 p-0 border-none">
        <SheetHeader class="p-4">
          <SheetTitle class="sr-only">Navigation Menu</SheetTitle>
          <Link @click="mobileMenuOpen = false" :href="route('dashboard.index')">
            <img class="h-6" alt="addy.io Logo" src="/svg/icon-logo.svg" />
          </Link>
        </SheetHeader>
        <div class="flex-1 h-full px-2 overflow-y-auto flex flex-col">
          <nav class="flex flex-col grow">
            <div class="space-y-1">
              <Link
                v-for="item in sidebarNavigation"
                :key="item.name"
                @click="mobileMenuOpen = false"
                :href="route(item.route)"
                :class="[
                  item.route.startsWith(route().current().split('.')[0])
                    ? 'bg-indigo-800 text-white'
                    : 'text-indigo-100 hover:bg-indigo-800 hover:text-white',
                  'group py-2 px-3 rounded-md flex items-center text-sm font-medium',
                ]"
                :aria-current="$page.component.startsWith(item.name) ? 'page' : undefined"
              >
                <component
                  :is="item.icon"
                  :class="[
                    item.route.startsWith(route().current().split('.')[0])
                      ? 'text-white'
                      : 'text-indigo-300 group-hover:text-white',
                    'mr-3 h-6 w-6',
                  ]"
                  aria-hidden="true"
                />
                <span>{{ item.name }}</span>
              </Link>
            </div>
          </nav>
          <div v-if="$page.props.version" class="text-indigo-200 shrink-0 text-center pb-4">
            <a
              :href="`https://github.com/anonaddy/anonaddy/releases/tag/v${$page.props.version}`"
              target="_blank"
              rel="nofollow noreferrer noopener"
              class="block sm:inline"
              >v{{ $page.props.version }}</a
            >
          </div>
          <div
            v-if="$page.props.updateAvailable"
            class="text-indigo-50 shrink-0 text-center text-sm font-semibold px-2 pb-4"
          >
            <a
              href="https://github.com/anonaddy/anonaddy/releases/latest"
              target="_blank"
              rel="nofollow noreferrer noopener"
              class="block sm:inline"
              >{{ t('common.updateAvailable') }}</a
            >
          </div>
        </div>
      </SheetContent>
    </Sheet>

    <!-- Content area -->
    <div class="flex-1 flex flex-col overflow-hidden min-h-screen md:pl-28">
      <header class="w-full">
        <div
          class="relative z-10 flex-shrink-0 h-16 bg-white border-b border-grey-200 shadow-sm flex dark:bg-grey-900 dark:border-grey-600"
        >
          <button
            type="button"
            class="border-r border-grey-200 px-4 text-grey-500 focus:outline-none md:hidden dark:border-grey-600"
            @click="mobileMenuOpen = true"
          >
            <span class="sr-only">{{ t('common.openSidebar') }}</span>
            <Menu class="h-6 w-6" aria-hidden="true" />
          </button>
          <div class="max-w-screen-2xl mx-auto flex-1 flex justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex-1 flex items-center">
              <form @submit.prevent="submitSearchForm()" class="w-full flex md:ml-0">
                <label for="search-input" class="sr-only">{{ t('search.searchAll') }}</label>
                <div
                  class="relative w-full text-grey-400 focus-within:text-grey-600 dark:text-white dark:focus-within:text-white"
                >
                  <div class="pointer-events-none absolute inset-y-0 left-1.5 flex items-center">
                    <Search class="flex-shrink-0 h-5 w-5" aria-hidden="true" />
                  </div>
                  <input
                    @keyup.esc="
                      search
                        ? $inertia.visit(
                            route(route().current(), omit(route().params, ['search', 'page'])),
                            {
                              only: ['initialRows', 'search'],
                            },
                          )
                        : null
                    "
                    name="search-input"
                    id="search-input"
                    v-model="searchForm.search"
                    class="h-full w-full leading-none border-indigo-50 border-2 py-2 pl-8 pr-3 text-base text-grey-900 placeholder-grey-500 focus:outline-none focus:border-indigo-50npm focus:ring-0 focus:placeholder-grey-400 outline-none rounded-l-md bg-indigo-50 dark:bg-white/5 dark:placeholder-grey-200 dark:border-grey-400 dark:text-white"
                    :placeholder="t('common.search')"
                    type="search"
                  />
                  <div
                    v-if="search"
                    @click="
                      ;((searchForm.search = ''),
                        $inertia.visit(
                          route(route().current(), omit(route().params, ['search', 'page'])),
                          {
                            only: ['initialRows', 'search'],
                          },
                        ))
                    "
                    class="absolute inset-y-0 right-0 cursor-pointer flex items-center pr-3 rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                  >
                    <X class="h-5 w-5 text-grey-400 dark:text-white" aria-hidden="true" />
                  </div>
                </div>
              </form>
              <!-- Search Type Dropdown -->
              <DropdownMenu>
                <DropdownMenuTrigger as-child>
                  <button
                    class="inline-flex items-center bg-indigo-500 p-2 rounded-r-md text-sm font-medium text-white hover:bg-indigo-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    style="height: 39px"
                  >
                    <span class="hidden md:inline-flex items-center pl-3 pr-2">
                      {{ searchTypeSelected.title }}
                    </span>
                    <ChevronDown class="h-5 w-5 text-white" aria-hidden="true" />
                  </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-64">
                  <DropdownMenuItem
                    v-for="option in searchOptions"
                    :key="option.title"
                    @click="searchTypeSelected = option"
                    class="flex flex-col items-start cursor-pointer"
                  >
                    <div class="flex w-full justify-between items-center">
                      <span :class="searchTypeSelected.title === option.title ? 'font-semibold' : ''">
                        {{ option.title }}
                      </span>
                      <Check
                        v-if="searchTypeSelected.title === option.title"
                        class="h-4 w-4 text-indigo-500"
                        aria-hidden="true"
                      />
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">{{ option.description }}</p>
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </div>
            <div class="ml-2 flex items-center space-x-4 sm:ml-6 sm:space-x-6">
              <!-- Profile dropdown -->
              <DropdownMenu>
                <DropdownMenuTrigger as-child>
                  <button
                    class="bg-white rounded-sm flex text-base focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-grey-900"
                  >
                    <span class="sr-only">{{ t('common.openUserMenu') }}</span>
                    <span id="dropdown-username" class="ml-2 md:ml-0 font-medium">{{
                      $page.props.user.username
                    }}</span>
                    <ChevronDown class="ml-1 h-5 w-5" aria-hidden="true" />
                  </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-48">
                  <DropdownMenuItem as-child>
                    <a
                      href="https://app.addy.io/docs/"
                      target="_blank"
                      rel="nofollow noreferrer noopener"
                      class="flex justify-between items-center cursor-pointer"
                    >
                      {{ t('nav.apiDocs') }}
                      <ExternalLink class="h-4 w-4" aria-hidden="true" />
                    </a>
                  </DropdownMenuItem>
                  <DropdownMenuItem as-child>
                    <a
                      href="https://addy.io/blog/"
                      target="_blank"
                      rel="nofollow noreferrer noopener"
                      class="flex justify-between items-center cursor-pointer"
                    >
                      {{ t('nav.blog') }}
                      <ExternalLink class="h-4 w-4" aria-hidden="true" />
                    </a>
                  </DropdownMenuItem>
                  <DropdownMenuItem as-child>
                    <a
                      href="https://addy.io/help/"
                      target="_blank"
                      rel="nofollow noreferrer noopener"
                      class="flex justify-between items-center cursor-pointer"
                    >
                      {{ t('nav.help') }}
                      <ExternalLink class="h-4 w-4" aria-hidden="true" />
                    </a>
                  </DropdownMenuItem>
                  <DropdownMenuSeparator />
                  <DropdownMenuSub>
                    <DropdownMenuSubTrigger class="cursor-pointer">
                      <Languages class="mr-2 h-4 w-4" />
                      {{ t('settings.language') }}
                    </DropdownMenuSubTrigger>
                    <DropdownMenuSubContent>
                      <DropdownMenuItem
                        @click="changeLocale('ko')"
                        class="cursor-pointer flex justify-between items-center"
                      >
                        한국어
                        <Check v-if="currentLocale === 'ko'" class="h-4 w-4 text-indigo-500" />
                      </DropdownMenuItem>
                      <DropdownMenuItem
                        @click="changeLocale('en')"
                        class="cursor-pointer flex justify-between items-center"
                      >
                        English
                        <Check v-if="currentLocale === 'en'" class="h-4 w-4 text-indigo-500" />
                      </DropdownMenuItem>
                    </DropdownMenuSubContent>
                  </DropdownMenuSub>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem as-child>
                    <Link
                      :href="route('logout')"
                      method="post"
                      as="button"
                      type="button"
                      class="w-full cursor-pointer"
                    >
                      {{ t('nav.logout') }}
                    </Link>
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </div>
          </div>
        </div>
      </header>

      <main class="overflow-y-visible">
        <section
          aria-labelledby="primary-heading"
          class="min-w-0 h-full px-4 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto py-6"
        >
          <!-- Main content -->
          <slot />
        </section>
      </main>
    </div>
    <notifications position="bottom right" />

    <FlashNotification v-if="$page.props.flash">
      <template v-slot:icon>
        <CheckCircle class="h-6 w-6 text-white" aria-hidden="true" />
      </template>
      <template v-slot:message>
        {{ $page.props.flash }}
      </template>
    </FlashNotification>
  </div>
</template>

<script setup lang="ts">
import { router, useForm, usePage, Link } from '@inertiajs/vue3'
import { ref, watch, computed, type Component } from 'vue'
import { useI18n } from 'vue-i18n'
import {
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
} from '@/components/ui/dropdown-menu'
import {
  Settings,
  Users,
  Home,
  Menu,
  Inbox,
  X,
  Globe,
  AtSign,
  AlertTriangle,
  Filter,
  CheckCircle,
  Search,
  Check,
  ChevronDown,
  ExternalLink,
  Languages,
} from 'lucide-vue-next'
import FlashNotification from './../Components/FlashNotification.vue'

interface SearchOption {
  title: string
  route: string
  description: string
}

interface NavItem {
  name: string
  route: string
  icon: Component
}

const props = defineProps<{
  search?: string
}>()

const { t, locale } = useI18n()

const currentLocale = computed(() => locale.value)

const changeLocale = (newLocale: string) => {
  locale.value = newLocale
  localStorage.setItem('locale', newLocale)
}

const sidebarNavigation = computed<NavItem[]>(() => [
  { name: t('nav.dashboard'), route: 'dashboard.index', icon: Home },
  { name: t('nav.aliases'), route: 'aliases.index', icon: AtSign },
  { name: t('nav.recipients'), route: 'recipients.index', icon: Inbox },
  { name: t('nav.usernames'), route: 'usernames.index', icon: Users },
  { name: t('nav.domains'), route: 'domains.index', icon: Globe },
  { name: t('nav.rules'), route: 'rules.index', icon: Filter },
  { name: t('nav.failedDeliveries'), route: 'failed_deliveries.index', icon: AlertTriangle },
  { name: t('nav.settings'), route: 'settings.show', icon: Settings },
])

const mobileMenuOpen = ref(false)

const searchForm = useForm({
  search: props.search ?? '',
})

const searchOptions = computed<SearchOption[]>(() => [
  { title: t('nav.aliases'), route: 'aliases.index', description: t('search.aliasesDesc') },
  { title: t('nav.recipients'), route: 'recipients.index', description: t('search.recipientsDesc') },
  { title: t('nav.usernames'), route: 'usernames.index', description: t('search.usernamesDesc') },
  { title: t('nav.domains'), route: 'domains.index', description: t('search.domainsDesc') },
  { title: t('nav.rules'), route: 'rules.index', description: t('search.rulesDesc') },
  { title: t('nav.failedDeliveries'), route: 'failed_deliveries.index', description: t('search.failedDeliveriesDesc') },
])

const searchTypeSelected = ref<SearchOption>(
  _.find(searchOptions.value, ['title', _.startCase(usePage().component.split('/')[0])]) ?? searchOptions.value[0]
)

watch(
  () => usePage().component,
  function (component) {
    searchTypeSelected.value =
      _.find(searchOptions.value, ['title', _.startCase(component.split('/')[0])]) ?? searchOptions.value[0]

    if (!props.search) {
      searchForm.search = ''
    }
  },
)

watch(
  () => props.search,
  function (search) {
    if (!search) {
      searchForm.search = ''
    }
  },
)

const submitSearchForm = () => {
  if (!searchForm.search.length && props.search) {
    router.visit(route(route().current(), _.omit(route().params, ['search', 'page', 'id'])), {
      only: ['initialRows', 'search'],
    })
  } else if (searchForm.search.length > 1) {
    searchForm.get(
      route(searchTypeSelected.value.route, _.omit(route().params, ['search', 'page', 'id'])),
      {
        only: ['initialRows', 'search'],
      },
    )
  }
}

const omit = (object: Record<string, unknown>, key: string | string[]) => {
  return _.omit(object, key)
}
</script>
