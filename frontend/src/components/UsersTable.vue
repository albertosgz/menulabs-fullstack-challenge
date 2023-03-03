<script>
import { MIN_PAGE, QUANTITY_PAGE, useUsersStore } from "@/stores/users";
import { mapState } from "pinia";

export default {
  created() {
    const store = useUsersStore();
    store.reload();
  },

  computed: {
    ...mapState(useUsersStore, [
      "loading",
      "users",
      "page",
      "next",
      "previous",
    ]),
    firstPage() {
      return this.page === MIN_PAGE;
    },
    lastPage() {
      return this.users?.length < QUANTITY_PAGE;
    },
  },
};
</script>

<template>
  <div>
    <progress :class="{ invisible: !loading }"></progress>
    <table role="grid">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <th scope="row">{{ user.id }}</th>
          <th>
            <router-link :to="`/user/${user.id}`">
              {{ user.name }}
            </router-link>
          </th>
          <th>
            <router-link :to="`/user/${user.id}`">
              {{ user.email }}
            </router-link>
          </th>
        </tr>
      </tbody>
    </table>
    <div class="grid">
      <button @click="previous()" :disabled="firstPage" class="primary">
        Previous
      </button>
      <span class="pagination-page">Page {{ page }}</span>
      <button @click="next()" :disabled="lastPage" class="primary">Next</button>
    </div>
  </div>
</template>

<style scoped>
.pagination-page {
  display: inline-flex;
  justify-self: center;
  align-self: center;
  margin-bottom: 20px;
}
.invisible {
  visibility: hidden;
}
</style>
