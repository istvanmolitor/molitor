mkdir packages
cd packages
git clone git@github.com:istvanmolitor/admin.git
git clone git@github.com:istvanmolitor/article-parser.git
git clone git@github.com:istvanmolitor/article-scraper.git
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

cd ../resources/js
mkdir packages
cd packages

git clone git@github.com:istvanmolitor/vue-admin.git
git clone git@github.com:istvanmolitor/vue-article-scraper.git
git clone git@github.com:istvanmolitor/vue-cms.git
git clone git@github.com:istvanmolitor/vue-media.git
git clone git@github.com:istvanmolitor/ts-menu.git
git clone git@github.com:istvanmolitor/vue-rss-watcher.git
git clone git@github.com:istvanmolitor/vue-theme.git
git clone git@github.com:istvanmolitor/vue-user.git
git clone git@github.com:istvanmolitor/vue-language.git

cd ../../../
if [ ! -f .env ]; then
    cp .env.example .env
fi

composer install

./vendor/bin/sail up -d

./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
