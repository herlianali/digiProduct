<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <form @submit.prevent="submit" class="bg-white p-8 rounded shadow-md w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

      <div class="mb-4">
        <label for="email" class="block mb-1 font-medium">Email</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-300"
        />
        <p v-if="errors.email" class="text-red-600 mt-1 text-sm">{{ errors.email }}</p>
      </div>

      <div class="mb-4">
        <label for="password" class="block mb-1 font-medium">Password</label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-300"
        />
        <p v-if="errors.password" class="text-red-600 mt-1 text-sm">{{ errors.password }}</p>
      </div>

      <div class="mb-4 flex items-center">
        <input
          id="remember"
          type="checkbox"
          v-model="form.remember"
          class="mr-2"
        />
        <label for="remember" class="select-none">Remember Me</label>
      </div>

      <button
        type="submit"
        :disabled="processing"
        class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 disabled:opacity-50"
      >
        Login
      </button>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post('/login', {
    onError: (errors) => {
      // errors otomatis terisi oleh Inertia
    },
    onFinish: () => {
      form.reset('password');
    },
  });
};

const errors = form.errors;
const processing = form.processing;
</script>
