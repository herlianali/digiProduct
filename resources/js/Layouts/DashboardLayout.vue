<template>
  <div class="flex min-h-screen bg-gray-100">
    <ProgressBar />

    <div
      v-if="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
    ></div>

    <aside
      :class="[
        'bg-white shadow-lg fixed inset-y-0 left-0 transform z-40 transition-all duration-300 ease-in-out',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        'lg:translate-x-0 lg:static lg:inset-auto lg:z-auto',
        'w-64 md:w-72'
      ]"
    >
      <div class="h-16 flex items-center justify-between px-6 border-b">
        <h1 class="text-lg font-bold text-gray-800 truncate">Admin Dashboard</h1>

        <button
          @click="sidebarOpen = false"
          class="lg:hidden text-gray-500 hover:text-gray-700"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <nav class="p-4 overflow-y-auto h-[calc(100vh-4rem)]">
        <ul class="space-y-1">
          <li class="relative">
            <div
              v-if="isActive('/dashboard')"
              class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-10 lg:h-12 bg-brand-600 rounded-r-lg"
            ></div>

            <Link
              href="/dashboard"
              class="flex items-center gap-3 p-3 rounded-lg transition-all duration-200"
              :class="isActive('/dashboard')
                ? 'bg-brand-50 text-brand-700 font-semibold'
                : 'text-gray-700 hover:bg-gray-100'"
            >
              <ArchiveBoxIcon class="w-5 h-5 flex-shrink-0"/>
              <span class="truncate">Dashboard</span>
            </Link>
          </li>

          <li class="relative">
            <div
              v-if="isActive('/products')"
              class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-10 lg:h-12 bg-brand-600 rounded-r-lg"
            ></div>

            <Link
              href="/products"
              class="flex items-center gap-3 p-3 rounded-lg transition-all duration-200"
              :class="isActive('/products')
                ? 'bg-brand-50 text-brand-700 font-semibold'
                : 'text-gray-700 hover:bg-gray-100'"
            >
              <CubeIcon class="w-5 h-5 flex-shrink-0"/>
              <span class="truncate">Products</span>
            </Link>
          </li>

          <li class="relative">
            <div
              v-if="isActive('/order')"
              class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-10 lg:h-12 bg-brand-600 rounded-r-lg"
            ></div>

            <Link
              href="/orders"
              class="flex items-center gap-3 p-3 rounded-lg transition-all duration-200"
              :class="isActive('/order')
                ? 'bg-brand-50 text-brand-700 font-semibold'
                : 'text-gray-700 hover:bg-gray-100'"
            >
              <ShoppingCartIcon class="w-5 h-5 flex-shrink-0"/>
              <span class="truncate">Orders</span>
            </Link>
          </li>

          <li class="relative">
            <div
              v-if="isActive('/article')"
              class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-10 lg:h-12 bg-brand-600 rounded-r-lg"
            ></div>

            <Link
              href="/article"
              class="flex items-center gap-3 p-3 rounded-lg transition-all duration-200"
              :class="isActive('/article')
                ? 'bg-brand-50 text-brand-700 font-semibold'
                : 'text-gray-700 hover:bg-gray-100'"
            >
              <BookOpenIcon class="w-5 h-5 flex-shrink-0"/>
              <span class="truncate">Articles</span>
            </Link>
          </li>

          <li class="relative">
            <div
              v-if="isActive('/analytic')"
              class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-10 lg:h-12 bg-brand-600 rounded-r-lg"
            ></div>

            <Link
              href="/analytic"
              class="flex items-center gap-3 p-3 rounded-lg transition-all duration-200"
              :class="isActive('/analytic')
                ? 'bg-brand-50 text-brand-700 font-semibold'
                : 'text-gray-700 hover:bg-gray-100'"
            >
              <ChartBarSquareIcon class="w-5 h-5 flex-shrink-0"/>
              <span class="truncate">Analytics</span>
            </Link>
          </li>

          <li class="relative">
            <div
              v-if="isActive('/setting')"
              class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-10 lg:h-12 bg-brand-600 rounded-r-lg"
            ></div>

            <button
              @click="settingOpen = !settingOpen"
              class="flex items-center justify-between w-full gap-3 p-3 rounded-lg transition-all duration-200"
              :class="isActive('/setting')
                ? 'bg-brand-50 text-brand-700 font-semibold'
                : 'text-gray-700 hover:bg-gray-100'"
            >
              <div class="flex items-center gap-3">
                <Cog6ToothIcon class="w-5 h-5 flex-shrink-0"/>
                <span class="truncate">Settings</span>
              </div>

              <ChevronDownIcon
                v-if="settingOpen"
                class="w-4 h-4 flex-shrink-0 transition-transform duration-200"
                :class="settingOpen ? 'rotate-180' : ''"
              />
              <ChevronRightIcon
                v-else
                class="w-4 h-4 flex-shrink-0 transition-transform duration-200"
              />
            </button>

            <transition
              enter-active-class="transition-all duration-200 ease-out"
              enter-from-class="transform opacity-0 -translate-y-2"
              enter-to-class="transform opacity-100 translate-y-0"
              leave-active-class="transition-all duration-150 ease-in"
              leave-from-class="transform opacity-100 translate-y-0"
              leave-to-class="transform opacity-0 -translate-y-2"
            >
              <ul
                v-if="settingOpen"
                class="mt-1 ml-9 lg:ml-10 space-y-1"
              >
                <li>
                  <Link
                    href="/setting/content"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors"
                    :class="isActive('/setting/content')
                      ? 'bg-brand-100 text-brand-700 font-medium'
                      : 'text-gray-600 hover:bg-gray-100'"
                  >
                    <span class="truncate">Content</span>
                  </Link>
                </li>

                <li>
                  <Link
                    href="/setting/slider-home"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors"
                    :class="isActive('/setting/slider-home')
                      ? 'bg-brand-100 text-brand-700 font-medium'
                      : 'text-gray-600 hover:bg-gray-100'"
                  >
                    <span class="truncate">Home Slider</span>
                  </Link>
                </li>

                <li>
                  <Link
                    href="/setting/slider-company"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors"
                    :class="isActive('/setting/slider-company')
                      ? 'bg-brand-100 text-brand-700 font-medium'
                      : 'text-gray-600 hover:bg-gray-100'"
                  >
                    <span class="truncate">Company Slider</span>
                  </Link>
                </li>

                <li>
                  <Link
                    href="/setting/testimoni-user"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors"
                    :class="isActive('/setting/testimoni-user')
                      ? 'bg-brand-100 text-brand-700 font-medium'
                      : 'text-gray-600 hover:bg-gray-100'"
                  >
                    <span class="truncate">User Testimonials</span>
                  </Link>
                </li>
              </ul>
            </transition>
          </li>
        </ul>

        <div class="lg:hidden mt-8 pt-6 border-t">
          <div class="flex items-center gap-3 px-3 py-2">
            <div class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center flex-shrink-0">
              <span class="text-white text-sm font-medium">{{ userInitial }}</span>
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-medium text-gray-800 truncate">{{ user.name }}</p>
              <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
            </div>
            <button
              @click="logout"
              class="ml-2 text-red-600 hover:text-red-800 p-1"
              title="Logout"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
            </button>
          </div>
        </div>
      </nav>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
      <header class="sticky top-0 z-20 bg-white border-b shadow-sm">
        <div class="h-16 flex items-center justify-between px-4 sm:px-6">
          <div class="flex items-center space-x-3">
            <button
              @click="sidebarOpen = !sidebarOpen"
              class="lg:hidden p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
              aria-label="Toggle sidebar"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>

            <div class="flex flex-col min-w-0">
              <h2 class="text-lg font-semibold text-gray-800 truncate">
                {{ getPageTitle() }}
              </h2>
              <p class="text-xs text-gray-500 hidden sm:block truncate">
                {{ getPageDescription() }}
              </p>
            </div>
          </div>

          <div class="relative" ref="profileContainer">
            <button
              @click.stop="toggleProfileDropdown"
              class="flex items-center space-x-3 p-1 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
              aria-label="User menu"
              :aria-expanded="profileOpen"
            >
              <div class="sm:hidden w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center">
                <span class="text-white text-sm font-medium">{{ userInitial }}</span>
              </div>

              <div class="hidden sm:flex items-center space-x-3">
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-800 truncate max-w-[120px]">{{ user.name }}</p>
                  <p class="text-xs text-gray-500 truncate max-w-[120px]">{{ user.email }}</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center flex-shrink-0">
                  <span class="text-white text-sm font-medium">{{ userInitial }}</span>
                </div>
                <div class="text-gray-500">
                  <ChevronDownIcon
                    v-if="!profileOpen"
                    class="w-4 h-4 transition-transform duration-200"
                    :class="profileOpen ? 'rotate-180' : ''"
                  />
                  <ChevronUpIcon
                    v-else
                    class="w-4 h-4 transition-transform duration-200"
                    :class="profileOpen ? 'rotate-180' : ''"
                  />
                </div>
              </div>
            </button>

            <transition
              enter-active-class="transition duration-100 ease-out"
              enter-from-class="transform scale-95 opacity-0"
              enter-to-class="transform scale-100 opacity-100"
              leave-active-class="transition duration-75 ease-in"
              leave-from-class="transform scale-100 opacity-100"
              leave-to-class="transform scale-95 opacity-0"
            >
              <div
                v-if="profileOpen"
                class="absolute right-0 mt-2 w-48 sm:w-56 bg-white rounded-lg shadow-lg border z-50"
                @click.stop
              >
                <div class="p-4 border-b">
                  <p class="text-sm font-medium text-gray-800 truncate">{{ user.name }}</p>
                  <p class="text-xs text-gray-500 truncate mt-1">{{ user.email }}</p>
                </div>

                <div class="py-1">
                  <Link
                    href="/profile"
                    class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                    @click="closeDropdowns"
                  >
                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div class="flex-1">
                      <p class="font-medium">Profile Settings</p>
                      <p class="text-xs text-gray-500 mt-0.5">Manage your account</p>
                    </div>
                  </Link>

                  <button
                    @click="handleLogout"
                    class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-gray-50 transition-colors"
                  >
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <div class="flex-1 text-left">
                      <p class="font-medium">Logout</p>
                      <p class="text-xs text-red-500/80 mt-0.5">Sign out of your account</p>
                    </div>
                  </button>
                </div>
              </div>
            </transition>
          </div>
        </div>
      </header>

      <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 bg-gray-50">
        <div class="max-w-7xl mx-auto">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import ProgressBar from '@/Components/ProgressBar.vue'
