<script setup lang="ts">
import { AdminLayout, toastService } from '@admin'
import CreateButton from '@admin/components/ui/button/CreateButton.vue'
import DeleteButton from '@admin/components/ui/button/DeleteButton.vue'
import EditButton from '@admin/components/ui/button/EditButton.vue'
import DataTable, { type Column, type PaginationMeta } from '@admin/components/ui/dataTable/DataTable.vue'
import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue'
import { galleryService, type Gallery } from '../../services/galleryService'

const router = useRouter()
const galleries = ref<Gallery[]>([])
const isLoading = ref(false)
const pagination = ref<PaginationMeta>({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0
})

const columns: Column<Gallery>[] = [
  { key: 'id', label: 'ID', sortable: true, width: '80px' },
  { key: 'name', label: 'Név', sortable: true },
  { key: 'slug', label: 'Slug', sortable: true },
  { key: 'created_at', label: 'Létrehozva', sortable: true, width: '200px' },
]

const fetchGalleries = async (params: {
  search?: string
  sort?: string
  direction?: 'asc' | 'desc'
  page?: number
}) => {
  try {
    isLoading.value = true
    const response = await galleryService.getAll(params)
    galleries.value = response.data.data
    pagination.value = response.data.meta
  } catch (error) {
    console.error('Hiba a galériák betöltésekor:', error)
    toastService.error('Hiba történt a galériák betöltése során.')
  } finally {
    isLoading.value = false
  }
}

const deleteGallery = async (id: number) => {
  try {
    await galleryService.delete(id)
    toastService.success('Galéria sikeresen törölve!')
    await fetchGalleries({ page: pagination.value.current_page })
  } catch (error) {
    console.error('Hiba a galéria törlésekor:', error)
    toastService.error('Hiba történt a törlés során.')
  }
}

const editGallery = (id: number) => {
  router.push(`/admin/gallery/${id}/edit`)
}

onMounted(() => {
  fetchGalleries({
    page: 1,
    sort: 'name',
    direction: 'asc'
  })
})
</script>

<template>
  <AdminLayout pageTitle="Galériák">
    <DataTable
      :columns="columns"
      :data="galleries"
      :loading="isLoading"
      :pagination="pagination"
      :searchable="true"
      search-placeholder="Keresés név vagy slug alapján..."
      default-sort="name"
      default-direction="asc"
      @fetch="fetchGalleries"
    >
      <template #actions>
        <CreateButton to="/admin/gallery/create">
          Új galéria
        </CreateButton>
      </template>

      <template #row-actions="{ row }">
        <EditButton
          @click="editGallery(row.id!)"
        />
        <DeleteButton
          @confirm="deleteGallery(row.id!)"
        />
      </template>

      <template #empty>
        Nincs megjeleníthető galéria.
      </template>
    </DataTable>
  </AdminLayout>
</template>
