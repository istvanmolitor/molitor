/**
 * Menu Registry Initialization
 * Registers all menu builders from packages
 */

import { menuRegistry } from '@menu/index.ts'
import { AdminMenuBuilder } from '@admin/lib/AdminMenuBuilder.ts'
// import { SettingsMenuBuilder } from '@admin/config/settingsMenuBuilder'
import { cmsMenuBuilder } from '@cms/index.ts'
import { cmsRelationsMenuBuilder } from '@cms-relations/index.ts'
import { currencyMenuBuilder } from '@currency/index.ts'
import { mediaMenuBuilder } from "@media/index.ts";
import { userMenuBuilder } from "@user/index.ts";
import { articleScraperMenuBuilder } from "@article-scraper/index.ts";
import { themeMenuBuilder } from "@theme/index.ts";
import { rssWatcherMenuBuilder } from "@rss-watcher/index.ts";
import { languageMenuBuilder } from "@language/index.ts";
import { productMenuBuilder } from "@product/index.ts";
import { stockMenuBuilder } from "@stock/index.ts";
import { galleryMenuBuilder } from "@gallery/index.ts";
import { unasMenuBuilder } from "@unas";
import { orderMenuBuilder } from "@order";
import { customerMenuBuilder } from "@customer/index.ts";
import { addressMenuBuilder } from "@address/index.ts";

// Register admin menu builders
const adminMenuBuilder = new AdminMenuBuilder()
// const settingsMenuBuilder = new SettingsMenuBuilder()

menuRegistry.register(adminMenuBuilder)
menuRegistry.register(mediaMenuBuilder)
menuRegistry.register(userMenuBuilder)
menuRegistry.register(cmsMenuBuilder)
menuRegistry.register(cmsRelationsMenuBuilder)
menuRegistry.register(articleScraperMenuBuilder)
menuRegistry.register(themeMenuBuilder)
menuRegistry.register(rssWatcherMenuBuilder)
menuRegistry.register(languageMenuBuilder)
menuRegistry.register(currencyMenuBuilder)
menuRegistry.register(productMenuBuilder)
menuRegistry.register(stockMenuBuilder)
menuRegistry.register(galleryMenuBuilder)
menuRegistry.register(unasMenuBuilder)
menuRegistry.register(orderMenuBuilder)
menuRegistry.register(customerMenuBuilder)
menuRegistry.register(addressMenuBuilder)

export { adminMenuBuilder, cmsMenuBuilder, cmsRelationsMenuBuilder }

