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
            name="name"
            filled
            v-model="form.name"
            label="Nome *"
            hint="Nome do Produto"
            lazy-rules
            :rules="[ val => val && val.length > 0 || 'Entre com o Nome da Produto']"
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
import { Category } from '../models';
import { api } from '../../boot/axios'

export default defineComponent({
  name: 'Category',
  components: {},
  props: {
    categoryId: {
      type: Number,
      default: null
    },
    showModal: {
      type: Boolean,
      default: false
    }
  },
  data () {
    const $q = useQuasar()
    const form = {} as Category

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
        name: ''
      }
    },
    async load () {
      if (this.categoryId > 0) {
        this.form = (await api.get(`category/${this.categoryId}`) as {data: Category}).data
      }
    },
    save () {
      if (this.categoryId) {
        api.put(`category/${this.categoryId}`, this.form)
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
      else {
        api.post('category', this.form)
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
    _showModal: function (newValue) {
      if (newValue) {
        if (this.categoryId > 0) {
          this.load()
        }
      }
    }
  }
})
</script>
