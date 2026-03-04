/**
 * Menu Registry Initialization
 * Registers all menu builders from packages
 */

import { menuRegistry } from '@menu/index.ts'
import { AdminMenuBuilder } from '@admin/lib/AdminMenuBuilder.ts'
// import { SettingsMenuBuilder } from '@admin/config/settingsMenuBuilder'
import { cmsMenuBuilder } from '@cms/index.ts'
import { mediaMenuBuilder } from "@media/index.ts";

// Register admin menu builders
const adminMenuBuilder = new AdminMenuBuilder()
// const settingsMenuBuilder = new SettingsMenuBuilder()

menuRegistry.register(adminMenuBuilder)
// menuRegistry.register(settingsMenuBuilder)
menuRegistry.register(mediaMenuBuilder)

// Register CMS menu builder
menuRegistry.register(cmsMenuBuilder)

// Export builders if needed elsewhere
export { adminMenuBuilder, cmsMenuBuilder }

