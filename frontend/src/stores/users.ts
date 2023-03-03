import { defineStore } from "pinia";
import { computed, ref } from "vue";
import type { Forecast } from "@/models/forecast";
import type { User } from "@/models/user";

export const MIN_PAGE = 1;
export const QUANTITY_PAGE = 10;

const BASE_URL = "http://localhost/api";

export const useUsersStore = defineStore("users", () => {
  const currentPage = ref<undefined | number>(undefined);
  const page = ref(MIN_PAGE);
  const users = ref<User[]>([]);
  const loading = ref(false);
  const userSelected = ref<User | undefined>(undefined);
  const forecastSelected = ref<Forecast | undefined>(undefined);
  const forecastDatetimeTimeRequest = ref<number | undefined>(undefined);

  async function requestUsers() {
    if (currentPage.value === undefined || currentPage.value !== page.value) {
      loading.value = true;
      currentPage.value = page.value;
      try {
        const url = `${BASE_URL}/users?page=${page.value}`;
        const response = await (await fetch(url)).json();
        users.value = response?.data || [];
      } catch (error) {
        alert(error);
        // TODO Set previous page
      }
      loading.value = false;
    }
  }

  async function reload() {
    await requestUsers();
  }
  async function next() {
    page.value++;
    await requestUsers();
  }
  async function previous() {
    if (page.value > MIN_PAGE) {
      page.value--;
      await requestUsers();
    }
  }

  async function getUser(id: number) {
    // Weather forecast cannot be cached more than 1h
    const now = Date.now();
    const oldCachedInfo =
      !forecastDatetimeTimeRequest.value ||
      forecastDatetimeTimeRequest.value + 1000 * 60 * 60 < now;

    if (!userSelected.value || userSelected.value.id !== id || oldCachedInfo) {
      // TODO User info can be reach from internal list when available instead of wait for API response
      loading.value = true;
      userSelected.value = undefined;
      forecastDatetimeTimeRequest.value = Date.now();
      const url = `${BASE_URL}/users/${id}`;
      try {
        const response = await (await fetch(url)).json();
        userSelected.value = response?.data;
        forecastSelected.value = response?.forecast;
      } catch (error) {
        alert(error);
        // TODO Set previous page
      }
      loading.value = false;
    }
  }

  return {
    page,
    users,
    loading,
    reload,
    next,
    previous,
    getUser,
    userSelected,
    forecastSelected,
  };
});
