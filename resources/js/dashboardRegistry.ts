import { dashboardRegistry } from '@admin/lib/DashboardRegistry'
import { userDashboardBuilder } from '@user'
import { orderDashboardBuilder } from '@order'

dashboardRegistry.register(userDashboardBuilder)
dashboardRegistry.register(orderDashboardBuilder)


