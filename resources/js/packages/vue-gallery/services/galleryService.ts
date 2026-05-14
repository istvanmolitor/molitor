import { createApiClient } from '@user/services/apiClient'

const api = createApiClient()

export interface GalleryImage {
  id?: number
  gallery_id?: number
  image_url: string
  title?: string | null
  order: number
}

export interface Gallery {
  id?: number
  name: string
  slug: string
  description?: string | null
  images: GalleryImage[]
  created_at?: string
  updated_at?: string
}

export interface PaginatedResponse<T> {
  data: T[]
  links: {
    first: string
    last: string
    prev: string | null
    next: string | null
  }
  meta: {
    current_page: number
    from: number
    last_page: number
    path: string
    per_page: number
    to: number
    total: number
  }
}

export interface SingleResponse<T> {
  data: T
}

export const galleryService = {
  getAll(params?: { page?: number }) {
    return api.get<PaginatedResponse<Gallery>>('/api/galleries', { params })
  },
  getById(id: number | string) {
    return api.get<SingleResponse<Gallery>>(`/api/galleries/${id}`)
  },
  create(gallery: Gallery) {
    return api.post<SingleResponse<Gallery>>('/api/galleries', gallery)
  },
  update(id: number | string, gallery: Gallery) {
    return api.put<SingleResponse<Gallery>>(`/api/galleries/${id}`, gallery)
  },
  delete(id: number | string) {
    return api.delete(`/api/galleries/${id}`)
  },
}
