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
            <q-td key="name" :props="props">
              {{ props.row.name }}
            </q-td>
            <q-td key="actions" :props="props">
              <q-btn
                flat
                round
                icon="edit"
                size="sm"
                color="primary"
                @click="editRow(props.row.id)"
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
                @click="deleteRow(props.row.id)"
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
  name: 'CategoryTable',
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
        { name: 'name', field: 'name', align: 'left', label: 'Nome', style: 'width: 90%' },
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
      this.request = await api.get('category', { params })
      this.pagination.rowsNumber = this.request.total
      this.pagination.page = props.pagination.page
      this.pagination.rowsPerPage = props.pagination.rowsPerPage
      this.pagination.sortBy = props.pagination.sortBy
      this.pagination.descending = props.pagination.descending
      this.loading = false
    },
    deleteRow (id: number) {
      this.$q.dialog({
        title: 'Remoção de Categoria',
        message: 'Deseja realmente remover esta categoria?',
        cancel: true,
        persistent: true
      }).onOk(async () => {
        await api.delete(`category/${id}`)
        .catch((err: {response: {data: {detail: string}}}) => {
          this.$q.notify({
            color: 'red-5',
            textColor: 'white',
            icon: 'warning',
            message: err.response.data.detail
          })
        })
        this.$emit('update')
      })
    },
    newRow () {
      this.$emit('new')
    },
    editRow (id: number) {
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
