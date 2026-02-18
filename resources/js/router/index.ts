import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'

// Import routes from all packages
import userRoutes from '@user/router/index'
import cmsRoutes from '@cms/router/index'
import mediaRoutes from '@media/router/index'
import adminRoutes from '@admin/router/index'
import rssWatcherRoutes from '@rss-watcher/router/index'

// Import guards
import { authGuard } from '@user/router/guards'

// Combine all routes
const routes: RouteRecordRaw[] = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  ...userRoutes,
  ...cmsRoutes,
  ...mediaRoutes,
  ...adminRoutes,
  ...rssWatcherRoutes,
  // 404 catch-all route
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFound.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Apply auth guard to all routes
router.beforeEach(authGuard)

export default router



