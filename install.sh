mkdir packages
cd packages
git clone git@github.com:istvanmolitor/admin.git
git clone git@github.com:istvanmolitor/article-parser.git
git clone git@github.com:istvanmolitor/article-scraper.git
git clone git@github.com:istvanmolitor/scraper.git
git clone git@github.com:istvanmolitor/cms.git
git clone git@github.com:istvanmolitor/cms-search.git
git clone git@github.com:istvanmolitor/html-parser.git
git clone git@github.com:istvanmolitor/language.git
git clone git@github.com:istvanmolitor/media.git
git clone git@github.com:istvanmolitor/menu.git
git clone git@github.com:istvanmolitor/rss-watcher.git
git clone git@github.com:istvanmolitor/setting.git
git clone git@github.com:istvanmolitor/theme.git
git clone git@github.com:istvanmolitor/user.git
git clone git@github.com:istvanmolitor/currency.git
git clone git@github.com:istvanmolitor/contact.git
git clone git@github.com:istvanmolitor/address.git
git clone git@github.com:istvanmolitor/cms-post-relations.git
git clone git@github.com:istvanmolitor/customer.git
git clone git@github.com:istvanmolitor/customer-product.git
git clone git@github.com:istvanmolitor/gallery.git
git clone git@github.com:istvanmolitor/order.git
git clone git@github.com:istvanmolitor/product.git
git clone git@github.com:istvanmolitor/purchase.git
git clone git@github.com:istvanmolitor/stock.git
git clone git@github.com:istvanmolitor/unas.git
git clone git@github.com:istvanmolitor/post-calendar.git
git clone git@github.com:istvanmolitor/tree.git
git clone git@github.com:istvanmolitor/shop.git
git clone git@github.com:istvanmolitor/text-mining.git

cd ../resources/js
mkdir packages
cd packages

git clone git@github.com:istvanmolitor/vue-admin.git
git clone git@github.com:istvanmolitor/vue-cms.git
git clone git@github.com:istvanmolitor/vue-media.git
git clone git@github.com:istvanmolitor/ts-menu.git
git clone git@github.com:istvanmolitor/vue-rss-watcher.git
git clone git@github.com:istvanmolitor/vue-theme.git
git clone git@github.com:istvanmolitor/vue-user.git
git clone git@github.com:istvanmolitor/vue-language.git
git clone git@github.com:istvanmolitor/vue-currency.git
git clone git@github.com:istvanmolitor/vue-contact.git
git clone git@github.com:istvanmolitor/vue-cms-post-relations.git
git clone git@github.com:istvanmolitor/vue-customer.git
git clone git@github.com:istvanmolitor/vue-customer-product.git
git clone git@github.com:istvanmolitor/vue-gallery.git
git clone git@github.com:istvanmolitor/vue-order.git
git clone git@github.com:istvanmolitor/vue-product.git
git clone git@github.com:istvanmolitor/vue-purchase.git
git clone git@github.com:istvanmolitor/vue-stock.git
git clone git@github.com:istvanmolitor/vue-unas.git
git clone git@github.com:istvanmolitor/vue-address.git
git clone git@github.com:istvanmolitor/vue-text-mining.git
git clone git@github.com:istvanmolitor/vue-article-scraper.git

cd ../../../
if [ ! -f .env ]; then
    cp .env.example .env
fi

composer install

./vendor/bin/sail up -d

./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
