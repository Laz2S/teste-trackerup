<template>
  <div>
    <div class="row q-pa-md q-gutter-sm">
      <q-input
        v-model="params.term"
        label="Busca rápida"
        class="col-12"
         @keyup.enter="load()"
      >
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
    </div>
    <div class="row  q-pa-md q-gutter-sm">
      <q-table class="col-12"
        :columns="columns"
        :rows="data.data"
        :loading="loading"
        :pagination="pagination"
        @request="onRequest"
      >
        <template v-slot:body="props">
          <q-tr :props="props">
            <q-td key="id" :props="props">
              {{ props.row.id }}
            </q-td>
            <q-td key="identifier" :props="props">
              {{ props.row.identifier }}
            </q-td>
            <q-td key="name" :props="props">
              {{ props.row.name }}
            </q-td>
            <q-td key="category" :props="props">
              {{ props.row.category ? props.row.category.name : '' }}
            </q-td>
            <q-td key="actions" :props="props">
              <q-btn
                flat
                round
                icon="edit"
                size="sm"
                color="primary"
                @click="editRow(props.row.identifier)"
              >
                <q-tooltip>
                  Editar
                </q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                icon="delete"
                size="sm"
                color="primary"
                @click="deleteRow(props.row.identifier)"
              >
                <q-tooltip>
                  Excluir
                </q-tooltip>
              </q-btn>
            </q-td>
          </q-tr>
        </template>
      </q-table>
    </div>
    <div class="row q-pa-md text-right">
      <div class="col-12">
        <q-btn label="Adicionar" icon="add" color="accent" @click="newRow()"/>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import { useQuasar, QTable, QTr, QTd, QTooltip, QInput, QIcon } from 'quasar'
import configTable from '../../components/table/config'
import { Params, Props, Pagination } from '../models';
import { api } from '../../boot/axios'

export default defineComponent({
  name: 'ProductTable',
  components: {
    QTable,
    QTr,
    QTd,
    QTooltip,
    QInput,
    QIcon
  },
  props: {
    selected: {
      default: () => []
    },
    selection: {
      type: String
    }
  },
  data () {
    const $q = useQuasar()
    const params = {} as Params
    const pagination = {} as Pagination
    const request = ref({ data: [], total: 0})

    return {
      $q,
      ...configTable,
      columns: [
        { name: 'id', field: 'id', label: '#', style: 'width: 5%' },
        { name: 'identifier', field: 'identifier', align: 'left', label: 'Identificador', style: 'width: 20%' },
        { name: 'name', field: 'name', align: 'left', label: 'Nome', style: 'width: 50%' },
        { name: 'category', field: 'category', align: 'left', label: 'Categoria', style: 'width: 20%' },
        { name: 'actions', field: '', align: 'center', style: 'width: 5%' }
      ],
      request,
      loading: false,
      pagination,
      params,
    }
  },
  methods: {
    async onRequest (props: Props) {
      this.loading = true
      let params = Object.assign({
        'sortBy': props.pagination.sortBy,
        'desc': props.pagination.descending,
        'rowsPerPage': props.pagination.rowsPerPage,
        'page': props.pagination.page
      }, this.params)
      this.request = await api.get('product', { params })
      this.pagination.rowsNumber = this.request.total
      this.pagination.page = props.pagination.page
      this.pagination.rowsPerPage = props.pagination.rowsPerPage
      this.pagination.sortBy = props.pagination.sortBy
      this.pagination.descending = props.pagination.descending
      this.loading = false
    },
    deleteRow (id: string) {
      this.$q.dialog({
        title: 'Remoção de Produto',
        message: 'Deseja realmente remover este produto?',
        cancel: true,
        persistent: true
      }).onOk(async () => {
        await api.delete(`product/${id}`)
        this.$emit('update')
      })
    },
    newRow () {
      this.$emit('new')
    },
    editRow (id: string) {
      this.$emit('edit', id)
    },
    load () {
      this.onRequest({
        pagination: this.pagination
      })
    }
  },
  computed: {
    data (): Array<[]> {
      return this.request.data || []
    }
  },
  beforeMount () {
    this.load()
  }
})
</script>

<style>
</style>
