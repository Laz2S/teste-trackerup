<template>
  <div class="q-ma-md">
    <div class="col">
      <h4>Categorias</h4>
    </div>
    <div class="col">
      <category-table
        :loading="loadingTable"
        :data="data"
        ref="table"
        @edit="editForm"
        @new="newForm()"
        @update="load"
      />
    </div>
    <category-form
      :show-modal="showModal"
      :categoryId="idSelected"
      @update="load"
      @showModal="showModal = false"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import CategoryTable from 'components/category/CategoryTable.vue'
import CategoryForm from 'components/category/CategoryForm.vue'

export default defineComponent({
  name: 'CategoryIndex',
  components: {
    CategoryTable,
    CategoryForm
  },
  setup () {
    const params = ref({
      term: '',
      status: null
    })
    const showModal = ref(false)
    const idSelected = ref(0)
    const categoryId = ref(0)
    const loadingTable = ref(false)
    const data = ref([])

    return {
      params,
      showModal,
      idSelected,
      categoryId,
      loadingTable,
      data,
    }
  },
  methods: {
    editForm (id: number) {
      this.showModal = true
      this.idSelected = id
    },
    load () {
      (this.$refs.table as any).load()
    },
    newForm () {
      this.showModal = true
      this.idSelected = 0
    }
  }
})
</script>
