<template>
  <div class="blank-wrapper">
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: "blank-layout"
};
</script>

<style scoped>
.blank-wrapper {
  min-height: 100vh;
  width: 100%;
}
</style>
