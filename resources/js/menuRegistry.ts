/**
 * Menu Registry Initialization
 * Registers all menu builders from packages
 */

import { menuRegistry } from '@menu/index.ts'
import { cmsMenuBuilder } from '@cms/index.ts'
import { currencyMenuBuilder } from '@currency/index.ts'
import { mediaMenuBuilder } from "@media/index.ts";
import { userMenuBuilder } from "@user/index.ts";
import { articleScraperMenuBuilder } from "@article-scraper/index.ts";
import { themeMenuBuilder } from "@theme/index.ts";
import { rssWatcherMenuBuilder } from "@rss-watcher/index.ts";
import { languageMenuBuilder } from "@language/index.ts";
import { productMenuBuilder } from "@product/index.ts";
import { cmsRelationsMenuBuilder } from "@cms-relations/index.ts";
import { customerMenuBuilder } from "@customer/index.ts";
import { orderMenuBuilder } from "@order/index.ts";
import { purchaseMenuBuilder } from "@purchase/index.ts";
import { stockMenuBuilder } from "@stock/index.ts";
import { unasMenuBuilder } from "@unas/index.ts";
import { textMiningMenuBuilder } from "@text-mining/index.ts";
import { scraperMenuBuilder } from "@scraper/index.ts";

menuRegistry.register(mediaMenuBuilder)
menuRegistry.register(userMenuBuilder)
menuRegistry.register(cmsMenuBuilder)
menuRegistry.register(articleScraperMenuBuilder)
menuRegistry.register(themeMenuBuilder)
menuRegistry.register(rssWatcherMenuBuilder)
menuRegistry.register(languageMenuBuilder)
menuRegistry.register(currencyMenuBuilder)
menuRegistry.register(productMenuBuilder)
menuRegistry.register(cmsRelationsMenuBuilder)
menuRegistry.register(customerMenuBuilder)
menuRegistry.register(orderMenuBuilder)
menuRegistry.register(purchaseMenuBuilder)
menuRegistry.register(stockMenuBuilder)
menuRegistry.register(unasMenuBuilder)
menuRegistry.register(textMiningMenuBuilder)
menuRegistry.register(scraperMenuBuilder)