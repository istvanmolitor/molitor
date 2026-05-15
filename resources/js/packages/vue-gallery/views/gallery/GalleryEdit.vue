<script setup lang="ts">
import { AdminLayout, toastService } from '@admin'
import Button from '@admin/components/ui/button/Button.vue'
import Icon from '@admin/components/ui/Icon.vue'
import { useRouter, useRoute } from 'vue-router'
import { ref, onMounted, computed } from 'vue'
import { galleryService, type Gallery, type GalleryImage } from '../../services/galleryService'
import MediaFilePicker from '@media/components/MediaFilePicker.vue'

const router = useRouter()
const route = useRoute()
const isEdit = computed(() => !!route.params.id)

const gallery = ref<Gallery>({
  name: '',
  slug: '',
  description: '',
  images: []
})

const isLoading = ref(false)
const isSaving = ref(false)

const fetchGallery = async (id: string | number) => {
  try {
    isLoading.value = true
    const response = await galleryService.getById(id)
    console.log('API response:', response.data)
    // Ha a JsonResource wrapping be van kapcsolva, akkor response.data.data,
    // ha nincs, akkor response.data
    const responseData = (response.data as any).data || response.data

    // Csak azokat a mezőket vegyük át, amikre az űrlapnak szüksége van,
    // hogy ne írjuk felül a reaktív objektum referenciáját teljesen, ha nem muszáj,
    // de a legegyszerűbb az assign
    Object.assign(gallery.value, responseData)

    // Biztosítsuk, hogy az images tömb mindig létezik
    if (!gallery.value.images) {
      gallery.value.images = []
    }
  } catch (error) {
    console.error('Hiba a galéria betöltésekor:', error)
    toastService.error('Hiba történt a galéria betöltése során.')
    router.push('/admin/gallery')
  } finally {
    isLoading.value = false
  }
}

const saveGallery = async () => {
  try {
    isSaving.value = true
    if (isEdit.value) {
      await galleryService.update(route.params.id as string, gallery.value)
      toastService.success('Galéria sikeresen frissítve!')
    } else {
      await galleryService.create(gallery.value)
      toastService.success('Galéria sikeresen létrehozva!')
    }
    router.push('/admin/gallery')
  } catch (error: any) {
    console.error('Hiba a mentéskor:', error)
    const message = error.response?.data?.message || 'Hiba történt a mentés során.'
    toastService.error(message)
  } finally {
    isSaving.value = false
  }
}

const addImage = (file: any) => {
  const url = file.download_url || file.url || file.path
  gallery.value.images.push({
    image_url: url,
    title: file.name,
    order: gallery.value.images.length
  })
}

const removeImage = (index: number) => {
  gallery.value.images.splice(index, 1)
  // Update orders
  gallery.value.images.forEach((img, i) => img.order = i)
}

const moveImage = (index: number, direction: 'up' | 'down') => {
  const newIndex = direction === 'up' ? index - 1 : index + 1
  if (newIndex < 0 || newIndex >= gallery.value.images.length) return

  const temp = gallery.value.images[index]
  gallery.value.images[index] = gallery.value.images[newIndex]
  gallery.value.images[newIndex] = temp

  // Update orders
  gallery.value.images.forEach((img, i) => img.order = i)
}

const generateSlug = () => {
  if (!isEdit.value && gallery.value.name) {
    gallery.value.slug = gallery.value.name
      .toLowerCase()
      .replace(/[^\w ]+/g, '')
      .replace(/ +/g, '-')
  }
}

onMounted(() => {
  if (isEdit.value) {
    fetchGallery(route.params.id as string)
  }
})
</script>

<template>
  <AdminLayout :pageTitle="isEdit ? 'Galéria szerkesztése' : 'Új galéria'">
    <div v-if="isLoading" class="flex justify-center p-12">
      <Icon name="loading" size="48" class="animate-spin text-primary" />
    </div>

    <form v-else @submit.prevent="saveGallery" class="space-y-6 max-w-4xl">
      <div class="bg-white p-6 rounded-lg shadow-sm border border-border">
        <h3 class="text-lg font-medium mb-4">Alapadatok</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-2">
            <label class="text-sm font-medium">Név</label>
            <input
              v-model="gallery.name"
              type="text"
              required
              class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
              placeholder="Galéria neve"
              @blur="generateSlug"
            />
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium">Slug</label>
            <input
              v-model="gallery.slug"
              type="text"
              required
              class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
              placeholder="galeria-slug"
            />
          </div>
          <div class="md:col-span-2 space-y-2">
            <label class="text-sm font-medium">Leírás</label>
            <textarea
              v-model="gallery.description"
              rows="3"
              class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
              placeholder="Galéria leírása (opcionális)"
            ></textarea>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-sm border border-border">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium">Képek</h3>
          <MediaFilePicker
            :show-preview="false"
            @select="addImage"
          >
            <template #default>
              <Button type="button" variant="outline" size="sm">
                <Icon name="add" size="18" class="mr-2" />
                Kép hozzáadása
              </Button>
            </template>
          </MediaFilePicker>
        </div>

        <div v-if="gallery.images.length === 0" class="text-center py-8 text-muted-foreground border-2 border-dashed rounded-lg">
          Nincsenek képek a galériában. Kattints a "Kép hozzáadása" gombra!
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="(image, index) in gallery.images"
            :key="index"
            class="flex items-center gap-4 p-3 border border-border rounded-lg bg-slate-50"
          >
            <div class="w-20 h-20 flex-shrink-0 bg-white border border-border rounded overflow-hidden">
              <img :src="image.image_url" class="w-full h-full object-cover" />
            </div>

            <div class="flex-1 space-y-1">
              <input
                v-model="image.title"
                type="text"
                class="flex h-8 w-full rounded-md border border-input bg-background px-3 py-1 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                placeholder="Kép címe"
              />
              <div class="text-xs text-muted-foreground truncate max-w-md">
                {{ image.image_url }}
              </div>
            </div>

            <div class="flex items-center gap-1">
              <Button
                type="button"
                variant="ghost"
                size="icon"
                :disabled="index === 0"
                @click="moveImage(index, 'up')"
              >
                <Icon name="arrow_upward" size="18" />
              </Button>
              <Button
                type="button"
                variant="ghost"
                size="icon"
                :disabled="index === gallery.images.length - 1"
                @click="moveImage(index, 'down')"
              >
                <Icon name="arrow_downward" size="18" />
              </Button>
              <Button
                type="button"
                variant="ghost"
                size="icon"
                class="text-destructive hover:text-destructive hover:bg-destructive/10"
                @click="removeImage(index)"
              >
                <Icon name="delete" size="18" />
              </Button>
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-3">
        <Button type="button" variant="outline" @click="router.push('/admin/gallery')">
          Mégse
        </Button>
        <Button type="submit" variant="primary" :disabled="isSaving">
          <Icon v-if="isSaving" name="loading" size="18" class="animate-spin mr-2" />
          {{ isEdit ? 'Mentés' : 'Létrehozás' }}
        </Button>
      </div>
    </form>
  </AdminLayout>
</template>
