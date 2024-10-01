<template>
  <nav>
    <RouterLink to="/">
      Home
    </RouterLink>&nbsp;
    <RouterLink to="/reset-password">
      Some other route
    </RouterLink>
  </nav>
  <main>
    <RouterView v-slot="{ Component }">
      <KeepAlive>
        <component :is="Component" />
      </KeepAlive>
    </RouterView>
  </main>
</template>