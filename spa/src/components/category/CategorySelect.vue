<template>
  <q-select
    filled
    use-input
    :options="options"
    :loading="loading"
    v-model="select.selected"
    :disable="disabled"
    :error="error"
    :error-message="errorMessage"
    :name="name"
    :label="label"
    input-debounce="300"
    @input="$emit('change')"
    @filter="filterFn"
    option-value="id"
    option-label="name"
  >
    <template v-slot:no-option>
      <q-item>
        <q-item-section class="text-grey">
          Nenhuma categoria encontrada
        </q-item-section>
      </q-item>
    </template>
  </q-select>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { QSelect } from 'quasar'
import { Category, Select, Option } from '../../components/models'
import { api } from '../../boot/axios'

export default defineComponent({
  name: 'Category',
  components: { QSelect },
  props: {
    value: {
      required: false
    },
    label: {
      required: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    error: {
      required: false
    },
    errorMessage: {
      required: false
    },
    name: {
      required: false
    }
  },
  data () {
    const select = {} as Select
    return {
      options: [] as Array<Option>,
      loading: false as boolean,
      select,
    }
  },
  watch: {
    'disabled' (value: boolean) {
      if (value) {
        this.select.selected = {
          id: '',
          name: ''
        }
        this.options = []
      }
    },
    'select.selected' (option: any) {
      let ret = null
      if (option) ret = option.id
      this.$emit('input', ret)
    },
    'select.options' (options: Array<Category>) {
      this.select.selected = this.getOption(options, this.value)
    },
    value (value: number) {
      if (value > 0) {
        if (this.select.options.length > 0) this.select.selected = this.getOption(this.select.options, value)
      } else {
        this.select.selected = {
          id: '',
          name: ''
        }
      }
    }
  },
  methods: {
    toSelect (data: Array<Category>): Array<Option> {
      if (!Array.isArray(data)) throw new TypeError('toSelect param must be an Array')
      return data.map(item => {
        return {
          id: item['id'],
          name: item['name']
        }
      })
    },
    getOption (data: Array<any>, value: any) {
      return data.find((o: any) => {
        return o.id === value
      })
    },
    filterFn (val: string, update: any) {
      if (val === '') {
        update(() => {
          this.options = this.select.options
        })
        return
      }
      update(() => {
        const needle = val.toLowerCase()
        this.options = this.select.options.filter((v: { name: string }) => v.name.toLowerCase().indexOf(needle) > -1)
      })
    },
    async load () {
      this.loading = true
      let response = (await api.get('category')).data
      this.select.options = this.toSelect(response)
      this.options = this.select.options
      this.loading = false
    }
  },
  beforeMount () {
    if (!this.disabled) {
      this.load()
    }
  }
})
</script>
