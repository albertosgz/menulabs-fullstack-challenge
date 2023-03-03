<script setup lang="ts"></script>

<template>
  <header class="container">
    <nav>
      <ul>
        <li><RouterLink to="/">Home</RouterLink></li>
      </ul>
      <ul>
        <li><strong>Menu Labs test</strong></li>
      </ul>
    </nav>
  </header>
  <main class="container">
    <RouterView />
  </main>
  <footer class="container">
    <small>
      Made by
      <a href="https://www.linkedin.com/in/albertosgz/"
        >Alberto Sanchez Gonzalez</a
      ></small
    >
  </footer>
</template>

<style scoped></style>
