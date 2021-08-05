<template>
  <q-dialog v-model="_showModal">
    <q-card style="width: 1000px; max-width: 80vw;">
      <q-card-section>
        <q-form
          @submit="onSubmit"
          @reset="onReset"
          class="q-gutter-md full-width"
        >
          <q-input
            name="identifier"
            filled
            v-model="form.identifier"
            label="Identificador *"
            hint="Código de identificação"
            lazy-rules
            :rules="[ val => val && val.length > 0 || 'Entre com o Código de identificação']"
          />
          <q-input
            name="name"
            filled
            v-model="form.name"
            label="Nome *"
            hint="Nome do Produto"
            lazy-rules
            :rules="[ val => val && val.length > 0 || 'Entre com o Nome da Produto']"
          />
          <category-select
            name="category"
            data-vv-as="Categoria"
            v-model="form.category"
            label="Categoria *"
            hint="Categoria do Produto"
            lazy-rules
            :rules="[ val => val || 'Escolha a Categoria do Produto']"
          />
          <div>
            <q-btn label="Enviar" type="submit" color="primary"/>
            <q-btn label="Limpar" type="reset" color="primary" flat class="q-ml-sm" />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import { useQuasar } from 'quasar'
import CategorySelect from '../../components/category/CategorySelect.vue'
import { Product } from '../models';
import { api } from '../../boot/axios'

export default defineComponent({
  name: 'Product',
  components: {
    CategorySelect
  },
  props: {
    productId: {
      type: String,
      default: null
    },
    showModal: {
      type: Boolean,
      default: false
    }
  },
  data () {
    const $q = useQuasar()
    const form = {} as Product

    return {
      $q,
      form,
    }
  },
  methods: {
    onSubmit () {
      this.save()
    },
    onReset () {
      this.form = {
        id: 0,
        identifier: '',
        name: '',
        category: {
          id: 0,
          name: ''
        },
        category_id: 0
      }
    },
    async load () {
      if (this.productId.length > 0) {
        this.form = (await api.get(`product/${this.productId}`) as {data: Product}).data
      }
    },
    save () {
      if (this.form.category) this.form.category_id = this.form.category.id
      if (this.productId) {
        api.put(`product/${this.productId}`, this.form)
        .then((succ: {data: string}) => {
          this.onReset()
          this.$emit('update')
          this._showModal = false
          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: succ.data
          })
        })
        .catch((err: {response: {data: {detail: string}}}) => {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'warning',
            message: err.response.data.detail
          })
        })
      } else {
        api.post('product', this.form)
        .then((succ: {data: string}) => {
          this.onReset()
          this.$emit('update')
          this._showModal = false
          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: succ.data
          })
        })
        .catch((err: {response: {data: {detail: string}}}) => {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'warning',
            message: err.response.data.detail
          })
        })
      }
    }
  },
  computed: {
    _showModal: {
      get (): boolean { return this.showModal },
      set (value: boolean) { this.$emit('showModal', value) }
    }
  },
  watch: {
    _showModal: function (newValue: boolean) {
      if (newValue) {
        if (this.productId.length > 0) {
          this.load()
        }
      }
    }
  }
})
</script>
