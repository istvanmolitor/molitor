import type { RouteRecordRaw } from 'vue-router'

export const galleryRoutes: RouteRecordRaw[] = [
  {
    path: '/admin/gallery',
    name: 'admin.gallery.index',
    component: () => import('../views/gallery/GalleryIndex.vue'),
    meta: {
      requiresAuth: true,
      permission: 'gallery'
    }
  },
  {
    path: '/admin/gallery/:id',
    name: 'admin.gallery.show',
    component: () => import('../views/gallery/GalleryShow.vue'),
    meta: {
      requiresAuth: true,
      permission: 'gallery'
    }
  },
  {
    path: '/admin/gallery/create',
    name: 'admin.gallery.create',
    component: () => import('../views/gallery/GalleryEdit.vue'),
    meta: {
      requiresAuth: true,
      permission: 'gallery'
    }
  },
  {
    path: '/admin/gallery/:id/edit',
    name: 'admin.gallery.edit',
    component: () => import('../views/gallery/GalleryEdit.vue'),
    meta: {
      requiresAuth: true,
      permission: 'gallery'
    }
  }
]