import {
  ArchiveBoxIcon,
  ChevronUpIcon,
  ChevronDownIcon,
  ChevronRightIcon,
  CubeIcon,
  ShoppingCartIcon,
  BookOpenIcon,
  Cog6ToothIcon,
  ChartBarSquareIcon
} from '@heroicons/vue/24/outline'

const sidebarOpen = ref(false)
const profileOpen = ref(false)
const settingOpen = ref(false)
const profileContainer = ref(null)

const page = usePage()
const user = computed(() => page.props.auth.user)

const userInitial = computed(() =>
  user.value?.name?.charAt(0).toUpperCase() ?? 'U'
)

const isActive = (url) => {
  return page.url.startsWith(url)
}

const toggleProfileDropdown = () => {
  profileOpen.value = !profileOpen.value
}

const closeDropdowns = () => {
  profileOpen.value = false
  settingOpen.value = false
}

const handleLogout = () => {
  closeDropdowns()
  router.post(route('logout'))
}

const getPageTitle = () => {
  const path = page.url

  if (path.startsWith('/dashboard')) return 'Dashboard'
  if (path.startsWith('/products')) return 'Products'
  if (path.startsWith('/order')) return 'Orders'
  if (path.startsWith('/article')) return 'Articles'
  if (path.startsWith('/setting')) return 'Settings'

  return 'Dashboard'
}

