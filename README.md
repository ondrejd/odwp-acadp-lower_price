# Pravidelné snižování cen

Jednoduchý plugin pro [WordPress][1], který pravidelně snižuje cenu produktů vytvořeného pomocí pluginu [Advanced Classifieds &amp; Directory Pro][2].

## Upozornění

__Plugin je vytvořen na míru webu [Nepotrebujem.eu][3], takže je pravděpodobné, že jej pro vaše použití budete muset upravit. Pokud o něco takového máte zájem, můžete si to objednat i přímo od [autora][4].__

__Plugin vyžaduje plugin [Advanced Classifieds & Directory Pro][2] - bez tohoto pluginu nainstalovaného nebudete moci aktivovat tento plugin__.

## Poznámka k implementaci

Plugin předpokládá, že produkty, které plugin <abbr title="Advanced Classifieds & Directory Pro">_ACADP_</abbr> vytváří jsou rozšířené o hodnoty `price_orig`, `price_reduce` a `price_reduce_days` - na příklad řešení se podívejte na tento [Gist][5].

## Popis pluginu

* plugin pravidelně snižuje cenu u produktů vytvořených pomocí pluginu <abbr title="Advanced Classifieds & Directory Pro">_ACADP_</abbr>, která obsahují meta hodnoty `price`, `price_orig`, `price_reduce` a `price_reduce_days`,
* plugin umožňuje nastavit, kdy bude příslušna _WP\_Cron_ úloha spuštěna,
* plugin obsahuje přehledný výpis o provedených změnách cen,
* plugin obsahuje přehledný výpis <abbr title="Advanced Classifieds & Directory Pro">_ACADP_</abbr> produktů z hlediska jejich ceny a jejích stavu a vývoje.

[1]: https://wordpress.org/
[2]: https://wordpress.org/plugins/advanced-classifieds-and-directory-pro/
[3]: https://nepotrebujem.eu/
[4]: mailto:ondrejd@gmail.com
[5]: about:blank

