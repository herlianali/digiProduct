<template>
  <div class="flex h-screen bg-gray-100">
    <ProgressBar />
    <aside
      :class="[
        'bg-white w-64 fixed inset-y-0 left-0 transform z-30 transition-transform duration-300 ease-in-out',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        'md:translate-x-0 md:static md:inset-auto'
      ]"
    >
      <div class="h-16 flex items-center justify-center">
        <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
      </div>

      <nav class="mt-6">
        <ul>
          <li class="mb-2 relative">
            <div
              v-if="isActive('/dashboard')"
              class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-12 bg-indigo-600 rounded-r-lg"
            ></div>

            <Link
              href="/dashboard"
              class="flex items-center font-bold gap-4 p-3 ml-6 mr-8 rounded-lg transition"
              :class="isActive('/dashboard')
                ? 'bg-indigo-600 text-white'
                : 'text-gray-700 hover:bg-gray-100'"
            >
              <ArchiveBoxIcon class="w-5 h-5"/>
              Dashboard
            </Link>
          </li>

          <li class="mb-2 relative">
            <div
              v-if="isActive('/product')"
              class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-12 bg-indigo-600 rounded-r-lg"
            ></div>

            <Link
              href="/product"
              class="flex items-center font-bold gap-4 p-3 ml-6 mr-8 rounded-lg transition"
              :class="isActive('/product')
                ? 'bg-indigo-600 text-white'
                : 'text-gray-700 hover:bg-gray-100'"
            >
              <CubeIcon class="w-5 h-5"/>
              Product
            </Link>
          </li>

          <li class="mb-2 relative">
            <div
              v-if="isActive('/order')"
              class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-12 bg-indigo-600 rounded-r-lg"
            ></div>

            <Link
              href="/order"
              class="flex items-center font-bold gap-4 p-3 ml-6 mr-8 rounded-lg transition"
              :class="isActive('/order')
                ? 'bg-indigo-600 text-white'
                : 'text-gray-700 hover:bg-gray-100'"
            >
              <ShoppingCartIcon class="w-5 h-5"/>
              Order
            </Link>
          </li>

          <li class="mb-2 relative">
            <div
              v-if="isActive('/order')"
              class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-12 bg-indigo-600 rounded-r-lg"
            ></div>

            <Link
              href="/order"
              class="flex items-center font-bold gap-4 p-3 ml-6 mr-8 rounded-lg transition"
              :class="isActive('/order')
                ? 'bg-indigo-600 text-white'
                : 'text-gray-700 hover:bg-gray-100'"
            >
              <BookOpenIcon class="w-5 h-5"/>
              Article
            </Link>
          </li>

          <li class="mb-2 relative">
            <!-- Active bar -->
            <div
              v-if="isActive('/setting')"
              class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-12 bg-indigo-600 rounded-r-lg"
            ></div>

            <!-- Parent -->
            <button
              @click="settingOpen = !settingOpen"
              class="flex items-center justify-between font-bold gap-16 p-3 ml-6 mr-8 rounded-lg transition"
              :class="isActive('/setting')
                ? 'bg-indigo-600 text-white'
                : 'text-gray-700 hover:bg-gray-100'"
            >
              <div class="flex items-center gap-4">
                <Cog6ToothIcon class="w-5 h-5"/>
                Setting
              </div>

              <ChevronDownIcon
                v-if="settingOpen"
                class="w-4 h-4"
              />
              <ChevronRightIcon
                v-else
                class="w-4 h-4"
              />
            </button>

            <!-- Dropdown -->
            <ul
              v-if="settingOpen"
              class="mt-1 ml-14 space-y-1"
            >
              <li>
                <Link
                  href="/setting/content"
                  class="block px-3 py-2 rounded-lg text-sm"
                  :class="isActive('/setting/content')
                    ? 'bg-indigo-100 text-indigo-700'
                    : 'text-gray-600 hover:bg-gray-100'"
                >
                  Content
                </Link>
              </li>

              <li>
                <Link
                  href="/setting/slider-home"
                  class="block px-3 py-2 rounded-lg text-sm"
                  :class="isActive('/setting/slider-home')
                    ? 'bg-indigo-100 text-indigo-700'
                    : 'text-gray-600 hover:bg-gray-100'"
                >
                  Slider Home
                </Link>
              </li>

              <li>
                <Link
                  href="/setting/slider-home"
                  class="block px-3 py-2 rounded-lg text-sm"
                  :class="isActive('/setting/slider-home')
                    ? 'bg-indigo-100 text-indigo-700'
                    : 'text-gray-600 hover:bg-gray-100'"
                >
                  Slider Company
                </Link>
              </li>

              <li>
                <Link
                  href="/setting/testimoni-user"
                  class="block px-3 py-2 rounded-lg text-sm"
                  :class="isActive('/setting/testimoni-user')
                    ? 'bg-indigo-100 text-indigo-700'
                    : 'text-gray-600 hover:bg-gray-100'"
                >
                  Testimoni User
                </Link>
              </li>
            </ul>
          </li>
        </ul>
      </nav>

      
    </aside>

    <div
      v-if="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 bg-black bg-opacity-25 z-20 md:hidden"
    ></div>

    <div class="flex-1 flex flex-col ml-0">
      <header class="h-16 bg-white border-b flex items-center justify-between px-6">
        <div class="flex items-center">
          <button
            @click="sidebarOpen = !sidebarOpen"
            class="text-gray-500 focus:outline-none md:hidden mr-4"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              viewBox="0 0 24 24"
            >
              <path d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <h2 class="text-xl font-semibold text-gray-800">Dashboard</h2>
        </div>
        
        <div class="relative">
          <button
            @click="profileOpen = !profileOpen"
            class="flex items-center space-x-2 focus:outline-none"
          >
            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center">
              <span class="text-white text-sm font-medium">
                {{ userInitial }}
              </span>
            </div>
            <div class="flex flex-col items-start px-4 py-3">
              <p class="text-sm font-medium text-gray-800">{{ user.name }}</p>
              <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
            </div>
            <div v-if="!profileOpen" class="rounded-full border border-gray-500 p-1">
              <ChevronDownIcon class="w-2 h-2 " />
            </div>
            <div v-else class="rounded-full border border-gray-500 p-1">
              <ChevronUpIcon class="w-2 h-2" />
            </div>
          </button>

          <!-- Dropdown -->
          <div
            v-if="profileOpen"
            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border z-50"
          >
            <div class="px-4 py-3 border-b">
              <p class="text-sm font-medium text-gray-800">{{ user.name }}</p>
              <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
            </div>

            <Link
              href="/profile"
              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
            >
              Profile
            </Link>

            <button
              @click="logout"
              class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
            >
              Logout
            </button>
          </div>
        </div>

      </header>

      <main class="flex-1 overflow-auto p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed } from 'vue'
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
    Cog6ToothIcon
  } from '@heroicons/vue/24/outline'

  const sidebarOpen = ref(false)
  const profileOpen = ref(false)
  const settingOpen = ref(false)

  const page = usePage()
  const user = computed(() => page.props.auth.user)

  const userInitial = computed(() =>
    user.value?.name?.charAt(0).toUpperCase() ?? 'U'
  )

  const isActive = (url) => {
    return page.url.startsWith(url)
  }

  const logout = () => {
    router.post(route('logout'))
  }

  if (page.url.startsWith('/setting')) {
    settingOpen.value = true
  }

</script>