const getPageDescription = () => {
  const path = page.url

  if (path.startsWith('/dashboard')) return 'Overview of your store performance'
  if (path.startsWith('/products')) return 'Manage your digital products'
  if (path.startsWith('/order')) return 'View and manage customer orders'
  if (path.startsWith('/article')) return 'Manage blog articles and content'
  if (path.startsWith('/setting')) return 'Configure your store settings'

  return 'Admin dashboard'
}

if (page.url.startsWith('/setting')) {
  settingOpen.value = true
}

const handleClickOutside = (event) => {
  if (sidebarOpen.value &&
      !event.target.closest('aside') &&
      !event.target.closest('button[aria-label="Toggle sidebar"]')) {
    sidebarOpen.value = false
  }

  if (profileOpen.value &&
      profileContainer.value &&
      !profileContainer.value.contains(event.target)) {
    profileOpen.value = false
  }

  if (settingOpen.value &&
      !event.target.closest('li.relative') &&
      !event.target.closest('button[aria-label*="Settings"]')) {
    settingOpen.value = false
  }
}

const handleEscapeKey = (event) => {
  if (event.key === 'Escape') {
    closeDropdowns()
  }
}

const handleResize = () => {
  if (window.innerWidth >= 1024) {
    sidebarOpen.value = true
  } else {
    sidebarOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleEscapeKey)
  window.addEventListener('resize', handleResize)

  handleResize()

  router.on('finish', () => {
    if (window.innerWidth < 1024) {
      sidebarOpen.value = false
    }
  })
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('keydown', handleEscapeKey)
  window.removeEventListener('resize', handleResize)
})
</script>

<style scoped>
nav::-webkit-scrollbar {
  width: 4px;
}

nav::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

nav::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

nav::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}

.transition-all {
  transition-property: all;
}

@media (max-width: 640px) {
  button, a {
    min-height: 44px;
    min-width: 44px;
  }
}
</style>
