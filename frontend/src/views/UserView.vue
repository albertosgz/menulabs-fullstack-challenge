<script setup lang="ts">
import { useUsersStore } from "@/stores/users";
import { computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import WeatherPeriod from "@/components/WeatherPeriod.vue";

const store = useUsersStore();
const route = useRoute();

onMounted(async () => {
  const id = route.params.id;
  await store.getUser(+id);
});

const user = computed(() => store.userSelected);
const forecast = computed(() => store.forecastSelected);
const forecastError = computed(() => !store.loading && forecast.value?.error);
const forecastPeriods = computed(() => forecast.value?.periods);
</script>

<template>
  <section>
    <h2>User info</h2>
    <progress :class="{ invisible: !store.loading }"></progress>
    <div v-if="!user && !store.loading">User not found</div>
    <div v-else-if="!store.loading">
      <article>
        <span
          >Name:
          <strong>
            {{ user?.name }}
          </strong></span
        >
        <br />
        <span
          >Email: <strong>{{ user?.email }}</strong></span
        >
      </article>
      <h3>Forecast</h3>
      <mark v-if="!!forecastError">{{ forecastError }}</mark>
      <div v-else class="forecast-grid">
        <span v-for="period in forecastPeriods" :key="period.number">
          <weather-period :period="period"></weather-period>
        </span>
      </div>
    </div>
  </section>
</template>

<style scoped>
.invisible {
  visibility: hidden;
}
.forecast-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-column-gap: 15px;
  grid-row-gap: 5px;
}
</style>
