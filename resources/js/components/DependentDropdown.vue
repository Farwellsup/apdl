<template>
  <div>
    <a-select :options="filteredOptions" v-model="selected"></a-select>
  </div>
</template>

<script>
export default {
  props: ['value', 'category'],
  data() {
    return {
      selected: this.value,
      allOptions: {
        books: [
          { value: 'fiction', label: 'Fiction' },
          { value: 'nonfiction', label: 'Non-fiction' }
        ],
        movies: [
          { value: 'action', label: 'Action' },
          { value: 'drama', label: 'Drama' }
        ]
      }
    }
  },
  computed: {
    filteredOptions() {
      return this.allOptions[this.category] || []
    }
  },
  watch: {
    selected(val) {
      this.$emit('input', val)
    }
  }
}
</script>