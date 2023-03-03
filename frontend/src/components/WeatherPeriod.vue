<script setup lang="ts">
import type { Period } from "@/models/forecast";
import { computed } from "vue";

const props = defineProps<{
  period: Period;
}>();

const humidityUnitCode = computed(() => {
  if (props.period.relativeHumidity?.unitCode === "wmoUnit:percent") {
    return "%";
  }
  return props.period.relativeHumidity?.unitCode;
});
const precipitationUnitCode = computed(() => {
  if (props.period.probabilityOfPrecipitation?.unitCode === "wmoUnit:percent") {
    return "%";
  }
  return props.period.probabilityOfPrecipitation?.unitCode;
});
const startTime = computed(() => {
  const d = new Date(props.period.startTime);
  return d.toLocaleString();
});
</script>

<template>
  <article>
    <header>
      <span
        >{{ period.name }}<br /><small>{{ startTime }}</small></span
      >

      <img
        v-if="period.icon"
        :src="period.icon"
        :alt="period.shortForecast"
        loading="lazy"
      />
    </header>
    <p>{{ period.detailedForecast }}</p>
    <footer>
      <div>
        <span>🌡️{{ period.temperature }}° {{ period.temperatureUnit }}</span>
        <span
          >💧 {{ period.relativeHumidity?.value || 0 }}{{ humidityUnitCode }}
        </span>
      </div>
      <div>
        <span>💨 {{ period.windSpeed }} {{ period.windDirection }}</span>
        <span
          >☔️ {{ period.probabilityOfPrecipitation?.value || 0
          }}{{ precipitationUnitCode }}</span
        >
      </div>
    </footer>
  </article>
</template>

<style scoped>
img {
  border-radius: 50%;
}
header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
p {
  min-height: 250px;
}
footer {
  display: flex;
  flex-direction: column;
}
footer div {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>
