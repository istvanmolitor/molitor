/**
 * Menu Registry Initialization
 * Registers all menu builders from packages
 */

import { menuRegistry } from '@menu/index'
import { AdminMenuBuilder } from '@admin/config/adminMenuBuilder'
import { SettingsMenuBuilder } from '@admin/config/settingsMenuBuilder'
import { cmsMenuBuilder } from '@cms/index'

// Register admin menu builders
const adminMenuBuilder = new AdminMenuBuilder()
const settingsMenuBuilder = new SettingsMenuBuilder()

menuRegistry.register(adminMenuBuilder)
menuRegistry.register(settingsMenuBuilder)

// Register CMS menu builder
menuRegistry.register(cmsMenuBuilder)

// Export builders if needed elsewhere
export { adminMenuBuilder, settingsMenuBuilder, cmsMenuBuilder }

