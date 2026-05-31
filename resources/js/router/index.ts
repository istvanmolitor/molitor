import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'

// Import routes from all packages
import userRoutes from '@user/router/index'
import cmsRoutes from '@cms/router/index'
import currencyRoutes from '@currency/router/index'
import mediaRoutes from '@media/router/index'
import adminRoutes from '@admin/router/index'
import rssWatcherRoutes from '@rss-watcher/router/index'
import themeRoutes from '@theme/router/index'
import languageRoutes from '@language/router/index'
import articleScraperRoutes from '@article-scraper/router/index'
import productRoutes from '@product/router/index'
import unasRoutes from '@unas/router/index'
import { galleryRoutes } from '@gallery/router/index'
import { customerRoutes } from '@customer/router'
import { addressRoutes } from '@address/router'
import cmsRelationRoutes from '@cms-relations/router/index'
import orderRoutes from '@order/router/index'
import purchaseRoutes from '@purchase/router/index'
import stockRoutes from '@stock/router/index'

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
  ...currencyRoutes,
  ...mediaRoutes,
  ...adminRoutes,
  ...rssWatcherRoutes,
  ...themeRoutes,
  ...languageRoutes,
  ...articleScraperRoutes,
  ...productRoutes,
  ...unasRoutes,
  ...galleryRoutes,
  ...customerRoutes,
  ...addressRoutes,
  ...cmsRelationRoutes,
  ...orderRoutes,
  ...purchaseRoutes,
  ...stockRoutes,
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



