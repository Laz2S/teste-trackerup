<template>
  <div class="q-ma-md">
    <div class="col">
      <h4>Produtos</h4>
    </div>
    <div class="col">
      <product-table
        :loading="loadingTable"
        :data="data"
        ref="table"
        @edit="editForm"
        @new="newForm()"
        @update="load"
      />
    </div>
    <product-form
      :show-modal="showModal"
      :productId="idSelected"
      @update="load"
      @showModal="showModal = false"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import ProductTable from 'components/product/ProductTable.vue'
import ProductForm from 'components/product/ProductForm.vue'

export default defineComponent({
  name: 'ProductIndex',
  components: {
    ProductTable,
    ProductForm
  },
  setup () {
    const params = ref({
      term: '',
      status: null
    })
    const showModal = ref(false)
    const idSelected = ref('')
    const productId = ref(0)
    const loadingTable = ref(false)
    const data = ref([])

    return {
      params,
      showModal,
      idSelected,
      productId,
      loadingTable,
      data,
    }
  },
  methods: {
    editForm (id: string) {
      this.showModal = true
      this.idSelected = id
    },
    load () {
      (this.$refs.table as any).load()
    },
    newForm () {
      this.showModal = true
      this.idSelected = ''
    }
  }
})
</script>
