import type { RouteRecordRaw } from 'vue-router'

export const galleryRoutes: RouteRecordRaw[] = [
  {
    path: '/admin/gallery',
    name: 'admin.gallery.index',
    component: () => import('../views/gallery/GalleryIndex.vue'),
    meta: {
      auth: true,
      permission: 'gallery.view'
    }
  },
  {
    path: '/admin/gallery/create',
    name: 'admin.gallery.create',
    component: () => import('../views/gallery/GalleryEdit.vue'),
    meta: {
      auth: true,
      permission: 'gallery.create'
    }
  },
  {
    path: '/admin/gallery/:id/edit',
    name: 'admin.gallery.edit',
    component: () => import('../views/gallery/GalleryEdit.vue'),
    meta: {
      auth: true,
      permission: 'gallery.edit'
    }
  }
]
